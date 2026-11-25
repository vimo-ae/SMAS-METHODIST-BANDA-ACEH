<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Katalog mata pelajaran berdasarkan sumber sekolah yang sudah terkonfirmasi. */
class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/school_structure_2026_2027.php');

        if (! is_file($path)) {
            throw new RuntimeException('SubjectSeeder: file struktur sekolah tidak ditemukan.');
        }

        $structure = require $path;
        $subjects = is_array($structure) ? ($structure['subjects'] ?? []) : [];

        if (! is_array($subjects) || $subjects === []) {
            throw new RuntimeException('SubjectSeeder: katalog mata pelajaran kosong/tidak valid.');
        }

        $rows = [];
        foreach ($subjects as $code => $name) {
            $code = strtoupper(trim((string) $code));
            $name = trim((string) $name);

            if ($code === '' || $name === '' || strlen($code) > 20) {
                throw new RuntimeException('SubjectSeeder: kode/nama mata pelajaran tidak valid.');
            }

            $rows[$code] = $name;
        }

        $created = 0;
        $preserved = 0;
        $conflicts = [];

        DB::transaction(function () use ($rows, &$created, &$preserved, &$conflicts): void {
            foreach ($rows as $code => $name) {
                $existing = Subject::query()->find($code);

                if ($existing) {
                    $preserved++;
                    if ((string) $existing->name !== $name) {
                        $conflicts[] = "{$code}: nama pada database berbeda; data lama dipertahankan.";
                    }
                    continue;
                }

                Subject::query()->create(['code' => $code, 'name' => $name]);
                $created++;
            }
        });

        foreach ($conflicts as $conflict) {
            $this->command?->warn('SubjectSeeder: ' . $conflict);
        }

        $pending = count($structure['pending_cst'] ?? []);
        if ($pending > 0) {
            $this->command?->warn(
                "SubjectSeeder: {$pending} penugasan mata pelajaran ditahan karena pemetaan sumber belum dikonfirmasi."
            );
        }

        $this->command?->info(
            'SubjectSeeder: ' . $created . ' mata pelajaran baru dibuat; '
            . $preserved . ' mata pelajaran lama dipertahankan.'
        );
    }
}
