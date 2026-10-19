<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Grade;
use App\Services\Access;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

/** Input nilai mata pelajaran untuk rapor (tugas / kuis / UTS / UAS). */
class GradeController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->where('teacher_id', $request->user()->teacher->nip)->orderBy('class_id')->get();

        return view('guru.grades.index', compact('classes'));
    }

    public function show(Request $request, ClassSubjectTeacher $cst, ReportCardService $svc)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $cst->load('subject', 'schoolClass.students.user');

        $rows = [];
        foreach ($cst->schoolClass->students as $st) {
            $svc->syncFromActivity($st, $cst);
            $g = Grade::where(['students_id' => $st->nis, 'subject_code' => $cst->subject_code,
                'semester' => $cst->semester, 'academic_year' => $cst->academic_year])
                ->pluck('score', 'grade_type')->all();
            $rows[] = ['student' => $st, 'g' => $g, 'final' => $svc->finalScore($g)];
        }

        return view('guru.grades.show', compact('cst', 'rows'));
    }

    public function store(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $request->validate(['scores' => 'required|array', 'scores.*.*' => 'nullable|numeric|min:0|max:100']);
        $nisList = $cst->schoolClass->students()->pluck('nis')->map(fn ($n) => (string) $n)->all();

        foreach ($request->input('scores') as $nis => $types) {
            abort_unless(in_array((string) $nis, $nisList, true), 403);
            foreach ($types as $type => $score) {
                abort_unless(in_array($type, ['tugas', 'kuis', 'uts', 'uas'], true), 422);
                if ($score === null || $score === '') {
                    continue;
                }
                Grade::updateOrCreate([
                    'students_id' => $nis, 'subject_code' => $cst->subject_code,
                    'semester' => $cst->semester, 'academic_year' => $cst->academic_year, 'grade_type' => $type,
                ], ['score' => $score]);
            }
        }

        return back()->with('success', 'Nilai tersimpan. Nilai akhir rapor dihitung otomatis.');
    }
}
