<?php

namespace Database\Seeders\Support;

use Illuminate\Console\Command;
use RuntimeException;

/**
 * Menyimpan password sementara akun BARU ke CSV lokal yang privat.
 *
 * File berada di storage/app/private dan tidak dicetak ke terminal/log.
 */
class CredentialReporter
{
    /** @var array<int, array{role:string, login:string, name:string, password:string}> */
    protected static array $buffer = [];

    protected static ?string $file = null;

    protected static function file(): string
    {
        return static::$file ??= 'kredensial_sementara_' . date('Y-m-d') . '.csv';
    }

    public static function generatePassword(int $length = 14): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnpqrstuvwxyz';
        $digit = '23456789';
        $sym = '!@#$%*?-_';
        $all = $upper . $lower . $digit . $sym;

        $chars = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $digit[random_int(0, strlen($digit) - 1)],
            $sym[random_int(0, strlen($sym) - 1)],
        ];

        for ($i = count($chars); $i < max(4, $length); $i++) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }

        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    /** Catat kredensial akun baru setelah transaksi berhasil. */
    public static function record(array $credential): void
    {
        static::$buffer[] = $credential;
    }

    public static function count(): int
    {
        return count(static::$buffer);
    }

    /**
     * Simpan kredensial ke file privat.
     */
    public static function flush(?Command $command = null): ?string
    {
        if (static::$buffer === []) {
            return null;
        }

        $directory = storage_path('app/private');

        if (
            ! is_dir($directory)
            && ! @mkdir($directory, 0700, true)
            && ! is_dir($directory)
        ) {
            throw new RuntimeException(
                'Folder penyimpanan kredensial privat tidak dapat dibuat.'
            );
        }

        @chmod($directory, 0700);

        $absolutePath = $directory . DIRECTORY_SEPARATOR . static::file();
        $rows = static::$buffer;

        $existing = is_file($absolutePath)
            ? file_get_contents($absolutePath)
            : '';

        if ($existing === false) {
            throw new RuntimeException(
                'File kredensial lama tidak dapat dibaca; tidak ada perubahan file.'
            );
        }

        $csv = static fn (string $value): string =>
            '"' . str_replace('"', '""', $value) . '"';

        $lines = [];

        if ($existing === '') {
            $lines[] = 'role,login_id,nama,password_sementara';
        }

        foreach ($rows as $row) {
            $lines[] = implode(',', [
                $csv((string) $row['role']),
                $csv((string) $row['login']),
                $csv((string) $row['name']),
                $csv((string) $row['password']),
            ]);
        }

        $separator = (
            $existing !== ''
            && ! str_ends_with($existing, "\n")
        ) ? "\n" : '';

        $content = $existing
            . $separator
            . implode("\n", $lines)
            . "\n";

        $previousUmask = umask(0077);

        $written = @file_put_contents(
            $absolutePath,
            $content,
            LOCK_EX
        );

        umask($previousUmask);

        if ($written === false) {
            $maskedLogins = array_map(
                static fn (array $row): string =>
                    (string) $row['role']
                    . ':***'
                    . substr((string) $row['login'], -2),
                $rows
            );

            $command?->error(
                'CredentialReporter: gagal menyimpan file kredensial privat. Password tidak dicetak.'
            );

            $command?->line(
                'Akun baru yang perlu direset bila belum tercatat: '
                . implode(', ', $maskedLogins)
            );

            throw new RuntimeException(
                'Gagal menulis kredensial sementara. Periksa permission storage/app/private dan reset password akun baru bila diperlukan.'
            );
        }

        @chmod($absolutePath, 0600);

        static::$buffer = [];

        return 'storage/app/private/'
            . static::file()
            . ' ('
            . count($rows)
            . ' akun baru)';
    }
}