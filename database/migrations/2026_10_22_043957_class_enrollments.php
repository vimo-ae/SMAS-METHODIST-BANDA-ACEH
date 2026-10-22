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

            // FK -> classes.class_id
            $table->foreignId('class_id')
                ->constrained('classes', 'class_id')
                ->cascadeOnDelete();

            // Contoh: 2025/2026
            $table->string('academic_year', 9);

            // Hasil siswa pada tahun ajaran tersebut
            $table->enum('status', [
                'aktif',
                'naik_kelas',
                'tinggal_kelas',
                'pindah',
                'lulus',
                'drop_out',
            ])->default('aktif');

            $table->date('enrolled_at')->nullable();   // tanggal masuk kelas
            $table->date('left_at')->nullable();       // tanggal keluar kelas

            $table->timestamps();

            // Satu siswa hanya punya satu kelas per tahun ajaran
            $table->unique(['students_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};