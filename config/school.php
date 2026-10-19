<?php

return [
    'name' => 'SMAS Methodist Banda Aceh',

    // Bobot nilai rapor (total 100). "Harian" = rata-rata tugas + kuis.
    'grade_weights' => [
        'harian' => 30,
        'uts'    => 30,
        'uas'    => 40,
    ],

    // KKM / nilai minimal tuntas
    'kkm' => 75,

    // Ujian
    'exam' => [
        'default_total_questions' => 35,   // 35 soal per mata pelajaran
        'max_violations'          => 3,    // batas pelanggaran sebelum ujian otomatis diakhiri
    ],

    'question_types' => [
        'pilihan_ganda'          => 'Pilihan Ganda',
        'pilihan_ganda_kompleks' => 'Pilihan Ganda Kompleks',
        'benar_salah'            => 'Benar / Salah',
        'menjodohkan'            => 'Mencocokkan Jawaban',
        'esai'                   => 'Esai (unggah foto lembar jawaban)',
    ],
];
