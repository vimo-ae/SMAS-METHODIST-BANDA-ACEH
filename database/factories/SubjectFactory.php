<?php
namespace Database\Factories; use Illuminate\Database\Eloquent\Factories\Factory;
class SubjectFactory extends Factory { protected $model=\App\Models\Subject::class; public function definition():array{$code=strtoupper(fake()->unique()->lexify('???')); return ['code'=>$code,'name'=>fake()->sentence(2)];} }
