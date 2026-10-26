<?php

namespace Database\Seeders;

use App\Models\ClassSubjectTeacher;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassSubjectTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::orderBy('nip')->get();

        foreach ([101, 102] as $classId) {
            $i = 0;
            foreach (array_keys(SubjectSeeder::SUBJECTS) as $code) {
                ClassSubjectTeacher::updateOrCreate(['cst_id' => "$classId-$code-G"], [
                    'class_id' => $classId,
                    'subject_code' => $code,
                    'teacher_id' => $teachers[$i % $teachers->count()]->nip,
                    'academic_year' => '2025/2026',
                    'semester' => 'ganjil',
                    'join_code' => $code.$classId, // contoh: MTK101
                ]);
                $i++;
            }
        }
    }
}
