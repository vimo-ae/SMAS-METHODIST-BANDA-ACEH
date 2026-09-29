<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            [
                'class_id' => 101,
                'name' => 'X IPA 1',
                'homeroom_teacher_id' => '1985000000001',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 102,
                'name' => 'X IPA 2',
                'homeroom_teacher_id' => '1985000000002',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 103,
                'name' => 'X IPS 1',
                'homeroom_teacher_id' => '1985000000003',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 104,
                'name' => 'X IPS 2',
                'homeroom_teacher_id' => '1985000000004',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 105,
                'name' => 'XI IPA 1',
                'homeroom_teacher_id' => '1985000000005',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 106,
                'name' => 'XI IPA 2',
                'homeroom_teacher_id' => '1985000000006',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 107,
                'name' => 'XI IPS 1',
                'homeroom_teacher_id' => '1985000000007',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 108,
                'name' => 'XI IPS 2',
                'homeroom_teacher_id' => '1985000000008',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 109,
                'name' => 'XII IPA 1',
                'homeroom_teacher_id' => '1985000000009',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 110,
                'name' => 'XII IPA 2',
                'homeroom_teacher_id' => '1985000000010',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 111,
                'name' => 'XII IPS 1',
                'homeroom_teacher_id' => '1985000000011',
                'academic_year' => '2025/2026',
            ],
            [
                'class_id' => 112,
                'name' => 'XII IPS 2',
                'homeroom_teacher_id' => '1985000000012',
                'academic_year' => '2025/2026',
            ],
        ];

        foreach ($classes as $class) {
            SchoolClass::updateOrCreate(
                [
                    'class_id' => $class['class_id'],
                ],
                [
                    'name' => $class['name'],
                    'homeroom_teacher_id' => $class['homeroom_teacher_id'],
                    'academic_year' => $class['academic_year'],
                ]
            );
        }
    }
}