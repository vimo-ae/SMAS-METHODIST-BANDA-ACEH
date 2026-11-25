<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseRecoveryService
{
    protected string $backupDir;
    protected string $connection = 'mysql_admin';

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Dapatkan daftar file cadangan (backup) yang tersedia.
     */
    public function listBackups(): array
    {
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if (in_array(strtolower($file->getExtension()), ['sql', 'gz'])) {
                $backups[] = [
                    'filename'   => $file->getFilename(),
                    'path'       => $file->getPathname(),
                    'size'       => $file->getSize(),
                    'size_human' => $this->formatBytes($file->getSize()),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                    'is_compressed' => strtolower($file->getExtension()) === 'gz',
                ];
            }
        }

        // Urutkan dari yang paling baru
        usort($backups, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $backups;
    }

    /**
     * Buat cadangan penuh database (Full Logical Backup).
     */
    public function createBackup(?string $customFilename = null): array
    {
        $connConfig = config('database.connections.' . $this->connection)
            ?? config('database.connections.' . config('database.default'));

        $database = $connConfig['database'];
        $username = $connConfig['username'];
        $password = $connConfig['password'] ?? '';
        $host     = $connConfig['host'] ?? '127.0.0.1';
        $port     = $connConfig['port'] ?? 3306;

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = $customFilename ?: "methodist_backup_{$timestamp}.sql";
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // Cari executable mysqldump
        $mysqldumpPath = $this->findExecutable('mysqldump');

        if ($mysqldumpPath) {
            $cmd = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s --routines --triggers --events --single-transaction --quick --add-drop-table %s > "%s"',
                $mysqldumpPath,
                escapeshellarg($host),
                escapeshellarg((string) $port),
                escapeshellarg($username),
                $password !== '' ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                $filepath
            );

            // Eksekusi
            $output = null;
            $resultCode = null;
            exec($cmd, $output, $resultCode);

            if ($resultCode === 0 && file_exists($filepath) && filesize($filepath) > 0) {
                return [
                    'success'  => true,
                    'filename' => $filename,
                    'path'     => $filepath,
                    'size'     => $this->formatBytes(filesize($filepath)),
                    'method'   => 'mysqldump',
                ];
            }
        }

        // Fallback: Backup berbasis PHP PDO jika mysqldump gagal / izin command terbatas
        return $this->createPhpBackup($filepath, $filename, $database);
    }

    /**
     * Pulihkan database dari file cadangan SQL.
     */
    public function restoreBackup(string $filename): array
    {
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);

        if (!File::exists($filepath)) {
            throw new Exception("Berkas cadangan {$filename} tidak ditemukan.");
        }

        $connConfig = config('database.connections.' . $this->connection)
            ?? config('database.connections.' . config('database.default'));

        $database = $connConfig['database'];
        $username = $connConfig['username'];
        $password = $connConfig['password'] ?? '';
        $host     = $connConfig['host'] ?? '127.0.0.1';
        $port     = $connConfig['port'] ?? 3306;

        $mysqlPath = $this->findExecutable('mysql');

        if ($mysqlPath) {
            $cmd = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s < "%s"',
                $mysqlPath,
                escapeshellarg($host),
                escapeshellarg((string) $port),
                escapeshellarg($username),
                $password !== '' ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                $filepath
            );

            $output = null;
            $resultCode = null;
            exec($cmd, $output, $resultCode);

            if ($resultCode === 0) {
                return [
                    'success'  => true,
                    'message'  => "Database berhasil dipulihkan dari {$filename}.",
                    'method'   => 'mysql-client',
                ];
            }
        }

        // Fallback restore via PDO
        return $this->restoreViaPdo($filepath, $filename);
    }

    /**
     * Hapus berkas cadangan.
     */
    public function deleteBackup(string $filename): bool
    {
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);
        if (File::exists($filepath)) {
            return File::delete($filepath);
        }
        return false;
    }

    /**
     * Dapatkan path absolut file cadangan untuk diunduh.
     */
    public function getBackupPath(string $filename): ?string
    {
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);
        return File::exists($filepath) ? $filepath : null;
    }

    /**
     * PEMULIHAN GRANULAR (Point-in-Time Record Recovery):
     * Memulihkan data 1 baris yang terhapus (DELETE) atau terupdate (UPDATE)
     * langsung dari tabel audit log ke tabel aktif.
     */
    public function recoverRecordFromLog(string $table, int $logId): array
    {
        $conn = DB::connection($this->connection);
        $schema = Schema::connection($this->connection);

        $logTable = $table === 'grades' ? 'grade_logs' : "{$table}_logs";
        if (!$schema->hasTable($logTable) || !$schema->hasTable($table)) {
            throw new Exception("Tabel {$table} atau tabel log {$logTable} tidak ditemukan.");
        }

        $logRow = $conn->table($logTable)->where('log_id', $logId)->first();
        if (!$logRow) {
            throw new Exception("Catatan log #{$logId} tidak ditemukan.");
        }

        $action = strtoupper((string) ($logRow->log_action ?? $logRow->action ?? ''));

        // Identifikasi kolom metadata yang tidak termasuk kolom tabel utama
        $metaColumns = ['log_id', 'log_action', 'log_by', 'log_at', 'action', 'db_user', 'changed_at'];

        // Ambil data asli yang akan dipulihkan
        $dataToRestore = [];
        $logArray = (array) $logRow;
        $tableColumns = $schema->getColumnListing($table);

        foreach ($tableColumns as $col) {
            if (array_key_exists($col, $logArray)) {
                $dataToRestore[$col] = $logArray[$col];
            }
        }

        if (empty($dataToRestore)) {
            throw new Exception("Tidak ada kolom valid yang dapat dipulihkan untuk tabel {$table}.");
        }

        $recoveredAction = '';

        if ($action === 'DELETE') {
            // Kasus data terhapus: Masukkan kembali data ke tabel aktif
            $conn->table($table)->upsert(
                [$dataToRestore],
                $this->getTablePrimaryKeys($table),
                array_keys($dataToRestore)
            );
            $recoveredAction = 'INSERT (Restore Terhapus)';
        } elseif (in_array($action, ['UPDATE_BEFORE', 'UPDATE_AFTER', 'UPDATE'])) {
            // Kasus data terupdate: Kembalikan nilai ke kondisi sebelum diubah (UPDATE_BEFORE)
            // Jika yang diklik adalah UPDATE_AFTER, cari counterpart UPDATE_BEFORE
            if ($action === 'UPDATE_AFTER') {
                $beforeRow = $conn->table($logTable)
                    ->where('log_action', 'UPDATE_BEFORE')
                    ->where('log_id', '<', $logId)
                    ->orderByDesc('log_id')
                    ->first();
                if ($beforeRow) {
                    $beforeArray = (array) $beforeRow;
                    foreach ($tableColumns as $col) {
                        if (array_key_exists($col, $beforeArray)) {
                            $dataToRestore[$col] = $beforeArray[$col];
                        }
                    }
                }
            }

            $pkColumns = $this->getTablePrimaryKeys($table);
            $pkConditions = [];
            foreach ($pkColumns as $pk) {
                if (isset($dataToRestore[$pk])) {
                    $pkConditions[$pk] = $dataToRestore[$pk];
                }
            }

            if (empty($pkConditions)) {
                throw new Exception("Kunci utama (Primary Key) tidak ditemukan untuk pemulihan tabel {$table}.");
            }

            $conn->table($table)->where($pkConditions)->update($dataToRestore);
            $recoveredAction = 'UPDATE (Rollback Nilai)';
        } else {
            throw new Exception("Aksi log {$action} tidak mendukung pemulihan otomatis.");
        }

        return [
            'success'   => true,
            'table'     => $table,
            'log_id'    => $logId,
            'action'    => $recoveredAction,
            'data'      => $dataToRestore,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Dapatkan statistik kesehatan database & telemetry recovery.
     */
    public function getDatabaseStats(): array
    {
        $conn = DB::connection($this->connection);
        $dbName = $conn->getDatabaseName();

        // Ukuran database
        $sizeRow = $conn->selectOne(
            "SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                ROUND(SUM(data_length) / 1024 / 1024, 2) AS data_mb,
                ROUND(SUM(index_length) / 1024 / 1024, 2) AS index_mb,
                COUNT(table_name) AS total_tables
            FROM information_schema.TABLES 
            WHERE table_schema = ?",
            [$dbName]
        );

        // Status crash recovery InnoDB
        $innodbStatus = $conn->selectOne("SHOW VARIABLES LIKE 'innodb_flush_log_at_trx_commit'");
        $logStatus = $conn->selectOne("SHOW VARIABLES LIKE 'log_bin'");

        // Total catatan audit log
        $totalLogs = 0;
        try {
            $totalLogs = $conn->table('v_all_logs')->count();
        } catch (\Exception) {
            // View fallback
        }

        return [
            'database_name' => $dbName,
            'total_tables'  => $sizeRow->total_tables ?? 0,
            'total_size_mb' => $sizeRow->size_mb ?? 0,
            'data_size_mb'  => $sizeRow->data_mb ?? 0,
            'index_size_mb' => $sizeRow->index_mb ?? 0,
            'innodb_trx_commit' => $innodbStatus->Value ?? '1',
            'binlog_enabled'    => strtolower((string) ($logStatus->Value ?? 'OFF')) === 'on',
            'total_audit_logs'  => $totalLogs,
            'engine'            => 'InnoDB (ACID Compliant)',
        ];
    }

    /**
     * Dapatkan primary key tabel target.
     */
    protected function getTablePrimaryKeys(string $table): array
    {
        $keys = [
            'users'                  => ['user_id'],
            'students'               => ['nis'],
            'teachers'               => ['nip'],
            'classes'                => ['class_id'],
            'subjects'               => ['code'],
            'class_subject_teacher'  => ['cst_id'],
            'class_enrollments'      => ['enrollment_id'],
            'cst_students'           => ['cst_id', 'students_id'],
            'schedules'              => ['cst_id', 'day', 'start_time'],
            'materials'              => ['material_id'],
            'assignments'            => ['assignment_id'],
            'assignment_submissions' => ['assignment_id', 'students_id'],
            'exams'                  => ['exam_id'],
            'exam_questions'         => ['exam_question_id'],
            'exam_sessions'          => ['exam_session_id'],
            'exam_answers'           => ['exam_answer_id'],
            'exam_violations'        => ['exam_violation_id'],
            'grades'                 => ['grade_id'],
            'announcements'          => ['announcement_id'],
            'notifications'          => ['notification_id'],
        ];

        return $keys[$table] ?? ['id'];
    }

    /**
     * Helper fallback PHP backup.
     */
    protected function createPhpBackup(string $filepath, string $filename, string $database): array
    {
        $pdo = DB::connection($this->connection)->getPdo();
        $handle = fopen($filepath, 'w+');

        fwrite($handle, "-- SMAS METHODIST BANDA ACEH DATABASE BACKUP\n");
        fwrite($handle, "-- Generated on: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Database: `{$database}`\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createTable['Create Table'] . ";\n\n");

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $colNames = implode('`, `', $columns);
                fwrite($handle, "INSERT INTO `{$table}` (`{$colNames}`) VALUES\n");

                $valLines = [];
                foreach ($rows as $row) {
                    $vals = array_map(function ($val) use ($pdo) {
                        return is_null($val) ? 'NULL' : $pdo->quote($val);
                    }, $row);
                    $valLines[] = "(" . implode(', ', $vals) . ")";
                }
                fwrite($handle, implode(",\n", $valLines) . ";\n\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return [
            'success'  => true,
            'filename' => $filename,
            'path'     => $filepath,
            'size'     => $this->formatBytes(filesize($filepath)),
            'method'   => 'php-stream',
        ];
    }

    /**
     * Helper fallback restore via PDO.
     */
    protected function restoreViaPdo(string $filepath, string $filename): array
    {
        $pdo = DB::connection($this->connection)->getPdo();
        $sql = file_get_contents($filepath);

        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $pdo->exec($sql);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

        return [
            'success' => true,
            'message' => "Database berhasil dipulihkan dari {$filename} (via PHP PDO).",
            'method'  => 'php-pdo',
        ];
    }

    /**
     * Cari lokasi executable binary.
     */
    protected function findExecutable(string $name): ?string
    {
        // Jalur standar Laragon / XAMPP / PATH Windows
        $candidates = [
            $name,
            'D:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\' . $name . '.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\' . $name . '.exe',
            'C:\\xampp\\mysql\\bin\\' . $name . '.exe',
        ];

        foreach ($candidates as $cand) {
            if ($cand === $name) {
                // Cek apakah ada di system PATH
                $check = shell_exec("where {$name} 2>nul");
                if ($check && trim($check) !== '') {
                    $firstLine = trim(explode("\n", trim($check))[0]);
                    return $firstLine;
                }
            } elseif (file_exists($cand)) {
                return $cand;
            }
        }

        return null;
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
