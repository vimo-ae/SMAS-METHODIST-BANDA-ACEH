<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Matematika',
            'Fisika',
            'Biologi',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Sejarah',
            'Kimia',
            'Ekonomi',
            'Geografi',
            'PPKn',
            'Sosiologi',
            'Informatika',
            'Seni Budaya',
            'PJOK',
            'Agama',
            'Prakarya',
            'Bahasa Jepang',
            'Bahasa Mandarin',
            'Bimbingan Konseling',
            'Statistika',
        ];

        for ($i = 1; $i <= 20; $i++) {

            $nip = '198500000' . str_pad(
                (string) $i,
                4,
                '0',
                STR_PAD_LEFT
            );

            $teacher = Teacher::firstOrCreate(
                [
                    'nip' => $nip,
                ],
                [
                    'specialization' => $specializations[$i - 1],
                ]
            );

            User::firstOrCreate(
                [
                    'email' => 'guru' . $i . '@methodistbandaaceh.sch.id',
                ],
                [
                    'name' => 'Guru ' . $i,
                    'password' => Hash::make('password123'),
                    'role' => 'guru',
                    'academic_key' => $teacher->nip,
                ]
            );
        }
    }
}