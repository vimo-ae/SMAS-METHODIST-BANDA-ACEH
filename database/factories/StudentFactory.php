<?php
namespace Database\Factories; use Illuminate\Database\Eloquent\Factories\Factory;
class StudentFactory extends Factory { protected $model=\App\Models\Student::class; public function definition():array{return ['nis'=>fake()->unique()->numerify('##########'),'class_id'=>null,'gender'=>fake()->randomElement(['L','P']),'birth_date'=>fake()->dateTimeBetween('-18 years','-15 years'),'address'=>fake('id_ID')->address()];} }
