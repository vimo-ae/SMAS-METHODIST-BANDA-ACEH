<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = \App\Models\SchoolClass::class;

    public function definition(): array
    {
        $grade = fake()->randomElement([10, 11, 12]);
        $jurusan = fake()->randomElement(['IPA', 'IPS']);
        $nomor = fake()->numberBetween(1, 3);

        return [
            'name' => $this->romanGrade($grade) . " $jurusan $nomor",
            'grade_level' => $grade,
            'homeroom_teacher_id' => null,
            'academic_year' => '2025/2026',
        ];
    }

    private function romanGrade(int $grade): string
    {
        return match ($grade) {
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        };
    }
}
