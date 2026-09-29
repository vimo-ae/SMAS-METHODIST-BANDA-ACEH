<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'nip' => 1234567899887,
                'name' => 'Budi Santoso',
                'email' => 'budi.guru@example.com',
                'specialization' => 'Matematika',
            ],
            [
                'nip' => 1234567899888,
                'name' => 'Siti Rahma',
                'email' => 'siti.guru@example.com',
                'specialization' => 'Fisika',
            ],
            [
                'nip' => 1234567899889,
                'name' => 'Andi Wijaya',
                'email' => 'andi.guru@example.com',
                'specialization' => 'Biologi',
            ],
        ];

        foreach ($teachers as $data) {

            // 1. Buat akun user dengan user_id = NIP
            User::updateOrCreate(
                [
                    'user_id' => $data['nip'],
                ],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                ]
            );

            // 2. NIP menjadi primary key teachers
            Teacher::updateOrCreate(
                [
                    'nip' => $data['nip'],
                ],
                [
                    'specialization' => $data['specialization'],
                ]
            );
        }
    }
}