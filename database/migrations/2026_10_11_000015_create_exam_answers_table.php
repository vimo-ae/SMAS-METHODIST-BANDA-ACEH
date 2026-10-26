<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {

            $table->id('exam_answer_id');

            // FK -> exams.exam_id
            $table->foreignId('exam_id')
                ->constrained('exams', 'exam_id')
                ->cascadeOnDelete();

            // FK -> students.nis
            $table->unsignedBigInteger('students_id');

            $table->foreign('students_id')
                ->references('nis')
                ->on('students')
                ->cascadeOnDelete();

            // FK -> exam_questions.exam_question_id
            $table->foreignId('question_id')
                ->constrained('exam_questions', 'exam_question_id')
                ->cascadeOnDelete();

            $table->text('answer')->nullable();

            // Path foto lembar jawaban esai
            $table->string('answer_file')->nullable();

            // Hasil koreksi otomatis (null untuk esai)
            $table->boolean('is_correct')->nullable();

            // Skor desimal (contoh: 1.5)
            $table->decimal('score', 6, 2)->nullable();

            // Penilaian esai oleh guru
            $table->unsignedBigInteger('graded_by')->nullable();   // FK -> teachers.nip
            $table->timestamp('graded_at')->nullable();

            $table->foreign('graded_by')
                ->references('nip')
                ->on('teachers')
                ->nullOnDelete();

            $table->timestamps();

            // Satu siswa hanya boleh punya satu jawaban untuk satu soal
            $table->unique(
                ['question_id', 'students_id'],
                'exam_answer_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};