<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\ExamSession;
use App\Models\Grade;
use App\Models\Student;

/**
 * Menyinkronkan nilai aktivitas ke buku nilai (tabel grades) berbasis cst_id.
 * Tidak ada lagi akumulasi rapor. Nama kelas dipertahankan agar pemanggil lama tetap jalan.
 */
class ReportCardService
{
    public function syncFromActivity(Student $student, ClassSubjectTeacher $cst): void
    {
        // Tugas: satu baris per tugas, skala 0-100
        $assignments = Assignment::where('cst_id', $cst->cst_id)->get()->keyBy('assignment_id');
        $subs = AssignmentSubmission::whereIn('assignment_id', $assignments->keys())
            ->where('students_id', $student->nis)->whereNotNull('score')->get();

        foreach ($subs as $s) {
            $max = max($assignments[$s->assignment_id]->max_score, 1);
            Grade::updateOrCreate(
                ['students_id' => $student->nis, 'assignment_id' => $s->assignment_id],
                [
                    'cst_id' => $cst->cst_id,
                    'grade_type' => 'tugas',
                    'score' => round($s->score / $max * 100, 2),
                    'graded_at' => now(),
                ]
            );
        }

        // Ujian yang sudah selesai
        $grader = app(ExamGrader::class);
        ExamSession::where('students_id', $student->nis)
            ->where('status', 'selesai')
            ->whereHas('exam', fn ($q) => $q->where('cst_id', $cst->cst_id))
            ->with('exam')->get()
            ->each(fn ($session) => $grader->recordToGradebook($session));
    }
}