<?php

namespace Database\Seeders;

use App\Models\ClassEnrollment;
use App\Models\ClassSubjectTeacher;
use App\Models\SchoolClass;
use App\Models\Student;
use Database\Seeders\Support\CredentialReporter;
use Database\Seeders\Support\InteractsWithUserAccounts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Seeder akun, profil, dan keanggotaan siswa sesuai aturan identitas MSBD. */
class StudentSeeder extends Seeder
{
    use InteractsWithUserAccounts;

    private const ACADEMIC_YEAR = '2026/2027';

    public function run(): void
    {
        $students = $this->loadDataFile('students_2026_2027.php');

        if ($students === null || $students === []) {
            throw new RuntimeException(
                'StudentSeeder: students_2026_2027.php tidak ditemukan atau kosong.'
            );
        }

        // Validasi seluruh sumber terlebih dahulu. NISN wajib, numerik, 10 digit,
        // dan unik sesuai aturan MSBD. Tidak ada perubahan database bila sumber konflik.
        $students = $this->validateSource($students);

        $classIds = array_values(array_unique(array_column($students, 'class_id')));
        $availableClasses = SchoolClass::query()
            ->whereIn('class_id', $classIds)
            ->where('academic_year', self::ACADEMIC_YEAR)
            ->pluck('class_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $missingClasses = array_diff($classIds, $availableClasses);
        if ($missingClasses !== []) {
            throw new RuntimeException(
                'StudentSeeder: kelas 2026/2027 belum tersedia: '
                . implode(', ', $missingClasses)
                . '. Jalankan SchoolClassSeeder terlebih dahulu.'
            );
        }

        $nisList = array_column($students, 'nis');
        $nisnList = array_column($students, 'nisn');

        // NISN sumber tidak boleh sudah dipakai NIS siswa lain di database.
        $databaseConflicts = Student::query()
            ->whereIn('nisn', $nisnList)
            ->whereNotIn('nis', $nisList)
            ->get(['nis', 'nisn']);

        if ($databaseConflicts->isNotEmpty()) {
            throw new RuntimeException(
                'StudentSeeder: ditemukan NISN sumber yang sudah dimiliki siswa lain di database. '
                . 'Periksa laporan konflik; tidak ada data baru diproses.'
            );
        }

        $classCsts = ClassSubjectTeacher::query()
            ->whereIn('class_id', $classIds)
            ->where('academic_year', self::ACADEMIC_YEAR)
            ->get(['cst_id', 'class_id', 'academic_year'])
            ->groupBy('class_id');

        $classesWithoutCst = [];
        foreach ($classIds as $classId) {
            if ($classCsts->get($classId, collect())->isEmpty()) {
                $classesWithoutCst[] = $classId;
            }
        }

        if ($classesWithoutCst !== []) {
            throw new RuntimeException(
                'StudentSeeder: belum ada penugasan kelas-mapel (CST) untuk kelas '
                . implode(', ', $classesWithoutCst)
                . '. Jalankan ClassSubjectTeacherSeeder terlebih dahulu.'
            );
        }

        $createdAccounts = 0;
        $preservedAccounts = 0;
        $createdProfiles = 0;
        $preservedProfiles = 0;
        $createdEnrollments = 0;
        $createdMemberships = 0;
        $skipped = [];
        $classConflicts = [];
        $credentials = [];

        DB::transaction(function () use (
            $students,
            $classCsts,
            &$createdAccounts,
            &$preservedAccounts,
            &$createdProfiles,
            &$preservedProfiles,
            &$createdEnrollments,
            &$createdMemberships,
            &$skipped,
            &$classConflicts,
            &$credentials
        ): void {
            foreach ($students as $row) {
                $nis = $row['nis'];
                $nisn = $row['nisn'];
                $name = $row['name'];
                $classId = $row['class_id'];
                $gender = $row['gender'];
                $status = $row['status'];
                $entryYear = $row['entry_year'];

                if ($status !== 'aktif') {
                    $skipped[] = $this->maskIdentifier($nis)
                        . ' (status sumber bukan aktif; akun dan enrollment tidak dibuat)';
                    continue;
                }

                $student = Student::query()->find($nis);

                // Status yang ditetapkan admin/sekolah tidak diaktifkan kembali.
                if ($student && strtolower((string) $student->status) !== 'aktif') {
                    $skipped[] = $this->maskIdentifier($nis)
                        . ' (status nonaktif/lulus/pindah dipertahankan)';
                    continue;
                }

                if ($student && ! empty($student->nisn)
                    && (string) $student->nisn !== $nisn) {
                    throw new RuntimeException(
                        'StudentSeeder: NISN pada profil database berbeda dari sumber untuk NIS '
                        . $this->maskIdentifier($nis)
                        . '. Perlu verifikasi sekolah; tidak ada perubahan yang di-commit.'
                    );
                }

                $classMatches = ! $student
                    || $student->class_id === null
                    || (string) $student->class_id === (string) $classId;

                if ($student && ! $classMatches) {
                    $classConflicts[] = $this->maskIdentifier($nis)
                        . " (kelas database {$student->class_id}, sumber {$classId}; kelas database dipertahankan)";
                }

                // Daftar absen tidak menyediakan email pribadi. Alias ini hanya
                // memenuhi kolom email unik untuk akun login berbasis NIS.
                $email = trim((string) ($row['email'] ?? ''));
                if ($email === '') {
                    $email = $nis . '@siswa.methodist.invalid';
                }

                $credential = $this->upsertUserAccount(
                    userId: $nis,
                    attrs: ['name' => $name, 'email' => $email, 'role' => 'siswa'],
                    roleLabel: 'siswa',
                    loginId: $nis,
                    name: $name,
                );

                if ($credential !== null) {
                    $credentials[] = $credential;
                    $createdAccounts++;
                } else {
                    $preservedAccounts++;
                }

                if (! $student) {
                    $attributes = [
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'class_id' => $classId,
                        'gender' => $gender,
                        'status' => $status,
                    ];

                    if ($entryYear !== null) {
                        $attributes['entry_year'] = $entryYear;
                    }

                    Student::query()->create($attributes);
                    $createdProfiles++;
                } else {
                    // Lengkapi kolom identitas wajib yang masih kosong; jangan
                    // menimpa data yang telah diperbarui admin.
                    $fill = [];
                    if ($student->nisn === null) {
                        $fill['nisn'] = $nisn;
                    }
                    if ($student->class_id === null) {
                        $fill['class_id'] = $classId;
                    }
                    if ($student->entry_year === null && $entryYear !== null) {
                        $fill['entry_year'] = $entryYear;
                    }
                    if ($fill !== []) {
                        $student->update($fill);
                    }
                    $preservedProfiles++;
                }

                // Jangan memindahkan siswa yang kelasnya telah diubah oleh admin.
                if (! $classMatches || $status !== 'aktif') {
                    continue;
                }

                foreach ($classCsts->get($classId, collect()) as $cst) {
                    $enrollment = ClassEnrollment::query()
                        ->where('students_id', $nis)
                        ->where('cst_id', $cst->cst_id)
                        ->first();

                    $membership = DB::table('cst_students')
                        ->where('cst_id', $cst->cst_id)
                        ->where('students_id', $nis)
                        ->first();

                    $enrollmentActive = ! $enrollment || (
                        strtolower((string) $enrollment->status) === 'aktif'
                        && empty($enrollment->left_at)
                    );
                    $membershipActive = ! $membership || (
                        strtolower((string) $membership->status) === 'aktif'
                        && empty($membership->left_at)
                    );

                    if (! $enrollmentActive || ! $membershipActive) {
                        continue;
                    }

                    if (! $enrollment) {
                        // enrolled_at sengaja tidak diisi karena tanggal masuk
                        // kelas resmi tidak tersedia pada daftar absensi.
                        ClassEnrollment::query()->create([
                            'students_id' => $nis,
                            'cst_id' => $cst->cst_id,
                            'academic_year' => $cst->academic_year,
                            'status' => 'aktif',
                        ]);
                        $createdEnrollments++;
                    }

                    if (! $membership) {
                        // joined_at menggunakan DEFAULT CURRENT_TIMESTAMP dari database.
                        DB::table('cst_students')->insert([
                            'cst_id' => $cst->cst_id,
                            'students_id' => $nis,
                            'status' => 'aktif',
                            'left_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $createdMemberships++;
                    }
                }
            }
        }, 3);

        foreach ($credentials as $credential) {
            CredentialReporter::record($credential);
        }

        if ($credentialFile = CredentialReporter::flush($this->command)) {
            $this->command?->info(
                'StudentSeeder: kredensial akun baru disimpan secara privat di ' . $credentialFile . '.'
            );
        }

        if ($classConflicts !== []) {
            $this->command?->warn(
                'StudentSeeder: kelas yang sudah diubah admin dipertahankan: '
                . implode('; ', $classConflicts)
            );
        }

        if ($skipped !== []) {
            $this->command?->warn(
                'StudentSeeder: ' . count($skipped) . ' siswa dilewati: '
                . implode('; ', $skipped)
            );
        }

        $this->command?->info(
            "StudentSeeder: {$createdAccounts} akun baru, {$preservedAccounts} akun lama dipertahankan, "
            . "{$createdProfiles} profil baru, {$preservedProfiles} profil lama dipertahankan, "
            . "{$createdEnrollments} enrollment baru, {$createdMemberships} keanggotaan E-Class baru."
        );
    }

    /**
     * @return array<int, array{nis:string,nisn:string,name:string,gender:string,class_id:int,status:string,entry_year:?int,email:?string}>
     */
    private function validateSource(array $rows): array
    {
        $validated = [];
        $seenNis = [];
        $seenNisn = [];
        $duplicates = [];

        foreach ($rows as $index => $row) {
            $line = $index + 1;
            if (! is_array($row)) {
                throw new RuntimeException("StudentSeeder: baris #{$line} bukan data siswa yang valid.");
            }

            $nis = trim((string) ($row['nis'] ?? ''));
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $gender = strtoupper(trim((string) ($row['gender'] ?? '')));
            $classId = (int) ($row['class_id'] ?? 0);
            $status = strtolower(trim((string) ($row['status'] ?? 'aktif')));
            $entryYear = isset($row['entry_year']) ? (int) $row['entry_year'] : null;
            $email = isset($row['email']) ? trim((string) $row['email']) : null;

            if ($nis === '' || ! ctype_digit($nis)) {
                throw new RuntimeException("StudentSeeder: NIS tidak valid pada baris #{$line}.");
            }
            if (! preg_match('/^\d{10}$/', $nisn)) {
                throw new RuntimeException(
                    "StudentSeeder: NISN wajib 10 digit dan tidak boleh kosong pada baris #{$line}."
                );
            }
            if ($name === '') {
                throw new RuntimeException("StudentSeeder: nama siswa kosong pada baris #{$line}.");
            }
            if (! in_array($gender, ['L', 'P'], true)) {
                throw new RuntimeException("StudentSeeder: gender tidak valid pada baris #{$line}.");
            }
            if ($classId <= 0) {
                throw new RuntimeException("StudentSeeder: kelas wajib diisi pada baris #{$line}.");
            }
            if (! in_array($status, ['aktif', 'lulus', 'pindah', 'drop_out'], true)) {
                throw new RuntimeException("StudentSeeder: status tidak valid pada baris #{$line}.");
            }
            if ($email !== null && $email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException("StudentSeeder: email tidak valid pada baris #{$line}.");
            }

            if (isset($seenNis[$nis])) {
                throw new RuntimeException("StudentSeeder: NIS duplikat pada baris #{$line}.");
            }
            $seenNis[$nis] = true;

            if (isset($seenNisn[$nisn])) {
                $duplicates[$nisn][] = $name;
                $duplicates[$nisn][] = $seenNisn[$nisn];
            } else {
                $seenNisn[$nisn] = $name;
            }

            $validated[] = [
                'nis' => $nis,
                'nisn' => $nisn,
                'name' => $name,
                'gender' => $gender,
                'class_id' => $classId,
                'status' => $status,
                'entry_year' => $entryYear,
                'email' => $email,
            ];
        }

        if ($duplicates !== []) {
            $details = [];
            foreach ($duplicates as $nisn => $names) {
                $details[] = implode(' dan ', array_unique($names))
                    . ' (NISN ' . $this->maskIdentifier((string) $nisn) . ')';
            }

            throw new RuntimeException(
                'StudentSeeder dihentikan: ditemukan NISN duplikat pada sumber: '
                . implode('; ', $details)
                . '. MSBD mewajibkan NISN unik dan tidak NULL. Minta sekolah mengonfirmasi nomor yang benar; seeder belum mengubah database.'
            );
        }

        return $validated;
    }
}
