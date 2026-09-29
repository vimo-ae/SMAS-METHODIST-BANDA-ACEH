<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $subjects = Subject::all();

        foreach ($students as $student) {
            foreach ($subjects as $subject) {

                Grade::updateOrCreate(
                    [
                        'students_id' => $student->nis,
                        'subject_code' => $subject->code,
                        'semester' => 'ganjil',
                        'academic_year' => '2025/2026',
                        'grade_type' => 'tugas',
                    ],
                    [
                        'score' => rand(70, 95),
                    ]
                );
            }
        }
    }
}