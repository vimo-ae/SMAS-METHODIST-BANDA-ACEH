<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Seeder inti SIA Methodist sesuai ruang lingkup dokumen MSBD. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Validasi read-only dijalankan sebelum ada perubahan data.
        $this->call(StudentSourcePreflightSeeder::class);

        // Hanya data inti SIA: Admin, Guru, mata pelajaran, kelas, CST,
        // jadwal, profil siswa, dan keanggotaan E-Class.
        $this->call([
            UserSeeder::class,
            TeacherSeeder::class,
            SubjectSeeder::class,
            SchoolClassSeeder::class,
            ClassSubjectTeacherSeeder::class,
            RosterScheduleSeeder::class,
            StudentSeeder::class,
        ]);

        // Demo, soal contoh, dan pengumuman dummy tidak dijalankan otomatis.
        // Jalankan DemoDatabaseSeeder secara terpisah hanya pada database pengembangan.
    }
}
