<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $i = 1;

        foreach (Student::all() as $student) {

            // NIK orang tua
            $nik = 3201000000000000 + $i;

            // 1. Buat / update akun User
            User::updateOrCreate(
                [
                    'user_id' => $nik,
                ],
                [
                    'name' => 'Orang Tua ' . $i,
                    'email' => 'orangtua' . $i . '@methodistbandaaceh.sch.id',
                    'password' => Hash::make('password123'),
                    'role' => 'orangtua',
                ]
            );

            // 2. Buat / update data Guardian
            $guardian = Guardian::updateOrCreate(
                [
                    'nik' => $nik,
                ],
                [
                    'relationship' => 'Orang Tua',
                    'occupation' => 'Karyawan',
                    'address' => 'Jl. Contoh',
                ]
            );

            // 3. Hubungkan orang tua dengan siswa
            $guardian->students()->syncWithoutDetaching([
                $student->nis,
            ]);

            $i++;
        }
    }
}