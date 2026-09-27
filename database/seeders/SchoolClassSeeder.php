<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    // Struktur kelas sekolah: data tetap sesuai jenjang SMA
    public function run(): void
    {
        $grades = [10 => 'X', 11 => 'XI', 12 => 'XII'];
        $jurusan = ['IPA', 'IPS'];

        foreach ($grades as $level => $roman) {
            foreach ($jurusan as $j) {
                for ($i = 1; $i <= 2; $i++) {
                    SchoolClass::firstOrCreate([
                        'name' => "$roman $j $i",
                        'academic_year' => '2025/2026',
                    ], [
                        'grade_level' => $level,
                    ]);
                }
            }
        }
    }
}
