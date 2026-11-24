<?php

namespace Database\Seeders\Support;

use App\Models\User;
use RuntimeException;

/**
 * Membuat akun User tanpa menimpa perubahan pengguna/admin.
 *
 * - Akun baru dibuat dengan password acak yang kuat.
 * - Akun lama dengan role yang sama dibiarkan utuh.
 * - Jika ID atau email sudah dipakai akun dengan identitas/role
 *   yang bertentangan, proses ditolak.
 */
trait InteractsWithUserAccounts
{
    /**
     * @param array{name:string, email:string, role:string, phone?:string|null} $attrs
     * @return array{role:string, login:string, name:string, password:string}|null
     */
    protected function upsertUserAccount(
        string $userId,
        array $attrs,
        string $roleLabel,
        string $loginId,
        string $name
    ): ?array {
        $existing = User::query()->whereKey($userId)->first();

        if ($existing) {
            if ((string) $existing->role !== (string) $attrs['role']) {
                throw new RuntimeException(
                    'ID akun sudah digunakan role lain; akun dan profil tidak diubah.'
                );
            }

            // Data akun lama tetap dipertahankan.
            return null;
        }

        $email = trim((string) ($attrs['email'] ?? ''));

        if ($email === '') {
            throw new RuntimeException(
                'Email akun baru kosong; akun tidak dibuat.'
            );
        }

        if (User::query()->where('email', $email)->exists()) {
            throw new RuntimeException(
                'Email sumber sudah dipakai akun lain; perlu pemeriksaan manual.'
            );
        }

        $plain = CredentialReporter::generatePassword();

        User::create($attrs + [
            'user_id'  => $userId,
            'password' => $plain,
        ]);

        return [
            'role' => $roleLabel,
            'login' => $loginId,
            'name' => $name,
            'password' => $plain,
        ];
    }

    /**
     * Mask identifier agar NIS/NUPTK tidak tercetak utuh.
     */
    protected function maskIdentifier(string $identifier): string
    {
        $length = strlen($identifier);

        if ($length <= 2) {
            return str_repeat('*', max(3, $length));
        }

        return substr($identifier, 0, 1)
            . str_repeat('*', max(3, $length - 2))
            . substr($identifier, -1);
    }

    /**
     * Memuat data dari database/seeders/data.
     */
    protected function loadDataFile(string $filename): ?array
    {
        $path = database_path('seeders/data/' . $filename);

        if (! is_file($path)) {
            return null;
        }

        $data = require $path;

        return is_array($data) ? $data : null;
    }
}