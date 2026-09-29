<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {

            // FK -> students.nis
            $table->unsignedBigInteger('students_id');

            // FK -> subjects.code
            $table->string('subject_code', 20);

            $table->string('semester', 10);

            $table->string('academic_year', 9);

            $table->enum('grade_type', [
                'tugas',
                'uts',
                'uas',
                'rapor'
            ]);

            $table->float('score');

            $table->timestamps();

            $table->foreign('students_id')
                ->references('nis')
                ->on('students')
                ->cascadeOnDelete();

            $table->foreign('subject_code')
                ->references('code')
                ->on('subjects')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};