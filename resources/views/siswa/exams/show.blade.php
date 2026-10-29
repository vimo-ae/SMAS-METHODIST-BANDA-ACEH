<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color:#172033;">{{ __('E-Exam') }}</h2>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-green: #205C3B;
            --c-green-dark: #17452D;
            --c-ivory: #FAF8F4;
            --c-sage: #E9E8D9;
            --c-navy: #172033;
            --c-pale: #DDE9DF;
            --c-muted: #73766F;
            --c-border: #E8E5DD;
            --c-orange: #E8742C;
            --c-orange-soft: #FCE7DA;
            --c-red: #B4432F;
            --c-red-soft: #F8E8E3;
        }

        .p-page { background: var(--c-ivory); color: var(--c-navy); min-height: 100vh; padding: 36px 0 64px; }
        .p-wrap { max-width: 1050px; margin: 0 auto; padding: 0 24px; }

        .p-back { display: inline-flex; gap: 8px; color: var(--c-green); text-decoration: none; font-weight: 600; margin-bottom: 24px; }
        .p-back:hover { color: var(--c-green-dark); }

        .p-badge {
            display: inline-block; background: var(--c-pale); color: var(--c-green);
            border-radius: 999px; padding: 8px 16px; font-size: 12px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase; margin-bottom: 16px;
        }
        .p-title { font-family: 'Playfair Display', serif; font-size: clamp(28px, 4vw, 42px); line-height: 1.2; font-weight: 700; margin: 0 0 10px; }
        .p-title span { color: var(--c-green); }
        .p-sub { color: var(--c-muted); line-height: 1.8; max-width: 640px; margin: 0 0 28px; }

        .p-layout { display: grid; grid-template-columns: 1fr; gap: 24px; align-items: start; }
        @media (min-width: 960px) { .p-layout { grid-template-columns: 1.4fr 1fr; } }

        .p-card {
            background: #fff; border: 1px solid var(--c-border); border-radius: 20px;
            box-shadow: 0 8px 28px rgba(23, 32, 51, .045); margin-bottom: 24px; overflow: hidden;
        }
        .p-card-h { padding: 20px 26px; border-bottom: 1px solid var(--c-border); }
        .p-card-h h4 { margin: 0; font-size: 18px; font-weight: 700; }
        .p-card-b { padding: 26px; }

        .p-exam-name { font-family: 'Playfair Display', serif; font-size: 25px; font-weight: 700; margin: 0 0 6px; }
        .p-exam-sub { color: var(--c-muted); margin: 0 0 20px; }

        .p-info { display: grid; grid-template-columns: 1fr; gap: 12px; }
        @media (min-width: 560px) { .p-info { grid-template-columns: 1fr 1fr; } }
        .p-info-item { background: var(--c-ivory); border: 1px solid var(--c-border); border-radius: 14px; padding: 16px; }
        .p-info-item small { display: block; color: var(--c-muted); font-size: 12px; margin-bottom: 6px; }
        .p-info-item b { font-weight: 600; overflow-wrap: anywhere; }

        .p-steps { list-style: none; counter-reset: s; padding: 0; margin: 0; }
        .p-steps li { counter-increment: s; position: relative; padding: 4px 0 18px 50px; color: #4F5663; line-height: 1.7; }
        .p-steps li:last-child { padding-bottom: 0; }
        .p-steps li::before {
            content: counter(s); position: absolute; left: 0; top: 0;
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--c-pale); color: var(--c-green);
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;
        }
        .p-steps b { color: var(--c-navy); }

        .p-warn { background: var(--c-orange-soft); border: 1px solid #F3D3BE; border-radius: 14px; padding: 16px 18px; color: #8A4A1E; line-height: 1.7; margin-bottom: 18px; }
        .p-rules { list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; }
        .p-rules li { display: flex; gap: 12px; color: #4F5663; line-height: 1.6; }
        .p-rules li::before { content: ''; flex-shrink: 0; width: 8px; height: 8px; border-radius: 50%; background: var(--c-orange); margin-top: 9px; }

        .p-side { position: relative; }
        @media (min-width: 960px) { .p-side { position: sticky; top: 24px; } }

        .p-start { background: var(--c-sage); border: 1px solid #DDDDCD; border-radius: 20px; padding: 28px; }
        .p-start h5 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; margin: 0 0 8px; }
        .p-start p { color: #62675F; line-height: 1.7; margin: 0 0 16px; }

        .p-check { list-style: none; padding: 0; margin: 0 0 20px; display: grid; gap: 10px; }
        .p-check li { display: flex; align-items: center; gap: 10px; background: #fff; border-radius: 12px; padding: 11px 14px; font-size: 14px; }
        .p-dot { width: 10px; height: 10px; border-radius: 50%; background: #BDBDB0; flex-shrink: 0; }
        .p-ok .p-dot { background: var(--c-green); }
        .p-bad .p-dot { background: var(--c-red); }
        .p-check .state { margin-left: auto; font-weight: 600; color: var(--c-muted); }
        .p-ok .state { color: var(--c-green); }
        .p-bad .state { color: var(--c-red); }

        .p-agree { display: flex; gap: 10px; align-items: flex-start; margin: 0 0 18px; line-height: 1.6; cursor: pointer; }
        .p-agree input { width: 18px; height: 18px; margin-top: 3px; accent-color: var(--c-green); flex-shrink: 0; cursor: pointer; }

        .p-btn {
            display: inline-flex; justify-content: center; align-items: center; gap: 10px; width: 100%;
            background: var(--c-green); color: #fff; border: 1px solid var(--c-green);
            border-radius: 12px; padding: 15px 22px; font-weight: 600; font-size: 15px;
            text-decoration: none; cursor: pointer; transition: background .2s, transform .2s;
        }
        .p-btn:hover { background: var(--c-green-dark); color: #fff; transform: translateY(-1px); }
        .p-btn:disabled { background: #A8B6AA; border-color: #A8B6AA; cursor: not-allowed; transform: none; }
        .p-btn-outline { background: transparent; color: var(--c-green); border-color: #B9CDBD; }
        .p-btn-outline:hover { background: var(--c-pale); color: var(--c-green-dark); }

        .p-alert { border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; line-height: 1.6; }
        .p-alert-success { background: var(--c-pale); border: 1px solid #C6D9C9; color: var(--c-green-dark); }
        .p-alert-error { background: var(--c-red-soft); border: 1px solid #EBCBC0; color: #8A3D2E; }
        .p-alert-warning { background: #F5EEDB; border: 1px solid #E7D8B4; color: #755A25; }

        .p-count { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: var(--c-green); margin: 4px 0 16px; }
    </style>

    @php
        $subject = $exam->classSubjectTeacher->subject->subject_name
            ?? $exam->classSubjectTeacher->subject->name
            ?? '-';
        $duration = $exam->duration_minutes ?? $exam->duration ?? null;
        $maxViolations = config('school.exam.max_violations');
        $isOpen = $exam->isOpen();
    @endphp

    <div class="p-page">
        <div class="p-wrap">

            @if (session('success')) <div class="p-alert p-alert-success">{{ session('success') }}</div> @endif
            @if (session('error')) <div class="p-alert p-alert-error">{{ session('error') }}</div> @endif
            @if ($errors->any()) <div class="p-alert p-alert-error">{{ $errors->first() }}</div> @endif

            <a href="{{ route('siswa.exams.index') }}" class="p-back">&larr; Kembali ke daftar ujian</a>

            <span class="p-badge">Persiapan Ujian</span>
            <h1 class="p-title">Siap untuk menguji <span>kemampuanmu?</span></h1>
            <p class="p-sub">
                Baca informasi, panduan, dan aturan di bawah ini sebelum memulai.
                Pastikan semua pengecekan di panel kanan sudah hijau.
            </p>

            <div class="p-layout">

                {{-- KOLOM KIRI --}}
                <div>
                    <section class="p-card">
                        <div class="p-card-h"><h4>Informasi Ujian</h4></div>
                        <div class="p-card-b">
                            <h3 class="p-exam-name">{{ $exam->title ?? $exam->exam_name ?? 'Ujian' }}</h3>
                            <p class="p-exam-sub">{{ $subject }}</p>

                            <div class="p-info">
                                <div class="p-info-item">
                                    <small>Jumlah Soal</small>
                                    <b>{{ $exam->questions_count }} soal</b>
                                </div>
                                <div class="p-info-item">
                                    <small>Durasi Pengerjaan</small>
                                    <b>{{ $duration ? $duration.' menit' : 'Sesuai ketentuan ujian' }}</b>
                                </div>
                                <div class="p-info-item">
                                    <small>Dibuka</small>
                                    <b>{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('d M Y, H:i') : '-' }}</b>
                                </div>
                                <div class="p-info-item">
                                    <small>Ditutup</small>
                                    <b>{{ $exam->end_time ? \Carbon\Carbon::parse($exam->end_time)->format('d M Y, H:i') : '-' }}</b>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="p-card">
                        <div class="p-card-h"><h4>Panduan Pengerjaan</h4></div>
                        <div class="p-card-b">
                            <ol class="p-steps">
                                <li><b>Siapkan perangkat.</b> Gunakan perangkat dengan baterai cukup dan koneksi internet stabil. Tutup aplikasi lain yang tidak perlu.</li>
                                <li><b>Mulai ujian.</b> Setelah menekan tombol mulai, waktu langsung berjalan dan tidak bisa dihentikan.</li>
                                <li><b>Jawaban tersimpan otomatis.</b> Pilihan ganda tersimpan setiap kali kamu menjawab. Untuk soal esai, unggah foto lembar jawabanmu.</li>
                                <li><b>Kumpulkan ujian.</b> Periksa kembali jawabanmu, lalu tekan tombol kumpulkan. Kalau waktu habis, ujian dikumpulkan dan dinilai otomatis.</li>
                            </ol>
                        </div>
                    </section>

                    <section class="p-card">
                        <div class="p-card-h"><h4>Aturan &amp; Pengawasan Sistem</h4></div>
                        <div class="p-card-b">
                            <div class="p-warn">
                                Sistem mencatat setiap pelanggaran. Jika mencapai
                                <strong>{{ $maxViolations ?? 'batas maksimal' }} pelanggaran</strong>,
                                ujianmu otomatis diselesaikan dan dinilai.
                            </div>
                            <ul class="p-rules">
                                <li>Jangan berpindah tab atau jendela selama ujian berlangsung.</li>
                                <li>Jangan keluar dari mode layar penuh (fullscreen).</li>
                                <li>Jangan menyalin atau menempel (copy-paste) teks soal.</li>
                                <li>Kerjakan secara mandiri, jujur, dan bertanggung jawab.</li>
                            </ul>
                        </div>
                    </section>
                </div>

                {{-- KOLOM KANAN --}}
                <aside class="p-side">
                    <section class="p-start">

                        @if ($session && $session->status !== 'berlangsung')

                            <h5>Ujian telah selesai</h5>
                            <p>Jawabanmu sudah dikumpulkan dan dikoreksi. Kamu dapat kembali ke daftar untuk melihat ujian lainnya.</p>
                            <a href="{{ route('siswa.exams.index') }}" class="p-btn p-btn-outline">Kembali ke Daftar Ujian</a>

                        @elseif ($session && $session->status === 'berlangsung')

                            <h5>Lanjutkan Ujian</h5>
                            <p>Kamu masih punya sesi ujian yang berjalan. Lanjutkan agar jawabanmu tetap tersimpan.</p>
                            <a href="{{ route('siswa.exams.take', $exam) }}" class="p-btn">Lanjutkan Ujian &rarr;</a>

                        @elseif (! $isOpen)

                            <div class="p-alert p-alert-warning">Ujian belum dibuka atau sudah ditutup.</div>
                            <h5>Ujian Belum Tersedia</h5>

                            @if ($exam->start_time && now()->lt(\Carbon\Carbon::parse($exam->start_time)))
                                <p>Ujian dibuka dalam:</p>
                                <div class="p-count" id="countdown" data-target="{{ \Carbon\Carbon::parse($exam->start_time)->toIso8601String() }}">--:--:--</div>
                            @else
                                <p>Silakan periksa jadwal atau hubungi guru yang bersangkutan.</p>
                            @endif

                            <button type="button" class="p-btn" disabled>Mulai Ujian</button>

                        @elseif ($exam->questions_count === 0)

                            <div class="p-alert p-alert-warning">Soal untuk ujian ini belum tersedia.</div>
                            <h5>Soal Belum Tersedia</h5>
                            <p>Kamu belum bisa memulai karena guru belum menambahkan soal.</p>

                        @else

                            <h5>Sudah siap memulai?</h5>
                            <p>Pastikan pengecekan berikut sudah hijau.</p>

                            <ul class="p-check">
                                <li id="chkNet"><span class="p-dot"></span> Koneksi internet <span class="state">Memeriksa...</span></li>
                                <li id="chkFs"><span class="p-dot"></span> Mode layar penuh <span class="state">Memeriksa...</span></li>
                            </ul>

                            <form action="{{ route('siswa.exams.start', $exam) }}" method="POST" id="startExamForm">
                                @csrf

                                <label class="p-agree" for="agreeRules">
                                    <input type="checkbox" id="agreeRules">
                                    <span>Saya telah membaca panduan dan aturan ujian, dan siap mengerjakan dengan jujur.</span>
                                </label>

                                <button type="submit" class="p-btn" id="startExamButton" disabled>
                                    Mulai Ujian &rarr;
                                </button>
                            </form>

                        @endif

                    </section>
                </aside>

            </div>
        </div>
    </div>

    <script>
        const agree = document.getElementById('agreeRules');
        const btn = document.getElementById('startExamButton');
        const form = document.getElementById('startExamForm');
        const chkNet = document.getElementById('chkNet');
        const chkFs = document.getElementById('chkFs');

        function setCheck(el, ok, text) {
            if (!el) return;
            el.classList.toggle('p-ok', ok);
            el.classList.toggle('p-bad', !ok);
            el.querySelector('.state').textContent = text;
        }

        function refreshButton() {
            if (!btn) return;
            btn.disabled = !(agree.checked && navigator.onLine);
        }

        function checkNet() {
            setCheck(chkNet, navigator.onLine, navigator.onLine ? 'Terhubung' : 'Terputus');
            refreshButton();
        }

        if (chkNet) {
            checkNet();
            window.addEventListener('online', checkNet);
            window.addEventListener('offline', checkNet);

            const fsOk = !!document.documentElement.requestFullscreen;
            setCheck(chkFs, fsOk, fsOk ? 'Didukung' : 'Tidak didukung');
        }

        if (agree) agree.addEventListener('change', refreshButton);

        if (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm('Apakah kamu yakin ingin memulai ujian? Waktu akan langsung berjalan.')) {
                    e.preventDefault();
                }
            });
        }

        const cd = document.getElementById('countdown');
        if (cd) {
            const target = new Date(cd.dataset.target).getTime();
            const pad = n => String(n).padStart(2, '0');
            const tick = () => {
                const diff = Math.floor((target - Date.now()) / 1000);
                if (diff <= 0) { location.reload(); return; }
                const d = Math.floor(diff / 86400);
                const h = Math.floor((diff % 86400) / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                cd.textContent = (d > 0 ? d + ' hari ' : '') + pad(h) + ':' + pad(m) + ':' + pad(s);
            };
            tick();
            setInterval(tick, 1000);
        }
    </script>
</x-app-layout>