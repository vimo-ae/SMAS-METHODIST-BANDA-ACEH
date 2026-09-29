<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // FK -> class_subject_teacher.cst_id
            $table->string('cst_id', 20);

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();

            $table->string('title');

            $table->enum('type', [
                'kuis',
                'uts',
                'uas'
            ]);

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