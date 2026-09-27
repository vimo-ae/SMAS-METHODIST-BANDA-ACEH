<?php
namespace Database\Factories; use Illuminate\Database\Eloquent\Factories\Factory;
class GuardianFactory extends Factory { protected $model=\App\Models\Guardian::class; public function definition():array{return ['nik'=>fake()->unique()->numerify('################'),'relationship'=>fake()->randomElement(['Ayah','Ibu','Wali']),'occupation'=>fake('id_ID')->jobTitle(),'address'=>fake('id_ID')->address()];} }
