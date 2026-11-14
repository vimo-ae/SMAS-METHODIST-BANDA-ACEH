<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('category', 30)->default('pengumuman')->after('slug');
            $table->string('image')->nullable()->after('content');
            $table->boolean('is_public')->default(false)->after('target_roles');
            $table->timestamp('published_at')->nullable()->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'category', 'image', 'is_public', 'published_at']);
        });
    }
};