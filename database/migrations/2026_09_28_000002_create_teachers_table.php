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

            $table->string('education_level', 30)->nullable();   // S1, S2, S3, D3, dst.
            $table->text('address')->nullable();
            $table->date('hire_date')->nullable();               // tanggal mulai bertugas
            $table->string('status', 15)->default('aktif');      // aktif | cuti | pensiun | resign

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};