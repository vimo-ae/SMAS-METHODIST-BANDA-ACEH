<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ExamViolation;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Student;
use App\Services\ExamGrader;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);
        $students = Student::where('class_id', 101)->orderBy('nis')->get();
        $joinedCodes = ['MTK', 'FIS', 'BIO', 'KIM', 'BIN']; // sisanya dipakai latihan "join kelas"

        foreach ($joinedCodes as $code) {
            $cst = ClassSubjectTeacher::find("101-$code-G");
            $cst->joinedStudents()->syncWithoutDetaching($students->pluck('nis')->all());
            $name = $cst->subject->name;

            Material::firstOrCreate(['cst_id' => $cst->cst_id, 'title' => "Modul $name Bab 1"], [
                'description' => 'Ringkasan materi (dummy). Guru dapat mengunggah PDF/PPT sebenarnya.',
                'uploaded_at' => now(),
            ]);

            $as = Assignment::firstOrCreate(['cst_id' => $cst->cst_id, 'title' => "Tugas 1 $name"], [
                'description' => 'Kerjakan latihan halaman 10 dan unggah hasilnya.',
                'due_date' => now()->addDays(7),
                'max_score' => 100,
            ]);

            $score = mt_rand(75, 95);

            AssignmentSubmission::firstOrCreate(
                ['assignment_id' => $as->assignment_id, 'students_id' => $students[0]->nis],
                ['submitted_at' => now()->subDay(), 'score' => $score, 'feedback' => 'Bagus, pertahankan.']
            );

            // Buku nilai e-class: nilai tugas diperiksa guru (graded_by = guru mapel)
            Grade::updateOrCreate(
                ['students_id' => $students[0]->nis, 'assignment_id' => $as->assignment_id],
                [
                    'cst_id' => $cst->cst_id,
                    'grade_type' => 'tugas',
                    'score' => round($score / max($as->max_score, 1) * 100, 2),
                    'graded_by' => $cst->teacher_id,
                    'graded_at' => now(),
                ]
            );
        }

        // Contoh sesi ujian selesai untuk 3 siswa pada ujian Matematika
        $exam = Exam::with('questions')->where('cst_id', '101-MTK-G')->first();
        $grader = app(ExamGrader::class);

        foreach ($students->take(3) as $idx => $st) {
            $session = ExamSession::updateOrCreate(
                ['exam_id' => $exam->exam_id, 'students_id' => $st->nis],
                [
                    'started_at' => now()->subHours(3),
                    'finished_at' => now()->subHours(2),
                    'status' => 'selesai',
                ]
            );

            foreach ($exam->questions as $q) {
                $correct = mt_rand(0, 100) < 70;
                $ans = match ($q->question_type) {
                    'pilihan_ganda' => $correct ? $q->correct_answer : ($q->correct_answer + 1) % 4,
                    'pilihan_ganda_kompleks' => $correct ? $q->correct_answer : [$q->correct_answer[0]],
                    'benar_salah' => $correct ? $q->correct_answer : array_map(fn ($b) => ! $b, $q->correct_answer),
                    'menjodohkan' => $correct ? $q->correct_answer : ['0' => 0, '1' => 1, '2' => 2],
                    default => null,
                };

                if ($ans !== null) {
                    ExamAnswer::updateOrCreate(
                        [
                            'exam_id' => $exam->exam_id,
                            'students_id' => $st->nis,
                            'question_id' => $q->exam_question_id,
                        ],
                        ['answer' => $ans]
                    );
                }
            }

            // Mengoreksi jawaban dan (lewat recordToGradebook) memasukkan nilai ujian ke buku nilai
            $grader->gradeSession($session);
        }

        $first = ExamSession::where('exam_id', $exam->exam_id)->orderBy('exam_session_id')->first();
        ExamViolation::firstOrCreate(
            ['exam_session_id' => $first->exam_session_id, 'students_id' => $first->students_id, 'type' => 'tab_switch'],
            ['detail' => 'Berpindah tab (dummy)', 'occurred_at' => now()->subHours(2)]
        );

        Announcement::firstOrCreate(['title' => 'Jadwal Ujian Semester Ganjil'], [
            'content' => 'Ujian berbasis komputer dimulai pekan depan. Pastikan perangkat dan kamera berfungsi.',
            'target_roles' => ['admin', 'guru', 'siswa'],
            'created_by' => 1,
        ]);

        Announcement::firstOrCreate(['title' => 'Cek Nilai untuk Orang Tua'], [
            'content' => 'Orang tua dapat mengecek nilai anak melalui menu Cek Nilai tanpa perlu login.',
            'target_roles' => ['siswa'],
            'created_by' => 1,
        ]);
    }
}