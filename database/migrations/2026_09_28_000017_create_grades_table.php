<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
    $table->id('grade_id');

    // FK -> students.nis
    $table->unsignedBigInteger('students_id');
    $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();

    // FK -> class_subject_teacher.cst_id
    $table->string('cst_id', 20);
    $table->foreign('cst_id')->references('cst_id')->on('class_subject_teacher')->cascadeOnDelete();

    $table->enum('grade_type', ['tugas', 'kuis', 'uts', 'uas', 'lainnya']);

    // Sumber nilai (salah satunya terisi, atau keduanya null untuk input manual lain)
    $table->foreignId('assignment_id')->nullable()
        ->constrained('assignments', 'assignment_id')->cascadeOnDelete();
    $table->foreignId('exam_id')->nullable()
        ->constrained('exams', 'exam_id')->cascadeOnDelete();

    $table->decimal('score', 6, 2)->nullable();

    // Null = dihitung sistem, terisi = diinput guru
    $table->unsignedBigInteger('graded_by')->nullable();
    $table->foreign('graded_by')->references('nip')->on('teachers')->nullOnDelete();
    $table->timestamp('graded_at')->nullable();

    $table->timestamps();

    $table->unique(['students_id', 'assignment_id']);
    $table->unique(['students_id', 'exam_id']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};