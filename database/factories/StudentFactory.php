<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = \App\Models\Student::class;

    public function definition(): array
    {
        return [
            // nis = user_id (FK ke users)
            'nis' => User::factory()->siswa(),
            'class_id' => null,
            'gender' => fake()->randomElement(['L', 'P']),
            'pob' => 'Banda Aceh',
            'dob' => fake()->dateTimeBetween('-18 years', '-15 years'),
            'address' => fake('id_ID')->address(),
            'status' => 'aktif',
        ];
    }
}