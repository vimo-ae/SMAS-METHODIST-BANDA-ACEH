<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subject_teacher', function (Blueprint $table) {

            // Primary Key
            $table->string('cst_id', 20)->primary();

            // FK ke classes.class_id
            $table->unsignedBigInteger('class_id');

            // FK ke subjects.code
            $table->string('subject_code', 20);

            // FK ke teachers.nip
            $table->unsignedBigInteger('teacher_id');

            // Tahun ajaran
            $table->string('academic_year', 9);

            // Semester
            $table->enum('semester', [
                'ganjil',
                'genap'
            ]);

            $table->timestamps();

            // Relasi ke classes
            $table->foreign('class_id')
                ->references('class_id')
                ->on('classes')
                ->cascadeOnDelete();

            // Relasi ke subjects
            $table->foreign('subject_code')
                ->references('code')
                ->on('subjects')
                ->cascadeOnDelete();

            // Relasi ke teachers
            $table->foreign('teacher_id')
                ->references('nip')
                ->on('teachers')
                ->cascadeOnDelete();

            // Satu kelas + mapel + tahun ajaran + semester
            // hanya boleh memiliki satu data
            $table->unique(
                [
                    'class_id',
                    'subject_code',
                    'academic_year',
                    'semester'
                ],
                'cst_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject_teacher');
    }
};