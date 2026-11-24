<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Database\Seeders\Support\CredentialReporter;
use Database\Seeders\Support\InteractsWithUserAccounts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class TeacherSeeder extends Seeder
{
    use InteractsWithUserAccounts;

    public function run(): void
    {
        $path = database_path('seeders/data/school_structure_2026_2027.php');

        if (! is_file($path)) {
            throw new RuntimeException(
                'TeacherSeeder: school_structure_2026_2027.php tidak ditemukan.'
            );
        }

        $structure = require $path;
        $sourceTeachers = is_array($structure)
            ? ($structure['teachers'] ?? [])
            : [];

        if (! is_array($sourceTeachers) || $sourceTeachers === []) {
            throw new RuntimeException(
                'TeacherSeeder: data guru 2026/2027 kosong atau tidak valid.'
            );
        }

        // MSBD menetapkan 18 guru mata pelajaran/wali kelas sebagai role Guru.
        // Kepala sekolah, wakil kepala sekolah, operator, dan TU tidak dimasukkan
        // ke tabel teachers melalui seeder ini.
        if (count($sourceTeachers) !== 18) {
            throw new RuntimeException(
                'TeacherSeeder: MSBD mengacu pada 18 guru mata pelajaran. '
                . 'Periksa kembali daftar guru sebelum seeding.'
            );
        }

        $this->assertStringKeySchema();
        $teachers = $this->validateTeacherData($sourceTeachers);

        $credentials = [];
        $createdAccounts = 0;
        $preservedAccounts = 0;
        $createdProfiles = 0;
        $preservedProfiles = 0;
        $completedProfiles = 0;

        DB::transaction(function () use (
            $teachers,
            &$credentials,
            &$createdAccounts,
            &$preservedAccounts,
            &$createdProfiles,
            &$preservedProfiles,
            &$completedProfiles
        ): void {
            foreach ($teachers as $teacherData) {
                $nip = $teacherData['nip'];

                // Buat akun hanya jika belum tersedia.
                // Akun lama dengan role yang sama tetap dipertahankan.
                $credential = $this->upsertUserAccount(
                    userId: $nip,
                    attrs: [
                        'name' => $teacherData['name'],
                        'email' => $teacherData['email'],
                        'role' => 'guru',
                    ],
                    roleLabel: 'guru',
                    loginId: $nip,
                    name: $teacherData['name'],
                );

                if ($credential !== null) {
                    $credentials[] = $credential;
                    $createdAccounts++;
                } else {
                    $preservedAccounts++;
                }

                $teacher = Teacher::query()->find($nip);

                // Buat profil guru baru.
                if (! $teacher) {
                    Teacher::query()->create([
                        'nip' => $nip,
                        'specialization' => $teacherData['specialization'],
                        'education_level' => $teacherData['education_level'],
                        'status' => 'aktif',
                    ]);

                    $createdProfiles++;
                    continue;
                }

                // Profil lama tidak ditimpa.
                // Hanya melengkapi kolom yang masih kosong.
                $fill = [];

                if (
                    blank($teacher->specialization)
                    && $teacherData['specialization'] !== null
                ) {
                    $fill['specialization'] =
                        $teacherData['specialization'];
                }

                if (
                    blank($teacher->education_level)
                    && $teacherData['education_level'] !== null
                ) {
                    $fill['education_level'] =
                        $teacherData['education_level'];
                }

                if ($fill !== []) {
                    $teacher->update($fill);
                    $completedProfiles++;
                } else {
                    $preservedProfiles++;
                }
            }
        });

        // Simpan kredensial setelah transaksi berhasil.
        foreach ($credentials as $credential) {
            CredentialReporter::record($credential);
        }

        $credentialFile = CredentialReporter::flush($this->command);

        $this->command?->info(
            'TeacherSeeder: ' . count($teachers) . ' data diperiksa; '
            . $createdAccounts . ' akun baru; '
            . $preservedAccounts . ' akun lama dipertahankan; '
            . $createdProfiles . ' profil baru; '
            . $completedProfiles . ' profil lama dilengkapi; '
            . $preservedProfiles . ' profil lama dipertahankan.'
        );

        if ($credentialFile !== null) {
            $this->command?->warn(
                'TeacherSeeder: kredensial akun baru disimpan di '
                . $credentialFile
                . '. Jaga kerahasiaan file tersebut.'
            );
        }
    }

    /**
     * NIP internal seperti GMA001 harus menggunakan VARCHAR.
     */
    private function assertStringKeySchema(): void
    {
        foreach (
            [
                ['users', 'user_id'],
                ['teachers', 'nip'],
            ] as [$table, $column]
        ) {
            if (
                ! Schema::hasTable($table)
                || ! Schema::hasColumn($table, $column)
            ) {
                throw new RuntimeException(
                    "TeacherSeeder: kolom {$table}.{$column} belum tersedia."
                );
            }

            $type = strtolower(
                Schema::getColumnType($table, $column)
            );

            $isString = str_contains($type, 'char')
                || str_contains($type, 'text')
                || str_contains($type, 'string');

            if (! $isString) {
                throw new RuntimeException(
                    "TeacherSeeder: {$table}.{$column} masih bertipe {$type}. "
                    . 'Jalankan dan verifikasi migration '
                    . 'widen_user_and_teacher_columns_to_varchar_30 '
                    . 'sebelum seeding.'
                );
            }
        }
    }

    /**
     * Validasi data guru dari file sumber.
     */
    private function validateTeacherData(array $sourceTeachers): array
    {
        $teachers = [];
        $seenNips = [];
        $seenEmails = [];

        foreach ($sourceTeachers as $index => $source) {
            if (! is_array($source)) {
                throw new RuntimeException(
                    'TeacherSeeder: format data guru baris #'
                    . ($index + 1) . ' tidak valid.'
                );
            }

            $nip = trim((string) ($source['nip'] ?? ''));
            $name = trim((string) ($source['name'] ?? ''));
            $email = strtolower(
                trim((string) ($source['email'] ?? ''))
            );

            $specialization = trim(
                (string) ($source['specialization'] ?? '')
            );

            $educationLevel = trim(
                (string) ($source['education_level'] ?? '')
            );

            if (
                $nip === ''
                || strlen($nip) > 30
                || $name === ''
                || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {
                throw new RuntimeException(
                    'TeacherSeeder: NIP, nama, atau email tidak valid pada baris #'
                    . ($index + 1) . '.'
                );
            }

            if (isset($seenNips[$nip])) {
                throw new RuntimeException(
                    'TeacherSeeder: NIP duplikat: ' . $nip
                );
            }

            if (isset($seenEmails[$email])) {
                throw new RuntimeException(
                    'TeacherSeeder: email duplikat: ' . $email
                );
            }

            $seenNips[$nip] = true;
            $seenEmails[$email] = true;

            $teachers[] = [
                'nip' => $nip,
                'name' => $name,
                'email' => $email,
                'specialization' => $specialization !== ''
                    ? $specialization
                    : null,
                'education_level' => $educationLevel !== ''
                    ? $educationLevel
                    : null,
            ];
        }

        return $teachers;
    }
}
