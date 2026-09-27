<?php
namespace Database\Factories; use Illuminate\Database\Eloquent\Factories\Factory;
class TeacherFactory extends Factory { protected $model=\App\Models\Teacher::class; public function definition():array{return ['nip'=>fake()->unique()->numerify('198#########001'),'specialization'=>fake()->randomElement(['Matematika','Fisika','Biologi','Bahasa Indonesia','Bahasa Inggris','Sejarah','Kimia','Ekonomi'])];} }
