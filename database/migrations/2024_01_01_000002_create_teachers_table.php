<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {

            // PK sekaligus FK ke users.user_id
            $table->unsignedBigInteger('nip')->primary();

            $table->foreign('nip')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('specialization')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};