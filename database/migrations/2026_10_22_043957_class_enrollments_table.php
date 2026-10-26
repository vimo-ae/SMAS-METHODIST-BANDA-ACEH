<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table) {

            // Primary Key
            $table->id('enrollment_id');

            // FK -> students.nis
            $table->unsignedBigInteger('students_id');

            $table->foreign('students_id')
                ->references('nis')
                ->on('students')
                ->cascadeOnDelete();

            // FK -> class_subject_teacher.cst_id
            $table->string('cst_id', 20);

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();

            // Contoh: 2025/2026
            $table->string('academic_year', 9);

            $table->enum('status', [
                'aktif',
                'naik_kelas',
                'tinggal_kelas',
                'pindah',
                'lulus',
                'drop_out',
            ])->default('aktif');

            $table->date('enrolled_at')->nullable();
            $table->date('left_at')->nullable();

            $table->timestamps();

            // Satu siswa hanya terdaftar sekali di satu kelas-mapel
            $table->unique(['students_id', 'cst_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};