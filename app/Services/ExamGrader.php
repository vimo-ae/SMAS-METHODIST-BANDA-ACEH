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

    /** Hitung ulang seluruh jawaban sesi dan simpan skor objektif (skala 0-100). */
    public function gradeSession(ExamSession $session): ExamSession
    {
        return DB::transaction(function () use ($session) {
            $exam = $session->exam()->with('questions')->first();
            $answers = $exam->answers()->where('students_id', $session->students_id)->get()->keyBy('question_id');

            $earned = 0.0;
            foreach ($exam->questions as $q) {
                if ($q->isEssay()) {
                    continue;
                }
                $row = $answers->get($q->id);
                [$score, $ok] = $this->gradeQuestion($q, $row?->answer);
                $earned += $score;

                if ($row) {
                    $row->update(['score' => $score, 'is_correct' => $ok]);
                }
            }

            $total = $exam->totalWeight();
            $objective = $total > 0 ? round($earned / $total * 100, 2) : 0.0;
            $hasEssay = $exam->questions->contains(fn ($q) => $q->isEssay());

            $session->update([
                'objective_score' => $objective,
                'status' => $hasEssay && $session->essay_score === null ? 'selesai' : 'dinilai',
                'finished_at' => $session->finished_at ?? now(),
            ]);

            return $session->fresh();
        });
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
}
