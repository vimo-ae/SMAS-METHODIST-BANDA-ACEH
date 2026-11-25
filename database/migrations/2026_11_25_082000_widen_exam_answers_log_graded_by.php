<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('database.connections.' . DB::getDefaultConnection());
        $charset = $connection['charset'] ?? 'utf8mb4';
        $collation = $connection['collation'] ?? 'utf8mb4_unicode_ci';

        DB::statement(
            "ALTER TABLE `exam_answers_logs` MODIFY `graded_by` VARCHAR(30) CHARACTER SET {$charset} COLLATE {$collation} NULL"
        );
    }

    public function down(): void
    {
        // Keep the widened audit column; narrowing it would reintroduce truncation.
    }
};
