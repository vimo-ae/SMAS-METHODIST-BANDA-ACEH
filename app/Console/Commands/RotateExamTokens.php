<?php

namespace App\Console\Commands;

use App\Services\ExamTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RotateExamTokens extends Command
{
    protected $signature = 'exam-tokens:rotate';
    protected $description = 'Mengganti token ujian aktif yang kedaluwarsa';

    public function handle(ExamTokenService $tokens): int
    {
        DB::setDefaultConnection('mysql');
        $rotated = $tokens->rotatePublishedTokens();
        $this->info("Token diperiksa untuk {$rotated} ujian aktif.");

        return self::SUCCESS;
    }
}
