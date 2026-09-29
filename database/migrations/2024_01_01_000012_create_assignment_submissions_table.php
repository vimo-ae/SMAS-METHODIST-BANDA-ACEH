<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {

            // FK -> assignments.assignment_id
            $table->unsignedBigInteger('assignment_id');

            $table->foreign('assignment_id')
                ->references('assignment_id')
                ->on('assignments')
                ->cascadeOnDelete();

            // FK -> students.nis
            $table->unsignedBigInteger('students_id');

            $table->foreign('students_id')
                ->references('nis')
                ->on('students')
                ->cascadeOnDelete();

            $table->string('file_path')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->unsignedSmallInteger('score')->nullable();

            $table->text('feedback')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh memiliki satu submission
            // untuk satu assignment
            $table->unique([
                'assignment_id',
                'students_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};