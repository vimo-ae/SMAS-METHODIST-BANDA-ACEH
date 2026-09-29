<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'MTK',
                'name' => 'Matematika',
            ],
            [
                'code' => 'FIS',
                'name' => 'Fisika',
            ],
            [
                'code' => 'BIO',
                'name' => 'Biologi',
            ],
            [
                'code' => 'KIM',
                'name' => 'Kimia',
            ],
            [
                'code' => 'BIN',
                'name' => 'Bahasa Indonesia',
            ],
            [
                'code' => 'BIG',
                'name' => 'Bahasa Inggris',
            ],
            [
                'code' => 'SEJ',
                'name' => 'Sejarah',
            ],
            [
                'code' => 'EKO',
                'name' => 'Ekonomi',
            ],
            [
                'code' => 'GEO',
                'name' => 'Geografi',
            ],
            [
                'code' => 'PKN',
                'name' => 'PPKn',
            ],
            [
                'code' => 'SOS',
                'name' => 'Sosiologi',
            ],
            [
                'code' => 'INF',
                'name' => 'Informatika',
            ],
            [
                'code' => 'SBD',
                'name' => 'Seni Budaya',
            ],
            [
                'code' => 'PJOK',
                'name' => 'PJOK',
            ],
            [
                'code' => 'AGM',
                'name' => 'Pendidikan Agama',
            ],
            [
                'code' => 'PRA',
                'name' => 'Prakarya',
            ],
            [
                'code' => 'JEP',
                'name' => 'Bahasa Jepang',
            ],
            [
                'code' => 'MAN',
                'name' => 'Bahasa Mandarin',
            ],
            [
                'code' => 'BK',
                'name' => 'Bimbingan Konseling',
            ],
            [
                'code' => 'STA',
                'name' => 'Statistika',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                [
                    'code' => $subject['code'],
                ],
                [
                    'name' => $subject['name'],
                ]
            );
        }
    }
}