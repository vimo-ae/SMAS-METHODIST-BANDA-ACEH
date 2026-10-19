<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\ReportNote;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

/** Wali kelas: rekap nilai otomatis & cetak e-rapor. */
class HomeroomController extends Controller
{
    private function classOf(Request $request, SchoolClass $class): SchoolClass
    {
        abort_unless((string) $class->homeroom_teacher_id === (string) $request->user()->teacher->nip, 403, 'Anda bukan wali kelas ini.');

        return $class;
    }

    public function index(Request $request)
    {
        $classes = SchoolClass::withCount('students')->where('homeroom_teacher_id', $request->user()->teacher->nip)->get();

        return view('guru.homeroom.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class, ReportCardService $svc)
    {
        $this->classOf($request, $class);
        $semester = $request->input('semester', 'ganjil');
        $class->load('students.user');
        $rows = $class->students->map(function ($st) use ($svc, $semester, $class) {
            $d = $svc->build($st, $semester, $class->academic_year);

            return ['student' => $st, 'average' => $d['average'], 'count' => count($d['rows'])];
        })->sortByDesc('average')->values();

        return view('guru.homeroom.show', compact('class', 'rows', 'semester'));
    }

    public function note(Request $request, SchoolClass $class, Student $student)
    {
        $this->classOf($request, $class);
        abort_unless((int) $student->class_id === (int) $class->class_id, 404);
        $v = $request->validate(['semester' => 'required|in:ganjil,genap', 'attitude' => 'nullable|string|max:30', 'note' => 'nullable|string|max:1000']);
        ReportNote::updateOrCreate(
            ['students_id' => $student->nis, 'semester' => $v['semester'], 'academic_year' => $class->academic_year],
            ['attitude' => $v['attitude'] ?? null, 'note' => $v['note'] ?? null]
        );

        return back()->with('success', 'Catatan wali kelas tersimpan.');
    }

    public function report(Request $request, SchoolClass $class, Student $student, ReportCardService $svc)
    {
        $this->classOf($request, $class);
        abort_unless((int) $student->class_id === (int) $class->class_id, 404);

        return view('report.print', $this->reportData($student, $request->input('semester', 'ganjil'), $svc) + ['canNote' => true]);
    }

    public function printAll(Request $request, SchoolClass $class, ReportCardService $svc)
    {
        $this->classOf($request, $class);
        $semester = $request->input('semester', 'ganjil');
        $reports = $class->students()->with('user')->get()->map(fn ($st) => $this->reportData($st, $semester, $svc));

        return view('report.print_all', compact('reports'));
    }

    public function reportData(Student $student, string $semester, ReportCardService $svc): array
    {
        $student->loadMissing('user', 'schoolClass.homeroomTeacher.user');
        $year = $student->schoolClass->academic_year;
        $att = Attendance::where('students_id', $student->nis)->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return [
            'student' => $student,
            'semester' => $semester,
            'year' => $year,
            'data' => $svc->build($student, $semester, $year),
            'note' => ReportNote::where(['students_id' => $student->nis, 'semester' => $semester, 'academic_year' => $year])->first(),
            'attendance' => $att,
        ];
    }
}
