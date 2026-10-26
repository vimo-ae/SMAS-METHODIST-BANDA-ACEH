<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {

            $table->unsignedBigInteger('class_id')->primary();

            $table->string('name');

            // FK ke teachers.nip
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable();

            $table->string('academic_year', 9);

            $table->timestamps();

            $table->foreign('homeroom_teacher_id')
                ->references('nip')
                ->on('teachers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};