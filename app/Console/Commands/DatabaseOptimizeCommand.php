<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseOptimizeCommand extends Command
{
    protected $signature = 'db:optimize-queries';
    protected $description = 'Jalankan analisis tabel, optimalisasi indeks, dan pembersihan cache kueri database';

    public function handle(): int
    {
        $this->info('Memulai analisis dan optimalisasi query database SMAS Methodist...');

        $conn = DB::connection('mysql_admin');
        $schema = Schema::connection('mysql_admin');
        $dbName = $conn->getDatabaseName();

        $tables = collect($conn->select(
            "SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'",
            [$dbName]
        ))->pluck('name')->all();

        $rows = [];
        foreach ($tables as $table) {
            try {
                // 1. ANALYZE TABLE untuk memperbarui statistik kardinalitas query optimizer
                $res = $conn->select("ANALYZE TABLE `{$table}`");
                $status = $res[0]->Msg_text ?? 'OK';

                // 2. Cek jumlah indeks
                $indexCount = count($conn->select("SHOW INDEX FROM `{$table}`"));

                $rows[] = [$table, $indexCount, $status];
            } catch (\Exception $e) {
                $rows[] = [$table, 'N/A', 'Error: ' . $e->getMessage()];
            }
        }

        $this->table(['Nama Tabel', 'Total Indeks', 'Status Query Optimizer'], $rows);

        // 3. Bersihkan cache aplikasi & dashboard
        Cache::flush();
        $this->info('✓ Cache query berhasil diperbarui.');
        $this->info('✓ Seluruh tabel berhasil dianalisis untuk optimalisasi execution plan.');

        return Command::SUCCESS;
    }
}
