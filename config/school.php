<?php

return [
    'name' => 'SMAS Methodist Banda Aceh',

    // KKM / nilai minimal tuntas (untuk nilai tugas, kuis, dan lainnya di buku nilai)
    'kkm' => 75,

    // Cek Nilai untuk orang tua (tanpa login)
    'guest_check' => [
        'require_dob' => true,   // verifikasi tanggal lahir anak sebelum nilai tampil
    ],

    // Ujian
    'exam' => [
        'max_violations' => 3,    // batas pelanggaran sebelum ujian otomatis diakhiri
    ],

    'question_types' => [
        'pilihan_ganda'          => 'Pilihan Ganda',
        'pilihan_ganda_kompleks' => 'Pilihan Ganda Kompleks',
        'benar_salah'            => 'Benar / Salah',
        'menjodohkan'            => 'Mencocokkan Jawaban',
        'esai'                   => 'Esai (unggah foto lembar jawaban)',
    ],
];