<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Kolom yang terlibat FK (tabel, kolom, definisi baru) */
    private function upKeyColumns(): array
    {
        $c = $this->collate();

        $cols = [
            ['users',               'user_id',             "VARCHAR(18) {$c} NOT NULL"],
            ['teachers',            'nip',                 "VARCHAR(18) {$c} NOT NULL"],
            ['classes',             'homeroom_teacher_id', "VARCHAR(18) {$c} NULL"],
            ['class_subject_teacher','teacher_id',         "VARCHAR(18) {$c} NOT NULL"],
            ['grades',              'graded_by',           "VARCHAR(18) {$c} NULL"],
            ['exam_answers',        'graded_by',           "VARCHAR(18) {$c} NULL"],
            ['announcements',       'created_by',          "VARCHAR(18) {$c} NOT NULL"],
            ['notifications',       'user_id',             "VARCHAR(18) {$c} NOT NULL"],
            ['sessions',            'user_id',             "VARCHAR(18) {$c} NULL"],
            ['students',            'nis',                 "VARCHAR(10) {$c} NOT NULL"],
        ];

        foreach ($this->studentTables() as $t) {
            $cols[] = [$t, 'students_id', "VARCHAR(10) {$c} NOT NULL"];
        }

        return $cols;
    }

    private function downKeyColumns(): array
    {
        $cols = [
            ['users',               'user_id',             'BIGINT UNSIGNED NOT NULL'],
            ['teachers',            'nip',                 'BIGINT UNSIGNED NOT NULL'],
            ['classes',             'homeroom_teacher_id', 'BIGINT UNSIGNED NULL'],
            ['class_subject_teacher','teacher_id',         'BIGINT UNSIGNED NOT NULL'],
            ['grades',              'graded_by',           'BIGINT UNSIGNED NULL'],
            ['exam_answers',        'graded_by',           'BIGINT UNSIGNED NULL'],
            ['announcements',       'created_by',          'BIGINT UNSIGNED NOT NULL'],
            ['notifications',       'user_id',             'BIGINT UNSIGNED NOT NULL'],
            ['sessions',            'user_id',             'BIGINT UNSIGNED NULL'],
            ['students',            'nis',                 'BIGINT UNSIGNED NOT NULL'],
        ];

        foreach ($this->studentTables() as $t) {
            $cols[] = [$t, 'students_id', 'BIGINT UNSIGNED NOT NULL'];
        }

        return $cols;
    }

    private function studentTables(): array
    {
        return [
            'assignment_submissions', 'grades', 'exam_answers',
            'cst_students', 'exam_sessions', 'exam_violations', 'class_enrollments',
        ];
    }

    private function collate(): string
    {
        $conn = config('database.connections.' . DB::getDefaultConnection());
        $charset = $conn['charset'] ?? 'utf8mb4';
        $collation = $conn['collation'] ?? 'utf8mb4_unicode_ci';

        return "CHARACTER SET {$charset} COLLATE {$collation}";
    }

    /**
     * Drop FK terkait -> ubah kolom -> pasang FK lagi.
     */
    private function rebuildKeys(array $changes): void
    {
        $targets = [];
        foreach ($changes as [$t, $c]) {
            $targets["{$t}.{$c}"] = true;
        }

        $rows = DB::select("
            SELECT kcu.TABLE_NAME AS tbl, kcu.CONSTRAINT_NAME AS name,
                   kcu.COLUMN_NAME AS col, kcu.REFERENCED_TABLE_NAME AS ref_tbl,
                   kcu.REFERENCED_COLUMN_NAME AS ref_col,
                   rc.UPDATE_RULE AS on_update, rc.DELETE_RULE AS on_delete
            FROM information_schema.KEY_COLUMN_USAGE kcu
            JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
              ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
             AND rc.CONSTRAINT_NAME   = kcu.CONSTRAINT_NAME
             AND rc.TABLE_NAME        = kcu.TABLE_NAME
            WHERE kcu.TABLE_SCHEMA = ?
              AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY kcu.TABLE_NAME, kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION
        ", [DB::getDatabaseName()]);

        // Kelompokkan per constraint (mendukung FK komposit)
        $fks = [];
        foreach ($rows as $r) {
            $key = "{$r->tbl}.{$r->name}";
            $fks[$key]['tbl']       = $r->tbl;
            $fks[$key]['name']      = $r->name;
            $fks[$key]['ref_tbl']   = $r->ref_tbl;
            $fks[$key]['on_update'] = $r->on_update;
            $fks[$key]['on_delete'] = $r->on_delete;
            $fks[$key]['cols'][]    = $r->col;
            $fks[$key]['ref_cols'][] = $r->ref_col;
        }

        // Ambil hanya FK yang menyentuh kolom target (sisi referensi atau direferensi)
        $affected = array_filter($fks, function ($fk) use ($targets) {
            foreach ($fk['cols'] as $i => $col) {
                if (isset($targets["{$fk['tbl']}.{$col}"])
                    || isset($targets["{$fk['ref_tbl']}.{$fk['ref_cols'][$i]}"])) {
                    return true;
                }
            }
            return false;
        });

        // 1. Drop FK
        foreach ($affected as $fk) {
            DB::statement("ALTER TABLE `{$fk['tbl']}` DROP FOREIGN KEY `{$fk['name']}`");
        }

        // 2. Ubah tipe kolom
        foreach ($changes as [$t, $c, $def]) {
            DB::statement("ALTER TABLE `{$t}` MODIFY `{$c}` {$def}");
        }

        // 3. Pasang FK kembali
        foreach ($affected as $fk) {
            $cols    = '`' . implode('`,`', $fk['cols']) . '`';
            $refCols = '`' . implode('`,`', $fk['ref_cols']) . '`';

            DB::statement("
                ALTER TABLE `{$fk['tbl']}`
                ADD CONSTRAINT `{$fk['name']}`
                FOREIGN KEY ({$cols}) REFERENCES `{$fk['ref_tbl']}` ({$refCols})
                ON UPDATE {$fk['on_update']} ON DELETE {$fk['on_delete']}
            ");
        }
    }

    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            return;
        }

        $c = $this->collate();

        // ---- Kolom kunci (user_id, nip, nis, dst.) ----
        $this->rebuildKeys($this->upKeyColumns());

        // NISN tetap CHAR(10)
        DB::statement("ALTER TABLE students MODIFY nisn CHAR(10) {$c} NULL");

        // ---- Kode / token / tahun ajaran ----
        DB::statement('ALTER TABLE class_subject_teacher MODIFY join_code CHAR(8) NULL');
        DB::statement('ALTER TABLE exam_sessions MODIFY token CHAR(40) NOT NULL');
        DB::statement('ALTER TABLE classes MODIFY academic_year CHAR(9) NOT NULL');
        DB::statement('ALTER TABLE class_subject_teacher MODIFY academic_year CHAR(9) NOT NULL');
        DB::statement('ALTER TABLE class_enrollments MODIFY academic_year CHAR(9) NOT NULL');

        // ---- status -> ENUM ----
        DB::statement("ALTER TABLE students MODIFY status ENUM('aktif','lulus','pindah','drop_out') NOT NULL DEFAULT 'aktif'");
        DB::statement("ALTER TABLE teachers MODIFY status ENUM('aktif','cuti','pensiun','resign') NOT NULL DEFAULT 'aktif'");
        DB::statement("ALTER TABLE exam_sessions MODIFY status ENUM('berlangsung','selesai','keluar') NOT NULL DEFAULT 'berlangsung'");

        // ---- Panjang teks ----
        DB::statement('ALTER TABLE users MODIFY name VARCHAR(100) NOT NULL');
        DB::statement('ALTER TABLE classes MODIFY name VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE students MODIFY pob VARCHAR(50) NULL');
        DB::statement('ALTER TABLE students MODIFY address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE teachers MODIFY address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE notifications MODIFY message TEXT NOT NULL');
    }

    public function down(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            return;
        }

        DB::statement('ALTER TABLE notifications MODIFY message VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE teachers MODIFY address TEXT NULL');
        DB::statement('ALTER TABLE students MODIFY address TEXT NULL');
        DB::statement('ALTER TABLE students MODIFY pob VARCHAR(100) NULL');
        DB::statement('ALTER TABLE classes MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY name VARCHAR(255) NOT NULL');

        DB::statement("ALTER TABLE exam_sessions MODIFY status VARCHAR(15) NOT NULL DEFAULT 'berlangsung'");
        DB::statement("ALTER TABLE teachers MODIFY status VARCHAR(15) NOT NULL DEFAULT 'aktif'");
        DB::statement("ALTER TABLE students MODIFY status VARCHAR(15) NOT NULL DEFAULT 'aktif'");

        DB::statement('ALTER TABLE class_enrollments MODIFY academic_year VARCHAR(9) NOT NULL');
        DB::statement('ALTER TABLE class_subject_teacher MODIFY academic_year VARCHAR(9) NOT NULL');
        DB::statement('ALTER TABLE classes MODIFY academic_year VARCHAR(9) NOT NULL');
        DB::statement('ALTER TABLE exam_sessions MODIFY token VARCHAR(64) NOT NULL');
        DB::statement('ALTER TABLE class_subject_teacher MODIFY join_code VARCHAR(8) NULL');

        DB::statement('ALTER TABLE students MODIFY nisn VARCHAR(10) NULL');

        $this->rebuildKeys($this->downKeyColumns());
    }
};