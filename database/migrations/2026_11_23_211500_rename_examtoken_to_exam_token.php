<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasExamToken = Schema::hasTable('exam_token');
        $hasExamTokenLegacy = Schema::hasTable('examtoken');

        if (! $hasExamToken && $hasExamTokenLegacy) {
            DB::statement('RENAME TABLE `examtoken` TO `exam_token`');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exam_token') && ! Schema::hasTable('examtoken')) {
            DB::statement('RENAME TABLE `exam_token` TO `examtoken`');
        }
    }
};
