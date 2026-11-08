<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\Grade;
use App\Services\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Buku nilai e-class: nilai ujian masuk otomatis, nilai tugas dan lainnya diinput guru. */
class GradeController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->where('teacher_id', $request->user()->teacher->nip)->orderBy('class_id')->get();

        return view('guru.grades.index', compact('classes'));
    }

    public function show(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $cst->load('subject', 'schoolClass.students.user');

        $grades = Grade::where('cst_id', $cst->cst_id)
            ->with('assignment', 'exam')
            ->get()
            ->groupBy('students_id');

        $assignments = Assignment::where('cst_id', $cst->cst_id)->get();

        $rows = [];
        foreach ($cst->schoolClass->students as $st) {
            $list = $grades->get($st->nis, collect());
            $rows[] = [
                'student' => $st,
                'grades' => $list,
                'average' => $list->whereNotNull('score')->isNotEmpty()
                    ? round($list->whereNotNull('score')->avg('score'), 2)
                    : null,
            ];
        }

        return view('guru.grades.show', compact('cst', 'rows', 'assignments'));
    }

    /** Input manual untuk nilai di luar tugas dan ujian (kuis lisan, praktik, dll.). */
    public function store(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $request->validate([
            'students_id' => 'required|string|max:10',
            'grade_type' => 'required|in:kuis,lainnya',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $nisList = $cst->schoolClass->students()->pluck('nis')->map(fn ($n) => (string) $n)->all();
        abort_unless(in_array((string) $request->students_id, $nisList, true), 403);

        Grade::create([
            'students_id' => $request->students_id,
            'cst_id' => $cst->cst_id,
            'grade_type' => $request->grade_type,
            'score' => $request->score,
            'graded_by' => $request->user()->teacher->nip,
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Nilai tersimpan.');
    }

    /** Guru memeriksa tugas: simpan ke submission dan ke buku nilai (skala 0-100). */
    public function gradeAssignment(Request $request, Assignment $assignment, string $nis)
    {
        Access::authorizeTeacher($request->user(), $assignment->classSubjectTeacher);
        $v = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        DB::transaction(function () use ($v, $assignment, $nis, $request) {
            AssignmentSubmission::where('assignment_id', $assignment->assignment_id)
                ->where('students_id', $nis)
                ->update(['score' => $v['score'], 'feedback' => $v['feedback'] ?? null]);

            Grade::updateOrCreate(
                ['students_id' => $nis, 'assignment_id' => $assignment->assignment_id],
                [
                    'cst_id' => $assignment->cst_id,
                    'grade_type' => 'tugas',
                    'score' => round($v['score'] / max($assignment->max_score, 1) * 100, 2),
                    'graded_by' => $request->user()->teacher->nip,
                    'graded_at' => now(),
                ]
            );
        });

        return back()->with('success', 'Nilai tugas tersimpan.');
    }
}