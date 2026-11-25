<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    E-Exam Siswa
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Portal Asesmen Digital &bull; {{ auth()->user()->student->schoolClass->name ?? 'Siswa Aktif' }}
                </span>
            </div>
        </div>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .eexam-serif { font-family: 'Playfair Display', Georgia, serif; }

        .eexam-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #E5EBF0;
            border-radius: 24px;
            background: #fff;
            color: inherit;
            text-decoration: none;
            box-shadow: 0 7px 25px rgba(18,59,96,.035);
            transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease;
        }

        .eexam-card:hover {
            transform: translateY(-6px);
            border-color: #BBD7E9;
            box-shadow: 0 18px 38px rgba(18,59,96,.10);
        }

        .eexam-card-cover {
            position: relative;
            min-height: 150px;
            padding: 18px 20px;
            color: white;
            background-color: #123B60;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .eexam-cover-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eexam-card:hover .eexam-cover-image {
            transform: scale(1.08);
        }

        .eexam-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(8, 22, 40, 0.90) 0%, rgba(18, 59, 96, 0.74) 60%, rgba(18, 59, 96, 0.40) 100%);
            pointer-events: none;
        }

        .eexam-cover-symbol {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 32px;
            padding: 0 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
    </style>

    @php
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

        // Hitung ringkasan status ujian siswa
        $totalExams = $exams->count();
        $activeCount = 0;
        $doneCount = 0;
        $upcomingCount = 0;

        foreach ($exams as $ex) {
            $sess = $sessions->get($ex->exam_id);
            if ($sess && $sess->status !== 'berlangsung') {
                $doneCount++;
            } elseif ($sess && $sess->status === 'berlangsung') {
                $activeCount++;
            } elseif ($ex->isOpen()) {
                $activeCount++;
            } else {
                $upcomingCount++;
            }
        }
    @endphp

    <div class="min-h-screen bg-[#FAF9F6] pb-16">
        <div class="mx-auto max-w-[1400px] space-y-8 px-4 py-6 sm:px-6 lg:px-8">

            {{-- ==========================================
                 HERO SECTION RESMI: E-EXAM SISWA
                 ========================================== --}}
            <section class="relative overflow-hidden rounded-[28px] bg-[#10243E] p-8 sm:p-10 text-white shadow-xl">
                @if(file_exists(public_path('images/hero-school.jpg')))
                    <img src="{{ asset('images/hero-school.jpg') }}" alt="SMAS Methodist" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/85 via-[#102a4e]/70 to-[#164273]/50"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123d80] via-[#1756a8] to-[#2879df]"></div>
                @endif

                <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
                <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-blue-400/10 blur-2xl"></div>

                <div class="relative z-10">
                    <div class="max-w-3xl">
                        <p class="mb-1 text-sm font-semibold text-blue-100 uppercase tracking-widest">
                            METHODIST LEARNING SPACE &bull; CBT ASSESSMENT
                        </p>
                        <h1 class="eexam-serif text-3xl font-extrabold tracking-tight sm:text-5xl text-white leading-tight">
                            E-Exam.
                        </h1>
                        <p class="mt-2 text-xl font-medium text-cyan-200 eexam-serif italic">
                            Jujur &bull; Teliti &bull; Raih Prestasi Tertinggi
                        </p>
                        <p class="mt-3 max-w-xl text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                            Akses lembar ujian resmi, kuis harian, asesmen tengah semester, dan evaluasi akhir di lingkungan akademik digital SMAS Methodist Banda Aceh.
                        </p>
                    </div>

                    {{-- STATS PILLS HERO --}}
                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl">
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-white">{{ $totalExams }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Total Ujian</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-emerald-300">{{ $activeCount }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 mt-0.5">Tersedia / Aktif</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-cyan-200">{{ $doneCount }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Selesai</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-white">100%</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Integritas</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ALERTS NOTIFIKASI --}}
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

            {{-- ==========================================
                 FILTER dan PENCARIAN UJIAN
                 ========================================== --}}
            <section class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-1.5 w-6 rounded-full bg-[#123B60]"></span>
                            <p class="text-[10px] font-extrabold uppercase tracking-[.2em] text-[#267BB5]">Daftar Evaluasi Siswa</p>
                        </div>
                        <h2 class="mt-1 text-2xl font-black text-slate-800 sm:text-3xl">Ujian dan Kuis Terdaftar</h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">Pilih ujian untuk melihat petunjuk pengerjaan dan mulai mengerjakan asesmen daring.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- FILTER TABS --}}
                        <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200" id="siswaExamFilterTabs">
                            <button
                                type="button"
                                data-filter="all"
                                class="exam-filter-btn rounded-lg px-3 py-1.5 text-xs transition bg-white text-[#123B60] shadow-sm font-bold">
                                Semua ({{ $totalExams }})
                            </button>
                            <button
                                type="button"
                                data-filter="active"
                                class="exam-filter-btn rounded-lg px-3 py-1.5 text-xs transition text-slate-500 hover:text-slate-800 font-medium">
                                Tersedia ({{ $activeCount }})
                            </button>
                            <button
                                type="button"
                                data-filter="done"
                                class="exam-filter-btn rounded-lg px-3 py-1.5 text-xs transition text-slate-500 hover:text-slate-800 font-medium">
                                Selesai ({{ $doneCount }})
                            </button>
                        </div>

                        {{-- SEARCH INPUT --}}
                        <div class="relative w-full sm:w-64">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                id="siswaExamSearch"
                                type="search"
                                placeholder="Cari nama ujian / mapel..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-10 pr-4 text-xs font-medium text-slate-700 shadow-sm transition focus:border-[#267BB5] focus:ring-[#267BB5]">
                        </div>
                    </div>
                </div>

                @if($exams->isEmpty())
                    {{-- EMPTY STATE --}}
                    <div class="overflow-hidden rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60]">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-black text-slate-800">Belum Ada Ujian yang Dijadwalkan</h3>
                        <p class="mx-auto mt-1 max-w-md text-xs sm:text-sm text-slate-400">
                            Saat ini belum ada jadwal asesmen atau ujian yang diterbitkan oleh bapak/ibu guru untuk kelas Anda.
                        </p>
                    </div>
                @else
                    {{-- GRID DAFTAR UJIAN SISWA (MAKSIMAL 6 PER SLIDE) --}}
                    <div id="siswaExamGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($exams as $exam)
                            @php
                                $session = $sessions->get($exam->exam_id);
                                $subjectName = $exam->classSubjectTeacher->subject->name
                                    ?? $exam->classSubjectTeacher->subject->subject_name
                                    ?? 'Mata Pelajaran';
                                $subjectCode = $exam->classSubjectTeacher->subject->code ?? $exam->classSubjectTeacher->subject_code ?? null;
                                $theme = $getSubjectTheme($subjectName, $subjectCode);
                                $className = $exam->classSubjectTeacher->schoolClass->name ?? 'Kelas Siswa';
                                $isOpen = $exam->isOpen();

                                // Status text dan styling
                                if ($session && $session->status !== 'berlangsung') {
                                    $statusCode = 'done';
                                    $statusBadge = 'Selesai';
                                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    $statusDot = 'bg-emerald-500';
                                    $btnLabel = 'Lihat Hasil / Nilai';
                                    $btnClass = 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200';
                                } elseif ($session && $session->status === 'berlangsung') {
                                    $statusCode = 'active';
                                    $statusBadge = 'Sedang Dikerjakan';
                                    $statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                    $statusDot = 'bg-amber-500 animate-pulse';
                                    $btnLabel = 'Lanjutkan Ujian';
                                    $btnClass = 'bg-amber-600 text-white hover:bg-amber-700 shadow-md';
                                } elseif ($isOpen) {
                                    $statusCode = 'active';
                                    $statusBadge = 'Tersedia Sekarang';
                                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    $statusDot = 'bg-emerald-500 animate-pulse';
                                    $btnLabel = 'Mulai Ujian Sekarang';
                                    $btnClass = 'bg-[#123B60] text-white hover:bg-[#267BB5] shadow-md';
                                } else {
                                    $statusCode = 'upcoming';
                                    $statusBadge = 'Terjadwal';
                                    $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                    $statusDot = 'bg-slate-400';
                                    $btnLabel = 'Lihat Informasi';
                                    $btnClass = 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200';
                                }

                                $searchText = strtolower($exam->title . ' ' . $subjectName . ' ' . $className . ' ' . $exam->exam_type);
                            @endphp

                            <a href="{{ route('siswa.exams.show', $exam) }}"
                               data-exam-card
                               data-status="{{ $statusCode }}"
                               data-search="{{ addslashes($searchText) }}"
                               class="eexam-card group">

                                {{-- COVER GAMBAR MAPEL --}}
                                <div class="eexam-card-cover" style="{{ empty($theme['imageUrl']) ? 'background: ' . $theme['gradient'] : '' }}">
                                    @if(!empty($theme['imageUrl']))
                                        <img src="{{ $theme['imageUrl'] }}" alt="{{ $subjectName }}" class="eexam-cover-image">
                                        <div class="eexam-cover-overlay"></div>
                                    @endif

                                    {{-- HEADER BAR: MAPEL dan TIPE UJIAN --}}
                                    <div class="relative z-10 flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center rounded-lg bg-white/20 backdrop-blur-md px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white border border-white/30">
                                            {{ strtoupper($exam->exam_type) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold border backdrop-blur-md {{ $statusClass }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}"></span>
                                            {{ $statusBadge }}
                                        </span>
                                    </div>

                                    {{-- FOOTER BAR: KATEGORI dan MONOGRAM --}}
                                    <div class="relative z-10 flex items-end justify-between gap-3 mt-5">
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-[.18em] text-cyan-200">
                                                {{ $theme['category'] }}
                                            </p>
                                            <h3 class="eexam-serif text-lg font-bold text-white leading-snug truncate">
                                                {{ $subjectName }}
                                            </h3>
                                        </div>
                                        <span class="eexam-cover-symbol shrink-0">
                                            {{ $theme['symbol'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- BADAN KARTU UJIAN --}}
                                <div class="p-5 flex flex-col flex-grow justify-between bg-white">
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-400 font-semibold mb-1.5">
                                            <span>{{ $className }}</span>
                                            <span>KKM: {{ $exam->kkm ?? 75 }}</span>
                                        </div>

                                        <h4 class="font-serif text-base font-bold text-slate-800 group-hover:text-[#267BB5] transition line-clamp-2">
                                            {{ $exam->title }}
                                        </h4>

                                        {{-- WAKTU MULAI dan JADWAL --}}
                                        <div class="mt-3.5 space-y-1.5 border-t border-slate-100 pt-3 text-xs text-slate-500">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <rect x="3" y="4" width="18" height="17" rx="2" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M16 2v4M8 2v4M3 10h18"/>
                                                </svg>
                                                <span>Mulai: <strong class="text-slate-700">{{ $exam->start_time->translatedFormat('d M Y, H:i') }}</strong></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/>
                                                </svg>
                                                <span>Selesai: <strong class="text-slate-700">{{ $exam->end_time->translatedFormat('d M Y, H:i') }}</strong></span>
                                            </div>
                                        </div>

                                        {{-- STATS STRIP 3 KOLOM --}}
                                        <div class="mt-4 grid grid-cols-2 rounded-xl bg-slate-50 p-2.5 text-center border border-slate-100">
                                            <div class="border-r border-slate-200">
                                                <p class="font-serif text-base font-black text-slate-800">{{ $exam->questions_count }}</p>
                                                <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Butir Soal</p>
                                            </div>
                                            <div>
                                                <p class="font-serif text-base font-black text-slate-800">{{ $exam->duration_minutes }}m</p>
                                                <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Durasi Pengerjaan</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CTA BUTTON --}}
                                    <div class="mt-5 pt-3 border-t border-slate-100">
                                        <div class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-2.5 text-xs font-bold transition {{ $btnClass }}">
                                            <span>{{ $btnLabel }}</span>
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- SEARCH / FILTER EMPTY RESULT --}}
                    <div id="noExamResult" class="hidden overflow-hidden rounded-3xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <p class="font-bold text-slate-700">Tidak ada ujian yang sesuai</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan filter atau kata kunci pencarian yang lain.</p>
                    </div>

                    {{-- NAVIGASI SLIDE / PAGINASI KARTU UJIAN (MAKSIMAL 6 PER SLIDE) --}}
                    <div id="siswaExamPagination" class="hidden"></div>
                @endif
            </section>

        </div>
    </div>

    {{-- SISWA EXAM SLIDE dan FILTER SCRIPT --}}
    @if($exams->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('siswaExamSearch');
                const filterBtns = Array.from(document.querySelectorAll('.exam-filter-btn'));
                const cards = Array.from(document.querySelectorAll('[data-exam-card]'));
                const empty = document.getElementById('noExamResult');
                const pagination = document.getElementById('siswaExamPagination');
                const grid = document.getElementById('siswaExamGrid');
                const perPage = 6;
                let activeFilter = 'all';
                let currentPage = 1;
                let filteredCards = [...cards];

                function updateFilterTabsUI() {
                    filterBtns.forEach(btn => {
                        const f = btn.getAttribute('data-filter');
                        if (f === activeFilter) {
                            btn.className = 'exam-filter-btn rounded-lg px-3 py-1.5 text-xs transition bg-white text-[#123B60] shadow-sm font-bold';
                        } else {
                            btn.className = 'exam-filter-btn rounded-lg px-3 py-1.5 text-xs transition text-slate-500 hover:text-slate-800 font-medium';
                        }
                    });
                }

                function filterAndPaginate() {
                    const query = (searchInput?.value || '').trim().toLowerCase();

                    filteredCards = cards.filter(card => {
                        const status = card.getAttribute('data-status') || '';
                        const searchContent = (card.getAttribute('data-search') || '').toLowerCase();

                        let matchFilter = false;
                        if (activeFilter === 'all') {
                            matchFilter = true;
                        } else if (activeFilter === 'active') {
                            matchFilter = (status === 'active');
                        } else if (activeFilter === 'done') {
                            matchFilter = (status === 'done');
                        }

                        let matchSearch = true;
                        if (query) {
                            matchSearch = searchContent.includes(query);
                        }

                        return matchFilter && matchSearch;
                    });

                    render();
                }

                function render() {
                    const total = filteredCards.length;
                    const totalPages = Math.ceil(total / perPage) || 1;

                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    // Sembunyikan semua kartu terlebih dahulu
                    cards.forEach(c => c.classList.add('hidden'));

                    // Tampilkan hanya kartu pada slide saat ini
                    filteredCards.forEach((c, idx) => {
                        if (idx >= start && idx < end) {
                            c.classList.remove('hidden');
                        }
                    });

                    // Tampilkan pesan kosong jika tidak ada hasil
                    if (empty) {
                        empty.classList.toggle('hidden', total > 0);
                    }

                    // Render tombol navigasi slide jika total > 6
                    renderPagination(total, totalPages);
                }

                function renderPagination(total, totalPages) {
                    if (!pagination) return;

                    if (totalPages <= 1) {
                        pagination.innerHTML = '';
                        pagination.classList.add('hidden');
                        return;
                    }

                    pagination.classList.remove('hidden');

                    const startItem = Math.min((currentPage - 1) * perPage + 1, total);
                    const endItem = Math.min(currentPage * perPage, total);

                    let pillsHtml = '';
                    for (let p = 1; p <= totalPages; p++) {
                        const isActive = p === currentPage;
                        pillsHtml += `
                            <button type="button"
                                    data-page="${p}"
                                    class="siswa-exam-page-btn flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold transition shadow-sm ${
                                        isActive
                                            ? 'bg-[#123B60] text-white shadow-md'
                                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-900'
                                    }">
                                ${p}
                            </button>
                        `;
                    }

                    pagination.innerHTML = `
                        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/80 p-4 backdrop-blur-sm shadow-sm">
                            <div class="text-xs font-semibold text-slate-500">
                                Menampilkan <strong class="text-slate-800">${startItem} - ${endItem}</strong> dari <strong class="text-slate-800">${total}</strong> ujian
                                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                    Slide ${currentPage} dari ${totalPages}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                        id="siswaExamPrevBtn"
                                        ${currentPage <= 1 ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1 rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-sm ${
                                            currentPage <= 1
                                                ? 'bg-slate-100 text-slate-300 cursor-not-allowed border border-slate-200'
                                                : 'bg-white text-slate-700 hover:bg-[#123B60] hover:text-white border border-slate-200'
                                        }">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    <span>Sebelumnya</span>
                                </button>

                                <div class="flex items-center gap-1 mx-1">
                                    ${pillsHtml}
                                </div>

                                <button type="button"
                                        id="siswaExamNextBtn"
                                        ${currentPage >= totalPages ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1 rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-sm ${
                                            currentPage >= totalPages
                                                ? 'bg-slate-100 text-slate-300 cursor-not-allowed border border-slate-200'
                                                : 'bg-white text-slate-700 hover:bg-[#123B60] hover:text-white border border-slate-200'
                                        }">
                                    <span>Berikutnya</span>
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;

                    // Bind Prev / Next
                    document.getElementById('siswaExamPrevBtn')?.addEventListener('click', function () {
                        if (currentPage > 1) {
                            currentPage--;
                            render();
                            scrollToGrid();
                        }
                    });

                    document.getElementById('siswaExamNextBtn')?.addEventListener('click', function () {
                        if (currentPage < totalPages) {
                            currentPage++;
                            render();
                            scrollToGrid();
                        }
                    });

                    // Bind page buttons
                    pagination.querySelectorAll('.siswa-exam-page-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const p = parseInt(this.getAttribute('data-page'), 10);
                            if (p && p !== currentPage) {
                                currentPage = p;
                                render();
                                scrollToGrid();
                            }
                        });
                    });
                }

                function scrollToGrid() {
                    if (grid) {
                        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }

                // Bind Filter Buttons
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        activeFilter = this.getAttribute('data-filter') || 'all';
                        currentPage = 1;
                        updateFilterTabsUI();
                        filterAndPaginate();
                    });
                });

                // Bind Search Input
                searchInput?.addEventListener('input', function () {
                    currentPage = 1;
                    filterAndPaginate();
                });

                // Inisialisasi awal
                filterAndPaginate();
            });
        </script>
    @endif

</x-app-layout>
