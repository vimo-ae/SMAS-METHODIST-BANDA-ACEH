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
            $table->string('nisn', 10)->nullable()->unique();

            // FK ke classes.class_id
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes', 'class_id')
                ->nullOnDelete();

            $table->string('pob', 100)->nullable();            // tempat lahir
            $table->date('dob')->nullable();                   // tanggal lahir
            $table->text('address')->nullable();               // jalan, desa/kelurahan
            $table->string('district', 100)->nullable();       // kecamatan
            $table->string('city', 100)->nullable();           // kota/kabupaten
            $table->string('province', 100)->nullable();
            $table->string('parent_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->year('entry_year')->nullable();            // tahun masuk
            $table->string('status', 15)->default('aktif');    // aktif | lulus | pindah | drop_out
            $table->enum('gender', ['L', 'P']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};