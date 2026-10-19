<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /** Tepat 15 mata pelajaran + durasi ujian (menit) masing-masing. */
    public const SUBJECTS = [
        'MTK' => ['Matematika', 120], 'FIS' => ['Fisika', 90], 'BIO' => ['Biologi', 90],
        'KIM' => ['Kimia', 90], 'BIN' => ['Bahasa Indonesia', 90], 'BIG' => ['Bahasa Inggris', 90],
        'SEJ' => ['Sejarah', 60], 'EKO' => ['Ekonomi', 75], 'GEO' => ['Geografi', 60],
        'PKN' => ['PPKn', 60], 'SOS' => ['Sosiologi', 60], 'INF' => ['Informatika', 100],
        'SBD' => ['Seni Budaya', 45], 'PJOK' => ['PJOK', 45], 'AGM' => ['Pendidikan Agama', 60],
    ];

    public function run(): void
    {
        Subject::whereNotIn('code', array_keys(self::SUBJECTS))->delete();
        foreach (self::SUBJECTS as $code => [$name]) {
            Subject::updateOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
