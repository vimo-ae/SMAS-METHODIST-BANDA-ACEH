<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hak akses database per role (strict least privilege):
 * Objek yang tidak di-GRANT otomatis tidak muncul dan tidak bisa diakses oleh user tersebut.
 */
return new class extends Migration
{
    private const HOSTS = ['localhost', '127.0.0.1'];

    private const NO_ACCESS = ['migrations', 'failed_jobs', 'job_batches', 'jobs'];

    private const READ_ONLY_AUDIT = ['grade_logs'];

    public function up(): void
    {
        $db = DB::getDatabaseName();
        $accounts = $this->accounts();

        // 1. Buat user & reset hak akses lama agar bersih
        foreach ($accounts as [$user, $pass]) {
            foreach (self::HOSTS as $host) {
                $account = $this->q($user) . '@' . $this->q($host);
                DB::unprepared("CREATE USER IF NOT EXISTS {$account} IDENTIFIED BY " . $this->q($pass));
                DB::unprepared("ALTER USER {$account} IDENTIFIED BY " . $this->q($pass));
                DB::unprepared("REVOKE ALL PRIVILEGES, GRANT OPTION FROM {$account}");
            }
        }

        // 2. Inventarisasi objek database aktual
        $tables = collect(DB::select(
            "SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'",
            [$db]
        ))->pluck('name')->reject(fn ($t) => in_array($t, self::NO_ACCESS, true))->all();

        $views = collect(DB::select(
            "SELECT TABLE_NAME AS name FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?",
            [$db]
        ))->pluck('name')->all();

        $routines = collect(DB::select(
            "SELECT ROUTINE_NAME AS name, ROUTINE_TYPE AS type FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ?",
            [$db]
        ));

        // 3. Matriks ketat objek yang boleh dilihat & diakses per role
        $guru = [
            'select' => [
                'users', 'students', 'teachers', 'classes', 'subjects', 'schedules',
                'sessions', 'cache', 'cache_locks',
            ],
            'dml' => [
                'class_subject_teacher', 'cst_students', 'announcements', 'material',
                'assignments', 'assignment_submissions',
                'exams', 'exam_token', 'exam_questions', 'exam_sessions', 'exam_answers', 'exam_violations',
                'grades',
            ],
            'views' => [
                'v_gradebook', 'v_student_grade_summary', 'v_exam_results',
                'v_exam_statistics', 'v_violation_summary', 'v_assignment_status',
                'v_essay_pending', 'v_class_roster', 'v_teaching_schedule',
            ],
            'routines' => [
                'fn_exam_objective_score', 'fn_is_exam_open', 'fn_score_pct',
                'fn_student_average', 'fn_violation_count',
                'sp_grade_assignment', 'sp_record_exam_grade', 'sp_sync_student_grades',
                'sp_find_expired_sessions', 'sp_generate_join_code', 'sp_exam_participants',
            ],
        ];

        $siswa = [
            'select' => [
                'users', 'students', 'classes', 'subjects', 'schedules',
                'class_subject_teacher', 'announcements', 'material',
                'assignments', 'exams', 'exam_questions',
            ],
            'dml' => [
                'cst_students', 'assignment_submissions',
                'exam_sessions', 'exam_answers', 'exam_violations',
                'exam_token',
                'grades',
                'sessions', 'cache', 'cache_locks',
            ],
            'views' => [
                'v_gradebook', 'v_student_grade_summary', 'v_teaching_schedule',
            ],
            'routines' => [
                'fn_is_exam_open', 'fn_student_average',
                'sp_join_class', 'sp_record_exam_grade', 'sp_public_check_grades',
            ],
        ];

        [$adminUser] = $accounts['admin'];
        [$guruUser]  = $accounts['guru'];
        [$siswaUser] = $accounts['siswa'];

        foreach (self::HOSTS as $host) {
            // ================= ADMIN: Semua objek aplikasi =================
            foreach ($tables as $t) {
                $privs = in_array($t, self::READ_ONLY_AUDIT, true) ? 'SELECT' : 'SELECT, INSERT, UPDATE, DELETE';
                $this->grant($privs, "`{$db}`.`{$t}`", $adminUser, $host);
            }
            foreach ($views as $v) {
                $this->grant('SELECT', "`{$db}`.`{$v}`", $adminUser, $host);
            }
            foreach ($routines as $r) {
                $this->grant('EXECUTE', "{$r->type} `{$db}`.`{$r->name}`", $adminUser, $host);
            }

            // ================= GURU =================
            $this->applyRoleGrants($db, $guruUser, $host, $guru, $tables, $views, $routines, 'SELECT, INSERT, UPDATE, DELETE');

            // ================= SISWA =================
            $this->applyRoleGrants($db, $siswaUser, $host, $siswa, $tables, $views, $routines, 'SELECT, INSERT, UPDATE');
        }

        DB::unprepared('FLUSH PRIVILEGES');
    }

    public function down(): void
    {
        foreach (['admin', 'guru', 'siswa'] as $key) {
            $user = config("database.connections.mysql_{$key}.username");
            if (! $user) {
                continue;
            }
            foreach (self::HOSTS as $host) {
                DB::unprepared('DROP USER IF EXISTS ' . $this->q($user) . '@' . $this->q($host));
            }
        }

        DB::unprepared('FLUSH PRIVILEGES');
    }

    private function applyRoleGrants(
        string $db,
        string $user,
        string $host,
        array $rules,
        array $existingTables,
        array $existingViews,
        $existingRoutines,
        string $writePrivs
    ): void {
        foreach ($rules['select'] as $t) {
            if (in_array($t, $existingTables, true)) {
                $this->grant('SELECT', "`{$db}`.`{$t}`", $user, $host);
            }
        }
        foreach ($rules['dml'] as $t) {
            if (in_array($t, $existingTables, true)) {
                $this->grant($writePrivs, "`{$db}`.`{$t}`", $user, $host);
            }
        }
        foreach ($rules['views'] as $v) {
            if (in_array($v, $existingViews, true)) {
                $this->grant('SELECT', "`{$db}`.`{$v}`", $user, $host);
            }
        }
        foreach ($existingRoutines as $r) {
            if (in_array($r->name, $rules['routines'], true)) {
                $this->grant('EXECUTE', "{$r->type} `{$db}`.`{$r->name}`", $user, $host);
            }
        }
    }

    private function accounts(): array
    {
        $accounts = [];
        foreach (['admin', 'guru', 'siswa'] as $key) {
            $cfg  = config("database.connections.mysql_{$key}");
            $user = $cfg['username'] ?? null;
            $pass = $cfg['password'] ?? null;

            if (! $user || ! $pass) {
                throw new RuntimeException(
                    'Isi DB_' . strtoupper($key) . '_USERNAME dan DB_' . strtoupper($key) . '_PASSWORD di .env, lalu jalankan php artisan config:clear.'
                );
            }
            $accounts[$key] = [$user, $pass];
        }

        return $accounts;
    }

    private function q(string $value): string
    {
        return DB::getPdo()->quote($value);
    }

    private function grant(string $privileges, string $on, string $user, string $host): void
    {
        DB::unprepared("GRANT {$privileges} ON {$on} TO " . $this->q($user) . '@' . $this->q($host));
    }
};