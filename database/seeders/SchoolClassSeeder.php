<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Seeder kelas SMAS Methodist Banda Aceh T.A. 2026/2027.
 * Kelas baru menggunakan ID 201-206.
 * Data kelas lama tidak dihapus atau ditimpa.
 */
class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path(
            'seeders/data/school_structure_2026_2027.php'
        );

        if (! is_file($path)) {
            throw new RuntimeException(
                'SchoolClassSeeder: file struktur sekolah tidak ditemukan.'
            );
        }

        $structure = require $path;

        if (! is_array($structure)) {
            throw new RuntimeException(
                'SchoolClassSeeder: format struktur sekolah tidak valid.'
            );
        }

        $classes = $structure['classes'] ?? [];

        if (! is_array($classes) || $classes === []) {
            throw new RuntimeException(
                'SchoolClassSeeder: data kelas kosong.'
            );
        }

        $created = 0;
        $preserved = 0;
        $conflicts = [];
        $seenIds = [];

        // Validasi seluruh kelas dan wali kelas sebelum menulis data.
        foreach ($classes as $classData) {
            if (! is_array($classData)) {
                throw new RuntimeException(
                    'SchoolClassSeeder: format baris kelas tidak valid.'
                );
            }

            $classId = (int) ($classData['class_id'] ?? 0);
            $name = trim((string) ($classData['name'] ?? ''));
            $year = trim(
                (string) ($classData['academic_year'] ?? '')
            );

            $homeroomId = $classData['homeroom_teacher_id'] ?? null;

            if (
                $classId <= 0
                || $name === ''
                || $year === ''
            ) {
                throw new RuntimeException(
                    'SchoolClassSeeder: ditemukan data kelas tidak lengkap.'
                );
            }

            if (isset($seenIds[$classId])) {
                throw new RuntimeException(
                    "SchoolClassSeeder: ID kelas duplikat {$classId}."
                );
            }

            $seenIds[$classId] = true;

            if (
                $homeroomId !== null
                && ! Teacher::query()
                    ->whereKey((string) $homeroomId)
                    ->exists()
            ) {
                throw new RuntimeException(
                    "SchoolClassSeeder: wali kelas untuk kelas {$classId} belum tersedia. "
                    . 'Jalankan TeacherSeeder terlebih dahulu.'
                );
            }
        }

        DB::transaction(function () use (
            $classes,
            &$created,
            &$preserved,
            &$conflicts
        ): void {
            foreach ($classes as $classData) {
                $classId = (int) $classData['class_id'];

                $attributes = [
                    'name' => $classData['name'],
                    'homeroom_teacher_id' =>
                        $classData['homeroom_teacher_id'] ?? null,
                    'academic_year' => $classData['academic_year'],
                ];

                $existing = SchoolClass::query()->find($classId);

                if ($existing) {
                    $preserved++;

                    foreach ($attributes as $column => $value) {
                        if (
                            (string) ($existing->{$column} ?? '')
                            !== (string) ($value ?? '')
                        ) {
                            $conflicts[] =
                                "kelas {$classId}: kolom {$column} berbeda "
                                . 'dari sumber; data lama dipertahankan';
                        }
                    }

                    continue;
                }

                SchoolClass::query()->create(
                    ['class_id' => $classId] + $attributes
                );

                $created++;
            }
        });

        // Data kelas lama tidak pernah dihapus.
        $obsolete = $structure['obsolete_class_ids'] ?? [];

        if ($obsolete !== []) {
            $this->command?->warn(
                'SchoolClassSeeder: daftar obsolete_class_ids diabaikan. '
                . 'Tidak ada kelas yang dihapus.'
            );
        }

        if ($conflicts !== []) {
            $this->command?->warn(
                'SchoolClassSeeder: ' . implode('; ', $conflicts)
            );
        }

        $this->command?->info(
            "SchoolClassSeeder: {$created} kelas baru dibuat; "
            . "{$preserved} kelas yang sudah ada dipertahankan."
        );
    }
}