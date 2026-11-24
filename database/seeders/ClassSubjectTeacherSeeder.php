<?php

namespace Database\Seeders;

use App\Models\ClassSubjectTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ClassSubjectTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path(
            'seeders/data/school_structure_2026_2027.php'
        );

        if (! is_file($path)) {
            throw new RuntimeException(
                'ClassSubjectTeacherSeeder: file struktur sekolah tidak ditemukan.'
            );
        }

        $structure = require $path;

        if (! is_array($structure)) {
            throw new RuntimeException(
                'ClassSubjectTeacherSeeder: format data tidak valid.'
            );
        }

        $source = $structure['cst'] ?? [];

        if (! is_array($source) || $source === []) {
            throw new RuntimeException(
                'ClassSubjectTeacherSeeder: data CST kosong.'
            );
        }

        $requiredColumns = [
            'cst_id',
            'class_id',
            'subject_code',
            'teacher_id',
            'academic_year',
            'semester',
            'join_code',
        ];

        if (! Schema::hasTable('class_subject_teacher')) {
            throw new RuntimeException(
                'Tabel class_subject_teacher belum tersedia.'
            );
        }

        foreach ($requiredColumns as $column) {
            if (! Schema::hasColumn('class_subject_teacher', $column)) {
                throw new RuntimeException(
                    "Kolom class_subject_teacher.{$column} tidak tersedia."
                );
            }
        }

        $rows = [];
        $seenIds = [];
        $seenPairs = [];
        $seenJoinCodes = [];

        // Validasi seluruh data sumber.
        foreach ($source as $index => $item) {
            if (! is_array($item)) {
                throw new RuntimeException(
                    'Format data CST tidak valid pada baris ' . ($index + 1)
                );
            }

            $row = [
                'cst_id' => trim((string) ($item['cst_id'] ?? '')),
                'class_id' => (int) ($item['class_id'] ?? 0),
                'subject_code' => trim(
                    (string) ($item['subject_code'] ?? '')
                ),
                'teacher_id' => trim(
                    (string) ($item['teacher_id'] ?? '')
                ),
                'academic_year' => trim(
                    (string) ($item['academic_year'] ?? '')
                ),
                'semester' => strtolower(
                    trim((string) ($item['semester'] ?? ''))
                ),
            ];

            if (
                $row['cst_id'] === ''
                || strlen($row['cst_id']) > 20
                || $row['class_id'] <= 0
                || $row['subject_code'] === ''
                || $row['teacher_id'] === ''
                || strlen($row['teacher_id']) > 30
                || $row['academic_year'] === ''
                || strlen($row['academic_year']) > 9
                || ! in_array($row['semester'], ['ganjil', 'genap'], true)
            ) {
                throw new RuntimeException(
                    'Data CST tidak lengkap atau tidak valid pada baris '
                    . ($index + 1)
                );
            }

            if (isset($seenIds[$row['cst_id']])) {
                throw new RuntimeException(
                    'CST ID duplikat: ' . $row['cst_id']
                );
            }

            $pairKey = implode('|', [
                $row['class_id'],
                $row['subject_code'],
                $row['academic_year'],
                $row['semester'],
            ]);

            if (isset($seenPairs[$pairKey])) {
                throw new RuntimeException(
                    'Pasangan kelas-mapel duplikat: ' . $pairKey
                );
            }

            $seenIds[$row['cst_id']] = true;
            $seenPairs[$pairKey] = true;

            $rows[] = $row;
        }

        // Ambil seluruh referensi yang dibutuhkan.
        $classIds = array_values(array_unique(
            array_column($rows, 'class_id')
        ));

        $teacherIds = array_values(array_unique(
            array_column($rows, 'teacher_id')
        ));

        $subjectCodes = array_values(array_unique(
            array_column($rows, 'subject_code')
        ));

        $classes = SchoolClass::query()
            ->whereIn('class_id', $classIds)
            ->get(['class_id', 'name']);

        $classNames = [];

        foreach ($classes as $class) {
            $classNames[(string) $class->class_id] = $class->name;
        }

        $teachers = Teacher::query()
            ->whereIn('nip', $teacherIds)
            ->pluck('nip')
            ->map(fn ($id) => (string) $id)
            ->all();

        $subjects = Subject::query()
            ->whereIn('code', $subjectCodes)
            ->pluck('code')
            ->map(fn ($code) => (string) $code)
            ->all();

        $missingClasses = array_diff(
            array_map('strval', $classIds),
            array_keys($classNames)
        );

        $missingTeachers = array_diff($teacherIds, $teachers);
        $missingSubjects = array_diff($subjectCodes, $subjects);

        if (
            $missingClasses !== []
            || $missingTeachers !== []
            || $missingSubjects !== []
        ) {
            throw new RuntimeException(
                'Referensi CST belum lengkap. '
                . 'Kelas: ' . implode(',', $missingClasses)
                . '; Guru: ' . implode(',', $missingTeachers)
                . '; Mapel: ' . implode(',', $missingSubjects)
                . '. Periksa seeder pendahulunya.'
            );
        }

        // Cocokkan generator join_code dengan model asli.
        $joinCodes = [];

        foreach ($rows as $row) {
            $joinCode = $this->makeJoinCode(
                $row['subject_code'],
                $classNames[(string) $row['class_id']],
                $row['class_id']
            );

            if (strlen($joinCode) > 8) {
                throw new RuntimeException(
                    "Join code {$joinCode} melebihi VARCHAR(8)."
                );
            }

            if (isset($joinCodes[$joinCode])) {
                throw new RuntimeException(
                    "Join code duplikat dalam sumber: {$joinCode}"
                );
            }

            $joinCodes[$joinCode] = true;
        }

        $created = 0;
        $preserved = 0;
        $conflicts = [];

        DB::transaction(function () use (
            $rows,
            &$created,
            &$preserved,
            &$conflicts
        ): void {
            foreach ($rows as $row) {
                $existing = ClassSubjectTeacher::query()
                    ->where('cst_id', $row['cst_id'])
                    ->first();

                if ($existing) {
                    $same = true;

                    foreach ($row as $column => $value) {
                        if (
                            (string) $existing->{$column}
                            !== (string) $value
                        ) {
                            $same = false;
                            $conflicts[] =
                                "CST {$row['cst_id']}: {$column} berbeda; "
                                . 'data lama dipertahankan.';
                        }
                    }

                    if ($same) {
                        $preserved++;
                    }

                    continue;
                }

                $existingPair = ClassSubjectTeacher::query()
                    ->where('class_id', $row['class_id'])
                    ->where('subject_code', $row['subject_code'])
                    ->where('academic_year', $row['academic_year'])
                    ->where('semester', $row['semester'])
                    ->first();

                if ($existingPair) {
                    $conflicts[] =
                        "Pasangan {$row['class_id']}-"
                        . "{$row['subject_code']} sudah memiliki CST "
                        . "{$existingPair->cst_id}; tidak ditimpa.";

                    continue;
                }

                $joinCode = $this->makeJoinCode(
                    $row['subject_code'],
                    SchoolClass::query()->findOrFail($row['class_id'])->name,
                    $row['class_id']
                );

                $existingCode = ClassSubjectTeacher::query()
                    ->where('join_code', $joinCode)
                    ->first();

                if ($existingCode) {
                    $conflicts[] =
                        "Join code {$joinCode} sudah digunakan oleh "
                        . "{$existingCode->cst_id}; data tidak ditimpa.";

                    continue;
                }

                // Model akan membuat join_code melalui event creating.
                ClassSubjectTeacher::query()->create($row);

                $created++;
            }
        });

        foreach ($conflicts as $conflict) {
            $this->command?->warn($conflict);
        }

        $this->command?->info(
            'ClassSubjectTeacherSeeder selesai: '
            . count($rows) . ' data sumber diperiksa; '
            . $created . ' penugasan baru dibuat; '
            . $preserved . ' penugasan lama dipertahankan; '
            . count($conflicts) . ' konflik dilewati.'
        );

        $pendingCst = $structure['pending_cst'] ?? [];
        if (is_array($pendingCst) && $pendingCst !== []) {
            $this->command?->warn(
                'ClassSubjectTeacherSeeder: ' . count($pendingCst)
                . ' penugasan ditahan sampai pemetaan KES/PKWU dikonfirmasi sekolah.'
            );
        }
    }

    /**
     * Harus sama dengan generator join_code di model.
     */
    private function makeJoinCode(
        string $subjectCode,
        string $className,
        int $classId
    ): string {
        return ClassSubjectTeacher::makeJoinCode($subjectCode, $className, $classId);
    }
}