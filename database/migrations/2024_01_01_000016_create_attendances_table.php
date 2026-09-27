<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cst_id')->constrained('class_subject_teacher')->cascadeOnDelete();
            $table->string('students_id');
            $table->foreign('students_id')->references('nis')->on('students')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha']);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['cst_id', 'students_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
