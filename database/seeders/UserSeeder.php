<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'user_id' => 1,
                'email' => 'superadmin@methodistbandaaceh.sch.id',
                'name' => 'Super Admin',
                'role' => 'superadmin',
            ],
            [
                'user_id' => 2,
                'email' => 'admin@methodistbandaaceh.sch.id',
                'name' => 'Admin Sekolah',
                'role' => 'admin',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                ],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password123'),
                    'role' => $data['role'],
                ]
            );
        }
    }
}