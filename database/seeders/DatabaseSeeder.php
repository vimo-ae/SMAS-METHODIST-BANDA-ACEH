<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,            // admin (tanpa super admin)
            TeacherSeeder::class,
            SubjectSeeder::class,         // 15 mata pelajaran
            SchoolClassSeeder::class,
            ClassSubjectTeacherSeeder::class,
            StudentSeeder::class,
            ExamSeeder::class,            // 15 ujian x 35 soal (kelas X IPA 1)
            DemoDataSeeder::class,        // materi, tugas, forum, nilai, dll.
        ]);
    }
}