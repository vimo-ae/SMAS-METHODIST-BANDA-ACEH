<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Services\Access;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;
        $joined = $student->joinedClasses()->with('subject', 'teacher.user', 'schoolClass')->get();

        return view('siswa.classes.index', compact('joined'));
    }

    public function join(Request $request)
    {
        $v = $request->validate(['join_code' => 'required|string|max:8']);
        $student = $request->user()->student;
        $cst = ClassSubjectTeacher::where('join_code', strtoupper(trim($v['join_code'])))->first();

        if (! $cst) {
            return back()->with('error', 'Kode kelas tidak ditemukan.');
        }
        if ((int) $cst->class_id !== (int) $student->class_id) {
            return back()->with('error', 'Kelas ini bukan untuk kelas Anda.');
        }
        $student->joinedClasses()->syncWithoutDetaching([$cst->cst_id => ['joined_at' => now()]]);

        return redirect()->route('siswa.classes.show', $cst)->with('success', 'Berhasil bergabung ke kelas '.$cst->label());
    }

    public function show(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeCst($request->user(), $cst);
        $student = $request->user()->student;
        $cst->load('subject', 'teacher.user', 'schoolClass', 'materials', 'assignments', 'exams');
        $exams = $cst->exams->where('is_published', true);
        $subs = AssignmentSubmission::where('students_id', $student->nis)
            ->whereIn('assignment_id', $cst->assignments->pluck('assignment_id'))->get()->keyBy('assignment_id');
        $sessions = $student->examSessions()->whereIn('exam_id', $exams->pluck('id'))->get()->keyBy('exam_id');

        return view('siswa.classes.show', compact('cst', 'exams', 'subs', 'sessions'));
    }

    public function submit(Request $request, Assignment $assignment, ReportCardService $svc)
    {
        $cst = $assignment->classSubjectTeacher;
        Access::authorizeCst($request->user(), $cst);
        abort_unless($request->user()->isSiswa(), 403);
        $student = $request->user()->student;

        $request->validate(['file' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip,ppt,pptx,xls,xlsx']);
        if (now()->gt($assignment->due_date)) {
            return back()->with('error', 'Batas pengumpulan sudah lewat.');
        }

        $existing = AssignmentSubmission::where(['assignment_id' => $assignment->assignment_id, 'students_id' => $student->nis])->first();
        if ($existing?->score !== null) {
            return back()->with('error', 'Tugas sudah dinilai, tidak dapat diubah.');
        }
        if ($existing?->file_path) {
            Storage::disk('local')->delete($existing->file_path);
        }
        $path = $request->file('file')->store('submissions/'.$assignment->assignment_id, 'local');

        // PK gabungan: update lewat query, bukan updateOrCreate()/save()
        if ($existing) {
            AssignmentSubmission::where('assignment_id', $assignment->assignment_id)
                ->where('students_id', $student->nis)
                ->update(['file_path' => $path, 'submitted_at' => now()]);
        } else {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->assignment_id,
                'students_id' => $student->nis,
                'file_path' => $path,
                'submitted_at' => now(),
            ]);
        }

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
