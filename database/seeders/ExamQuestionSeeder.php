<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

/**
 * Data dummy soal ujian, mengikuti "Format Soal Semester berbasis komputer/HP":
 *
 *  - format 'eksakta' (Matematika, Fisika, Kimia, Biologi, Ekonomi): 30 soal / 120 menit
 *      PG 10 + PG Kompleks 10 + Menjodohkan 5 + Esai 5
 *  - format 'umum' (mapel selain di atas): 35 soal / 90 menit
 *      PG 10 + PG Kompleks 15 + Menjodohkan 5 + Esai 5
 *
 * Isi soal contoh bergaya soal sekolah (Bahasa Indonesia, Bahasa Inggris, Sosiologi).
 *
 * ASUMSI kolom tabel exam_questions (sesuaikan dengan migration kamu):
 *   question_type  : 'pg' | 'pg_kompleks' | 'menjodohkan' | 'esai'
 *   question_text, options (cast array), answer_key (cast array), score_weight
 * Jalankan: php artisan db:seed --class=ExamQuestionSeeder
 */
class ExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $exams = Exam::orderBy('exam_id')->take(2)->get();
        if ($exams->isEmpty()) {
            $this->command?->warn('Belum ada data ujian (exams). Buat ujian dulu, lalu jalankan seeder ini.');
            return;
        }

        self::seedExam($exams[0], 'umum');                 // 35 soal / 90 menit
        if (isset($exams[1])) {
            self::seedExam($exams[1], 'eksakta');          // 30 soal / 120 menit
        }
    }

    public static function seedExam(Exam $exam, string $format = 'umum'): void
    {
        $jumlahKompleks = $format === 'eksakta' ? 10 : 15;

        $rows = array_merge(
            self::pg(),
            array_slice(self::kompleks(), 0, $jumlahKompleks),
            self::menjodohkan(),
            self::esai(),
        );

        $exam->questions()->delete(); // supaya seeder bisa dijalankan ulang
        foreach ($rows as $row) {
            $exam->questions()->create($row);
        }
    }

    private static function q(string $type, string $text, ?array $options, $key, int $weight): array
    {
        return [
            'question_type' => $type,
            'question_text' => $text,
            'options'       => $options,
            'answer_key'    => $key,
            'score_weight'  => $weight,
        ];
    }

    /** 10 soal pilihan ganda (satu jawaban benar). */
    private static function pg(): array
    {
        $w = 2;
        return [
            self::q('pg', 'Indonesia disebut negara kepulauan terbesar di dunia karena memiliki lebih dari ... pulau.',
                ['A' => '10.000', 'B' => '13.000', 'C' => '17.000', 'D' => '20.000', 'E' => '25.000'], 'C', $w),
            self::q('pg', "Vitamin D membantu penyerapan kalsium dan dapat diperoleh dari sinar matahari serta makanan seperti ikan berlemak dan telur.\n\nIde pokok paragraf tersebut adalah ....",
                ['A' => 'Vitamin D hanya terdapat pada ikan', 'B' => 'Vitamin D penting bagi tubuh dan dapat diperoleh dari sinar matahari dan makanan', 'C' => 'Kalsium tidak memerlukan vitamin D', 'D' => 'Sinar matahari berbahaya bagi tulang', 'E' => 'Telur adalah sumber utama kalsium'], 'B', $w),
            self::q('pg', 'Peran utama akar pohon mangrove bagi wilayah pesisir adalah ....',
                ['A' => 'mencegah abrasi pantai', 'B' => 'menambah kadar garam laut', 'C' => 'menghalangi nelayan melaut', 'D' => 'menyuburkan tanah pertanian', 'E' => 'mengurangi curah hujan'], 'A', $w),
            self::q('pg', "He pretended to be the gorilla successfully.\n\nThe word 'pretended' is closest in meaning to ....",
                ['A' => 'to do whatever people ask him to do', 'B' => 'to put on the gorilla suit', 'C' => 'to play and make fun of people', 'D' => 'to behave as if something is true', 'E' => 'to entertain crowds as a clown'], 'D', $w),
            self::q('pg', 'The clown decided to make a spectacular performance by ....',
                ['A' => 'swinging on tires', 'B' => "living in the lion's cage", 'C' => 'climbing to the top of his cage', 'D' => 'making the lion furious', 'E' => 'loving the crowds'], 'C', $w),
            self::q('pg', 'Kalimat berikut yang menggunakan kata kerja pasif adalah ....',
                ['A' => 'Hatta memimpin organisasi mahasiswa.', 'B' => 'Hatta dipindahkan ke tempat pembuangan di Banda Neira.', 'C' => 'Hatta menyampaikan pidato.', 'D' => 'Hatta sangat aktif berorganisasi.', 'E' => 'Hatta menyelesaikan kuliahnya.'], 'B', $w),
            self::q('pg', 'Proses meluasnya pengaruh budaya, ekonomi, dan teknologi antarnegara sehingga batas wilayah seakan hilang disebut ....',
                ['A' => 'globalisasi', 'B' => 'segregasi', 'C' => 'diferensiasi', 'D' => 'stratifikasi', 'E' => 'asimilasi'], 'A', $w),
            self::q('pg', 'Contoh dampak positif globalisasi di bidang ekonomi adalah ....',
                ['A' => 'lunturnya budaya gotong royong', 'B' => 'meningkatnya gaya hidup konsumtif', 'C' => 'ketergantungan pada produk impor', 'D' => 'pelaku UMKM dapat memasarkan produk lewat e-commerce', 'E' => 'masuknya budaya asing tanpa seleksi'], 'D', $w),
            self::q('pg', 'Makna kata "virtual" dalam kalimat "Orang banyak berinteraksi secara virtual" adalah ....',
                ['A' => 'hadir dengan perangkat lunak', 'B' => 'hadir di layar televisi', 'C' => 'hadir dengan kondisi tidak sungguh-sungguh', 'D' => 'interaksi yang tidak sungguh-sungguh', 'E' => 'interaksi antara beberapa orang melalui layar'], 'E', $w),
            self::q('pg', 'Choose the correct sentence.',
                ['A' => 'She go to school every day.', 'B' => 'She goes to school every day.', 'C' => 'She going to school every day.', 'D' => 'She gone to school every day.', 'E' => 'She are going to school every day.'], 'B', $w),
        ];
    }

    /** 15 soal PG kompleks (jawaban benar lebih dari satu). */
    private static function kompleks(): array
    {
        $w = 3;
        $t = 'Pilihlah jawaban yang benar. Jawaban benar lebih dari satu.';
        return [
            self::q('pg_kompleks', "Minuman berenergi yang dikonsumsi berlebihan dapat meningkatkan risiko kerusakan hati dan gangguan jantung.\n\n$t Pernyataan yang sesuai dengan teks adalah ....",
                ['A' => 'Konsumsi berlebihan berisiko bagi kesehatan', 'B' => 'Minuman berenergi aman diminum tanpa batas', 'C' => 'Risiko dapat mengenai hati dan jantung', 'D' => 'Minuman berenergi menyembuhkan penyakit jantung', 'E' => 'Iklan kerap hanya menonjolkan manfaat sesaat'], ['A', 'C', 'E'], $w),
            self::q('pg_kompleks', "$t Risiko kesehatan akibat terlalu sering makan makanan cepat saji adalah ....",
                ['A' => 'hipertensi', 'B' => 'obesitas', 'C' => 'diabetes tipe 2', 'D' => 'rabun jauh seketika', 'E' => 'penyakit jantung koroner'], ['A', 'B', 'C', 'E'], $w),
            self::q('pg_kompleks', "$t Manfaat pemanasan sebelum berolahraga adalah ....",
                ['A' => 'aliran darah ke otot meningkat', 'B' => 'tubuh menjadi lebih fleksibel', 'C' => 'otot menjadi kaku', 'D' => 'risiko cedera berkurang', 'E' => 'detak jantung langsung berhenti'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "$t Fungsi hutan mangrove bagi lingkungan adalah ....",
                ['A' => 'mencegah abrasi', 'B' => 'menjadi sumber makanan bagi hewan', 'C' => 'menyerap karbon dioksida', 'D' => 'meningkatkan pemanasan global', 'E' => 'mempercepat kerusakan pantai'], ['A', 'B', 'C'], $w),
            self::q('pg_kompleks', "$t Sumber vitamin D yang disebutkan dalam teks adalah ....",
                ['A' => 'hati sapi', 'B' => 'ikan berlemak', 'C' => 'sayuran hijau', 'D' => 'telur', 'E' => 'sinar matahari langsung'], ['A', 'B', 'D', 'E'], $w),
            self::q('pg_kompleks', "$t Potensi maritim Indonesia yang belum dimanfaatkan secara maksimal ditunjukkan oleh ....",
                ['A' => 'banyak pelabuhan yang tidak dikelola dengan baik', 'B' => 'nelayan masih menggunakan alat tradisional', 'C' => 'seluruh laut sudah dikelola optimal', 'D' => 'jumlah pulau yang sangat banyak sudah dihuni semua', 'E' => 'letak strategis sebagai jalur perdagangan dunia'], ['A', 'B'], $w),
            self::q('pg_kompleks', "$t Dampak positif globalisasi adalah ....",
                ['A' => 'kemudahan memperoleh informasi', 'B' => 'terbukanya peluang kerja sama internasional', 'C' => 'berkembangnya teknologi komunikasi', 'D' => 'meningkatnya individualisme', 'E' => 'hilangnya identitas budaya'], ['A', 'B', 'C'], $w),
            self::q('pg_kompleks', "$t Dampak negatif globalisasi adalah ....",
                ['A' => 'gaya hidup konsumtif', 'B' => 'menurunnya rasa kebersamaan', 'C' => 'meluasnya akses pendidikan daring', 'D' => 'masuknya budaya asing tanpa filter', 'E' => 'tumbuhnya wirausaha digital'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "$t Keuntungan pembayaran digital (e-wallet) bagi masyarakat adalah ....",
                ['A' => 'transaksi lebih cepat', 'B' => 'tidak perlu membawa uang tunai', 'C' => 'dapat digunakan saat tidak ada internet', 'D' => 'mudah dicatat riwayat transaksinya', 'E' => 'selalu bebas biaya di semua layanan'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "Adam memberi semangat kepada Ilman yang takut merantau ke Jakarta.\n\n$t Sifat Adam dalam cerita tersebut adalah ....",
                ['A' => 'suportif', 'B' => 'setia kawan', 'C' => 'iri hati', 'D' => 'rendah hati', 'E' => 'pemarah'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "Kakek Marsam tetap menolong Udin yang dahulu bersikap tidak sopan.\n\n$t Karakter yang menonjol dari Kakek Marsam adalah ....",
                ['A' => 'sabar', 'B' => 'pemaaf', 'C' => 'ikhlas', 'D' => 'pendendam', 'E' => 'pemarah'], ['A', 'B', 'C'], $w),
            self::q('pg_kompleks', "Choose the good characters shown by the clown in the story. More than one answer is correct.",
                ['A' => 'a brave man', 'B' => 'a smart man', 'C' => 'a lazy man', 'D' => 'a self-confident man', 'E' => 'a dishonest man'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "$t Kalimat berikut yang menggunakan kata kerja pasif adalah ....",
                ['A' => 'Dia diangkat sebagai bendahara.', 'B' => 'Dia mengangkat tas itu.', 'C' => 'Surat itu ditulis oleh Hatta.', 'D' => 'Hatta menulis surat.', 'E' => 'Tokoh itu dipindahkan ke Banda Neira.'], ['A', 'C', 'E'], $w),
            self::q('pg_kompleks', "$t Ciri teks eksposisi adalah ....",
                ['A' => 'menyajikan informasi atau penjelasan', 'B' => 'didukung fakta dan data', 'C' => 'bertujuan menghibur dengan tokoh rekaan', 'D' => 'menggunakan bahasa lugas', 'E' => 'selalu berisi dialog antartokoh'], ['A', 'B', 'D'], $w),
            self::q('pg_kompleks', "$t Bahan pangan lokal yang dapat menggantikan beras adalah ....",
                ['A' => 'sagu', 'B' => 'jagung', 'C' => 'singkong', 'D' => 'gandum impor', 'E' => 'sorgum'], ['A', 'B', 'C', 'E'], $w),
        ];
    }

    /** 5 soal menjodohkan. options = ['left' => [id => teks], 'right' => [kunci => teks]]; answer_key = [id kiri => kunci kanan]. */
    private static function menjodohkan(): array
    {
        $w = 4;
        $t = 'Jodohkan setiap pernyataan di kiri dengan pasangan yang tepat di kanan.';
        return [
            self::q('menjodohkan', "$t (Fenomena globalisasi dan bidangnya)",
                ['left' => ['1' => 'E-commerce', '2' => 'Musik K-pop', '3' => 'Media sosial'],
                 'right' => ['A' => 'Bidang budaya', 'B' => 'Bidang komunikasi', 'C' => 'Bidang ekonomi', 'D' => 'Bidang pertahanan']],
                ['1' => 'C', '2' => 'A', '3' => 'B'], $w),
            self::q('menjodohkan', "$t (Istilah dan maknanya)",
                ['left' => ['1' => 'Kecanduan digital', '2' => 'Virtual', '3' => 'Abrasi'],
                 'right' => ['A' => 'Pengikisan pantai oleh gelombang laut', 'B' => 'Ketergantungan berlebihan pada teknologi digital', 'C' => 'Interaksi melalui layar', 'D' => 'Proses penyerapan kalsium']],
                ['1' => 'B', '2' => 'C', '3' => 'A'], $w),
            self::q('menjodohkan', "$t (Nutrisi dan fungsinya)",
                ['left' => ['1' => 'Vitamin D', '2' => 'Kalsium', '3' => 'Serat'],
                 'right' => ['A' => 'Membantu pencernaan', 'B' => 'Membantu penyerapan kalsium', 'C' => 'Pembentuk tulang dan gigi', 'D' => 'Sumber utama lemak']],
                ['1' => 'B', '2' => 'C', '3' => 'A'], $w),
            self::q('menjodohkan', "Match each good character with the right evidence.",
                ['left' => ['1' => 'a brave man', '2' => 'a smart man', '3' => 'a helpful man'],
                 'right' => ['A' => 'He accepted the zookeeper\'s job when the gorilla died.', 'B' => 'He climbed to the top of his cage to perform.', 'C' => 'He made people laugh with his performance.', 'D' => 'He ran away from the zoo.']],
                ['1' => 'B', '2' => 'C', '3' => 'A'], $w),
            self::q('menjodohkan', "$t (Sebab dan akibat)",
                ['left' => ['1' => 'Hutan mangrove ditebang secara liar', '2' => 'Makanan cepat saji dikonsumsi berlebihan', '3' => 'Pelabuhan tidak dikelola dengan baik'],
                 'right' => ['A' => 'Risiko obesitas dan hipertensi meningkat', 'B' => 'Garis pantai mudah terkikis', 'C' => 'Potensi maritim tidak termanfaatkan', 'D' => 'Harga ikan selalu turun']],
                ['1' => 'B', '2' => 'A', '3' => 'C'], $w),
        ];
    }

    /** 5 soal esai (dinilai manual oleh guru; jawaban berupa foto lembar jawaban). */
    private static function esai(): array
    {
        $w = 6;
        return [
            self::q('esai', 'Mengapa masyarakat dapat memberikan respons yang berbeda terhadap fenomena globalisasi yang sama? Jelaskan berdasarkan hasil pembelajaranmu.', null, null, $w),
            self::q('esai', 'Pilih satu fenomena globalisasi (misalnya e-commerce). Jelaskan satu peluang dan satu persoalan yang dapat muncul bagi masyarakat.', null, null, $w),
            self::q('esai', 'Bagaimana sebaiknya masyarakat menyikapi globalisasi? Berikan satu contoh penerapannya dalam kehidupan sehari-hari.', null, null, $w),
            self::q('esai', "Read the text again, then give evidence of the good character of the clown: 'a smart man'. Write your answer in 1–2 sentences.", null, null, $w),
            self::q('esai', 'Banyak hutan mangrove rusak karena penebangan liar dan alih fungsi lahan. Tuliskan dua dampak yang ditimbulkan dan satu solusi yang dapat dilakukan masyarakat.', null, null, $w),
        ];
    }
}