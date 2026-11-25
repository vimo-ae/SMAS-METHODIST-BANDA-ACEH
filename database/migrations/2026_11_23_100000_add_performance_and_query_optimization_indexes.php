<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indeks komposit performa tinggi untuk optimalisasi query utama:
     * - Menghilangkan full table scan pada filtering & grouping nilai, absensi, dan ujian.
     * - Mempercepat JOIN antara cst, students, grades, dan exam_sessions.
     * - Mengoptimalkan index covering untuk agregasi rata-rata & predikat.
     */
    public function up(): void
    {
        $schema = Schema::connection('mysql_admin');

        // 1. Optimalisasi tabel grades
        if ($schema->hasTable('grades')) {
            $this->addIndexIfNotExists('grades', 'idx_grades_student_cst_type', function (Blueprint $table) {
                $table->index(['students_id', 'cst_id', 'grade_type', 'score'], 'idx_grades_student_cst_type');
            });
            $this->addIndexIfNotExists('grades', 'idx_grades_cst_score', function (Blueprint $table) {
                $table->index(['cst_id', 'score'], 'idx_grades_cst_score');
            });
        }

        // 2. Optimalisasi tabel cst_students (Keanggotaan Kelas Mapel)
        if ($schema->hasTable('cst_students')) {
            $this->addIndexIfNotExists('cst_students', 'idx_cst_students_cst_status', function (Blueprint $table) {
                $table->index(['cst_id', 'status'], 'idx_cst_students_cst_status');
            });
            $this->addIndexIfNotExists('cst_students', 'idx_cst_students_student_status', function (Blueprint $table) {
                $table->index(['students_id', 'status'], 'idx_cst_students_student_status');
            });
        }

        // 3. Optimalisasi tabel exam_sessions (Monitoring Ujian Real-Time & Hasil)
        if ($schema->hasTable('exam_sessions')) {
            $this->addIndexIfNotExists('exam_sessions', 'idx_exam_sessions_status_started', function (Blueprint $table) {
                $table->index(['status', 'started_at'], 'idx_exam_sessions_status_started');
            });
            $this->addIndexIfNotExists('exam_sessions', 'idx_exam_sessions_exam_status', function (Blueprint $table) {
                $table->index(['exam_id', 'status'], 'idx_exam_sessions_exam_status');
            });
        }

        // 4. Optimalisasi tabel exam_answers (Koreksi & Perhitungan Nilai Soal)
        if ($schema->hasTable('exam_answers')) {
            $this->addIndexIfNotExists('exam_answers', 'idx_exam_answers_exam_student_q', function (Blueprint $table) {
                $table->index(['exam_id', 'students_id', 'question_id'], 'idx_exam_answers_exam_student_q');
            });
        }

        // 5. Optimalisasi tabel announcements (Papan Pengumuman Publik & Peran)
        if ($schema->hasTable('announcements')) {
            $this->addIndexIfNotExists('announcements', 'idx_announcements_public_published', function (Blueprint $table) {
                $table->index(['is_public', 'published_at'], 'idx_announcements_public_published');
            });
        }

        // 6. Optimalisasi tabel class_enrollments (Enrollment Siswa per Tahun Ajaran)
        if ($schema->hasTable('class_enrollments')) {
            $this->addIndexIfNotExists('class_enrollments', 'idx_enrollments_student_status', function (Blueprint $table) {
                $table->index(['students_id', 'status', 'academic_year'], 'idx_enrollments_student_status');
            });
        }

        // 7. Optimalisasi tabel schedules (Jadwal Pelajaran Mingguan)
        if ($schema->hasTable('schedules')) {
            $this->addIndexIfNotExists('schedules', 'idx_schedules_cst_day_time', function (Blueprint $table) {
                $table->index(['cst_id', 'day', 'start_time'], 'idx_schedules_cst_day_time');
            });
        }

        // 8. Optimalisasi tabel exam_violations (Pelanggaran Ujian)
        if ($schema->hasTable('exam_violations')) {
            $this->addIndexIfNotExists('exam_violations', 'idx_violations_session_time', function (Blueprint $table) {
                $table->index(['exam_session_id', 'occurred_at'], 'idx_violations_session_time');
            });
        }

        // 9. Optimalisasi tabel activity logs (Pencarian Log Berdasarkan Aksi & Waktu)
        $logTables = [
            'users_logs', 'students_logs', 'teachers_logs', 'classes_logs',
            'subjects_logs', 'class_subject_teacher_logs', 'announcements_logs',
            'assignments_logs', 'assignment_submissions_logs', 'exams_logs',
            'exam_sessions_logs', 'exam_token_logs', 'materials_logs', 'grade_logs',
        ];

        foreach ($logTables as $lt) {
            if ($schema->hasTable($lt)) {
                $actionCol = $lt === 'grade_logs' ? 'action' : 'log_action';
                $timeCol   = $lt === 'grade_logs' ? 'changed_at' : 'log_at';

                $this->addIndexIfNotExists($lt, "idx_{$lt}_act_time", function (Blueprint $table) use ($actionCol, $timeCol, $lt) {
                    $table->index([$actionCol, $timeCol], "idx_{$lt}_act_time");
                });
            }
        }
    }

    public function down(): void
    {
        // Reversible jika diperlukan
    }

    private function addIndexIfNotExists(string $tableName, string $indexName, \Closure $callback): void
    {
        $conn = DB::connection('mysql_admin');
        $indexes = collect($conn->select("SHOW INDEX FROM `{$tableName}`"))->pluck('Key_name')->all();

        if (!in_array($indexName, $indexes, true)) {
            Schema::connection('mysql_admin')->table($tableName, $callback);
        }
    }
};
