<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_token', function (Blueprint $table) {
            $table->id('exam_token_id');
            $table->unsignedBigInteger('exam_id');
            $table->string('token_hash', 64);
            $table->text('token_cipher');
            $table->timestamp('issued_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('exam_id')
                ->references('exam_id')
                ->on('exams')
                ->cascadeOnDelete();

            $table->index(['exam_id', 'is_active', 'expires_at']);
            $table->unique(['exam_id', 'token_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_token');
    }
};
