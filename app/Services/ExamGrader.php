<?php

namespace App\Services;

use App\Models\ExamQuestion;
use App\Models\ExamSession;
use Illuminate\Support\Facades\DB;

/**
 * Koreksi otomatis berdasarkan kunci jawaban yang dimasukkan guru.
 * Esai TIDAK berbobot dan tidak dikoreksi otomatis.
 */
class ExamGrader
{
    /** @return array{0: float, 1: bool|null} [skor, benar-penuh?] */
    public function gradeQuestion(ExamQuestion $q, mixed $answer): array
    {
        $weight = (float) $q->score_weight;
        $key = $q->correct_answer;

        if ($q->isEssay() || $answer === null || $answer === '' || $answer === []) {
            return [0.0, $q->isEssay() ? null : false];
        }

        switch ($q->question_type) {
            case 'pilihan_ganda':
                $ok = (string) $answer === (string) $key;
                return [$ok ? $weight : 0.0, $ok];

            case 'pilihan_ganda_kompleks':
                $key = array_map('intval', (array) $key);
                $ans = array_map('intval', (array) $answer);
                if (empty($key)) {
                    return [0.0, false];
                }
                $right = count(array_intersect($ans, $key));
                $wrong = count(array_diff($ans, $key));
                $ratio = max(0, ($right - $wrong) / count($key));
                $full = $right === count($key) && $wrong === 0;
                return [round($weight * $ratio, 2), $full];

            case 'benar_salah':
                $key = array_values((array) $key);
                $ans = (array) $answer;
                if (empty($key)) {
                    return [0.0, false];
                }
                $match = 0;
                foreach ($key as $i => $k) {
                    if (isset($ans[$i]) && filter_var($ans[$i], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === (bool) $k) {
                        $match++;
                    }
                }
                return [round($weight * $match / count($key), 2), $match === count($key)];

            case 'menjodohkan':
                $key = (array) $key;
                $ans = (array) $answer;
                if (empty($key)) {
                    return [0.0, false];
                }
                $match = 0;
                foreach ($key as $left => $right) {
                    if (isset($ans[$left]) && (string) $ans[$left] === (string) $right) {
                        $match++;
                    }
                }
                return [round($weight * $match / count($key), 2), $match === count($key)];
        }

        return [0.0, false];
    }

    /**
     * Koreksi seluruh jawaban sesi. Skor dan is_correct ditulis ke exam_answers,
     * sesi hanya ditandai selesai (tidak ada lagi skor di exam_sessions).
     */
    public function gradeSession(ExamSession $session): ExamSession
    {
        return DB::transaction(function () use ($session) {
            $exam = $session->exam()->with('questions')->first();
            $answers = $exam->answers()->where('students_id', $session->students_id)->get()->keyBy('question_id');

            foreach ($exam->questions as $q) {
                if ($q->isEssay()) {
                    continue; // esai dinilai manual oleh guru
                }
                $row = $answers->get($q->exam_question_id);
                [$score, $ok] = $this->gradeQuestion($q, $row?->answer);

                if ($row) {
                    // exam_answers tanpa primary key tunggal: update lewat query
                    \App\Models\ExamAnswer::where('exam_id', $row->exam_id)
                        ->where('students_id', $row->students_id)
                        ->where('question_id', $row->question_id)
                        ->update(['score' => $score, 'is_correct' => $ok]);
                }
            }

            $session->update([
                'status' => 'selesai',
                'finished_at' => $session->finished_at ?? now(),
            ]);

            $this->recordToGradebook($session);   // BARU

            return $session->fresh();
        });
    }

    /** Nilai objektif sesi, skala 0-100, dihitung dari exam_answers. */
    public function objectiveScore(ExamSession $session): float
    {
        $exam = $session->exam()->with('questions')->first();
        $total = $exam->totalWeight();
        if ($total <= 0) {
            return 0.0;
        }

        $earned = \App\Models\ExamAnswer::where('exam_id', $session->exam_id)
            ->where('students_id', $session->students_id)
            ->whereIn('question_id', $exam->questions->where('question_type', '!=', 'esai')->pluck('exam_question_id'))
            ->sum('score');

        return round($earned / $total * 100, 2);
    }

    /** Validasi: apakah kunci jawaban soal lengkap & masuk akal? */
    public static function validateKey(string $type, mixed $options, mixed $key): ?string
    {
        return match ($type) {
            'pilihan_ganda' => (is_array($options) && count($options) >= 2 && $key !== null && isset($options[(int) $key]))
                ? null : 'Pilihan ganda butuh minimal 2 opsi dan satu kunci jawaban.',
            'pilihan_ganda_kompleks' => (is_array($options) && count($options) >= 2 && is_array($key) && count($key) >= 1)
                ? null : 'Pilihan ganda kompleks butuh minimal 2 opsi dan minimal satu kunci.',
            'benar_salah' => (is_array($options) && count($options) >= 1 && is_array($key) && count($key) === count($options))
                ? null : 'Setiap pernyataan benar/salah wajib punya kunci.',
            'menjodohkan' => (is_array($options) && count($options['left'] ?? []) >= 2 && count($options['right'] ?? []) >= 2 && is_array($key) && count($key) === count($options['left']))
                ? null : 'Pasangan jawaban belum lengkap.',
            default => null,
        };
    }

    /** Masukkan nilai ujian ke buku nilai (dihitung sistem, graded_by = null). */
public function recordToGradebook(ExamSession $session): void
{
    $exam = $session->exam;

    \App\Models\Grade::updateOrCreate(
        ['students_id' => $session->students_id, 'exam_id' => $exam->exam_id],
        [
            'cst_id' => $exam->cst_id,
            'grade_type' => $exam->exam_type,   // uts | uas
            'score' => $this->objectiveScore($session),
            'graded_by' => null,
            'graded_at' => now(),
        ]
    );
}

    /** Ringkasan untuk siswa/orang tua: benar, salah, tidak dijawab, nilai, lulus atau tidak. */
    public function summary(ExamSession $session): array
    {
        $exam = $session->exam()->with('questions')->first();
        $objective = $exam->questions->where('question_type', '!=', 'esai');
        $essays = $exam->questions->where('question_type', 'esai');

        $answers = \App\Models\ExamAnswer::where('exam_id', $session->exam_id)
            ->where('students_id', $session->students_id)
            ->whereIn('question_id', $objective->pluck('exam_question_id'))
            ->get();

        $correct = $answers->where('is_correct', true)->count();
        $wrong = $answers->filter(
            fn ($a) => $a->is_correct === false && $a->answer !== null && $a->answer !== [] && $a->answer !== ''
        )->count();
        $total = $objective->count();
        $score = $this->objectiveScore($session);

        $essayGraded = \App\Models\ExamAnswer::where('exam_id', $session->exam_id)
            ->where('students_id', $session->students_id)
            ->whereIn('question_id', $essays->pluck('exam_question_id'))
            ->whereNotNull('graded_at')
            ->count();

        return [
            'total' => $total,
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => max(0, $total - $correct - $wrong),
            'score' => $score,
            'pass_score' => (float) $exam->pass_score,
            'passed' => $score >= (float) $exam->pass_score,
            'essay_total' => $essays->count(),
            'essay_graded' => $essayGraded,
        ];
    }
}