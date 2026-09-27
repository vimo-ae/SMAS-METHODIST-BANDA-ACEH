<?php
namespace Database\Seeders; use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder { public function run():void { $this->call([UserSeeder::class,SubjectSeeder::class,SchoolClassSeeder::class,TeacherSeeder::class,StudentSeeder::class,GuardianSeeder::class,ClassSubjectTeacherSeeder::class]); } }
