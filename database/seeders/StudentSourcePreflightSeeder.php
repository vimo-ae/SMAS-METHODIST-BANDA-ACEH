<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;
use Throwable;

/**
 * Pemeriksaan read-only untuk sumber data sekolah TA 2026/2027.
 * Tidak membuka koneksi database dan tidak menulis/mengubah data.
 */
class StudentSourcePreflightSeeder extends Seeder
{
    private const ACADEMIC_YEAR = '2026/2027';

    /**
     * Jumlah ini adalah snapshot berkas sumber yang telah diperiksa,
     * bukan ketentuan jumlah personel atau kelas yang ditetapkan MSBD.
     */
    private const EXPECTED_COUNTS = [
        'students' => 97,
        'teachers' => 18,
        'classes' => 6,
        'subjects' => 15,
        'cst' => 82,
        'pending_cst' => 5,
        'roster' => 276,
        'pending_roster' => 14,
    ];

    private const VALID_DAYS = [
        'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu',
    ];

    public function run(): void
    {
        $paths = [
            'students' => database_path('seeders/data/students_2026_2027.php'),
            'structure' => database_path('seeders/data/school_structure_2026_2027.php'),
            'roster' => database_path('seeders/data/roster_schedule_2026_2027.php'),
            'pending_roster' => database_path('seeders/data/roster_schedule_pending_2026_2027.php'),
        ];

        foreach ($paths as $label => $path) {
            if (! is_file($path)) {
                throw new RuntimeException(
                    'Preflight MSBD: file sumber ' . $label . ' tidak ditemukan: ' . basename($path)
                );
            }
        }

        try {
            $students = require $paths['students'];
            $structure = require $paths['structure'];
            $roster = require $paths['roster'];
            $pendingRoster = require $paths['pending_roster'];
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Preflight MSBD: gagal membaca salah satu file sumber. Periksa sintaks dan isi file.',
                0,
                $exception
            );
        }

        if (! is_array($students) || ! is_array($structure) || ! is_array($roster) || ! is_array($pendingRoster)) {
            throw new RuntimeException('Preflight MSBD: format salah satu file sumber bukan array yang valid.');
        }

        $teachers = $structure['teachers'] ?? null;
        $classes = $structure['classes'] ?? null;
        $subjects = $structure['subjects'] ?? null;
        $cst = $structure['cst'] ?? null;
        $pendingCst = $structure['pending_cst'] ?? null;

        foreach ([
            'teachers' => $teachers,
            'classes' => $classes,
            'subjects' => $subjects,
            'cst' => $cst,
            'pending_cst' => $pendingCst,
        ] as $label => $value) {
            if (! is_array($value)) {
                throw new RuntimeException("Preflight MSBD: struktur '{$label}' tidak tersedia atau bukan array.");
            }
        }

        $errors = [];
        $counts = [
            'students' => count($students),
            'teachers' => count($teachers),
            'classes' => count($classes),
            'subjects' => count($subjects),
            'cst' => count($cst),
            'pending_cst' => count($pendingCst),
            'roster' => count($roster),
            'pending_roster' => count($pendingRoster),
        ];

        foreach (self::EXPECTED_COUNTS as $label => $expected) {
            if ($counts[$label] !== $expected) {
                $errors[] = sprintf(
                    'jumlah %s pada snapshot sumber %d, seharusnya %d; tinjau perubahan sumber sebelum seeding',
                    $label,
                    $counts[$label],
                    $expected
                );
            }
        }

        $teacherIds = $this->validateTeachers($teachers, $errors);
        $classIds = $this->validateClasses($classes, $teacherIds, $errors);
        $subjectCodes = $this->validateSubjects($subjects, $errors);
        $this->validateStudents($students, $classIds, $errors);

        [$confirmedCstPairs, $pendingCstPairs, $teacherByPair] = $this->validateCst(
            $cst,
            $pendingCst,
            $classIds,
            $teacherIds,
            $subjectCodes,
            $errors
        );

        $this->validateRoster(
            $roster,
            $classIds,
            $subjectCodes,
            $confirmedCstPairs,
            $teacherByPair,
            $errors
        );

        $this->validatePendingRoster(
            $pendingRoster,
            $classIds,
            $pendingCstPairs,
            $roster,
            $errors
        );

        if ($errors !== []) {
            $visibleErrors = array_slice($errors, 0, 30);
            $remaining = count($errors) - count($visibleErrors);
            $message = "Preflight MSBD gagal. Tidak ada perubahan database dilakukan:\n- "
                . implode("\n- ", $visibleErrors);

            if ($remaining > 0) {
                $message .= "\n- dan {$remaining} temuan lainnya.";
            }

            throw new RuntimeException($message);
        }

        $this->command?->info(
            'Preflight MSBD lulus: '
            . $counts['students'] . ' siswa, '
            . $counts['teachers'] . ' guru, '
            . $counts['classes'] . ' kelas, '
            . $counts['subjects'] . ' mata pelajaran, '
            . $counts['cst'] . ' CST terkonfirmasi, dan '
            . $counts['roster'] . ' slot roster terkonfirmasi.'
        );

        if ($counts['pending_cst'] > 0 || $counts['pending_roster'] > 0) {
            $this->command?->warn(
                $counts['pending_cst'] . ' CST dan '
                . $counts['pending_roster']
                . ' slot roster masih ditahan untuk konfirmasi sekolah.'
            );
        }
    }

    /** @return array<string, true> */
    private function validateTeachers(array $teachers, array &$errors): array
    {
        $ids = [];
        $emails = [];

        foreach ($teachers as $index => $teacher) {
            $line = $index + 1;

            if (! is_array($teacher)) {
                $errors[] = "data guru pada baris #{$line} bukan array";
                continue;
            }

            $nip = trim((string) ($teacher['nip'] ?? ''));
            $name = trim((string) ($teacher['name'] ?? ''));
            $email = strtolower(trim((string) ($teacher['email'] ?? '')));
            $specialization = trim((string) ($teacher['specialization'] ?? ''));
            $idType = trim((string) ($teacher['id_type'] ?? ''));

            if ($nip === '' || strlen($nip) > 30) {
                $errors[] = "NIP/ID guru kosong atau melebihi 30 karakter pada baris #{$line}";
            } elseif (isset($ids[$nip])) {
                $errors[] = "NIP/ID guru duplikat: {$nip}";
            } else {
                $ids[$nip] = true;
            }

            if ($name === '') {
                $errors[] = "nama guru kosong pada baris #{$line}";
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "format email teknis guru tidak valid pada baris #{$line}";
            } elseif (isset($emails[$email])) {
                $errors[] = "email guru duplikat pada baris #{$line}";
            } else {
                $emails[$email] = true;
            }
            if ($specialization === '') {
                $errors[] = "spesialisasi guru kosong pada baris #{$line}";
            }
            if (! in_array($idType, ['ID_INTERNAL', 'NUPTK'], true)) {
                $errors[] = "jenis identitas guru tidak valid pada baris #{$line}";
            }
        }

        return $ids;
    }

    /** @return array<int, true> */
    private function validateClasses(array $classes, array $teacherIds, array &$errors): array
    {
        $ids = [];
        $names = [];

        foreach ($classes as $index => $class) {
            $line = $index + 1;

            if (! is_array($class)) {
                $errors[] = "data kelas pada baris #{$line} bukan array";
                continue;
            }

            $classId = (int) ($class['class_id'] ?? 0);
            $name = trim((string) ($class['name'] ?? ''));
            $academicYear = trim((string) ($class['academic_year'] ?? ''));
            $homeroomTeacherId = trim((string) ($class['homeroom_teacher_id'] ?? ''));

            if ($classId <= 0) {
                $errors[] = "class_id tidak valid pada baris kelas #{$line}";
            } elseif (isset($ids[$classId])) {
                $errors[] = "class_id duplikat: {$classId}";
            } else {
                $ids[$classId] = true;
            }

            if ($name === '') {
                $errors[] = "nama kelas kosong pada baris #{$line}";
            } elseif (isset($names[$name])) {
                $errors[] = "nama kelas duplikat: {$name}";
            } else {
                $names[$name] = true;
            }

            if ($academicYear !== self::ACADEMIC_YEAR) {
                $errors[] = "tahun ajaran kelas {$name} tidak sesuai snapshot 2026/2027";
            }
            if ($homeroomTeacherId === '' || ! isset($teacherIds[$homeroomTeacherId])) {
                $errors[] = "wali kelas {$name} tidak merujuk ID guru yang tersedia";
            }
        }

        return $ids;
    }

    /** @return array<string, true> */
    private function validateSubjects(array $subjects, array &$errors): array
    {
        $codes = [];
        $names = [];

        foreach ($subjects as $code => $name) {
            $code = trim((string) $code);
            $name = trim((string) $name);

            if ($code === '' || $name === '') {
                $errors[] = 'kode atau nama mata pelajaran kosong';
                continue;
            }
            if (isset($codes[$code])) {
                $errors[] = "kode mata pelajaran duplikat: {$code}";
            }
            if (isset($names[$name])) {
                $errors[] = "nama mata pelajaran duplikat: {$name}";
            }

            $codes[$code] = true;
            $names[$name] = true;
        }

        return $codes;
    }

    /** @param array<int, true> $classIds */
    private function validateStudents(array $students, array $classIds, array &$errors): void
    {
        $seenNis = [];
        $seenNisn = [];
        $duplicateNisn = [];

        foreach ($students as $index => $student) {
            $line = $index + 1;

            if (! is_array($student)) {
                $errors[] = "data siswa pada baris #{$line} bukan array";
                continue;
            }

            $nis = trim((string) ($student['nis'] ?? ''));
            $nisn = trim((string) ($student['nisn'] ?? ''));
            $name = trim((string) ($student['name'] ?? ''));
            $classId = (int) ($student['class_id'] ?? 0);
            $gender = strtoupper(trim((string) ($student['gender'] ?? '')));

            if ($nis === '' || ! ctype_digit($nis)) {
                $errors[] = "NIS kosong atau bukan angka pada baris siswa #{$line}";
            } elseif (isset($seenNis[$nis])) {
                $errors[] = "NIS duplikat pada baris siswa #{$line}";
            } else {
                $seenNis[$nis] = true;
            }

            if (! preg_match('/^\d{10}$/D', $nisn)) {
                $errors[] = "NISN wajib 10 digit pada baris siswa #{$line}";
            } elseif (isset($seenNisn[$nisn])) {
                if (! isset($duplicateNisn[$nisn])) {
                    $duplicateNisn[$nisn] = [$seenNisn[$nisn]];
                }
                $duplicateNisn[$nisn][] = $name . ' (NIS ' . $nis . ')';
            } else {
                $seenNisn[$nisn] = $name . ' (NIS ' . $nis . ')';
            }

            if ($name === '') {
                $errors[] = "nama siswa kosong pada baris #{$line}";
            }
            if (! isset($classIds[$classId])) {
                $errors[] = "siswa {$name} merujuk class_id {$classId} yang tidak tersedia";
            }
            if (! in_array($gender, ['L', 'P'], true)) {
                $errors[] = "jenis kelamin siswa tidak valid pada baris #{$line}";
            }
        }

        foreach ($duplicateNisn as $studentsWithSameNisn) {
            $errors[] = 'NISN tercatat untuk lebih dari satu siswa: '
                . implode(' dan ', $studentsWithSameNisn)
                . '. Minta sekolah memverifikasi nomor resmi; seeder tidak membuat NISN pengganti.';
        }
    }

    /**
     * @return array{0: array<string, true>, 1: array<string, true>, 2: array<string, string>}
     */
    private function validateCst(
        array $cst,
        array $pendingCst,
        array $classIds,
        array $teacherIds,
        array $subjectCodes,
        array &$errors
    ): array {
        $confirmedPairs = [];
        $pendingPairs = [];
        $teacherByPair = [];
        $seenIds = [];

        foreach ($cst as $index => $assignment) {
            $line = $index + 1;
            if (! is_array($assignment)) {
                $errors[] = "CST terkonfirmasi pada baris #{$line} bukan array";
                continue;
            }

            $cstId = trim((string) ($assignment['cst_id'] ?? ''));
            $classId = (int) ($assignment['class_id'] ?? 0);
            $subjectCode = trim((string) ($assignment['subject_code'] ?? ''));
            $teacherId = trim((string) ($assignment['teacher_id'] ?? ''));
            $academicYear = trim((string) ($assignment['academic_year'] ?? ''));
            $semester = strtolower(trim((string) ($assignment['semester'] ?? '')));
            $pair = $classId . '|' . $subjectCode;

            $this->validateCstFields(
                $line,
                $cstId,
                $classId,
                $subjectCode,
                $teacherId,
                $academicYear,
                $semester,
                $classIds,
                $teacherIds,
                $subjectCodes,
                $errors,
                'CST'
            );

            if ($cstId !== '' && isset($seenIds[$cstId])) {
                $errors[] = "cst_id duplikat: {$cstId}";
            }
            // Satu pasangan kelas-mapel hanya boleh memiliki satu CST
            // dalam snapshot roster yang tidak mencantumkan semester.
            if (isset($confirmedPairs[$pair])) {
                $errors[] = "pasangan kelas-mapel CST duplikat dalam snapshot roster: {$pair}";
            }

            $seenIds[$cstId] = true;
            $confirmedPairs[$pair] = true;
            $teacherByPair[$pair] = $teacherId;
        }

        foreach ($pendingCst as $index => $assignment) {
            $line = $index + 1;
            if (! is_array($assignment)) {
                $errors[] = "CST pending pada baris #{$line} bukan array";
                continue;
            }

            $cstId = trim((string) ($assignment['cst_id'] ?? ''));
            $classId = (int) ($assignment['class_id'] ?? 0);
            $subjectCode = trim((string) ($assignment['subject_code'] ?? ''));
            $teacherId = trim((string) ($assignment['teacher_id'] ?? ''));
            $academicYear = trim((string) ($assignment['academic_year'] ?? ''));
            $semester = strtolower(trim((string) ($assignment['semester'] ?? '')));
            $pair = $classId . '|' . $subjectCode;

            if ($cstId === '' || strlen($cstId) > 20) {
                $errors[] = "cst_id pending kosong atau terlalu panjang pada baris #{$line}";
            }
            if (! isset($classIds[$classId])) {
                $errors[] = "CST pending merujuk kelas yang tidak tersedia: {$classId}";
            }
            if ($subjectCode === '' || isset($subjectCodes[$subjectCode])) {
                $errors[] = "kode mapel CST pending kosong atau sudah dianggap terkonfirmasi: {$subjectCode}";
            }
            if (! isset($teacherIds[$teacherId])) {
                $errors[] = "CST pending merujuk guru yang tidak tersedia: {$teacherId}";
            }
            if ($academicYear !== self::ACADEMIC_YEAR || ! in_array($semester, ['ganjil', 'genap'], true)) {
                $errors[] = "tahun ajaran/semester CST pending tidak valid pada baris #{$line}";
            }
            if (trim((string) ($assignment['pending_reason'] ?? '')) === '') {
                $errors[] = "alasan penahanan CST kosong pada baris #{$line}";
            }
            if (isset($seenIds[$cstId])) {
                $errors[] = "cst_id duplikat antara data terkonfirmasi/pending: {$cstId}";
            }
            if (isset($confirmedPairs[$pair]) || isset($pendingPairs[$pair])) {
                $errors[] = "CST pending bertabrakan dengan pasangan CST lain: {$pair}";
            }

            $seenIds[$cstId] = true;
            $pendingPairs[$pair] = true;
        }

        return [$confirmedPairs, $pendingPairs, $teacherByPair];
    }

    private function validateCstFields(
        int $line,
        string $cstId,
        int $classId,
        string $subjectCode,
        string $teacherId,
        string $academicYear,
        string $semester,
        array $classIds,
        array $teacherIds,
        array $subjectCodes,
        array &$errors,
        string $label
    ): void {
        if ($cstId === '' || strlen($cstId) > 20) {
            $errors[] = "{$label} cst_id kosong atau melebihi 20 karakter pada baris #{$line}";
        }
        if (! isset($classIds[$classId])) {
            $errors[] = "{$label} merujuk kelas yang tidak tersedia: {$classId}";
        }
        if (! isset($subjectCodes[$subjectCode])) {
            $errors[] = "{$label} merujuk mapel yang tidak tersedia: {$subjectCode}";
        }
        if (! isset($teacherIds[$teacherId])) {
            $errors[] = "{$label} merujuk guru yang tidak tersedia: {$teacherId}";
        }
        if ($academicYear !== self::ACADEMIC_YEAR) {
            $errors[] = "{$label} tahun ajaran tidak sesuai snapshot 2026/2027 pada baris #{$line}";
        }
        if (! in_array($semester, ['ganjil', 'genap'], true)) {
            $errors[] = "{$label} semester tidak valid pada baris #{$line}";
        }
    }

    /**
     * @param array<int, true> $classIds
     * @param array<string, true> $subjectCodes
     * @param array<string, true> $confirmedCstPairs
     * @param array<string, string> $teacherByPair
     */
    private function validateRoster(
        array $roster,
        array $classIds,
        array $subjectCodes,
        array $confirmedCstPairs,
        array $teacherByPair,
        array &$errors
    ): void {
        $classSlots = [];
        $teacherSlots = [];
        $seenSlots = [];

        foreach ($roster as $index => $slot) {
            $line = $index + 1;
            if (! is_array($slot)) {
                $errors[] = "slot roster pada baris #{$line} bukan array";
                continue;
            }

            $classId = (int) ($slot['class_id'] ?? 0);
            $subjectCode = trim((string) ($slot['subject_code'] ?? ''));
            $day = strtolower(trim((string) ($slot['day'] ?? '')));
            $start = trim((string) ($slot['start'] ?? ''));
            $end = trim((string) ($slot['end'] ?? ''));
            $pair = $classId . '|' . $subjectCode;
            $startSeconds = $this->timeToSeconds($start);
            $endSeconds = $this->timeToSeconds($end);

            if (! isset($classIds[$classId])) {
                $errors[] = "roster merujuk kelas yang tidak tersedia: {$classId}";
            }
            if (! isset($subjectCodes[$subjectCode])) {
                $errors[] = "roster merujuk mapel yang tidak tersedia: {$subjectCode}";
            }
            if (! isset($confirmedCstPairs[$pair])) {
                $errors[] = "slot roster terkonfirmasi tidak memiliki CST: {$pair}";
            }
            if (! in_array($day, self::VALID_DAYS, true)) {
                $errors[] = "hari roster tidak valid pada baris #{$line}";
            }
            if ($startSeconds === null || $endSeconds === null || $startSeconds >= $endSeconds) {
                $errors[] = "rentang waktu roster tidak valid pada baris #{$line}";
                continue;
            }

            $slotKey = $classId . '|' . $day . '|' . $start . '|' . $end;
            if (isset($seenSlots[$slotKey])) {
                $errors[] = "slot kelas duplikat pada roster: {$slotKey}";
            }
            $seenSlots[$slotKey] = true;
            $classSlots[$classId . '|' . $day][] = [$startSeconds, $endSeconds, $line];
        }

        $this->appendOverlapErrors($classSlots, 'kelas', $errors);

        // Periksa benturan guru berdasarkan pemetaan CST terkonfirmasi.
        // Jika satu guru mengajar dua kelas pada waktu yang sama, sumber ditahan.
        foreach ($roster as $index => $slot) {
            if (! is_array($slot)) {
                continue;
            }

            $classId = (int) ($slot['class_id'] ?? 0);
            $subjectCode = trim((string) ($slot['subject_code'] ?? ''));
            $day = strtolower(trim((string) ($slot['day'] ?? '')));
            $startSeconds = $this->timeToSeconds(trim((string) ($slot['start'] ?? '')));
            $endSeconds = $this->timeToSeconds(trim((string) ($slot['end'] ?? '')));
            $teacherId = $teacherByPair[$classId . '|' . $subjectCode] ?? '';

            if ($startSeconds !== null && $endSeconds !== null && $teacherId !== '') {
                $teacherSlots[$teacherId . '|' . $day][] = [$startSeconds, $endSeconds, $index + 1];
            }
        }

        $this->appendOverlapErrors($teacherSlots, 'guru', $errors);
    }

    /**
     * @param array<int, true> $classIds
     * @param array<string, true> $pendingCstPairs
     */
    private function validatePendingRoster(
        array $pendingRoster,
        array $classIds,
        array $pendingCstPairs,
        array $confirmedRoster,
        array &$errors
    ): void {
        $confirmedSlots = [];
        foreach ($confirmedRoster as $slot) {
            if (is_array($slot)) {
                $key = (int) ($slot['class_id'] ?? 0) . '|'
                    . strtolower(trim((string) ($slot['day'] ?? ''))) . '|'
                    . trim((string) ($slot['start'] ?? '')) . '|'
                    . trim((string) ($slot['end'] ?? ''));
                $confirmedSlots[$key] = true;
            }
        }

        $seenSlots = [];
        $pendingClassSlots = [];
        $allClassSlots = [];

        foreach ($confirmedRoster as $index => $confirmedSlot) {
            if (! is_array($confirmedSlot)) {
                continue;
            }
            $confirmedStart = $this->timeToSeconds(trim((string) ($confirmedSlot['start'] ?? '')));
            $confirmedEnd = $this->timeToSeconds(trim((string) ($confirmedSlot['end'] ?? '')));
            if ($confirmedStart !== null && $confirmedEnd !== null) {
                $confirmedClass = (int) ($confirmedSlot['class_id'] ?? 0);
                $confirmedDay = strtolower(trim((string) ($confirmedSlot['day'] ?? '')));
                $allClassSlots[$confirmedClass . '|' . $confirmedDay][] = [
                    $confirmedStart,
                    $confirmedEnd,
                    $index + 1,
                ];
            }
        }

        foreach ($pendingRoster as $index => $slot) {
            $line = $index + 1;
            if (! is_array($slot)) {
                $errors[] = "slot roster pending pada baris #{$line} bukan array";
                continue;
            }

            $classId = (int) ($slot['class_id'] ?? 0);
            $subjectCode = trim((string) ($slot['subject_code'] ?? ''));
            $day = strtolower(trim((string) ($slot['day'] ?? '')));
            $start = trim((string) ($slot['start'] ?? ''));
            $end = trim((string) ($slot['end'] ?? ''));
            $pair = $classId . '|' . $subjectCode;
            $startSeconds = $this->timeToSeconds($start);
            $endSeconds = $this->timeToSeconds($end);

            if (! isset($classIds[$classId])) {
                $errors[] = "roster pending merujuk kelas yang tidak tersedia: {$classId}";
            }
            if (! isset($pendingCstPairs[$pair])) {
                $errors[] = "slot roster pending tidak memiliki CST pending: {$pair}";
            }
            if (! in_array($day, self::VALID_DAYS, true)) {
                $errors[] = "hari roster pending tidak valid pada baris #{$line}";
            }
            if ($startSeconds === null || $endSeconds === null || $startSeconds >= $endSeconds) {
                $errors[] = "rentang waktu roster pending tidak valid pada baris #{$line}";
                continue;
            }

            $slotKey = $classId . '|' . $day . '|' . $start . '|' . $end;
            if (isset($seenSlots[$slotKey])) {
                $errors[] = "slot kelas duplikat pada roster pending: {$slotKey}";
            }
            if (isset($confirmedSlots[$slotKey])) {
                $errors[] = "slot pending bertabrakan dengan roster terkonfirmasi: {$slotKey}";
            }

            $seenSlots[$slotKey] = true;
            $pendingClassSlots[$classId . '|' . $day][] = [$startSeconds, $endSeconds, $line];
            $allClassSlots[$classId . '|' . $day][] = [$startSeconds, $endSeconds, $line];
        }

        $this->appendOverlapErrors($pendingClassSlots, 'kelas pada roster pending', $errors);
        $this->appendOverlapErrors($allClassSlots, 'kelas pada gabungan roster terkonfirmasi/pending', $errors);
    }

    /**
     * @param array<string, array<int, array{0:int,1:int,2:int}>> $groupedSlots
     */
    private function appendOverlapErrors(array $groupedSlots, string $label, array &$errors): void
    {
        foreach ($groupedSlots as $group => $slots) {
            usort($slots, static fn (array $left, array $right): int => $left[0] <=> $right[0]);

            $previous = null;
            foreach ($slots as $slot) {
                if ($previous !== null && $slot[0] < $previous[1]) {
                    $errors[] = "jadwal {$label} bertabrakan pada {$group}, baris #{$previous[2]} dan #{$slot[2]}";
                }

                if ($previous === null || $slot[1] > $previous[1]) {
                    $previous = $slot;
                }
            }
        }
    }

    private function timeToSeconds(string $time): ?int
    {
        if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/D', $time)) {
            return null;
        }

        [$hours, $minutes, $seconds] = array_map('intval', explode(':', $time));

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }
}
