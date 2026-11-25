<?php

namespace App\Console\Commands;

use App\Services\DatabaseRecoveryService;
use Illuminate\Console\Command;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup {--filename= : Nama file kustom untuk backup}';
    protected $description = 'Buat cadangan penuh database SMAS Methodist (Full Logical Backup)';

    public function handle(DatabaseRecoveryService $recoveryService): int
    {
        $this->info('Memulai pencadangan basis data SMAS Methodist...');

        try {
            $filename = $this->option('filename');
            $result = $recoveryService->createBackup($filename);

            $this->info("✓ Cadangan berhasil dibuat!");
            $this->table(['Metrik', 'Informasi'], [
                ['Nama File', $result['filename']],
                ['Ukuran', $result['size']],
                ['Metode Backup', $result['method']],
                ['Lokasi', $result['path']],
                ['Waktu Selesai', now()->toDateTimeString()],
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal membuat cadangan database: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
