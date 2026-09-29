<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {

            // Primary Key materials
            $table->id();

            // FK -> class_subject_teacher.cst_id
            $table->string('cst_id', 20);

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('file_path')->nullable();

            $table->timestamp('uploaded_at')
                ->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};