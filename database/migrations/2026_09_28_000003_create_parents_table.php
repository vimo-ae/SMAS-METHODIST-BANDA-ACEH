<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {

            // PK sekaligus FK ke users.user_id
            $table->unsignedBigInteger('nik')->primary();

            $table->foreign('nik')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('relationship')->nullable();

            $table->string('occupation')->nullable();

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};