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
use App\Models\ForumThread;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Material;
use App\Models\ParentMessage;
use App\Models\ReportNote;
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

            AssignmentSubmission::firstOrCreate(
                ['assignment_id' => $as->assignment_id, 'students_id' => $students[0]->nis],
                ['submitted_at' => now()->subDay(), 'score' => mt_rand(75, 95), 'feedback' => 'Bagus, pertahankan.']
            );

            ForumThread::firstOrCreate(['cst_id' => $cst->cst_id, 'title' => "Bingung soal $name"], [
                'body' => 'Bu/Pak, bagaimana cara mengerjakan latihan nomor 3?',
                'created_by' => $students[1]->nis,
            ])->replies()->firstOrCreate([
                'user_id' => $cst->teacher_id,
                'content' => 'Perhatikan rumus dasar di Modul Bab 1, lalu coba lagi ya.',
            ]);

            // nilai rapor dummy
            foreach ($students as $st) {
                foreach (['uts' => [70, 95], 'uas' => [68, 95]] as $type => [$lo, $hi]) {
                    Grade::updateOrCreate([
                        'students_id' => $st->nis,
                        'subject_code' => $code,
                        'semester' => 'ganjil',
                        'academic_year' => '2025/2026',
                        'grade_type' => $type,
                    ], ['score' => mt_rand($lo, $hi)]);
                }
            }
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

            $grader->gradeSession($session);
        }

        $first = ExamSession::where('exam_id', $exam->exam_id)->orderBy('exam_session_id')->first();
        ExamViolation::firstOrCreate(
            ['exam_session_id' => $first->exam_session_id, 'students_id' => $first->students_id, 'type' => 'tab_switch'],
            ['detail' => 'Berpindah tab (dummy)', 'occurred_at' => now()->subHours(2)]
        );

        ReportNote::updateOrCreate(
            ['students_id' => $students[0]->nis, 'semester' => 'ganjil', 'academic_year' => '2025/2026'],
            ['attitude' => 'Baik', 'note' => 'Pertahankan semangat belajar.']
        );

        // Pesan orang tua → guru matematika
        $guardian = Guardian::whereHas('students', fn ($q) => $q->where('students.nis', $students[0]->nis))->first();
        $mtk = ClassSubjectTeacher::find('101-MTK-G');
        ParentMessage::firstOrCreate(
            ['parent_user_id' => $guardian->nik, 'subject' => 'Perkembangan nilai Matematika'],
            [
                'teacher_user_id' => $mtk->teacher_id,
                'students_id' => $students[0]->nis,
                'type' => 'pertanyaan',
                'message' => 'Apakah anak saya perlu les tambahan untuk Matematika?',
                'reply' => 'Nilainya sudah baik, cukup rutin latihan soal di rumah.',
                'replied_by' => $mtk->teacher_id,
                'replied_at' => now(),
            ]
        );

        Announcement::firstOrCreate(['title' => 'Jadwal Ujian Semester Ganjil'], [
            'content' => 'Ujian berbasis komputer dimulai pekan depan. Pastikan perangkat dan kamera berfungsi.',
            'target_roles' => ['admin', 'guru', 'siswa', 'orangtua'],
            'created_by' => 1,
        ]);

        Announcement::firstOrCreate(['title' => 'Pengambilan E-Rapor'], [
            'content' => 'Orang tua dapat memantau nilai anak dari akun masing-masing.',
            'target_roles' => ['orangtua', 'siswa'],
            'created_by' => 1,
        ]);
    }
}