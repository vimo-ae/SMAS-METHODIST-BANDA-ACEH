<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Audit READ-ONLY untuk memetakan data lama yang harus dipertahankan. */
class LegacyDataAuditSeeder extends Seeder
{
    private const NEW_CLASS_IDS = [201, 202, 203, 204, 205, 206];

    public function run(): void
    {
        $bar = str_repeat('=', 68);
        $this->out($bar);
        $this->out('AUDIT DATA LAMA (READ-ONLY) — tidak ada data yang diubah/dihapus');
        $this->out($bar);

        $oldStudents = collect();
        if (Schema::hasTable('users') && $this->hasAllColumns('users', ['user_id', 'role', 'name', 'email'])) {
            $oldStudents = DB::table('users')
                ->where('role', 'siswa')
                ->where(function ($query) {
                    $query->where('name', 'like', 'Siswa %')
                        ->orWhere('email', 'like', 'siswa%@%');
                })
                ->pluck('user_id');
        }
        $this->out("\n[A] Akun siswa dummy lama        : " . $oldStudents->count());

        $oldTeachers = collect();
        if (Schema::hasTable('users') && $this->hasAllColumns('users', ['user_id', 'role', 'email'])) {
            $oldTeachers = DB::table('users')
                ->where('role', 'guru')
                ->where(function ($query) {
                    $query->where('email', 'like', '%.guru@methodistbandaaceh.sch.id')
                        ->orWhere('user_id', 'like', '12345678998%');
                })
                ->pluck('user_id');
        }
        $this->out('[B] Akun guru dummy lama         : ' . $oldTeachers->count());

        $oldClasses = collect();
        $old2526 = 0;
        if (Schema::hasTable('classes') && $this->hasAllColumns('classes', ['class_id', 'academic_year'])) {
            $oldClasses = DB::table('classes')->whereNotIn('class_id', self::NEW_CLASS_IDS)->pluck('class_id');
            $old2526 = DB::table('classes')->where('academic_year', '2025/2026')->count();
        }
        $this->out('[C] Kelas lama (bukan 201–206)   : ' . $oldClasses->count() . " (T.A. 2025/2026: {$old2526})");

        $oldCst = collect();
        if (Schema::hasTable('class_subject_teacher') && $this->hasAllColumns('class_subject_teacher', ['cst_id', 'class_id'])) {
            $oldCst = DB::table('class_subject_teacher')
                ->whereNotIn('class_id', self::NEW_CLASS_IDS)
                ->pluck('cst_id');
        }
        $this->out('[D] CST lama (kelas bukan 201–206) : ' . $oldCst->count());

        $this->out("\n[E] Data akademik terhubung (HARUS DIPERTAHANKAN):");
        $preserve = 0;
        $preserve += $this->countLinked('grades', 'students_id', $oldStudents->all(), 'nilai (grades) siswa lama');
        $preserve += $this->countLinked('exam_sessions', 'student_id', $oldStudents->all(), 'sesi ujian siswa lama');
        $preserve += $this->countLinked('exam_answers', 'students_id', $oldStudents->all(), 'jawaban ujian siswa lama');
        $preserve += $this->countLinked('assignment_submissions', 'students_id', $oldStudents->all(), 'pengumpulan tugas siswa lama');
        $preserve += $this->countLinked('exams', 'cst_id', $oldCst->all(), 'ujian pada CST lama');
        $preserve += $this->countLinked('assignments', 'cst_id', $oldCst->all(), 'tugas pada CST lama');
        $preserve += $this->countLinked('materials', 'cst_id', $oldCst->all(), 'materi pada CST lama');
        $preserve += $this->countLinked('schedules', 'cst_id', $oldCst->all(), 'jadwal pada CST lama');

        $this->out("\n{$bar}");
        $this->out('RINGKASAN REKONSILIASI');
        $this->out($bar);
        $this->out("  • Data akademik lama yang perlu dipertahankan: {$preserve} baris.");
        $this->out('  • Kelas baru memakai class_id 201–206 dan tidak memakai ulang ID lama.');
        $this->out('  • Seeder ini hanya membaca; tidak menghapus, memindahkan, atau mengubah data.');
        $this->out('  • Pengarsipan/penghapusan data lama memerlukan persetujuan admin dan proses terpisah.');
        $this->out($bar);
    }

    private function hasAllColumns(string $table, array $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function countLinked(string $table, string $column, array $ids, string $label): int
    {
        if ($ids === [] || ! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0;
        }

        $count = DB::table($table)->whereIn($column, $ids)->count();
        if ($count > 0) {
            $this->out("    - {$label}: {$count}");
        }

        return $count;
    }

    private function out(string $message): void
    {
        $this->command?->getOutput()?->writeln($message);
    }
}
