<?php

namespace Database\Seeders;

use App\Models\ClassSubjectTeacher;
use App\Models\Exam;
use Illuminate\Database\Seeder;

/** 15 mata pelajaran x 35 soal (5 tipe soal) untuk kelas X IPA 1. Data dummy. */
class ExamSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ClassSubjectTeacher::with('subject')->where('class_id', 101)->get() as $cst) {
            [$name, $duration] = SubjectSeeder::SUBJECTS[$cst->subject_code];

            $exam = Exam::updateOrCreate(['cst_id' => $cst->cst_id, 'title' => "Ujian TKA $name"], [
                'type' => 'uas',
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(60),
                'duration_minutes' => $duration,
                'total_questions' => 35,
                'is_published' => true,
                'require_face_photo' => true,
                'instructions' => 'Kerjakan dengan jujur. Berpindah tab/jendela akan dicatat sebagai pelanggaran. Esai dikerjakan di lembar jawaban lalu difoto dan diunggah.',
            ]);

            if ($exam->questions()->count() === 35) {
                continue;
            }
            $exam->questions()->delete();
            mt_srand(crc32($cst->cst_id));

            for ($n = 1; $n <= 35; $n++) {
                $text = "Soal contoh $name nomor $n (data dummy).";
                if ($n <= 20) {
                    $q = ['pilihan_ganda', ['Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D'], mt_rand(0, 3), 2];
                } elseif ($n <= 25) {
                    $k = array_rand([0, 1, 2, 3, 4], 2);
                    $q = ['pilihan_ganda_kompleks', ['Opsi 1', 'Opsi 2', 'Opsi 3', 'Opsi 4', 'Opsi 5'], array_values($k), 3];
                } elseif ($n <= 29) {
                    $q = ['benar_salah', ['Pernyataan 1', 'Pernyataan 2', 'Pernyataan 3'], [(bool) mt_rand(0, 1), (bool) mt_rand(0, 1), (bool) mt_rand(0, 1)], 3];
                } elseif ($n <= 33) {
                    $q = ['menjodohkan', ['left' => ['Premis 1', 'Premis 2', 'Premis 3'], 'right' => ['Jawaban X', 'Jawaban Y', 'Jawaban Z']], ['0' => 1, '1' => 2, '2' => 0], 3];
                } else {
                    $q = ['esai', null, null, 0]; // esai tanpa bobot
                }
                $exam->questions()->create([
                    'question_text' => $text, 'question_type' => $q[0], 'options' => $q[1],
                    'correct_answer' => $q[2], 'score_weight' => $q[3],
                ]);
            }
        }
    }
}
