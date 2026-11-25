<?php

namespace App\Services;

class RosterService
{
    public const ACADEMIC_YEAR = '2026 - 2027';
    public const SCHOOL_NAME = 'SMAS METHODIST BANDA ACEH';
    public const PRINCIPAL_NAME = 'Sheilisa, S.Pd., Gr.';
    public const CURRICULUM_HEAD = 'Nasrul Hanafiah';
    public const DECREE_DATE = 'Banda Aceh, 12 Juli 2026';

    public const CLASSES = [
        'X IPAS A' => ['label' => 'X-A',     'grade' => 'X',   'major' => 'IPAS', 'color' => '#123B60'],
        'X IPAS B' => ['label' => 'X-B',     'grade' => 'X',   'major' => 'IPAS', 'color' => '#123B60'],
        'XI A'     => ['label' => 'XI-A',    'grade' => 'XI',  'major' => 'Umum', 'color' => '#1B4D7E'],
        'XI B'     => ['label' => 'XI-B',    'grade' => 'XI',  'major' => 'Umum', 'color' => '#1B4D7E'],
        'XII A'    => ['label' => 'XII-A',   'grade' => 'XII', 'major' => 'Umum', 'color' => '#267BB5'],
        'XII B'    => ['label' => 'XII-B',   'grade' => 'XII', 'major' => 'Umum', 'color' => '#267BB5'],
    ];

    public const TEACHERS = [
        'NH' => ['code' => 'NH', 'name' => 'Nasrul Hanafiah', 'role' => 'Waka. Kurikulum Ur. Administrasi', 'subject' => 'MATE', 'desc' => 'Matematika'],
        'ZV' => ['code' => 'ZV', 'name' => 'Zakiyul Vuady, S.Pd', 'role' => 'Wali Kelas XI A & Guru PJOK', 'subject' => 'PJOK', 'desc' => 'PJOK'],
        'MR' => ['code' => 'MR', 'name' => 'Merry Rouli Siahaan, S.Th., M.Pd.K', 'role' => 'Guru BK, Pembina Pramuka, Bendahara BOS', 'subject' => 'PAK', 'desc' => 'Agama Kristen & Ibadah'],
        'DN' => ['code' => 'DN', 'name' => 'Daniatul Hikmah, S.Pd', 'role' => 'Pembina OSIS, Wali Kelas X A', 'subject' => 'KES', 'desc' => 'PKWU / Kesenian'],
        'MS' => ['code' => 'MS', 'name' => 'Mulyadi Syahputra, M.Pd', 'role' => 'Koordinator Club BEAMS', 'subject' => 'B.ING', 'desc' => 'Bahasa Inggris'],
        'AP' => ['code' => 'AP', 'name' => 'Ajeng Prasasti, S.Pd', 'role' => 'Wali Kelas X B', 'subject' => 'SEJ / SOS', 'desc' => 'Sejarah & Sosiologi'],
        'RH' => ['code' => 'RH', 'name' => 'Rahmat Hidayat, S.Pd., M.Pd., Gr.', 'role' => 'Waka Kurikulum Peningkatan Mutu, Wali Kelas XII B', 'subject' => 'KIM', 'desc' => 'Kimia'],
        'NR' => ['code' => 'NR', 'name' => 'Nisrina Umar Abbas, S.Pd.', 'role' => 'Wali Kelas XII A', 'subject' => 'BIO', 'desc' => 'Biologi'],
        'RZ' => ['code' => 'RZ', 'name' => 'Reza Fachrulrazi, S.Pd., Gr.', 'role' => 'Wali Kelas XI B', 'subject' => 'B.INDO', 'desc' => 'Bahasa Indonesia'],
        'MT' => ['code' => 'MT', 'name' => 'Drs. Muhammad Toha', 'role' => 'Guru Bidang Studi', 'subject' => 'FISIKA', 'desc' => 'Fisika'],
        'AK' => ['code' => 'AK', 'name' => 'Muhammad Akbarul, SE', 'role' => 'Guru Bidang Studi', 'subject' => 'EKO', 'desc' => 'Ekonomi'],
        'OS' => ['code' => 'OS', 'name' => 'Oslen Rajagukguk', 'role' => 'Guru Bidang Studi', 'subject' => 'GEO', 'desc' => 'Geografi'],
        'NN' => ['code' => 'NN', 'name' => 'Nurul Husna, S.Pd., M.Pd', 'role' => 'Koordinator Career Coaching', 'subject' => 'MATE', 'desc' => 'Matematika'],
        'IM' => ['code' => 'IM', 'name' => 'Irhamni Mardhatillah, S.Pd', 'role' => 'Guru Bidang Studi', 'subject' => 'B.ING', 'desc' => 'Bahasa Inggris'],
        'WH' => ['code' => 'WH', 'name' => 'Wahyu, S.Pd', 'role' => 'Guru Bidang Studi', 'subject' => 'PPKN', 'desc' => 'PPKn'],
        'RW' => ['code' => 'RW', 'name' => 'Rayyan Wahyu Maulana, S.Pd', 'role' => 'Guru Bidang Studi', 'subject' => 'SOS / SEJ', 'desc' => 'Sosiologi & Sejarah'],
        'RE' => ['code' => 'RE', 'name' => 'Reinaldo Lionojaya, B.ED', 'role' => 'Guru Bidang Studi', 'subject' => 'MAND', 'desc' => 'Bahasa Mandarin'],
        'GR' => ['code' => 'GR', 'name' => 'George Rendy, S.Kom', 'role' => 'Operator & Guru Bidang Studi', 'subject' => 'TIK', 'desc' => 'Informatika / TIK'],
    ];

    /**
     * Data Pembagian Tugas Guru SMA Methodist Banda Aceh Tahun Ajaran 2026 - 2027
     */
    public const TEACHER_DUTIES = [
        [
            'no' => 1,
            'name' => 'Sheilisa, S.Pd., Gr.',
            'role' => 'Kepala Sekolah',
            'subject' => '—',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => null, 'XI B' => null, 'XII A' => null, 'XII B' => null],
            'total' => null,
            'code' => null,
        ],
        [
            'no' => 2,
            'name' => 'Fransisca, S.Pd',
            'role' => 'Waka. Kesiswaan',
            'subject' => '—',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => null, 'XI B' => null, 'XII A' => null, 'XII B' => null],
            'total' => null,
            'code' => null,
        ],
        [
            'no' => 3,
            'name' => 'Nasrul Hanafiah',
            'role' => 'Waka. Kurikulum Ur. Administrasi',
            'subject' => 'Matematika',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => null, 'XI B' => null, 'XII A' => 5, 'XII B' => 5],
            'total' => 10,
            'code' => 'NH',
        ],
        [
            'no' => 4,
            'name' => 'Zakiyul Vuady, S.Pd',
            'role' => 'Wali Kelas XI A / Guru Bidang Studi',
            'subject' => 'PJOK',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 3, 'XI B' => 3, 'XII A' => 3, 'XII B' => 3],
            'total' => 18,
            'code' => 'ZV',
        ],
        [
            'no' => 5,
            'name' => 'Merry Rouli Siahaan, S.Th., M.Pd.K',
            'role' => 'Guru BK / Pembina Pramuka / Bendahara BOS',
            'subject' => 'Agama Kristen & Ibadah',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 3, 'XI B' => 3, 'XII A' => 3, 'XII B' => 3],
            'total' => 18,
            'code' => 'MR',
        ],
        [
            'no' => 6,
            'name' => 'Daniatul Hikmah, S.Pd',
            'role' => 'Pembina OSIS / Wali Kelas X A',
            'subject' => 'PKWU / Kesenian',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 3, 'XI B' => 3, 'XII A' => 2, 'XII B' => 2],
            'total' => 16,
            'code' => 'DN',
        ],
        [
            'no' => 7,
            'name' => 'Mulyadi Syahputra, M.Pd',
            'role' => 'Koordinator Club BEAMS',
            'subject' => 'Bahasa Inggris',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => 4, 'XI B' => 4, 'XII A' => 4, 'XII B' => 4],
            'total' => 16,
            'code' => 'MS',
        ],
        [
            'no' => 8,
            'name' => 'Ajeng Prasasti, S.Pd',
            'role' => 'Wali Kelas X B / Guru Bidang Studi',
            'subject' => 'Sejarah & Sosiologi',
            'hours' => ['X IPAS A' => 6, 'X IPAS B' => 6, 'XI A' => 2, 'XI B' => 2, 'XII A' => 2, 'XII B' => 7],
            'total' => 25,
            'code' => 'AP',
        ],
        [
            'no' => 9,
            'name' => 'Rahmat Hidayat, S.Pd., M.Pd., Gr.',
            'role' => 'Waka Kurikulum Peningkatan Mutu / Wali Kelas XII B',
            'subject' => 'Kimia',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 5, 'XI B' => 4, 'XII A' => 5, 'XII B' => 4],
            'total' => 24,
            'code' => 'RH',
        ],
        [
            'no' => 10,
            'name' => 'Nisrina Umar Abbas, S.Pd.',
            'role' => 'Wali Kelas XII A / Guru Bidang Studi',
            'subject' => 'Biologi',
            'hours' => ['X IPAS A' => 4, 'X IPAS B' => 4, 'XI A' => 5, 'XI B' => 3, 'XII A' => 4, 'XII B' => null],
            'total' => 20,
            'code' => 'NR',
        ],
        [
            'no' => 11,
            'name' => 'Reza Fachrulrazi, S.Pd., Gr.',
            'role' => 'Wali Kelas XI B / Guru Bidang Studi',
            'subject' => 'Bahasa Indonesia',
            'hours' => ['X IPAS A' => 4, 'X IPAS B' => 4, 'XI A' => 4, 'XI B' => 4, 'XII A' => 4, 'XII B' => 4],
            'total' => 24,
            'code' => 'RZ',
        ],
        [
            'no' => 12,
            'name' => 'Drs. Muhammad Toha',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Fisika',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 5, 'XI B' => null, 'XII A' => 5, 'XII B' => null],
            'total' => 16,
            'code' => 'MT',
        ],
        [
            'no' => 13,
            'name' => 'Muhammad Akbarul, SE',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Ekonomi',
            'hours' => ['X IPAS A' => 3, 'X IPAS B' => 3, 'XI A' => 5, 'XI B' => 4, 'XII A' => 5, 'XII B' => 5],
            'total' => 25,
            'code' => 'AK',
        ],
        [
            'no' => 14,
            'name' => 'Oslen Rajagukguk',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Geografi',
            'hours' => ['X IPAS A' => 4, 'X IPAS B' => 4, 'XI A' => null, 'XI B' => 5, 'XII A' => null, 'XII B' => 5],
            'total' => 18,
            'code' => 'OS',
        ],
        [
            'no' => 15,
            'name' => 'Nurul Husna, S.Pd., M.Pd',
            'role' => 'Koordinator Career Coaching / Guru Bidang Studi',
            'subject' => 'Matematika',
            'hours' => ['X IPAS A' => 4, 'X IPAS B' => 4, 'XI A' => 5, 'XI B' => 5, 'XII A' => null, 'XII B' => null],
            'total' => 18,
            'code' => 'NN',
        ],
        [
            'no' => 16,
            'name' => 'Irhamni Mardhatillah, S.Pd',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Bahasa Inggris',
            'hours' => ['X IPAS A' => 4, 'X IPAS B' => 4, 'XI A' => null, 'XI B' => null, 'XII A' => null, 'XII B' => null],
            'total' => 8,
            'code' => 'IM',
        ],
        [
            'no' => 17,
            'name' => 'Wahyu, S.Pd',
            'role' => 'Guru Bidang Studi',
            'subject' => 'PPKN',
            'hours' => ['X IPAS A' => 2, 'X IPAS B' => 2, 'XI A' => 2, 'XI B' => 2, 'XII A' => 2, 'XII B' => 2],
            'total' => 12,
            'code' => 'WH',
        ],
        [
            'no' => 18,
            'name' => 'Rayyan Wahyu Maulana, S.Pd',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Sosiologi',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => null, 'XI B' => 4, 'XII A' => null, 'XII B' => null],
            'total' => 4,
            'code' => 'RW',
        ],
        [
            'no' => 19,
            'name' => 'Reinaldo Lionojaya, B.ED',
            'role' => 'Guru Bidang Studi',
            'subject' => 'Bahasa Mandarin',
            'hours' => ['X IPAS A' => 2, 'X IPAS B' => 2, 'XI A' => 2, 'XI B' => 2, 'XII A' => 2, 'XII B' => 2],
            'total' => 12,
            'code' => 'RE',
        ],
        [
            'no' => 20,
            'name' => 'George Rendy, S.Kom',
            'role' => 'Operator & Guru Bidang Studi',
            'subject' => 'Informatika',
            'hours' => ['X IPAS A' => 2, 'X IPAS B' => 2, 'XI A' => 2, 'XI B' => 2, 'XII A' => 2, 'XII B' => 2],
            'total' => 12,
            'code' => 'GR',
        ],
        [
            'no' => 21,
            'name' => 'Frisna RS, Amd',
            'role' => 'Tata Usaha',
            'subject' => '—',
            'hours' => ['X IPAS A' => null, 'X IPAS B' => null, 'XI A' => null, 'XI B' => null, 'XII A' => null, 'XII B' => null],
            'total' => null,
            'code' => null,
        ],
    ];

    public const SUBJECT_STYLES = [
        'BIO' => [
            'name' => 'Biologi',
            'bg' => '#22C55E',
            'text' => '#FFFFFF',
            'badge' => 'background:#22C55E; color:#FFFFFF;',
            'tag' => 'bg-emerald-50 text-emerald-800 border-emerald-300'
        ],
        'KIM' => [
            'name' => 'Kimia',
            'bg' => '#B8860B',
            'text' => '#FFFFFF',
            'badge' => 'background:#A16207; color:#FFFFFF;',
            'tag' => 'bg-amber-50 text-amber-800 border-amber-300'
        ],
        'FIS' => [
            'name' => 'Fisika',
            'bg' => '#312E81',
            'text' => '#FFFFFF',
            'badge' => 'background:#312E81; color:#FFFFFF;',
            'tag' => 'bg-indigo-50 text-indigo-800 border-indigo-300'
        ],
        'FISIKA' => [
            'name' => 'Fisika',
            'bg' => '#312E81',
            'text' => '#FFFFFF',
            'badge' => 'background:#312E81; color:#FFFFFF;',
            'tag' => 'bg-indigo-50 text-indigo-800 border-indigo-300'
        ],
        'B.INDO' => [
            'name' => 'Bahasa Indonesia',
            'bg' => '#0284C7',
            'text' => '#FFFFFF',
            'badge' => 'background:#0284C7; color:#FFFFFF;',
            'tag' => 'bg-sky-50 text-sky-800 border-sky-300'
        ],
        'B.ING' => [
            'name' => 'Bahasa Inggris',
            'bg' => '#0369A1',
            'text' => '#FFFFFF',
            'badge' => 'background:#0369A1; color:#FFFFFF;',
            'tag' => 'bg-blue-50 text-blue-800 border-blue-300'
        ],
        'MATE' => [
            'name' => 'Matematika',
            'bg' => '#CA8A04',
            'text' => '#FFFFFF',
            'badge' => 'background:#CA8A04; color:#FFFFFF;',
            'tag' => 'bg-yellow-50 text-yellow-800 border-yellow-300'
        ],
        'EKO' => [
            'name' => 'Ekonomi',
            'bg' => '#0891B2',
            'text' => '#FFFFFF',
            'badge' => 'background:#0891B2; color:#FFFFFF;',
            'tag' => 'bg-cyan-50 text-cyan-800 border-cyan-300'
        ],
        'SEJ' => [
            'name' => 'Sejarah',
            'bg' => '#EAB308',
            'text' => '#1F2937',
            'badge' => 'background:#EAB308; color:#1F2937;',
            'tag' => 'bg-yellow-100 text-yellow-900 border-yellow-400'
        ],
        'SOS' => [
            'name' => 'Sosiologi',
            'bg' => '#F43F5E',
            'text' => '#FFFFFF',
            'badge' => 'background:#F43F5E; color:#FFFFFF;',
            'tag' => 'bg-rose-50 text-rose-800 border-rose-300'
        ],
        'GEO' => [
            'name' => 'Geografi',
            'bg' => '#9333EA',
            'text' => '#FFFFFF',
            'badge' => 'background:#9333EA; color:#FFFFFF;',
            'tag' => 'bg-purple-50 text-purple-800 border-purple-300'
        ],
        'MAND' => [
            'name' => 'Bahasa Mandarin',
            'bg' => '#10B981',
            'text' => '#FFFFFF',
            'badge' => 'background:#10B981; color:#FFFFFF;',
            'tag' => 'bg-green-50 text-green-800 border-green-300'
        ],
        'KES' => [
            'name' => 'Kesenian / Seni Budaya',
            'bg' => '#71717A',
            'text' => '#FFFFFF',
            'badge' => 'background:#71717A; color:#FFFFFF;',
            'tag' => 'bg-zinc-100 text-zinc-800 border-zinc-300'
        ],
        'PJOK' => [
            'name' => 'PJOK',
            'bg' => '#EA580C',
            'text' => '#FFFFFF',
            'badge' => 'background:#EA580C; color:#FFFFFF;',
            'tag' => 'bg-orange-50 text-orange-800 border-orange-300'
        ],
        'PAK' => [
            'name' => 'Pendidikan Agama Kristen',
            'bg' => '#DB2777',
            'text' => '#FFFFFF',
            'badge' => 'background:#DB2777; color:#FFFFFF;',
            'tag' => 'bg-pink-50 text-pink-800 border-pink-300'
        ],
        'PPKN' => [
            'name' => 'PPKn',
            'bg' => '#0F172A',
            'text' => '#FFFFFF',
            'badge' => 'background:#0F172A; color:#FFFFFF;',
            'tag' => 'bg-slate-100 text-slate-900 border-slate-400'
        ],
        'TIK' => [
            'name' => 'TIK / Informatika',
            'bg' => '#DC2626',
            'text' => '#FFFFFF',
            'badge' => 'background:#DC2626; color:#FFFFFF;',
            'tag' => 'bg-red-50 text-red-800 border-red-300'
        ],
    ];

    /**
     * Data Roster Pelajaran Lengkap Mingguan (Senin - Sabtu).
     */
    public static function getRosterData(): array
    {
        return [
            'senin' => [
                'day_name' => 'Senin',
                'day_code' => 'senin',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.10',
                        'is_special' => true,
                        'special_title' => 'U P A C A R A',
                        'slots' => [],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.10 - 08.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'X IPAS B' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI A'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI B'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII A'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII B'    => ['subject' => 'SEJ', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.50 - 09.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'X IPAS B' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI A'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI B'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII A'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII B'    => ['subject' => 'SEJ', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.30 - 10.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'X IPAS B' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI A'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI B'     => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XII A'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII B'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '10.10 - 10.30',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.30 - 11.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI A'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI B'     => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XII A'    => ['subject' => 'FISIKA', 'teacher' => 'MT'],
                            'XII B'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '11.10 - 11.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI A'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI B'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XII A'    => ['subject' => 'FISIKA', 'teacher' => 'MT'],
                            'XII B'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                        ],
                    ],
                    [
                        'les' => '7',
                        'time' => '11.50 - 12.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI A'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI B'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XII A'    => ['subject' => 'FISIKA', 'teacher' => 'MT'],
                            'XII B'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '12.30 - 12.40',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '8',
                        'time' => '12.40 - 13.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI A'     => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI B'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII A'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII B'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                        ],
                    ],
                    [
                        'les' => '9',
                        'time' => '13.20 - 14.00',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI A'     => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI B'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII A'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII B'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                        ],
                    ],
                ],
            ],

            'selasa' => [
                'day_name' => 'Selasa',
                'day_code' => 'selasa',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'X IPAS B' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI B'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII A'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII B'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                        ],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.10 - 08.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'X IPAS B' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI B'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII A'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII B'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.50 - 09.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'X IPAS B' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI B'     => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XII A'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII B'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.30 - 10.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'X IPAS B' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI A'     => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XI B'     => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XII A'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII B'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '10.10 - 10.30',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.30 - 11.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'X IPAS B' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI A'     => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XI B'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII A'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII B'    => ['subject' => 'EKO', 'teacher' => 'AK'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '11.10 - 11.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'X IPAS B' => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI A'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI B'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII A'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII B'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                        ],
                    ],
                    [
                        'les' => '7',
                        'time' => '11.50 - 12.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'X IPAS B' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI A'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI B'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII A'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII B'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '12.30 - 12.40',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '8',
                        'time' => '12.40 - 13.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI A'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI B'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII A'    => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XII B'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                        ],
                    ],
                    [
                        'les' => '9',
                        'time' => '13.20 - 14.00',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI A'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XI B'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII A'    => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XII B'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                        ],
                    ],
                ],
            ],

            'rabu' => [
                'day_name' => 'Rabu',
                'day_code' => 'rabu',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'X IPAS B' => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XI A'     => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XI B'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII A'    => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XII B'    => ['subject' => 'GEO', 'teacher' => 'OS'],
                        ],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.10 - 08.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'X IPAS B' => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XI A'     => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XI B'     => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XII A'    => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XII B'    => ['subject' => 'GEO', 'teacher' => 'OS'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.50 - 09.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'X IPAS B' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI A'     => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI B'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XII A'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                            'XII B'    => ['subject' => 'GEO', 'teacher' => 'OS'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.30 - 10.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'X IPAS B' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI A'     => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI B'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XII A'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                            'XII B'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '10.10 - 10.30',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.30 - 11.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'X IPAS B' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XI A'     => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'XI B'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XII A'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                            'XII B'    => ['subject' => 'KIM', 'teacher' => 'RH'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '11.10 - 11.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'X IPAS B' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XI A'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI B'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII A'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII B'    => ['subject' => 'TIK', 'teacher' => 'GR'],
                        ],
                    ],
                    [
                        'les' => '7',
                        'time' => '11.50 - 12.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'X IPAS B' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI A'     => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI B'     => ['subject' => 'KIM', 'teacher' => 'RH'],
                            'XII A'    => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII B'    => ['subject' => 'TIK', 'teacher' => 'GR'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '12.30 - 12.40',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '8',
                        'time' => '12.40 - 13.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'X IPAS B' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI A'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XI B'     => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XII A'    => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII B'    => ['subject' => 'PAK', 'teacher' => 'MR'],
                        ],
                    ],
                    [
                        'les' => '9',
                        'time' => '13.20 - 14.00',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'FIS', 'teacher' => 'MT'],
                            'X IPAS B' => ['subject' => 'EKO', 'teacher' => 'AK'],
                            'XI A'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XI B'     => ['subject' => 'TIK', 'teacher' => 'GR'],
                            'XII A'    => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII B'    => ['subject' => 'PAK', 'teacher' => 'MR'],
                        ],
                    ],
                ],
            ],

            'kamis' => [
                'day_name' => 'Kamis',
                'day_code' => 'kamis',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.10',
                        'is_special' => true,
                        'special_title' => 'K E B A K T I A N',
                        'slots' => [],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.10 - 08.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI A'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI B'     => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XII A'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XII B'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.50 - 09.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'X IPAS B' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI A'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI B'     => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XII A'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XII B'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.30 - 10.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'X IPAS B' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'XI A'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XI B'     => ['subject' => 'SOS', 'teacher' => 'RW'],
                            'XII A'    => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XII B'    => ['subject' => 'SOS', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '10.10 - 10.30',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.30 - 11.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'X IPAS B' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'XI A'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI B'     => ['subject' => 'SOS', 'teacher' => 'RW'],
                            'XII A'    => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XII B'    => ['subject' => 'SOS', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '11.10 - 11.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'X IPAS B' => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XI A'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI B'     => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XII A'    => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII B'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                        ],
                    ],
                    [
                        'les' => '7',
                        'time' => '11.50 - 12.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'X IPAS B' => ['subject' => 'MAND', 'teacher' => 'RE'],
                            'XI A'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI B'     => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XII A'    => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XII B'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '12.30 - 12.40',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '8',
                        'time' => '12.40 - 13.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'X IPAS B' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI A'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI B'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII A'    => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XII B'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                        ],
                    ],
                    [
                        'les' => '9',
                        'time' => '13.20 - 14.00',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'X IPAS B' => ['subject' => 'BIO', 'teacher' => 'NR'],
                            'XI A'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI B'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XII A'    => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XII B'    => ['subject' => 'MAND', 'teacher' => 'RE'],
                        ],
                    ],
                ],
            ],

            'jumat' => [
                'day_name' => 'Jumat',
                'day_code' => 'jumat',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.05',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'X IPAS B' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI A'     => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XI B'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII A'    => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XII B'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                        ],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.05 - 08.40',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'X IPAS B' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI A'     => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XI B'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII A'    => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XII B'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.40 - 09.15',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'X IPAS B' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'XI A'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI B'     => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XII A'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                            'XII B'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.15 - 09.35',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'X IPAS B' => ['subject' => 'B.ING', 'teacher' => 'IM'],
                            'XI A'     => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'XI B'     => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XII A'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                            'XII B'    => ['subject' => 'SOS', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '09.35 - 10.10',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.10 - 10.45',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'X IPAS B' => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XI A'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XI B'     => ['subject' => 'SOS', 'teacher' => 'RW'],
                            'XII A'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII B'    => ['subject' => 'SOS', 'teacher' => 'AP'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '10.45 - 11.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'MATE', 'teacher' => 'NN'],
                            'X IPAS B' => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XI A'     => ['subject' => 'B.ING', 'teacher' => 'MS'],
                            'XI B'     => ['subject' => 'SOS', 'teacher' => 'RW'],
                            'XII A'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII B'    => ['subject' => 'SOS', 'teacher' => 'AP'],
                        ],
                    ],
                ],
            ],

            'sabtu' => [
                'day_name' => 'Sabtu',
                'day_code' => 'sabtu',
                'rows' => [
                    [
                        'les' => '1',
                        'time' => '07.30 - 08.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI A'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI B'     => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XII A'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII B'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                        ],
                    ],
                    [
                        'les' => '2',
                        'time' => '08.10 - 08.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI A'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI B'     => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XII A'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII B'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                        ],
                    ],
                    [
                        'les' => '3',
                        'time' => '08.50 - 09.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'SOS', 'teacher' => 'AP'],
                            'X IPAS B' => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XI A'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XI B'     => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XII A'    => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII B'    => ['subject' => 'MATE', 'teacher' => 'NH'],
                        ],
                    ],
                    [
                        'les' => '4',
                        'time' => '09.30 - 10.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'X IPAS B' => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XI A'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI B'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII A'    => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XII B'    => ['subject' => 'GEO', 'teacher' => 'OS'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '10.10 - 10.30',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '5',
                        'time' => '10.30 - 11.10',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'X IPAS B' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XI B'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII A'    => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XII B'    => ['subject' => 'GEO', 'teacher' => 'OS'],
                        ],
                    ],
                    [
                        'les' => '6',
                        'time' => '11.10 - 11.50',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'X IPAS B' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XI B'     => ['subject' => 'PJOK', 'teacher' => 'ZV'],
                            'XII A'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII B'    => ['subject' => 'PPKN', 'teacher' => 'WH'],
                        ],
                    ],
                    [
                        'les' => '7',
                        'time' => '11.50 - 12.30',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'X IPAS B' => ['subject' => 'SEJ', 'teacher' => 'AP'],
                            'XI A'     => ['subject' => 'PAK', 'teacher' => 'MR'],
                            'XI B'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XII A'    => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'XII B'    => ['subject' => 'PPKN', 'teacher' => 'WH'],
                        ],
                    ],
                    [
                        'les' => '—',
                        'time' => '12.30 - 12.40',
                        'is_break' => true,
                        'break_title' => 'I S T I R A H A T',
                        'slots' => [],
                    ],
                    [
                        'les' => '8',
                        'time' => '12.40 - 13.20',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'X IPAS B' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XI A'     => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XI B'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XII A'    => null,
                            'XII B'    => null,
                        ],
                    ],
                    [
                        'les' => '9',
                        'time' => '13.20 - 14.00',
                        'is_special' => false,
                        'slots' => [
                            'X IPAS A' => ['subject' => 'B.INDO', 'teacher' => 'RZ'],
                            'X IPAS B' => ['subject' => 'GEO', 'teacher' => 'OS'],
                            'XI A'     => ['subject' => 'PPKN', 'teacher' => 'WH'],
                            'XI B'     => ['subject' => 'KES', 'teacher' => 'DN'],
                            'XII A'    => null,
                            'XII B'    => null,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Dapatkan jadwal khusus untuk seorang guru (berdasarkan kode guru atau nama).
     */
    public static function getScheduleForTeacher(string $teacherCode): array
    {
        $all = self::getRosterData();
        $teacherSchedule = [];

        foreach ($all as $dayKey => $dayData) {
            $slotsForDay = [];
            foreach ($dayData['rows'] as $row) {
                if (!empty($row['is_break']) || !empty($row['is_special'])) {
                    continue;
                }
                foreach ($row['slots'] as $className => $slot) {
                    if ($slot && $slot['teacher'] === $teacherCode) {
                        $slotsForDay[] = [
                            'les' => $row['les'],
                            'time' => $row['time'],
                            'class' => $className,
                            'subject' => $slot['subject'],
                            'teacher' => $slot['teacher'],
                        ];
                    }
                }
            }
            if (!empty($slotsForDay)) {
                $teacherSchedule[$dayKey] = [
                    'day_name' => $dayData['day_name'],
                    'items' => $slotsForDay,
                ];
            }
        }

        return $teacherSchedule;
    }

    /**
     * Dapatkan jadwal hari ini untuk seorang guru.
     */
    public static function getTodayScheduleForTeacher(string $teacherCode): array
    {
        $dayMap = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'senin', // Minggu default ke senin
        ];
        $currentDayCode = $dayMap[(int) date('N')] ?? 'senin';
        $fullTeacherSchedule = self::getScheduleForTeacher($teacherCode);

        return [
            'day_code' => $currentDayCode,
            'day_name' => ucfirst($currentDayCode),
            'items' => $fullTeacherSchedule[$currentDayCode]['items'] ?? [],
        ];
    }

    /**
     * Dapatkan jadwal lengkap untuk satu rombel kelas tertentu (misal: X-A / X IPAS A).
     */
    public static function getScheduleForClass(string $targetClass): array
    {
        $all = self::getRosterData();
        $classSchedule = [];

        // Normalisasi nama kelas
        $normalized = strtoupper(trim($targetClass));
        if ($normalized === 'X-A' || $normalized === 'X IPA 1' || $normalized === 'XA') {
            $normalized = 'X IPAS A';
        } elseif ($normalized === 'X-B' || $normalized === 'X IPA 2' || $normalized === 'XB') {
            $normalized = 'X IPAS B';
        } elseif ($normalized === 'XI-A' || $normalized === 'XI IPA 1' || $normalized === 'XIA') {
            $normalized = 'XI A';
        } elseif ($normalized === 'XI-B' || $normalized === 'XI IPA 2' || $normalized === 'XIB') {
            $normalized = 'XI B';
        } elseif ($normalized === 'XII-A' || $normalized === 'XII IPA 1' || $normalized === 'XIIA') {
            $normalized = 'XII A';
        } elseif ($normalized === 'XII-B' || $normalized === 'XII IPA 2' || $normalized === 'XIIB') {
            $normalized = 'XII B';
        }

        foreach ($all as $dayKey => $dayData) {
            $slotsForDay = [];
            foreach ($dayData['rows'] as $row) {
                $isBreak = !empty($row['is_break']);
                $isSpecial = !empty($row['is_special']);

                if ($isBreak || $isSpecial) {
                    $slotsForDay[] = [
                        'les' => $row['les'],
                        'time' => $row['time'],
                        'activity' => $row['activity'] ?? $row['special_title'] ?? $row['break_title'] ?? ($isBreak ? 'Istirahat' : 'Kegiatan Khusus'),
                        'is_break' => $isBreak,
                        'is_special' => $isSpecial,
                        'subject' => null,
                        'teacher' => null,
                        'teacher_name' => null,
                        'subject_desc' => null,
                    ];
                    continue;
                }

                $slot = $row['slots'][$normalized] ?? null;
                $teacherInfo = $slot && isset(self::TEACHERS[$slot['teacher']]) ? self::TEACHERS[$slot['teacher']] : null;

                $slotsForDay[] = [
                    'les' => $row['les'],
                    'time' => $row['time'],
                    'activity' => null,
                    'is_break' => false,
                    'is_special' => false,
                    'subject' => $slot ? $slot['subject'] : '-',
                    'teacher' => $slot ? $slot['teacher'] : null,
                    'teacher_name' => $teacherInfo ? $teacherInfo['name'] : null,
                    'subject_desc' => $teacherInfo ? $teacherInfo['desc'] : null,
                ];
            }

            $classSchedule[$dayKey] = [
                'day_name' => $dayData['day_name'],
                'items' => $slotsForDay,
            ];
        }

        return $classSchedule;
    }

    /**
     * Dapatkan jadwal hari ini untuk satu rombel kelas tertentu.
     */
    public static function getTodayScheduleForClass(string $targetClass): array
    {
        $dayMap = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'senin',
        ];
        $currentDayCode = $dayMap[(int) date('N')] ?? 'senin';
        $fullSchedule = self::getScheduleForClass($targetClass);

        return [
            'day_code' => $currentDayCode,
            'day_name' => ucfirst($currentDayCode),
            'items' => $fullSchedule[$currentDayCode]['items'] ?? [],
        ];
    }

    /**
     * Mengelompokkan slot pelajaran berturutan yang memiliki mapel dan guru yang sama
     * menjadi satu blok sesi terpadu (misal: Les 2-4 PKWU alih-alih 3 kartu berulang).
     */
    public static function groupConsecutiveSlots(array $items): array
    {
        $grouped = [];
        $current = null;

        foreach ($items as $item) {
            $isBreak = !empty($item['is_break']);
            $isSpecial = !empty($item['is_special']);

            if ($isBreak || $isSpecial) {
                if ($current !== null) {
                    $grouped[] = $current;
                    $current = null;
                }
                $grouped[] = [
                    'les_label' => $item['les'] ?? '—',
                    'time' => $item['time'],
                    'is_break' => $isBreak,
                    'is_special' => $isSpecial,
                    'activity' => $item['activity'] ?? ($isBreak ? 'Istirahat' : 'Kegiatan Khusus'),
                    'subject' => null,
                    'subject_desc' => null,
                    'teacher' => null,
                    'teacher_name' => null,
                ];
                continue;
            }

            $subject = $item['subject'] ?? '';
            $teacher = $item['teacher'] ?? '';

            if ($current !== null && $current['subject'] === $subject && $current['teacher'] === $teacher) {
                $current['end_les'] = $item['les'];
                $timeParts = explode('-', $item['time']);
                $current['end_time'] = trim(end($timeParts));
                $current['les_label'] = "Les {$current['start_les']} - {$current['end_les']}";
                $current['time'] = "{$current['start_time']} - {$current['end_time']}";
                $current['count']++;
            } else {
                if ($current !== null) {
                    $grouped[] = $current;
                }
                $timeParts = explode('-', $item['time']);
                $current = [
                    'is_break' => false,
                    'is_special' => false,
                    'start_les' => $item['les'],
                    'end_les' => $item['les'],
                    'start_time' => trim($timeParts[0] ?? $item['time']),
                    'end_time' => trim(end($timeParts)),
                    'les_label' => "Les {$item['les']}",
                    'time' => $item['time'],
                    'count' => 1,
                    'subject' => $subject,
                    'subject_desc' => $item['subject_desc'] ?? $subject,
                    'teacher' => $teacher,
                    'teacher_name' => $item['teacher_name'] ?? $teacher,
                ];
            }
        }

        if ($current !== null) {
            $grouped[] = $current;
        }

        return $grouped;
    }
}
