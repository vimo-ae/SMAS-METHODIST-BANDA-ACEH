<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // FK -> forum_threads.id
            $table->foreignId('thread_id')
                ->constrained('forum_threads')
                ->cascadeOnDelete();

            // FK -> users.id
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Isi balasan
            $table->text('content');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
    }
};