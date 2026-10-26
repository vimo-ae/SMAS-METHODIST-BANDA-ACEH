<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {

            // Primary Key
            $table->id('assignment_id');

            // FK -> class_subject_teacher.cst_id
            $table->string('cst_id', 20);

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->dateTime('due_date');

            $table->unsignedSmallInteger('max_score')
                ->default(100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};