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
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->foreign('cst_id')->references('cst_id')->on('class_subject_teacher')->cascadeOnDelete();
            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->unique(['cst_id', 'students_id']);
        });

        // Sesi ujian = absensi ujian (foto wajah) + status pengerjaan
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id('exam_session_id');
            $table->foreignId('exam_id')->constrained('exams', 'exam_id')->cascadeOnDelete();
            $table->unsignedBigInteger('students_id');
            $table->string('face_photo')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('status', 15)->default('berlangsung'); // berlangsung | selesai | dinilai
            $table->decimal('objective_score', 6, 2)->default(0);  // 0-100, dari soal bobot
            $table->decimal('essay_score', 6, 2)->nullable();      // 0-100, manual guru (tanpa bobot)
            $table->unsignedSmallInteger('violation_count')->default(0);
            $table->text('teacher_note')->nullable();
            $table->timestamps();

            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->unique(['exam_id', 'students_id']);
        });

        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id('exam_violation_id');
            $table->foreignId('exam_session_id')->constrained('exam_sessions', 'exam_session_id')->cascadeOnDelete();
            $table->string('type', 30); // tab_switch | window_blur | fullscreen_exit | copy_paste
            $table->string('detail')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });

        // Catatan wali kelas untuk rapor
        Schema::create('report_notes', function (Blueprint $table) {
            
            $table->unsignedBigInteger('students_id');
            $table->string('semester', 10);
            $table->string('academic_year', 9);
            $table->string('attitude', 30)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->unique(['students_id', 'semester', 'academic_year']);
        });

        // Tanya guru / pengaduan dari orang tua
        Schema::create('parent_messages', function (Blueprint $table) {
            $table->id('parent_message_id');
            $table->unsignedBigInteger('parent_user_id');
            $table->unsignedBigInteger('teacher_user_id')->nullable(); // null = ditujukan ke admin/sekolah
            $table->unsignedBigInteger('students_id')->nullable();
            $table->string('type', 15)->default('pertanyaan'); // pertanyaan | pengaduan
            $table->string('subject');
            $table->text('message');
            $table->text('reply')->nullable();
            $table->unsignedBigInteger('replied_by')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->foreign('parent_user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('teacher_user_id')->references('user_id')->on('users')->nullOnDelete();
            $table->foreign('students_id')->references('nis')->on('students')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_messages');
        Schema::dropIfExists('report_notes');
        Schema::dropIfExists('exam_violations');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('cst_students');
    }
};
