<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->string('students_id');
            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('exam_questions')->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->unsignedSmallInteger('score')->nullable();
            $table->timestamps();

            $table->unique(['question_id', 'students_id'], 'exam_answer_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
