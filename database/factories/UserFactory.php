<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition(): array
    {
        return [
            // user_id diisi manual (NIP / NIS), tidak auto-increment
            'user_id' => fake()->unique()->numerify('##########'),
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'phone' => fake('id_ID')->numerify('08##########'),
            'photo' => null,
        ];
    }

    public function admin(): static { return $this->state(fn () => ['role' => 'admin']); }
    public function guru(): static { return $this->state(fn () => ['role' => 'guru']); }
    public function siswa(): static { return $this->state(fn () => ['role' => 'siswa']); }
}