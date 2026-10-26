<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {

            // Tidak ada primary key
            $table->string('cst_id', 20);

            $table->enum('day', [
                'senin',
                'selasa',
                'rabu',
                'kamis',
                'jumat',
                'sabtu'
            ]);

            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();

            $table->foreign('cst_id')
                ->references('cst_id')
                ->on('class_subject_teacher')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};