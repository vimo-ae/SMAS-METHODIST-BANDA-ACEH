<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSubjectTeacherFactory extends Factory
{
    protected $model = \App\Models\ClassSubjectTeacher::class;

    public function definition(): array
    {
        return [
            // cst_id string manual, contoh: 101-MTK-G
            'cst_id' => fake()->unique()->bothify('###-???-G'),
            'class_id' => null,
            'subject_code' => null,
            'teacher_id' => null,
            'academic_year' => '2025/2026',
            'semester' => fake()->randomElement(['ganjil', 'genap']),
        ];
    }
}