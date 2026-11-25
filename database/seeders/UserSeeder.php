<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Support\CredentialReporter;
use Database\Seeders\Support\InteractsWithUserAccounts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** Akun bootstrap Admin untuk Operator/TU; identitas petugas perlu dipastikan sekolah. */
class UserSeeder extends Seeder
{
    use InteractsWithUserAccounts;

    private const ADMIN_ID = '1';
    private const ADMIN_EMAIL = 'admin@methodist.invalid';
    private const ADMIN_NAME = 'Administrator SMAS Methodist';
    private const OLD_DEFAULT_PASSWORD = 'password123';

    public function run(): void
    {
        $credential = null;

        DB::transaction(function () use (&$credential): void {
            $existing = User::query()->find(self::ADMIN_ID);

            if (
                $existing
                && (string) $existing->role === 'admin'
                && Hash::check(self::OLD_DEFAULT_PASSWORD, (string) $existing->password)
            ) {
                $this->command?->warn(
                    'UserSeeder: akun admin masih memakai password default lama. '
                    . 'Password tidak diubah otomatis; reset secara manual.'
                );
            }

            $credential = $this->upsertUserAccount(
                userId: self::ADMIN_ID,
                attrs: [
                    'name' => self::ADMIN_NAME,
                    'email' => self::ADMIN_EMAIL,
                    'role' => 'admin',
                ],
                roleLabel: 'admin',
                loginId: self::ADMIN_EMAIL,
                name: self::ADMIN_NAME,
            );
        });

        if ($credential !== null) {
            CredentialReporter::record($credential);
            $this->command?->info('UserSeeder: akun bootstrap Admin baru dibuat.');
        } else {
            $this->command?->info('UserSeeder: akun Admin lama dipertahankan tanpa mengubah password atau profil.');
        }

        if ($summary = CredentialReporter::flush($this->command)) {
            $this->command?->info('UserSeeder: kredensial disimpan privat di ' . $summary . '.');
        }
    }
}
