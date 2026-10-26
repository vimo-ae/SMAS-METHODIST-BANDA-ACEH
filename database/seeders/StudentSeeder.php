<?php

namespace Database\Seeders;

use App\Models\ClassEnrollment;
use App\Models\ClassSubjectTeacher;
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
                        'nisn' => str_pad((string) $nis, 10, '0', STR_PAD_LEFT),
                        'class_id' => $class->class_id,
                        'gender' => $i % 2 ? 'L' : 'P',
                        'pob' => 'Banda Aceh',
                        // Tanggal tetap (tidak bergantung hari seeder dijalankan)
                        'dob' => sprintf('%d-05-%02d', 2009 + ($i % 2), $i),
                        'address' => 'Jl. Contoh No. ' . $i,
                        'district' => 'Baiturrahman',
                        'city' => 'Banda Aceh',
                        'province' => 'Aceh',
                        'parent_name' => 'Orang Tua Siswa ' . $no,
                        'parent_phone' => '0812345' . str_pad((string) $no, 5, '0', STR_PAD_LEFT),
                        'entry_year' => 2025,
                        'status' => 'aktif',
                    ]
                );

                // 3. Daftarkan siswa ke semua kelas-mapel di kelasnya
                foreach (ClassSubjectTeacher::where('class_id', $class->class_id)->get() as $cst) {
                    ClassEnrollment::updateOrCreate(
                        [
                            'students_id' => $nis,
                            'cst_id' => $cst->cst_id,
                        ],
                        [
                            'academic_year' => $cst->academic_year,
                            'status' => 'aktif',
                            'enrolled_at' => now()->startOfYear(),
                        ]
                    );
                }

                $no++;
            }
        }
    }
}