<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id('exam_question_id');
            $table->foreignId('exam_id')->constrained('exams', 'exam_id')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['pilihan_ganda', 'esai']);
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->unsignedSmallInteger('score_weight')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
