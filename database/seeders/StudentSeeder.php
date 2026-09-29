<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $no = 1;

        foreach (SchoolClass::all() as $class) {

            for ($i = 1; $i <= 5; $i++) {

                // NIS siswa
                $nis = 20260000 + $no;

                // 1. Buat / update akun User
                User::updateOrCreate(
                    [
                        'user_id' => $nis,
                    ],
                    [
                        'name' => 'Siswa ' . $no,
                        'email' => 'siswa' . $no . '@methodistbandaaceh.sch.id',
                        'password' => Hash::make('password123'),
                        'role' => 'siswa',
                    ]
                );

                // 2. Buat / update data Student
                Student::updateOrCreate(
                    [
                        'nis' => $nis,
                    ],
                    [
                        'class_id' => $class->class_id,
                        'gender' => $i % 2 ? 'L' : 'P',
                        'birth_date' => now()
                            ->subYears(16 + ($i % 2))
                            ->subDays($i),
                        'address' => 'Jl. Contoh No. ' . $i,
                    ]
                );

                $no++;
            }
        }
    }
}