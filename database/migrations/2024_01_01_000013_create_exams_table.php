<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cst_id')->constrained('class_subject_teacher')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['kuis', 'uts', 'uas']);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedSmallInteger('duration_minutes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
