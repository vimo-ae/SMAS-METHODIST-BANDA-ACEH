<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua guru berdasarkan NIP
        $teachers = Teacher::orderBy('nip')->get();

        $classes = [
            [
                'class_id' => 101,
                'name' => 'X IPA 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 102,
                'name' => 'X IPA 2',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 103,
                'name' => 'X IPS 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 104,
                'name' => 'X IPS 2',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 105,
                'name' => 'XI IPA 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 106,
                'name' => 'XI IPA 2',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 107,
                'name' => 'XI IPS 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 108,
                'name' => 'XI IPS 2',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 109,
                'name' => 'XII IPA 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 110,
                'name' => 'XII IPA 2',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 111,
                'name' => 'XII IPS 1',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 112,
                'name' => 'XII IPS 2',
                'academic_year' => '2025/2026',
            ],
        ];

        foreach ($classes as $index => $class) {

            // Ambil guru sesuai urutan
            $teacher = $teachers->get($index);

            SchoolClass::updateOrCreate(
                [
                    'class_id' => $class['class_id'],
                ],
                [
                    'name' => $class['name'],

                    // Menggunakan NIP sebagai FK
                    'homeroom_teacher_id' => $teacher?->nip,

                    'academic_year' => $class['academic_year'],
                ]
            );
        }
    }
}