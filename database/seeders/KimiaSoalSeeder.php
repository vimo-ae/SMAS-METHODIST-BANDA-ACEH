<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

/**
 * Soal TKA Kimia (Pilihan) SMA/MA/SMK 2025 — nomor 1, 3–15, 17–25.
 * Nomor 2 dan 16 belum ada di PDF ("OTW"), tambahkan nanti di method soal().
 *
 * Jalankan: php artisan db:seed --class=KimiaSoalSeeder
 * Seeder akan menanyakan exam_id ujian Kimia, lalu MENGGANTI semua soal di ujian itu.
 *
 * Kunci jawaban (hasil hitung sendiri, mohon dicek ulang dengan guru/pembahasan resmi):
 *  1 A | 3 D | 4 A | 5 D | 6 B,C | 7 A,B,D | 8 A,B | 9 A,B | 10 E | 11 E | 12 E | 13 B
 *  14 B | 15 A | 17 B | 18 B | 19 C | 20 C | 21 A | 22 B | 23 C | 24 D | 25 B
 *
 * Soal "Benar/Salah" (kategori: 6, 8, 9) bertipe benar_salah: tiap pernyataan jadi satu opsi,
 * kunci = daftar true/false per pernyataan. Soal bergambar (7, 8, 9, 17, 20, 22, 23, 25) diberi deskripsi
 * "[Gambar: ...]" di teks; kalau mau gambar asli, isi kolom image_path (simpan file di storage/app/public).
 *
 * Format mengikuti tabel exam_questions yang sudah ada: tipe 'pilihan_ganda', 'pilihan_ganda_kompleks',
 * 'benar_salah'; kolom kunci 'correct_answer' (indeks mulai 0). Model perlu cast array untuk options & correct_answer.
 */
class KimiaSoalSeeder extends Seeder
{
    private const PG = 'pilihan_ganda';
    private const KOMPLEKS = 'pilihan_ganda_kompleks';
    private const BS = 'benar_salah';

    public function run(): void
    {
        $default = Exam::where('title', 'like', '%Kimia%')->value('exam_id');
        $id = $this->command->ask('Masukkan exam_id ujian Kimia (soal lama di ujian ini akan diganti)', $default);
        $exam = Exam::find($id);
        if (! $exam) {
            $this->command->error("Ujian dengan id {$id} tidak ditemukan.");
            return;
        }

        $exam->questions()->delete();
        foreach (self::soal() as $row) {
            $exam->questions()->create($row);
        }
        $this->command->info(count(self::soal()) . ' soal Kimia dimasukkan ke ujian #' . $exam->exam_id . '.');
    }

    /**
     * Soal ditulis dengan opsi berhuruf (A, B, C...) dan kunci berhuruf supaya mudah dibaca,
     * lalu diubah di sini ke format database:
     *  - options        : daftar biasa ["teks A", "teks B", ...]
     *  - pilihan_ganda  : correct_answer = indeks (mulai 0), mis. 3
     *  - pg kompleks    : correct_answer = daftar indeks, mis. [0, 2]
     *  - benar_salah    : correct_answer = daftar true/false per pernyataan, mis. [false, true, true]
     */
    private static function q(string $type, string $text, array $options, $key, int $weight): array
    {
        $idx = fn ($huruf) => ord($huruf) - 65;

        if ($type === self::PG) {
            $correct = $idx($key);
        } elseif ($type === self::KOMPLEKS) {
            $correct = array_values(array_map($idx, $key));
            sort($correct);
        } else { // benar_salah: $key = huruf pernyataan yang BENAR
            $correct = array_map(fn ($huruf) => in_array($huruf, $key, true), array_keys($options));
        }

        return [
            'question_type'  => $type,
            'question_text'  => $text,
            'options'        => array_values($options),
            'correct_answer' => $correct,
            'score_weight'   => $weight,
            'image_path'     => null,
        ];
    }

    private static function soal(): array
    {
        $bs = "Tentukan Benar atau Salah untuk setiap pernyataan berikut.";
        $mcma = "Pilih semua jawaban benar. Jawaban benar lebih dari satu.";

        return [
            // ---------- 1 ----------
            self::q(self::PG,
                "Fosfor merupakan salah satu unsur penting yang banyak digunakan dalam produk kimia, baik di bidang pertanian, industri, maupun laboratorium (bahan pupuk, bahan tambahan makanan, bahan baku zat kimia lainnya).\nBeberapa senyawa yang mengandung fosfor beserta massa molekul relatifnya (Mr):\n\nNama senyawa | Rumus | Mr\nNatrium dihidrogen fosfat | NaH₂PO₄ | 120\nNatrium hidrogen fosfat | Na₂HPO₄ | 142\nNatrium fosfat | Na₃PO₄ | 164\nFosfor triklorida | PCl₃ | 137,5\nFosfor pentaklorida | PCl₅ | 208,5\n\nSenyawa yang memiliki kandungan fosfor tertinggi adalah .... (diketahui Ar P = 31)",
                ['A' => 'Natrium dihidrogen fosfat', 'B' => 'Natrium hidrogen fosfat', 'C' => 'Natrium fosfat', 'D' => 'Fosfor triklorida', 'E' => 'Fosfor pentaklorida'],
                'A', 2),

            // ---------- 3 ----------
            self::q(self::PG,
                "Penggunaan gas etana dan propana sebagai bahan bakar alternatif semakin meningkat di kawasan industri. Meski pembakarannya sempurna, gas rumah kaca berupa CO₂ tetap dihasilkan.\nDalam suatu percobaan, campuran 10 liter etana (C₂H₆) dan propana (C₃H₈) dibakar sempurna menghasilkan gas karbon dioksida dan 36 L air. Reaksi (belum setara):\nC₂H₆(g) + O₂(g) → CO₂(g) + H₂O(g)\nC₃H₈(g) + O₂(g) → CO₂(g) + H₂O(g)\n\nBerapakah volume total gas CO₂ yang dihasilkan dari pembakaran campuran tersebut jika semua gas diukur pada suhu dan tekanan yang sama?",
                ['A' => '8 L', 'B' => '18 L', 'C' => '20 L', 'D' => '26 L', 'E' => '44 L'],
                'D', 2),

            // ---------- 4 ----------
            self::q(self::PG,
                "Sulfur dioksida (SO₂) merupakan polutan udara berbahaya yang dihasilkan dari pembakaran bahan bakar fosil dan aktivitas industri. Sebuah stasiun pemantau kualitas udara mengambil sampel udara di area industri sebanyak 6 liter pada suhu 25 °C dan tekanan 1 atm. Hasil pengukuran menunjukkan konsentrasi sulfur dioksida di area industri sebanyak 0,0002% volume. Berapakah jumlah molekul SO₂ yang terdeteksi dalam sampel?\n(Diketahui: bilangan Avogadro 6,02 × 10²³ molekul/mol)",
                ['A' => '2,95 × 10¹⁷ molekul', 'B' => '12,02 × 10¹⁷ molekul', 'C' => '3,23 × 10¹⁸ molekul', 'D' => '1,51 × 10²³ molekul', 'E' => '2,41 × 10²⁴ molekul'],
                'A', 2),

            // ---------- 5 ----------
            self::q(self::PG,
                "Tembaga (Cu) banyak dimanfaatkan dalam industri kelistrikan seperti kabel, kumparan motor, dan komponen elektronik karena konduktivitas listriknya sangat tinggi. Salah satu cara mendapatkan tembaga murni adalah mereduksi CuO menggunakan karbon:\n2CuO(s) + C(s) → 2Cu(s) + CO₂(g)\n\nDi laboratorium sekolah seorang murid mereaksikan 39,75 gram CuO dan 3,0 gram karbon. Berapa gram tembaga murni (Cu) maksimum yang dapat diperoleh?\n(Diketahui Ar: Cu = 63,5; O = 16; C = 12)",
                ['A' => '16 gram', 'B' => '23,8 gram', 'C' => '25,4 gram', 'D' => '31,75 gram', 'E' => '47,62 gram'],
                'D', 2),

            // ---------- 6 (Benar/Salah) ----------
            self::q(self::BS,
                "Kristal kalium klorida (KCl) merupakan garam penting yang banyak kegunaannya, misalnya sebagai suplemen kalium atau pupuk tanaman. Seorang peneliti membuat kristal KCl dari kalium hidroksida dan asam klorida:\nKOH(aq) + HCl(aq) → KCl(aq) + H₂O(l)\n\nLarutan yang tersedia: Botol A: HCl 1 M; Botol B: HCl 2 M; Botol C: KOH 1 M; Botol D: KOH 2 M.\n(Diketahui Ar K = 39, Cl = 35,5)\n\nSecara teori, manakah komposisi berikut yang jika direaksikan akan menghasilkan tepat 29,8 gram kristal KCl? $bs",
                ['A' => '300 mL larutan dari botol B + 200 mL larutan dari botol C.',
                 'B' => '200 mL larutan dari botol B + 300 mL larutan dari botol D.',
                 'C' => '400 mL larutan dari botol A + 500 mL larutan dari botol C.'],
                ['B', 'C'], 3),

            // ---------- 7 (MCMA, bergambar) ----------
            self::q(self::KOMPLEKS,
                "Minyak bumi merupakan campuran berbagai zat organik dengan komponen pokok senyawa hidrokarbon. Pemisahan minyak mentah dilakukan dalam kilang minyak melalui destilasi bertingkat berdasarkan perbedaan titik didih. Fraksi minyak bumi antara lain senyawa P, Q, R, dan S yang memiliki kegunaan berbeda.\n\n[Gambar: grafik hubungan bobot molekul (sumbu x) dengan titik didih °C (sumbu y), titik-titik kira-kira P (44; −42), Q (128; 150), R (268; 330), S (296; 355), makin berat molekul makin tinggi titik didih.]\n\nBerdasarkan grafik, tentukan pasangan senyawa hidrokarbon dengan potensi pemanfaatannya yang benar! $mcma",
                ['A' => 'Senyawa Q dimanfaatkan sebagai bahan bakar kendaraan bermotor.',
                 'B' => 'Senyawa R dapat digunakan sebagai minyak pelumas.',
                 'C' => 'Senyawa P bermanfaat sebagai bahan bakar pesawat terbang.',
                 'D' => 'Senyawa S dapat digunakan sebagai bahan pembuatan lilin.',
                 'E' => 'Senyawa P dapat dimanfaatkan sebagai pelarut.'],
                ['A', 'B', 'D'], 3),

            // ---------- 8 (Benar/Salah) ----------
            self::q(self::BS,
                "Suatu hidrokarbon pada suhu kamar berbentuk gas dan tidak berwarna, digunakan sebagai komponen utama LPG dan kompor portabel. Pembakaran sempurna 2 mol hidrokarbon tersebut menghasilkan 8 mol CO₂ dan 10 mol H₂O. Senyawa tersebut memiliki 2 atom C primer dan 2 atom C sekunder.\n\nBagaimana hasil analisis terhadap senyawa tersebut? $bs",
                ['A' => 'Rumus kimia hidrokarbon tersebut adalah C₄H₁₀.',
                 'B' => 'Struktur hidrokarbon tersebut adalah rantai lurus H₃C–CH₂–CH₂–CH₃.',
                 'C' => 'Hidrokarbon tersebut diperkirakan memiliki titik didih lebih rendah dibandingkan pasangan isomernya.'],
                ['A', 'B'], 3),

            // ---------- 9 (Benar/Salah) ----------
            self::q(self::BS,
                "Suatu perusahaan energi meneliti dua senyawa hidrokarbon, yaitu 1-butena (CH₂=CH–CH₂–CH₃) dan 2-metil-1-propena (CH₂=C(CH₃)₂), untuk bahan bakar tambahan dalam campuran bensin ramah lingkungan. Tim peneliti ingin mengetahui senyawa mana yang memberikan energi pembakaran lebih besar per mol.\nPerubahan entalpi pembakaran (ΔHc) saat keduanya dibakar sempurna:\n\nSenyawa C₄H₈ | ΔHc (kJ/mol)\n2-metil-1-propena | −2.715\n1-butena | −2.721\n\nBagaimana hasil analisis terhadap kedua isomer tersebut? $bs",
                ['A' => '1-butena memberikan efisiensi pembakaran yang lebih baik dibandingkan 2-metil-1-propena.',
                 'B' => 'Secara termodinamika 2-metil-1-propena lebih stabil dan memiliki tingkat energi lebih rendah dibandingkan 1-butena.',
                 'C' => 'Hibridisasi senyawa karbon hasil pembakaran adalah sp³.'],
                ['A', 'B'], 3),

            // ---------- 10 ----------
            self::q(self::PG,
                "Dalam sebuah artikel ilmiah dinyatakan bahwa penggunaan pupuk amonium berlebih dapat meningkatkan keasaman tanah. Ion amonium (NH₄⁺) yang terhidrolisis menghasilkan ion hidronium (H₃O⁺) yang dapat menurunkan pH tanah. Salah satu reaksi yang terjadi dalam tanah:\nNH₄⁺ + H₂O ⇌ NH₃ + H₃O⁺\n\nBerdasarkan reaksi dan teori Brønsted-Lowry, manakah yang merupakan pasangan asam-basa konjugasi yang tepat?",
                ['A' => 'NH₄⁺ dan H₂O', 'B' => 'NH₃ dan H₃O⁺', 'C' => 'H₃O⁺ dan NH₄⁺', 'D' => 'NH₃ dan H₂O', 'E' => 'NH₄⁺ dan NH₃'],
                'E', 2),

            // ---------- 11 ----------
            self::q(self::PG,
                "Seorang peneliti lingkungan mengambil 5 sampel air limbah cair dari industri peternakan yang diduga mengandung spesi asam/basa. Pengujian memakai indikator fenolftalein (trayek pH 8,0–10,0; perubahan warna dari tidak berwarna menjadi merah muda).\n\nSampel limbah | Kandungan\nA | HI\nB | H₂SO₃\nC | Sr(OH)₂\nD | Ca(OH)₂\nE | HClO₃\n\nBerdasarkan data tersebut, dugaan peneliti kemungkinan benar jika sampel ditetesi indikator fenolftalein menunjukkan warna ....",
                ['A' => 'A tak berwarna; B merah muda; C merah muda; D merah muda; E tak berwarna',
                 'B' => 'A merah muda; B tak berwarna; C tak berwarna; D merah muda; E tak berwarna',
                 'C' => 'A tak berwarna; B merah muda; C merah muda; D tak berwarna; E merah muda',
                 'D' => 'A merah muda; B tak berwarna; C tak berwarna; D tak berwarna; E merah muda',
                 'E' => 'A tak berwarna; B tak berwarna; C merah muda; D merah muda; E tak berwarna'],
                'E', 2),

            // ---------- 12 ----------
            self::q(self::PG,
                "Deterjen cair memiliki beberapa keunggulan dibandingkan deterjen bubuk. Annisa menemukan pada label deterjen cair bahwa senyawa basa NaOH berkadar 0,1% (kadar alkali bebas maksimal menurut BSN). Ia mengambil 10 mL sampel deterjen lalu diencerkan dengan akuades sampai 100 mL. Kemudian 20 mL larutan encer dititrasi dengan HCl 0,1 M. Titrasi dilakukan tiga kali:\n\nTitrasi ke- | Volume NaOH (mL) | Volume HCl 0,1 M (mL)\n1 | 20 | 8,2\n2 | 20 | 8,0\n3 | 20 | 7,8\n\nBerdasarkan hasil titrasi, manakah pernyataan yang tepat mengenai kadar NaOH dalam deterjen cair tersebut? (Ar Na = 23, O = 16, H = 1; anggap massa jenis deterjen 1 g/mL)",
                ['A' => 'Kadar NaOH di bawah yang tercantum pada label, yaitu mengandung 0,04% NaOH.',
                 'B' => 'Kadar NaOH sesuai dengan yang tercantum pada label, yaitu mengandung 0,1% NaOH.',
                 'C' => 'Kadar NaOH melebihi yang tercantum pada label, yaitu mengandung 0,4% NaOH.',
                 'D' => 'Kadar NaOH melebihi yang tercantum pada label, yaitu mengandung 0,16% NaOH.',
                 'E' => 'Kadar NaOH melebihi yang tercantum pada label, yaitu mengandung 1,6% NaOH.'],
                'E', 2),

            // ---------- 13 ----------
            self::q(self::PG,
                "Sekelompok siswa menguji daya hantar listrik tiga senyawa yang belum diketahui (X, Y, Z) menggunakan indikator lampu. Hasil pengamatan:\n\nZat | Lelehan | Larutan\nX | Redup; ada gelembung | Terang; banyak gelembung\nY | Tidak menyala; tidak ada gelembung | Redup; sedikit gelembung\nZ | Tidak menyala; tidak ada gelembung | Terang; banyak gelembung\n\nBerdasarkan data hasil percobaan, manakah kemungkinan pasangan zat/senyawa yang sesuai dengan hasil percobaan?",
                ['A' => 'Senyawa X adalah Na₂S₂O₃ dan senyawa Y adalah KI.',
                 'B' => 'Senyawa X adalah Na₂S₂O₃ dan senyawa Y adalah CH₃COOH.',
                 'C' => 'Senyawa X adalah KI dan senyawa Z adalah CH₃COOH.',
                 'D' => 'Senyawa Y adalah CH₃COOH dan senyawa Z adalah Na₂S₂O₃.',
                 'E' => 'Senyawa Y adalah HCl dan senyawa Z adalah Na₂S₂O₃.'],
                'B', 2),

            // ---------- 14 ----------
            self::q(self::PG,
                "Sebuah pabrik farmasi memproduksi sirup obat batuk untuk anak-anak. Kandungan per botol (isi 50 mL):\n\nBahan aktif / tambahan | Jumlah (g)\nDekstrometorfan HBr | 0,6\nGula (sukrosa) | 10,0\nPerisa buah sintetis | 0,2\nAir dan bahan lain | sampai 50 mL\n\nStandar mutu aman (persen massa per 100 mL): dekstrometorfan HBr maksimal 1%; gula maksimal 15%; perisa buah sintetis maksimal 0,3%.\n\nPernyataan manakah yang paling tepat mengenai komposisi sirup tersebut? (Massa jenis sirup 1 g/mL)",
                ['A' => 'Mengandung dekstrometorfan HBr sebesar 2%, sudah tidak aman untuk digunakan.',
                 'B' => 'Mengandung gula sebesar 20%, sudah melebihi batas aman untuk anak-anak.',
                 'C' => 'Mengandung perisa buah sintetis sebesar 0,2% masih pada batas aman.',
                 'D' => 'Semua kandungan zat aktif dan bahan tambahan berada pada batas aman sesuai standar mutu.',
                 'E' => 'Kandungan dekstrometorfan HBr dan perisa buah masih berada pada batas aman untuk anak-anak.'],
                'B', 2),

            // ---------- 15 ----------
            self::q(self::PG,
                "Dalam mempelajari pengolahan limbah rumah tangga, Anisa dan Kristin melakukan percobaan dengan mencampurkan 100 mL CaBr₂ 0,01 M dengan 100 mL Na₂SO₄ 0,02 M. Ion Ca²⁺ dan SO₄²⁻ dapat membentuk endapan CaSO₄ yang sukar larut, sehingga dimanfaatkan untuk mengisolasi ion sulfat pada air limbah.\n\nBerdasarkan informasi tersebut, manakah pernyataan yang tepat mengenai kemungkinan terbentuknya endapan CaSO₄ pada percobaan? (Diketahui Ksp CaSO₄ = 2,4 × 10⁻⁶)",
                ['A' => 'Terbentuk endapan CaSO₄ karena Ksp lebih kecil dari harga Q.',
                 'B' => 'Garam CaSO₄ akan terus larut karena Ksp lebih besar dari harga Q.',
                 'C' => 'Larutan CaSO₄ tepat jenuh karena Ksp sama dengan harga Q.',
                 'D' => 'Terbentuk endapan CaSO₄ karena Ksp lebih besar dari harga Q.',
                 'E' => 'Garam CaSO₄ akan larut karena harga Q lebih besar dari Ksp.'],
                'A', 2),

            // ---------- 17 ----------
            self::q(self::PG,
                "Natrium klorida (NaCl), sukrosa (C₁₂H₂₂O₁₁), dan metanol (CH₃OH) memiliki peran yang berbeda dalam industri. NaCl, sukrosa, dan metanol dalam jumlah (mol) sama dilarutkan dalam air dengan volume sama.\n\n[Gambar: tiga labu dengan manometer tekanan uap, A: NaCl(aq), B: C₁₂H₂₂O₁₁(aq), C: CH₃OH(aq).]\n\nBerdasarkan wacana, pernyataan berikut yang benar terkait tekanan uap dari ketiga larutan adalah ....",
                ['A' => 'Tekanan uap larutan A < larutan C < larutan B karena jumlah partikel NaCl > CH₃OH > C₁₂H₂₂O₁₁.',
                 'B' => 'Larutan C memiliki tekanan uap tinggi karena metanol memperbesar kecenderungan molekul meninggalkan fase cair.',
                 'C' => 'Larutan A memiliki tekanan uap lebih rendah karena penambahan NaCl menyebabkan lebih banyak air yang diubah menjadi uap air.',
                 'D' => 'Tekanan uap larutan C lebih tinggi dari larutan B karena jumlah partikel metanol lebih banyak meskipun jumlah molnya sama.',
                 'E' => 'Penambahan sukrosa pada larutan B menyebabkan tekanan uap turun karena sukrosa merupakan partikel ionik.'],
                'B', 2),

            // ---------- 18 ----------
            self::q(self::PG,
                "Tekanan osmotik memiliki beberapa aplikasi penting dalam bidang kesehatan, antara lain pembatas cairan infus dan proses dialisis. Ani, seorang peneliti di laboratorium farmasi, membuat cairan infus yang diukur pada suhu 27 °C. Ia mengambil 14,9 g KCl (Mr = 74,5) dan melarutkannya ke dalam 1000 mL air. Berapakah tekanan osmotik larutan KCl tersebut?\n(Diketahui R = 0,082 L atm mol⁻¹ K⁻¹)",
                ['A' => '10,16 atm', 'B' => '9,84 atm', 'C' => '4,92 atm', 'D' => '0,88 atm', 'E' => '0,44 atm'],
                'B', 2),

            // ---------- 19 ----------
            self::q(self::PG,
                "Larutan antibeku yang mengandung propilen glikol (C₃H₈O₂) digunakan dalam pencairan es untuk menghilangkan salju atau embun beku pada permukaan pesawat sebelum lepas landas (anti-icing). Sebelum lepas landas pesawat disemprotkan larutan antibeku yang mengandung 1140 g propilen glikol dalam 1 liter air.\n\nBerdasarkan wacana, apakah larutan antibeku tersebut dapat mencegah pembentukan es ketika pesawat terbang di daerah dengan suhu −22,3 °C?\n(Diketahui Ar C = 12, H = 1, O = 16, Kf air = 1,86 °C/molal)",
                ['A' => 'Ya, karena titik beku larutan tersebut mencapai −18,6 °C.',
                 'B' => 'Ya, karena titik beku larutan tersebut mencapai −22,3 °C.',
                 'C' => 'Ya, karena titik beku larutan tersebut mencapai −27,9 °C.',
                 'D' => 'Tidak, karena titik beku larutan tersebut hanya 18,6 °C.',
                 'E' => 'Tidak, karena titik beku larutan tersebut hanya 27,9 °C.'],
                'C', 2),

            // ---------- 20 ----------
            self::q(self::PG,
                "Perhatikan grafik yang menggambarkan konsep laju reaksi dari suatu reaksi berikut.\n\n[Gambar: grafik konsentrasi (M) terhadap waktu (detik). Kurva yang menurun: 4NH₃(g) dan 5O₂(g). Kurva yang meningkat: 4NO(g) dan 6H₂O(g).]\n\nBerdasarkan grafik tersebut, reaktan dari reaksi tersebut adalah ....",
                ['A' => 'NO dan H₂O', 'B' => 'NH₃ dan NO', 'C' => 'NH₃ dan O₂', 'D' => 'NH₃ dan H₂O', 'E' => 'H₂O dan O₂'],
                'C', 2),

            // ---------- 21 ----------
            self::q(self::PG,
                "Rania mencari informasi mengenai hidrokarbon yang memiliki 5 atom karbon. Hidrokarbon dengan atom karbon rendah banyak digunakan sebagai sumber bahan bakar. Data entalpi pembakaran standar (ΔHc°):\n\nNo | Hidrokarbon dengan 5 atom C | ΔHc° (kJ/mol)\n1 | n-pentana (C₅H₁₂) | −3509\n2 | 1-pentena (C₅H₁₀) | −3349\n3 | siklopentana (C₅H₁₀) | −3291\n\nBerdasarkan data yang diperoleh, senyawa yang dapat dijadikan bahan bakar yang paling ramah lingkungan adalah ....",
                ['A' => 'n-pentana karena menghasilkan CO₂ paling sedikit per energi yang sama.',
                 'B' => '1-pentena karena memiliki ikatan rangkap sehingga pembakaran lebih mudah.',
                 'C' => 'Siklopentana karena menghasilkan energi paling besar untuk setiap mol pembakaran.',
                 'D' => 'Semua sama karena masing-masing menghasilkan 5 mol CO₂ per mol.',
                 'E' => '1-pentena karena menghasilkan H₂O lebih banyak dibandingkan n-pentana dan siklopentana.'],
                'A', 2),

            // ---------- 22 ----------
            self::q(self::PG,
                "Dalam industri bahan bakar ramah lingkungan, etena (C₂H₄) diubah menjadi etanol (C₂H₅OH) melalui reaksi kesetimbangan:\nC₂H₄(g) + H₂O(g) ⇌ C₂H₅OH(g)\n\nReaksi dilakukan pada suhu dan tekanan tinggi dalam suatu reaktor. Operator ingin menentukan suhu optimal untuk memaksimalkan produksi etanol. Hasil pengamatan konsentrasi C₂H₄ pada dua suhu:\n\n[Gambar: grafik konsentrasi C₂H₄ (mol/L) terhadap waktu. Pada T = 500 °C konsentrasi setimbang lebih tinggi; pada T = 300 °C konsentrasi setimbang lebih rendah.]\n\nOperator menggunakan reaktor yang sama pada suhu 400 °C dan memperoleh Kc = 0,9. Agar gas etanol yang dihasilkan meningkat, upaya terbaik selain menambah lebih banyak C₂H₄ ke dalam reaktor adalah ....",
                ['A' => 'Menurunkan suhu hingga mendekati suhu kamar ±25 °C karena reaksinya bersifat eksoterm, sehingga nilai Kc akan berkurang (Kc < 0,9).',
                 'B' => 'Menurunkan suhu dengan kisaran 400 °C > T > 300 °C karena reaksinya bersifat eksoterm, sehingga nilai Kc meningkat (Kc > 0,9).',
                 'C' => 'Menurunkan suhu dengan kisaran 400 °C > T > 25 °C karena reaksinya bersifat eksoterm, sehingga nilai Kc meningkat (Kc > 0,9).',
                 'D' => 'Meningkatkan suhu dengan kisaran 500 °C > T > 400 °C karena reaksinya bersifat endoterm, sehingga nilai Kc meningkat (Kc > 0,9).',
                 'E' => 'Meningkatkan suhu dengan kisaran 500 °C > T > 400 °C karena reaksinya bersifat eksoterm, sehingga nilai Kc berkurang (Kc < 0,9).'],
                'B', 2),

            // ---------- 23 ----------
            self::q(self::PG,
                "Seorang peneliti melakukan studi reaksi kesetimbangan gas buang kendaraan di atmosfer.\n\n[Gambar: grafik konsentrasi reaktan/produk (mol) terhadap waktu (menit). Reaktan [A] turun dari 6 ke 2, reaktan [B] turun dari 5 ke 3, produk [A₂B] naik dari 0 ke 2; keadaan setimbang tercapai mulai menit ke-10.]\n\nBerdasarkan grafik reaksi kesetimbangan tersebut, apakah yang terjadi jika tekanan wadah dinaikkan dua kali lipat?",
                ['A' => 'Kesetimbangan reaksi bergeser ke kiri karena jumlah mol produk lebih kecil.',
                 'B' => 'Kesetimbangan reaksi bergeser ke kiri karena jumlah mol reaktan lebih besar.',
                 'C' => 'Kesetimbangan reaksi bergeser ke kanan karena jumlah mol produk lebih kecil.',
                 'D' => 'Kesetimbangan tidak akan bergeser, karena semua zat berwujud gas.',
                 'E' => 'Kesetimbangan akan bergeser ke kanan, karena konsentrasi semua zat meningkat.'],
                'C', 2),

            // ---------- 24 ----------
            self::q(self::PG,
                "Sel Volta atau sel galvanik adalah dasar dari konsep baterai. Salah satu jenis baterai adalah baterai alkalin yang menghasilkan energi lebih besar sehingga lebih kuat dan tahan lama, umum digunakan pada remot kontrol, kamera digital, dan jam tangan. Reaksi pada baterai alkalin:\nZn(s) + 2MnO₂(s) + H₂O(l) → ZnO(s) + Mn₂O₃(s) + 2H₂O(l)\n\nBerdasarkan informasi, zat apakah yang bertindak sebagai oksidator dalam reaksi pada baterai alkalin?",
                ['A' => 'Mn₂O₃', 'B' => 'ZnO', 'C' => 'H₂O', 'D' => 'MnO₂', 'E' => 'Zn'],
                'D', 2),

            // ---------- 25 ----------
            self::q(self::PG,
                "Sebuah sel volta disusun menggunakan dua logam, X dan Y, yang dicelupkan ke dalam larutan berisi ion dari masing-masing logam.\n\n[Gambar: logam X dalam larutan X²⁺(aq) 1,0 M dan logam Y dalam larutan Y²⁺(aq) 1,0 M dihubungkan jembatan garam dan voltmeter. Pada kabel, elektron mengalir dari elektroda Y menuju voltmeter, lalu ke elektroda X.]\n\nDari informasi dan skema yang diberikan, manakah pernyataan berikut yang tepat mengenai reaksi reduksi dan oksidasi pada sel volta tersebut?",
                ['A' => 'Elektroda X mengalami oksidasi, X(s) → X²⁺(aq) + 2e⁻.',
                 'B' => 'Massa logam Y berkurang seiring waktu disebabkan Y mengalami oksidasi.',
                 'C' => 'Konsentrasi ion Y²⁺ berkurang seiring waktu sebab ion ini mengalami reduksi.',
                 'D' => 'Elektroda Y berperan sebagai katoda, tempat terjadinya reaksi reduksi.',
                 'E' => 'Konsentrasi ion X²⁺ bertambah seiring waktu karena oksidasi logam X.'],
                'B', 2),
        ];
    }
}
