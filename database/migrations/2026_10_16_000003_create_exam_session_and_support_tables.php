<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Siswa yang bergabung ke kelas mata pelajaran (join kelas guru mapel)
        Schema::create('cst_students', function (Blueprint $table) {
            $table->string('cst_id', 20);
            $table->unsignedBigInteger('students_id');
        
            $table->enum('status', ['aktif', 'keluar', 'selesai'])->default('aktif');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        
            $table->foreign('cst_id')->references('cst_id')->on('class_subject_teacher')->cascadeOnDelete();
            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
        
            $table->primary(['cst_id', 'students_id']);   // sebelumnya unique
        });

        // Sesi ujian = absensi ujian (foto wajah) + status pengerjaan
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id('exam_session_id');
            $table->foreignId('exam_id')->constrained('exams', 'exam_id')->cascadeOnDelete();
            $table->unsignedBigInteger('students_id');
            $table->string('token', 64)->unique();        // token akses ujian (baru)
            $table->timestamp('started_at')->nullable();  // kapan masuk/mengakses ujian
            $table->timestamp('finished_at')->nullable(); // kapan menekan selesai
            $table->timestamp('left_at')->nullable();     // kapan keluar/out dari ujian (baru)
            $table->string('status', 15)->default('berlangsung'); // berlangsung | selesai | keluar
            $table->timestamps();

            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->unique(['exam_id', 'students_id']);
        });

        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id('exam_violation_id');
            $table->foreignId('exam_session_id')->constrained('exam_sessions', 'exam_session_id')->cascadeOnDelete();

            // Siswa yang melakukan pelanggaran (FK -> students.nis)
            $table->unsignedBigInteger('students_id');
            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->enum('type', ['tab_switch', 'window_blur', 'fullscreen_exit', 'copy_paste']);
            $table->string('detail')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_violations');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('cst_students');
    }
};
