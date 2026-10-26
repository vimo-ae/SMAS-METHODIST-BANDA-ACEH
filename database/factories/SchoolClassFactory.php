<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = \App\Models\SchoolClass::class;

    public function definition(): array
    {
        return [
            // class_id diisi manual (tidak auto-increment)
            'class_id' => fake()->unique()->numberBetween(200, 9999),
            'name' => fake()->randomElement(['X', 'XI', 'XII']).' '.fake()->randomElement(['IPA', 'IPS']).' '.fake()->numberBetween(1, 3),
            'homeroom_teacher_id' => null,
            'academic_year' => '2025/2026',
        ];
    }
}