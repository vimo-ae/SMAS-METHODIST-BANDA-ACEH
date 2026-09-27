<?php

namespace Database\Factories;

use App\Models\ClassSubjectTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = \App\Models\Assignment::class;

    public function definition(): array
    {
        return [
            'cst_id' => ClassSubjectTeacher::factory(),
            'title' => 'Tugas ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'max_score' => 100,
        ];
    }
}
