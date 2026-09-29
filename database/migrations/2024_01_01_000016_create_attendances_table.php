<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // FK -> class_subject_teacher.cst_id
            $table->string('cst_id', 20);

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();

            // FK -> students.nis
            $table->string('students_id');

            $table->foreign('students_id')
                ->references('nis')
                ->on('students')
                ->cascadeOnDelete();

            $table->date('date');

            $table->enum('status', [
                'hadir',
                'izin',
                'sakit',
                'alpha'
            ]);

            $table->string('note')->nullable();

            $table->timestamps();

            // Satu siswa hanya memiliki satu absensi
            // untuk satu mata pelajaran pada satu tanggal
            $table->unique(
                ['cst_id', 'students_id', 'date']
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};