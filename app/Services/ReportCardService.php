<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\ExamSession;
use App\Models\Grade;
use App\Models\Student;

/**
 * Akumulasi nilai rapor otomatis.
 *   Harian = rata-rata(tugas, kuis)   [yang tersedia]
 *   Akhir  = Harian*30% + UTS*30% + UAS*40%   (bisa diubah di config/school.php)
 */
class ReportCardService
{
    /** Sinkronkan nilai tugas/kuis/uts/uas siswa untuk satu kelas-mapel dari data aktivitas. */
    public function syncFromActivity(Student $student, ClassSubjectTeacher $cst): void
    {
        $scores = [];

        // Tugas: skor dinormalkan ke 0-100
        $subs = AssignmentSubmission::whereIn('assignment_id', Assignment::where('cst_id', $cst->cst_id)->pluck('assignment_id'))
            ->where('students_id', $student->nis)->whereNotNull('score')->with('assignment')->get();
        if ($subs->isNotEmpty()) {
            $scores['tugas'] = $subs->avg(fn ($s) => $s->assignment->max_score > 0 ? $s->score / $s->assignment->max_score * 100 : 0);
        }

        // Kuis / UTS / UAS dari sesi ujian yang sudah dinilai
        $sessions = ExamSession::where('students_id', $student->nis)
            ->whereIn('status', ['selesai', 'dinilai'])
            ->whereHas('exam', fn ($q) => $q->where('cst_id', $cst->cst_id))
            ->with('exam')->get()->groupBy(fn ($s) => $s->exam->type);
        foreach (['kuis', 'uts', 'uas'] as $type) {
            if (isset($sessions[$type])) {
                $scores[$type] = $sessions[$type]->avg('objective_score');
            }
        }

        foreach ($scores as $type => $score) {
            Grade::updateOrCreate([
                'students_id' => $student->nis,
                'subject_code' => $cst->subject_code,
                'semester' => $cst->semester,
                'academic_year' => $cst->academic_year,
                'grade_type' => $type,
            ], ['score' => round($score, 2)]);
        }
    }

    public function finalScore(array $s): ?float
    {
        $w = config('school.grade_weights');
        $daily = array_filter([$s['tugas'] ?? null, $s['kuis'] ?? null], fn ($v) => $v !== null);
        $parts = [
            'harian' => $daily ? array_sum($daily) / count($daily) : null,
            'uts' => $s['uts'] ?? null,
            'uas' => $s['uas'] ?? null,
        ];

        $sum = 0.0;
        $weightUsed = 0;
        foreach ($parts as $k => $v) {
            if ($v !== null) {
                $sum += $v * $w[$k];
                $weightUsed += $w[$k];
            }
        }

        return $weightUsed > 0 ? round($sum / $weightUsed, 2) : null;
    }

    public static function predicate(?float $score): string
    {
        if ($score === null) {
            return '-';
        }
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            default => 'D',
        };
    }

    /**
     * Data rapor satu siswa. Mapel diambil dari kelas-mapel (class_subject_teacher)
     * kelas siswa pada semester & tahun ajaran terkait.
     */
    public function build(Student $student, string $semester, string $academicYear): array
    {
        $csts = ClassSubjectTeacher::with('subject', 'teacher.user')
            ->where('class_id', $student->class_id)
            ->where('semester', $semester)->where('academic_year', $academicYear)->get();

        $grades = Grade::where('students_id', $student->nis)
            ->where('semester', $semester)->where('academic_year', $academicYear)->get()
            ->groupBy('subject_code');

        $rows = [];
        foreach ($csts as $cst) {
            $this->syncFromActivity($student, $cst);
            $g = Grade::where([
                'students_id' => $student->nis, 'subject_code' => $cst->subject_code,
                'semester' => $semester, 'academic_year' => $academicYear,
            ])->pluck('score', 'grade_type')->all();

            $final = $this->finalScore($g);
            $rows[] = [
                'subject' => $cst->subject->name ?? $cst->subject_code,
                'teacher' => $cst->teacher?->user?->name,
                'tugas' => $g['tugas'] ?? null, 'kuis' => $g['kuis'] ?? null,
                'uts' => $g['uts'] ?? null, 'uas' => $g['uas'] ?? null,
                'final' => $final,
                'predicate' => self::predicate($final),
                'passed' => $final !== null && $final >= config('school.kkm'),
            ];
        }

        $finals = collect($rows)->pluck('final')->filter(fn ($v) => $v !== null);

        return [
            'rows' => $rows,
            'average' => $finals->isNotEmpty() ? round($finals->avg(), 2) : null,
        ];
    }

    /** Simpan nilai akhir ke tabel grades (grade_type = rapor). */
    public function commitFinal(Student $student, string $semester, string $academicYear): void
    {
        $data = $this->build($student, $semester, $academicYear);
        $csts = ClassSubjectTeacher::where('class_id', $student->class_id)
            ->where('semester', $semester)->where('academic_year', $academicYear)->with('subject')->get();

        foreach ($data['rows'] as $i => $row) {
            if ($row['final'] === null) {
                continue;
            }
            Grade::updateOrCreate([
                'students_id' => $student->nis, 'subject_code' => $csts[$i]->subject_code,
                'semester' => $semester, 'academic_year' => $academicYear, 'grade_type' => 'rapor',
            ], ['score' => $row['final']]);
        }
    }
}
