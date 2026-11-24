<?php

namespace Database\Seeders;

use App\Models\ClassSubjectTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Memasukkan jadwal roster tanpa menghapus atau menimpa jadwal yang sudah ada. */
class RosterScheduleSeeder extends Seeder
{
    private const ACADEMIC_YEAR = '2026/2027';
    private const DAYS = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function run(): void
    {
        $path = database_path('seeders/data/roster_schedule_2026_2027.php');
        if (! is_file($path)) {
            throw new RuntimeException('RosterScheduleSeeder: file roster terkonfirmasi tidak ditemukan.');
        }

        $slots = require $path;
        if (! is_array($slots) || $slots === []) {
            throw new RuntimeException('RosterScheduleSeeder: file roster kosong/tidak valid.');
        }

        $cstLookup = [];
        foreach (ClassSubjectTeacher::query()
            ->where('academic_year', self::ACADEMIC_YEAR)
            ->get(['cst_id', 'class_id', 'subject_code']) as $cst) {
            $key = $cst->class_id . '|' . $cst->subject_code;
            if (isset($cstLookup[$key])) {
                throw new RuntimeException(
                    "RosterScheduleSeeder: pasangan kelas-mapel {$key} memiliki lebih dari satu CST."
                );
            }
            $cstLookup[$key] = $cst->cst_id;
        }

        $desired = [];
        $managedCsts = [];
        $unmapped = 0;
        $invalid = 0;
        $duplicates = 0;

        foreach ($slots as $index => $slot) {
            if (! is_array($slot)) {
                $invalid++;
                continue;
            }

            $classId = (int) ($slot['class_id'] ?? 0);
            $subjectCode = strtoupper(trim((string) ($slot['subject_code'] ?? '')));
            $day = strtolower(trim((string) ($slot['day'] ?? '')));
            $start = trim((string) ($slot['start'] ?? ''));
            $end = trim((string) ($slot['end'] ?? ''));

            if (
                $classId <= 0
                || $subjectCode === ''
                || ! in_array($day, self::DAYS, true)
                || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $start)
                || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $end)
                || $start >= $end
            ) {
                $invalid++;
                continue;
            }

            $cstId = $cstLookup[$classId . '|' . $subjectCode] ?? null;
            if ($cstId === null) {
                $unmapped++;
                continue;
            }

            $managedCsts[$cstId] = true;
            $key = "{$cstId}|{$day}|{$start}";
            if (isset($desired[$key])) {
                $duplicates++;
                continue;
            }

            $desired[$key] = [
                'cst_id' => $cstId,
                'day' => $day,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        $inserted = 0;
        $unchanged = 0;
        $conflicts = [];

        DB::transaction(function () use ($desired, &$inserted, &$unchanged, &$conflicts): void {
            foreach ($desired as $slot) {
                $existing = DB::table('schedules')
                    ->where('cst_id', $slot['cst_id'])
                    ->where('day', $slot['day'])
                    ->where('start_time', $slot['start_time'])
                    ->first();

                if ($existing) {
                    if ((string) $existing->end_time !== (string) $slot['end_time']) {
                        $conflicts[] = $slot['cst_id'] . ' ' . $slot['day'] . ' ' . $slot['start_time'];
                    } else {
                        $unchanged++;
                    }
                    continue;
                }

                DB::table('schedules')->insert([
                    'cst_id' => $slot['cst_id'],
                    'day' => $slot['day'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }, 3);

        // Jadwal manual/di luar roster hanya dilaporkan, tidak dihapus.
        $stale = [];
        if ($managedCsts !== []) {
            $existingRows = DB::table('schedules')
                ->whereIn('cst_id', array_keys($managedCsts))
                ->get(['cst_id', 'day', 'start_time']);

            foreach ($existingRows as $row) {
                $key = "{$row->cst_id}|{$row->day}|{$row->start_time}";
                if (! isset($desired[$key])) {
                    $stale[] = "{$row->cst_id} {$row->day} {$row->start_time}";
                }
            }
        }

        $pendingCount = 0;
        $pendingPath = database_path('seeders/data/roster_schedule_pending_2026_2027.php');
        if (is_file($pendingPath)) {
            $pending = require $pendingPath;
            $pendingCount = is_array($pending) ? count($pending) : 0;
        }

        if ($unmapped > 0) {
            $this->command?->warn("RosterScheduleSeeder: {$unmapped} slot belum terpetakan ke CST dan tidak dimasukkan.");
        }
        if ($pendingCount > 0) {
            $this->command?->warn(
                "RosterScheduleSeeder: {$pendingCount} slot ditahan karena pemetaan KES belum dikonfirmasi."
            );
        }
        if ($invalid > 0) {
            $this->command?->warn("RosterScheduleSeeder: {$invalid} baris roster tidak valid.");
        }
        if ($duplicates > 0) {
            $this->command?->warn("RosterScheduleSeeder: {$duplicates} slot duplikat pada sumber diabaikan.");
        }
        if ($conflicts !== []) {
            $this->command?->warn(
                'RosterScheduleSeeder: ' . count($conflicts)
                . ' jadwal memiliki jam selesai berbeda; data database dipertahankan.'
            );
        }
        if ($stale !== []) {
            $this->command?->warn(
                'RosterScheduleSeeder: ' . count($stale)
                . ' jadwal manual/di luar roster dipertahankan; tidak ada jadwal dihapus.'
            );
        }

        $this->command?->info(
            'RosterScheduleSeeder: ' . $inserted . ' slot baru ditambahkan; '
            . $unchanged . ' slot sudah sesuai; ' . count($desired)
            . ' slot sumber terkonfirmasi dipetakan. Tidak ada jadwal diperbarui/dihapus.'
        );
    }
}
