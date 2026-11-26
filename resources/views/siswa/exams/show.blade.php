<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    Persiapan Ujian: {{ $exam->title ?? 'Ujian' }}
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $exam->classSubjectTeacher->schoolClass->name ?? 'Kelas' }} &bull; {{ $exam->classSubjectTeacher->subject->name ?? 'Mata Pelajaran' }}
                </span>
            </div>
        </div>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $subject = $exam->classSubjectTeacher->subject->subject_name
            ?? $exam->classSubjectTeacher->subject->name
            ?? '-';
        $subjectCode = $exam->classSubjectTeacher->subject->code ?? $exam->classSubjectTeacher->subject_code ?? null;
        $duration = $exam->duration_minutes ?? $exam->duration ?? null;
        $maxViolations = config('school.exam.max_violations') ?? 3;
        $isOpen = $exam->isOpen();

        $getSubjectTheme = function($subjectName, $subjectCode = null) {
            $name = strtolower($subjectName ?? '');
            $code = strtolower($subjectCode ?? '');

            $themes = [
                'matematika' => ['symbol' => 'MAT', 'category' => 'MATEMATIKA', 'image' => 'matematika.jpg', 'gradient' => 'linear-gradient(135deg, #0d2847 0%, #174e7d 100%)'],
                'fisika' => ['symbol' => 'FIS', 'category' => 'FISIKA', 'image' => 'fisika.jpg', 'gradient' => 'linear-gradient(135deg, #091e38 0%, #15508a 100%)'],
                'biologi' => ['symbol' => 'BIO', 'category' => 'BIOLOGI', 'image' => 'biologi.jpg', 'gradient' => 'linear-gradient(135deg, #06282d 0%, #0d5f66 100%)'],
                'kimia' => ['symbol' => 'KIM', 'category' => 'KIMIA', 'image' => 'kimia.jpg', 'gradient' => 'linear-gradient(135deg, #10263f 0%, #1a568a 100%)'],
                'indonesia' => ['symbol' => 'IDN', 'category' => 'BAHASA INDONESIA', 'image' => 'indonesia.jpg', 'gradient' => 'linear-gradient(135deg, #301712 0%, #7d3725 100%)'],
                'inggris' => ['symbol' => 'ENG', 'category' => 'BAHASA INGGRIS', 'image' => 'inggris.svg', 'gradient' => 'linear-gradient(135deg, #18223c 0%, #29457a 100%)'],
                'informatika' => ['symbol' => 'TIK', 'category' => 'INFORMATIKA', 'image' => 'informatika.jpg', 'gradient' => 'linear-gradient(135deg, #092338 0%, #104a75 100%)'],
                'sejarah' => ['symbol' => 'SEJ', 'category' => 'SEJARAH', 'image' => 'sejarah.svg', 'gradient' => 'linear-gradient(135deg, #2b1f13 0%, #694a28 100%)'],
                'geografi' => ['symbol' => 'GEO', 'category' => 'GEOGRAFI', 'image' => 'geografi.svg', 'gradient' => 'linear-gradient(135deg, #0f281e 0%, #1f6146 100%)'],
                'ekonomi' => ['symbol' => 'EKO', 'category' => 'EKONOMI', 'image' => 'ekonomi.svg', 'gradient' => 'linear-gradient(135deg, #132438 0%, #1f5080 100%)'],
                'sosiologi' => ['symbol' => 'SOS', 'category' => 'SOSIOLOGI', 'image' => 'sosiologi.svg', 'gradient' => 'linear-gradient(135deg, #281a33 0%, #5a3575 100%)'],
                'agama' => ['symbol' => 'AGM', 'category' => 'PENDIDIKAN AGAMA', 'image' => 'agama.svg', 'gradient' => 'linear-gradient(135deg, #1f2716 0%, #4a5c2d 100%)'],
                'pjok' => ['symbol' => 'PJK', 'category' => 'PJOK', 'image' => 'pjok.svg', 'gradient' => 'linear-gradient(135deg, #162a33 0%, #255d73 100%)'],
                'seni' => ['symbol' => 'SBD', 'category' => 'SENI BUDAYA', 'image' => 'seni-budaya.svg', 'gradient' => 'linear-gradient(135deg, #2d182b 0%, #6e2e68 100%)'],
                'pkn' => ['symbol' => 'PKN', 'category' => 'PPKN', 'image' => 'ppkn.svg', 'gradient' => 'linear-gradient(135deg, #30141b 0%, #732a3d 100%)'],
            ];

            $aliases = [
                'mtk' => 'matematika', 'fis' => 'fisika', 'bio' => 'biologi', 'kim' => 'kimia',
                'bin' => 'indonesia', 'bahasa indonesia' => 'indonesia', 'big' => 'inggris',
                'bahasa inggris' => 'inggris', 'sej' => 'sejarah', 'inf' => 'informatika',
                'tik' => 'informatika', 'komputer' => 'informatika', 'eko' => 'ekonomi',
                'geo' => 'geografi', 'sos' => 'sosiologi', 'agm' => 'agama',
                'pendidikan agama' => 'agama', 'penjas' => 'pjok', 'sbd' => 'seni',
                'seni budaya' => 'seni', 'ppkn' => 'pkn',
            ];

            $matchedKey = null;
            foreach ($aliases as $alias => $target) {
                if (str_contains($name, $alias) || str_contains($code, $alias)) {
                    $matchedKey = $target;
                    break;
                }
            }

            if (!$matchedKey) {
                foreach (array_keys($themes) as $k) {
                    if (str_contains($name, $k) || str_contains($code, $k)) {
                        $matchedKey = $k;
                        break;
                    }
                }
            }

            $t = $matchedKey ? $themes[$matchedKey] : [
                'symbol' => 'EXAM', 'category' => 'ASESMEN DIGITAL', 'image' => null,
                'gradient' => 'linear-gradient(135deg, #123B60 0%, #267BB5 100%)',
            ];

            $imgUrl = null;
            if (!empty($t['image'])) {
                $jpgName = preg_replace('/\.(svg|png)$/i', '.jpg', $t['image']);
                $svgName = preg_replace('/\.(jpg|png)$/i', '.svg', $t['image']);
                if (file_exists(public_path('images/subjects/' . $jpgName))) {
                    $imgUrl = asset('images/subjects/' . $jpgName);
                } elseif (file_exists(public_path('images/subjects/' . $svgName))) {
                    $imgUrl = asset('images/subjects/' . $svgName);
                }
            }

            if (!$imgUrl && file_exists(public_path('images/hero-school.jpg'))) {
                $imgUrl = asset('images/hero-school.jpg');
            }

            $t['imageUrl'] = $imgUrl;
            return $t;
        };

        $theme = $getSubjectTheme($subject, $subjectCode);
    @endphp

    <style>
        .p-page {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #FAF9F6;
            min-height: 100vh;
            padding-bottom: 64px;
        }

        .p-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .p-card {
            background: #fff;
            border: 1px solid #E5EBF0;
            border-radius: 24px;
            box-shadow: 0 7px 25px rgba(18, 59, 96, 0.035);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .p-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .p-card-body {
            padding: 24px;
        }

        .p-steps {
            list-style: none;
            counter-reset: step;
            padding: 0;
            margin: 0;
        }

        .p-steps li {
            counter-increment: step;
            position: relative;
            padding: 4px 0 20px 48px;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }

        .p-steps li:last-child {
            padding-bottom: 0;
        }

        .p-steps li::before {
            content: counter(step);
            position: absolute;
            left: 0;
            top: 2px;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #EEF4F9;
            color: #123B60;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid #DDE7F0;
        }

        .p-check-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .p-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #94A3B8;
            flex-shrink: 0;
        }

        .p-ok .p-dot {
            background: #10B981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
        }

        .p-bad .p-dot {
            background: #EF4444;
        }

        .p-ok .state {
            margin-left: auto;
            color: #059669;
            font-size: 12px;
            font-weight: 700;
        }

        .p-bad .state {
            margin-left: auto;
            color: #DC2626;
            font-size: 12px;
            font-weight: 700;
        }
    </style>

    <div class="p-page pt-6">
        <div class="mx-auto max-w-[1240px] px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- BREADCRUMB dan BACK BUTTON --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('siswa.exams.index') }}" class="hover:text-[#267BB5] transition flex items-center gap-1.5 font-semibold text-slate-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                        </svg>
                        Kembali ke Daftar Ujian
                    </a>
                    <span>/</span>
                    <span class="font-bold text-[#123B60]">{{ $exam->title }}</span>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-white px-3.5 py-1 text-xs font-bold text-[#123B60] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ strtoupper($exam->exam_type) }} &bull; KKM: {{ $exam->kkm ?? 75 }}
                </span>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
                <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="pt-0.5 font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="pt-0.5 font-semibold">{{ session('error') ?? $errors->first() }}</span>
                </div>
            @endif

            {{-- SUBJECT COVER BANNER HERO --}}
            <section class="relative isolate overflow-hidden rounded-[28px] bg-[#10243E] p-7 sm:p-10 text-white shadow-xl">
                @if(!empty($theme['imageUrl']))
                    <img src="{{ $theme['imageUrl'] }}" alt="{{ $subject }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/95 via-[#102a4e]/85 to-[#164273]/65 pointer-events-none"></div>
                @else
                    <div class="absolute inset-0" style="background: {{ $theme['gradient'] }};"></div>
                @endif

                <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
                <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-cyan-400/10 blur-2xl"></div>

                <div class="relative z-10 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-lg bg-white/20 backdrop-blur-md px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white border border-white/30">
                            {{ strtoupper($exam->exam_type) }}
                        </span>
                        <span class="inline-flex items-center rounded-lg bg-white/15 backdrop-blur-md px-2.5 py-1 text-[11px] font-extrabold text-cyan-200 border border-white/20">
                            {{ $theme['symbol'] }} &bull; {{ $theme['category'] }}
                        </span>
                    </div>

                    <h1 class="p-serif mt-3 text-2xl font-extrabold tracking-tight sm:text-4xl text-white leading-tight">
                        {{ $exam->title ?? 'Ujian Digital' }}
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-blue-100 font-medium">
                        {{ $subject }} &bull; {{ $exam->classSubjectTeacher->schoolClass->name ?? 'Kelas' }}
                    </p>
                </div>
            </section>

            {{-- 2 KOLOM LAYOUT: INFORMASI dan PANEL EKSEKUSI --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- KOLOM KIRI (7/12): DETAIL, PANDUAN, ATURAN --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- KARTU 1: INFORMASI UJIAN --}}
                    <div class="p-card">
                        <div class="p-card-header bg-slate-50/50">
                            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Informasi Teknis Ujian
                            </h3>
                        </div>
                        <div class="p-card-body">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jumlah Soal</span>
                                    <p class="font-serif text-xl font-black text-slate-800 mt-1">{{ $exam->questions_count }}</p>
                                    <span class="text-[10px] text-slate-500">Butir Soal</span>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Durasi</span>
                                    <p class="font-serif text-xl font-black text-slate-800 mt-1">{{ $duration ? $duration.'m' : '-' }}</p>
                                    <span class="text-[10px] text-slate-500">Menit Pengerjaan</span>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">KKM</span>
                                    <p class="font-serif text-xl font-black text-slate-800 mt-1">{{ $exam->kkm ?? 75 }}</p>
                                    <span class="text-[10px] text-slate-500">Standar Kelulusan</span>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Toleransi</span>
                                    <p class="font-serif text-xl font-black text-rose-600 mt-1">{{ $maxViolations }}x</p>
                                    <span class="text-[10px] text-slate-500">Maks. Pelanggaran</span>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-slate-100 pt-3.5 space-y-2 text-xs text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Jadwal Dibuka:</span>
                                    <strong class="text-slate-800">{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->translatedFormat('d M Y, H:i') : '-' }} WIB</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Jadwal Ditutup:</span>
                                    <strong class="text-slate-800">{{ $exam->end_time ? \Carbon\Carbon::parse($exam->end_time)->translatedFormat('d M Y, H:i') : '-' }} WIB</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KARTU 2: PANDUAN PENGERJAAN --}}
                    <div class="p-card">
                        <div class="p-card-header bg-slate-50/50">
                            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Panduan dan Tata Cara
                            </h3>
                        </div>
                        <div class="p-card-body">
                            <ol class="p-steps">
                                <li>
                                    <strong class="text-slate-800">Siapkan perangkat dan internet stabil.</strong> Pastikan baterai mencukupi dan koneksi internet stabil. Tutup aplikasi/tab lain yang tidak berkepentingan.
                                </li>
                                <li>
                                    <strong class="text-slate-800">Mode Layar Penuh (Fullscreen).</strong> Ujian berjalan otomatis dalam layar penuh untuk menjaga integritas dan ketenangan pengerjaan.
                                </li>
                                <li>
                                    <strong class="text-slate-800">Penyimpanan Jawaban Otomatis.</strong> Sistem menyimpan pilihan jawaban setiap kali kamu mengklik opsi secara seketika ke database.
                                </li>
                                <li>
                                    <strong class="text-slate-800">Kumpulkan Ujian dengan Teliti.</strong> Periksa lembar navigasi soal sebelum menekan tombol Selesai. Saat waktu habis, sistem mengumpulkan ujian secara otomatis.
                                </li>
                            </ol>
                        </div>
                    </div>

                    {{-- KARTU 3: ATURAN dan PENGAWASAN SISTEM --}}
                    <div class="p-card">
                        <div class="p-card-header bg-rose-50/50 border-rose-100">
                            <h3 class="font-bold text-sm text-rose-800 flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Integritas dan Pengawasan Pelanggaran
                            </h3>
                        </div>
                        <div class="p-card-body">
                            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs leading-relaxed text-rose-900 mb-4">
                                Sistem pengawas mendeteksi perpindahan tab browser, keluar layar penuh, atau copy-paste. Jika mencapai <strong>{{ $maxViolations }} kali pelanggaran</strong>, sesi ujian kamu akan terkunci otomatis dan dinilai apa adanya.
                            </div>

                            <ul class="space-y-2 text-xs text-slate-600">
                                <li class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    <span>Dilarang keras membuka browser tab lain atau aplikasi lain selama ujian.</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    <span>Dilarang menyalin (copy) pertanyaan maupun menempel (paste) jawaban.</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    <span>Kerjakan ujian secara mandiri dengan menjunjung tinggi kejujuran.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN (5/12): KESIAPAN dan TOMBOL AKSI --}}
                <div class="lg:col-span-5 sticky top-6">
                    <div class="p-card border-[#BBD7E9] bg-white">
                        <div class="p-card-header bg-[#123B60] text-white">
                            <h3 class="font-bold text-sm flex items-center gap-2">
                                <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Status dan Panel Mulai
                            </h3>
                        </div>

                        <div class="p-card-body space-y-5">
                            @if ($session && $session->status !== 'berlangsung')
                                {{-- SESI SUDAH SELESAI --}}
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-emerald-900 text-base">Ujian Telah Selesai</h4>
                                    <p class="text-xs text-emerald-700 mt-1">Jawaban Anda sudah terkumpul dengan aman di sistem penilaian.</p>
                                </div>

                                <a href="{{ route('siswa.exams.index') }}"
                                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                    &larr; Kembali ke Daftar Ujian
                                </a>

                            @elseif ($session && $session->status === 'berlangsung')
                                {{-- SESI SEDANG BERLANGSUNG --}}
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-amber-900 text-base">Sesi Ujian Sedang Berjalan</h4>
                                    <p class="text-xs text-amber-700 mt-1">Waktu ujian Anda sedang berjalan. Segera lanjutkan untuk menyelesaikan jawaban.</p>
                                </div>

                                <a href="{{ route('siswa.exams.take', $exam) }}"
                                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 py-3.5 text-xs font-bold text-white shadow-md transition hover:bg-amber-700 active:scale-95">
                                    <span>Lanjutkan Pengerjaan Ujian</span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>

                            @elseif (! $isOpen)
                                {{-- BELUM BUKA ATAU SUDAH TUTUP --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-200 text-slate-600 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-base">Ujian Belum Dibuka</h4>
                                    @if ($exam->start_time && now()->lt(\Carbon\Carbon::parse($exam->start_time)))
                                        <p class="text-xs text-slate-500 mt-1">Ujian akan dibuka dalam waktu:</p>
                                        <div class="font-serif text-2xl font-black text-[#123B60] mt-2" id="countdown" data-target="{{ \Carbon\Carbon::parse($exam->start_time)->toIso8601String() }}">--:--:--</div>
                                    @else
                                        <p class="text-xs text-slate-500 mt-1">Jadwal pengerjaan ujian ini telah berakhir.</p>
                                    @endif
                                </div>

                                <button type="button" disabled class="w-full rounded-xl bg-slate-200 py-3 text-xs font-bold text-slate-400 cursor-not-allowed">
                                    Mulai Ujian
                                </button>

                            @elseif ($exam->questions_count === 0)
                                {{-- BELUM ADA SOAL --}}
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center">
                                    <h4 class="font-bold text-amber-900 text-sm">Soal Belum Tersedia</h4>
                                    <p class="text-xs text-amber-700 mt-1">Guru pengampu belum mengunggah butir soal untuk ujian ini.</p>
                                </div>

                            @else
                                {{-- READY TO START --}}
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Cek Kesiapan Perangkat</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">Sistem memeriksa koneksi dan kompatibilitas layar.</p>
                                </div>

                                <div class="space-y-2">
                                    <div class="p-check-item" id="chkNet">
                                        <span class="p-dot"></span>
                                        <span>Koneksi Internet</span>
                                        <span class="state">Memeriksa...</span>
                                    </div>
                                    <div class="p-check-item" id="chkFs">
                                        <span class="p-dot"></span>
                                        <span>Dukungan Layar Penuh</span>
                                        <span class="state">Memeriksa...</span>
                                    </div>
                                </div>

                                <form action="{{ route('siswa.exams.start', $exam) }}" method="POST" id="startExamForm" class="space-y-4 pt-2">
                                    @csrf

                                    @if (config('exam.token_enabled'))
                                        <label class="block text-xs font-bold text-slate-700" for="exam_token">
                                            Token Ujian
                                        </label>
                                        <input
                                            type="text"
                                            id="exam_token"
                                            name="exam_token"
                                            value="{{ old('exam_token') }}"
                                            maxlength="10"
                                            autocomplete="off"
                                            placeholder="Masukkan token 6 karakter"
                                            class="w-full rounded-xl border-slate-300 py-3 text-center font-mono text-lg font-black uppercase tracking-[0.35em] text-[#123B60] focus:border-[#267BB5] focus:ring-[#267BB5]"
                                            required
                                        >
                                        @error('exam_token')
                                            <p class="text-xs font-semibold text-rose-600">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <p class="rounded-xl bg-blue-50 px-4 py-3 text-xs font-medium text-blue-800">
                                            Token ujian dinonaktifkan sementara. Kamu bisa memulai ujian tanpa token.
                                        </p>
                                    @endif

                                    <label class="flex items-start gap-2.5 cursor-pointer text-xs leading-relaxed text-slate-600" for="agreeRules">
                                        <input type="checkbox" id="agreeRules" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#123B60] focus:ring-[#267BB5] cursor-pointer">
                                        <span>Saya telah membaca panduan dan aturan ujian, dan siap mengerjakan dengan jujur dan penuh tanggung jawab.</span>
                                    </label>

                                    <button type="submit" id="startExamButton" disabled
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#123B60] py-3.5 text-xs font-bold text-white shadow-md transition hover:bg-[#267BB5] disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed active:scale-95">
                                        <span>Mulai Mengerjakan Ujian</span>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        const agree = document.getElementById('agreeRules');
        const btn = document.getElementById('startExamButton');
        const form = document.getElementById('startExamForm');
        const chkNet = document.getElementById('chkNet');
        const chkFs = document.getElementById('chkFs');
        const examToken = document.getElementById('exam_token');

        function setCheck(el, ok, text) {
            if (!el) return;
            el.classList.toggle('p-ok', ok);
            el.classList.toggle('p-bad', !ok);
            const stateEl = el.querySelector('.state');
            if (stateEl) stateEl.textContent = text;
        }

        function refreshButton() {
            if (!btn || !agree) return;
            const tokenIsValid = !examToken || examToken.value.trim().length >= 6;
            btn.disabled = !(agree.checked && navigator.onLine && tokenIsValid);
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
        if (examToken) {
            examToken.addEventListener('input', function () {
                this.value = this.value.toUpperCase().replace(/[^A-Z2-9]/g, '').slice(0, 6);
                refreshButton();
            });
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm('Apakah kamu yakin ingin memulai ujian? Waktu pengerjaan akan langsung berjalan.')) {
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
