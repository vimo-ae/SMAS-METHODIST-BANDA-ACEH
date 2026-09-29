<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            // PK sekaligus FK ke users.user_id
            $table->unsignedBigInteger('nis')->primary();

            $table->foreign('nis')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            // FK ke classes.class_id
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes', 'class_id')
                ->nullOnDelete();

            $table->enum('gender', ['L', 'P']);

            $table->date('birth_date')->nullable();

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};