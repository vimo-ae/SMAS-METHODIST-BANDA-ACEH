<?php

namespace Database\Seeders;

use App\Models\ClassSubjectTeacher;
use Illuminate\Database\Seeder;

class ClassSubjectTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'cst_id' => 'W1001',
                'class_id' => 101,
                'subject_code' => 'MTK',
                'teacher_id' => '1985000000001',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],

            [
                'cst_id' => 'H1002',
                'class_id' => 101,
                'subject_code' => 'FIS',
                'teacher_id' => '1985000000002',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],

            [
                'cst_id' => 'I1003',
                'class_id' => 101,
                'subject_code' => 'BIO',
                'teacher_id' => '1985000000003',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],

            [
                'cst_id' => 'A1004',
                'class_id' => 101,
                'subject_code' => 'KIM',
                'teacher_id' => '1985000000007',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],

            [
                'cst_id' => 'J1005',
                'class_id' => 102,
                'subject_code' => 'MTK',
                'teacher_id' => '1985000000001',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],

            [
                'cst_id' => 'G1006',
                'class_id' => 102,
                'subject_code' => 'FIS',
                'teacher_id' => '1985000000002',
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
            ],
        ];

        foreach ($data as $item) {
            ClassSubjectTeacher::updateOrCreate(
                [
                    'cst_id' => $item['cst_id'],
                ],
                [
                    'class_id' => $item['class_id'],
                    'subject_code' => $item['subject_code'],
                    'teacher_id' => $item['teacher_id'],
                    'academic_year' => $item['academic_year'],
                    'semester' => $item['semester'],
                ]
            );
        }
    }
}