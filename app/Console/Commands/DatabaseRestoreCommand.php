<?php

namespace App\Console\Commands;

use App\Services\DatabaseRecoveryService;
use Illuminate\Console\Command;

class DatabaseRestoreCommand extends Command
{
    protected $signature = 'db:restore {file? : Nama file backup SQL yang akan dipulihkan}';
    protected $description = 'Pulihkan basis data SMAS Methodist dari file cadangan SQL';

    public function handle(DatabaseRecoveryService $recoveryService): int
    {
        $backups = $recoveryService->listBackups();

        if (empty($backups)) {
            $this->error('Tidak ada berkas cadangan database yang ditemukan di storage/app/backups.');
            return Command::FAILURE;
        }

        $file = $this->argument('file');

        if (!$file) {
            $choices = array_map(fn ($b) => "{$b['filename']} ({$b['size_human']}, {$b['created_at']->format('d M Y H:i')})", $backups);
            $choiceIndex = $this->choice('Pilih file cadangan yang ingin dipulihkan:', $choices, 0);

            // Ambil filename dari pilihan
            $selected = $backups[array_search($choiceIndex, $choices, true)] ?? $backups[0];
            $file = $selected['filename'];
        }

        if (!$this->confirm("PERINGATAN: Memulihkan database dari '{$file}' akan menimpa data yang ada saat ini. Lanjutkan?", false)) {
            $this->warn('Pemulihan database dibatalkan.');
            return Command::SUCCESS;
        }

        $this->info("Sedang memulihkan database dari {$file}...");

        try {
            $result = $recoveryService->restoreBackup($file);
            $this->info("✓ " . $result['message']);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal memulihkan database: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
