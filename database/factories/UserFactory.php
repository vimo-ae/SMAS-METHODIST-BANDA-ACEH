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
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // password default untuk data dummy
            'role' => 'siswa',
            'academic_key' => null,
            'phone' => fake('id_ID')->phoneNumber(),
            'avatar' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => ['role' => 'superadmin']);
    }

    public function guru(): static
    {
        return $this->state(fn () => ['role' => 'guru']);
    }

    public function siswa(): static
    {
        return $this->state(fn () => ['role' => 'siswa']);
    }

    public function orangTua(): static
    {
        return $this->state(fn () => ['role' => 'orangtua']);
    }
}
