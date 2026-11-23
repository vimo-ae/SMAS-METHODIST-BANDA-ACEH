@section('title', strtoupper($class->schoolClass->name ?? 'Kelas') . ' - ' . strtoupper($class->subject->name ?? $class->subject_code) . ' | E-Class Guru')

<x-app-layout>
    @php
        $now = now();

        $status = function ($e) use ($now) {
            if (! $e->is_published) {
                return ['Draf', 'bg-slate-100 text-slate-600 border-slate-200'];
            }

            $start = \Illuminate\Support\Carbon::parse($e->start_time);
            $end = \Illuminate\Support\Carbon::parse($e->end_time);

            if ($now->lt($start)) {
                return ['Belum dibuka', 'bg-blue-50 text-blue-700 border-blue-200'];
            }

            if ($now->gt($end)) {
                return ['Ditutup', 'bg-slate-100 text-slate-600 border-slate-200'];
            }

            return ['Berlangsung', 'bg-emerald-50 text-emerald-700 border-emerald-200'];
        };

        $formatOptions = [
            'pdf' => 'PDF',
            'doc' => 'DOC',
            'docx' => 'DOCX',
            'ppt' => 'PPT',
            'pptx' => 'PPTX',
            'xls' => 'XLS',
            'xlsx' => 'XLSX',
            'jpg' => 'JPG / JPEG',
            'png' => 'PNG',
            'zip' => 'ZIP',
            'txt' => 'TXT',
        ];

        $oldFormats = old('allowed_formats', ['pdf']);

        $materialCount = $class->materials->count();
        $assignmentCount = $class->assignments->count();
        $examCount = $exams->count();
        $studentCount = $students->count();

        $subjectName = $class->subject->name ?? $class->subject_code;
        $className = $class->schoolClass->name ?? 'Kelas '.$class->class_id;
        $fullClassTitle = strtoupper($className) . ' - ' . strtoupper($subjectName);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    E-Class Guru · SMAS Methodist Banda Aceh
                </p>
                <h2 class="text-xl font-extrabold text-slate-800 leading-tight">
                    {{ $fullClassTitle }}
                </h2>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Kelas Aktif · {{ $class->academic_year }} ({{ ucfirst($class->semester) }})
                </span>
            </div>
        </div>
    </x-slot>

    {{-- QUILL STYLE --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap');

        .lms-page {
            font-family: 'DM Sans', sans-serif;
        }

        .lms-card {
            border: 1px solid #e8eef6;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(15, 53, 100, .045);
        }

        .lms-input {
            width: 100%;
            border: 1px solid #dce5f0;
            border-radius: 12px;
            padding: 11px 13px;
            font-size: 13px;
            color: #1e293b;
            transition: .2s;
        }

        .lms-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }

        .lms-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .lms-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 11px;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 700;
            transition: .2s;
        }

        .lms-btn-primary {
            background: #2563eb;
            color: white;
        }

        .lms-btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .lms-btn-light {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .lms-btn-light:hover {
            background: #dbeafe;
        }

        .lms-section-title {
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -.35px;
        }

        .rich {
            font-size: 13px;
            line-height: 1.75;
            color: #64748b;
            overflow-wrap: anywhere;
        }

        .rich ul {
            list-style: disc;
            padding-left: 1.25rem;
        }

        .rich ol {
            list-style: decimal;
            padding-left: 1.25rem;
        }

        .rich h2 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #334155;
            margin-top: .5rem;
        }

        .rich h3 {
            font-size: .95rem;
            font-weight: 700;
            color: #334155;
            margin-top: .5rem;
        }

        .rich a {
            color: #0369a1;
            text-decoration: underline;
        }

        .rich img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 12px 0;
            border-radius: 12px;
        }

        .ql-editor {
            min-height: 140px;
            font-size: 13px;
            line-height: 1.7;
        }

        .ql-container {
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            border-color: #dce5f0 !important;
        }

        .ql-toolbar {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-color: #dce5f0 !important;
            background: #f8fafc;
        }

        .ql-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .lms-stat {
            transition: .2s;
        }

        .lms-stat:hover {
            transform: translateY(-3px);
        }

        .lms-item {
            transition: background .2s;
        }

        .lms-item:hover {
            background: #f8fbff;
        }

        .lms-modal {
            backdrop-filter: blur(5px);
        }

        .lms-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .lms-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        @media(max-width: 640px) {
            .lms-hero {
                padding: 22px !important;
            }

            .lms-hero h1 {
                font-size: 24px !important;
            }
        }
    </style>

    <div class="lms-page min-h-screen bg-[#f5f8fc] py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB dan TOMBOL KEMBALI --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('guru.classes.index') }}"
                       class="transition hover:text-blue-600">
                        E-Class
                    </a>
                    <span>/</span>
                    <span class="font-bold text-blue-700">{{ $fullClassTitle }}</span>
                </div>

                <a href="{{ route('guru.classes.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>←</span>
                    Kembali ke Daftar E-Class
                </a>
            </div>

            {{-- ==========================================
                 HERO SECTION: NAMA KELAS SANGAT JELAS (CONTOH: X-IPA 1 - GEOGRAFI)
            =========================================== --}}
            <section class="lms-hero relative overflow-hidden rounded-[25px] bg-[#10243E] p-7 text-white shadow-xl sm:p-9">
                @php
                    $subjectLower = strtolower($subjectName);
                    $subjectCoverImage = null;
                    $subjectCoverMap = [
                        'matematika' => 'matematika.jpg', 'biologi' => 'biologi.jpg', 'kimia' => 'kimia.jpg',
                        'fisika' => 'fisika.jpg', 'indonesia' => 'indonesia.jpg', 'bahasa indonesia' => 'indonesia.jpg',
                        'informatika' => 'informatika.jpg', 'inggris' => 'inggris.svg', 'sejarah' => 'sejarah.svg',
                        'geografi' => 'geografi.svg', 'ekonomi' => 'ekonomi.svg', 'sosiologi' => 'sosiologi.svg',
                        'agama' => 'agama.svg', 'pjok' => 'pjok.svg', 'seni' => 'seni-budaya.svg', 'ppkn' => 'ppkn.svg',
                    ];
                    foreach ($subjectCoverMap as $k => $f) {
                        if (str_contains($subjectLower, $k)) {
                            $jpg = preg_replace('/\.(svg|png)$/i', '.jpg', $f);
                            $svg = preg_replace('/\.(jpg|png)$/i', '.svg', $f);
                            if (file_exists(public_path('images/subjects/' . $jpg))) {
                                $subjectCoverImage = asset('images/subjects/' . $jpg);
                                break;
                            } elseif (file_exists(public_path('images/subjects/' . $svg))) {
                                $subjectCoverImage = asset('images/subjects/' . $svg);
                                break;
                            }
                        }
                    }
                    if (!$subjectCoverImage && file_exists(public_path('images/hero-school.jpg'))) {
                        $subjectCoverImage = asset('images/hero-school.jpg');
                    }
                @endphp

                @if($subjectCoverImage)
                    <img src="{{ $subjectCoverImage }}" alt="{{ $subjectName }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/95 via-[#102a4e]/85 to-[#164273]/65"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123d80] via-[#1756a8] to-[#2879df]"></div>
                @endif

                <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
                <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-blue-400/10 blur-2xl"></div>

                <div class="relative z-10">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[1.5px] text-blue-100">
                            RUANG PEMBELAJARAN GURU
                        </span>
                        <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-[10px] font-bold text-emerald-100">
                            ● KELAS AKTIF
                        </span>
                    </div>

                    <div class="max-w-3xl">
                        <p class="mb-1 text-sm font-semibold text-blue-100">
                            Tahun Ajaran {{ $class->academic_year }} · Semester {{ ucfirst($class->semester) }}
                        </p>

                        {{-- NAMA KELAS DAN MATA PELAJARAN --}}
                        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
                            {{ $fullClassTitle }}
                        </h1>

                        <p class="mt-3 max-w-2xl text-xs sm:text-sm leading-6 sm:leading-7 text-blue-100">
                            Kelola seluruh aktivitas kelas, bagikan materi pelajaran, atur tugas siswa,
                            pantau jadwal mengajar resmi, dan bimbing siswa dalam ruang kelas ini.
                        </p>
                    </div>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-200">
                                Tahun Ajaran
                            </p>
                            <p class="mt-1 text-sm font-bold">
                                {{ $class->academic_year }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-200">
                                Semester
                            </p>
                            <p class="mt-1 text-sm font-bold">
                                {{ ucfirst($class->semester) }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-2.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-200">
                                Kode Bergabung Siswa
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <span id="join-code"
                                      class="font-mono text-sm font-extrabold tracking-[2px]">
                                    {{ $class->join_code }}
                                </span>
                                <button type="button"
                                        onclick="copyJoinCode()"
                                        class="rounded-md bg-white/20 px-2 py-1 text-[10px] font-bold transition hover:bg-white/30">
                                    Salin Kode
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI CEPAT --}}
                    <div class="mt-5 flex flex-wrap gap-2.5 pt-2">
                        <a href="{{ route('guru.classes.materials.create', $cst) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/15 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-white/25">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <span>＋ Tambah Materi</span>
                        </a>

                        <a href="{{ route('guru.classes.assignments.create', $cst) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/15 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-white/25">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                            <span>＋ Buat Tugas</span>
                        </a>

                        <a href="{{ route('guru.exams.create') }}"
                           class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/15 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-white/25">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11M7 10h4M7 14h6M7 18h8"/>
                            </svg>
                            <span>＋ Buat Ujian</span>
                        </a>
                    </div>
                </div>
            </section>

            {{-- ==========================================
                 STATISTIK RINGKAS KELAS
            =========================================== --}}
            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="lms-card lms-stat p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500">
                            Materi
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-extrabold text-slate-800">
                        {{ $materialCount }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Materi pembelajaran</p>
                </div>

                <div class="lms-card lms-stat p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">
                            Tugas
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-extrabold text-slate-800">
                        {{ $assignmentCount }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Tugas aktif</p>
                </div>

                <div class="lms-card lms-stat p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11M7 10h4M7 14h6M7 18h8"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-violet-500">
                            Ujian
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-extrabold text-slate-800">
                        {{ $examCount }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Ujian dan kuis</p>
                </div>

                <div class="lms-card lms-stat p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                            Siswa
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-extrabold text-slate-800">
                        {{ $studentCount }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Siswa bergabung</p>
                </div>
            </section>

            {{-- ==========================================
                 TEACHING SCHEDULE (JADWAL MENGAJAR KELAS INI - DIPERBAIKI LENGKAP)
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-blue-50/70 to-white px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h2 class="lms-section-title">Jadwal Mengajar</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Jadwal tatap muka resmi untuk kelas <span class="font-bold text-[#123B60]">{{ $fullClassTitle }}</span>.
                        </p>
                    </div>

                    <span class="rounded-full bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-blue-700 border border-blue-100">
                        {{ $schedule->count() }} Jadwal Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3.5 font-bold">Hari</th>
                                <th class="px-6 py-3.5 font-bold">Jam Pembelajaran</th>
                                <th class="px-6 py-3.5 font-bold">Durasi</th>
                                <th class="px-6 py-3.5 font-bold">Mata Pelajaran</th>
                                <th class="px-6 py-3.5 font-bold">Kelas</th>
                                <th class="px-6 py-3.5 font-bold">Semester dan T.A.</th>
                                <th class="px-6 py-3.5 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($schedule as $slot)
                                @php
                                    $startTime = substr($slot->start_time, 0, 5);
                                    $endTime = substr($slot->end_time, 0, 5);

                                    $startCarbon = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $slot->start_time);
                                    $endCarbon = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $slot->end_time);
                                    $durationMinutes = $startCarbon->diffInMinutes($endCarbon);
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 font-bold text-blue-800">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                                            </svg>
                                            <span>{{ ucfirst($slot->day) }}</span>
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-extrabold text-slate-800">
                                        {{ $startTime }} – {{ $endTime }} WIB
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                        {{ $durationMinutes }} Menit
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-[#123B60]">
                                        {{ $slot->subject_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-800">
                                        {{ $slot->class_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-500">
                                        Semester {{ ucfirst($slot->semester) }} · {{ $slot->academic_year }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                        <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-600">Belum ada jadwal mengajar untuk kelas ini.</p>
                                        <p class="text-xs text-slate-400 mt-1">Jadwal tatap muka yang ditetapkan oleh bagian kurikulum akan ditampilkan di tabel ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- NOTIFIKASI ERROR / SUKSES --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700">
                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Periksa kembali isian formulir Anda:</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==========================================
                 DAFTAR MATERI PEMBELAJARAN
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-[#267BB5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <h2 class="lms-section-title">Materi Pembelajaran</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">
                            Daftar seluruh materi yang telah dibagikan kepada siswa pada kelas ini.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-blue-700">
                            {{ $materialCount }} Materi
                        </span>

                        <a href="{{ route('guru.classes.materials.create', $cst) }}"
                           class="lms-btn lms-btn-primary">
                            <span>＋</span>
                            Tambah Materi
                        </a>
                    </div>
                </div>

                @forelse ($class->materials->sortByDesc('uploaded_at') as $material)
                    <div class="lms-item flex flex-col gap-4 border-b border-slate-100 px-6 py-5 last:border-0 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#123B60]">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-extrabold text-slate-800">
                                    {{ $material->title }}
                                </h3>

                                @if ($material->description)
                                    <div class="rich mt-2">
                                        {!! $material->description !!}
                                    </div>
                                @endif

                                <p class="mt-2 text-[11px] text-slate-400">
                                    Diunggah: {{ optional($material->uploaded_at)->format('d M Y, H:i') ?? $material->created_at?->format('d M Y, H:i') }} WIB
                                </p>

                                @if ($material->file_path)
                                    <a href="{{ asset('storage/' . $material->file_path) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100">
                                        <span>↗</span>
                                        Lihat / Unduh Berkas
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button"
                                    class="js-edit-material lms-btn lms-btn-light"
                                    data-action="{{ route('guru.classes.materials.update', ['cst' => $cst->cst_id, 'material' => $material->material_id]) }}"
                                    data-title="{{ $material->title }}"
                                    data-description="{{ $material->description }}">
                                Edit
                            </button>

                            <form action="{{ route('guru.classes.materials.destroy', ['cst' => $cst->cst_id, 'material' => $material->material_id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="lms-btn border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 mb-3">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-bold text-slate-700">
                            Belum Ada Materi Pembelajaran
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Materi yang Anda unggah akan ditampilkan di sini.
                        </p>
                        <a href="{{ route('guru.classes.materials.create', $cst) }}"
                           class="mt-4 lms-btn lms-btn-primary">
                            <span>＋</span>
                            Tambah Materi Pertama
                        </a>
                    </div>
                @endforelse
            </section>

            {{-- ==========================================
                 DAFTAR TUGAS KELAS INI
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <h2 class="lms-section-title">Daftar Tugas</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">
                            Pantau instruksi, batas pengumpulan, dan nilai jawaban siswa.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-indigo-50 px-3.5 py-1.5 text-xs font-bold text-indigo-700">
                            {{ $assignmentCount }} Tugas
                        </span>

                        <a href="{{ route('guru.classes.assignments.create', $cst) }}"
                           class="lms-btn lms-btn-primary bg-indigo-600 hover:bg-indigo-700">
                            <span>＋</span>
                            Buat Tugas Baru
                        </a>
                    </div>
                </div>

                @forelse ($class->assignments->sortByDesc('created_at') as $assignment)
                    @php
                        $formats = $assignment->allowed_formats ?? [];
                    @endphp

                    <div class="lms-item flex flex-col gap-4 border-b border-slate-100 px-6 py-5 last:border-0 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex min-w-0 gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-extrabold text-slate-800">
                                    {{ $assignment->title }}
                                </h3>

                                @if ($assignment->description)
                                    <div class="rich mt-2">
                                        {!! $assignment->description !!}
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-[10px] font-semibold text-slate-600">
                                        <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                        </svg>
                                        <span>Batas: {{ $assignment->due_date?->format('d M Y, H:i') }} WIB</span>
                                    </span>

                                    <span class="rounded-lg bg-blue-50 px-3 py-1.5 text-[10px] font-semibold text-blue-700">
                                        Format: {{ $formats ? strtoupper(implode(', ', $formats)) : 'Semua format' }}
                                    </span>

                                    <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-[10px] font-semibold text-emerald-700">
                                        {{ $assignment->submissions->count() }} Pengumpulan
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 xl:shrink-0">
                            {{-- TOMBOL LINK LANGSUNG KE PENGUMPULAN TUGAS --}}
                            <a href="{{ route('guru.classes.assignments.show', $assignment) }}"
                               class="lms-btn lms-btn-primary">
                                Periksa Pengumpulan →
                            </a>

                            <button type="button"
                                    class="js-edit-assignment lms-btn lms-btn-light"
                                    data-action="{{ route('guru.classes.assignments.update', $assignment) }}"
                                    data-title="{{ $assignment->title }}"
                                    data-description="{{ $assignment->description }}"
                                    data-due="{{ $assignment->due_date?->format('Y-m-d\TH:i') }}"
                                    data-formats="{{ json_encode($formats) }}">
                                Edit
                            </button>

                            <form action="{{ route('guru.classes.assignments.destroy', $assignment) }}"
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini beserta seluruh data pengumpulannya?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="lms-btn border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-bold text-slate-700">
                            Belum Ada Tugas
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Buat tugas pertama untuk kelas ini agar siswa dapat mengumpulkan pekerjaannya.
                        </p>
                        <a href="{{ route('guru.classes.assignments.create', $cst) }}"
                           class="mt-4 lms-btn lms-btn-primary bg-indigo-600 hover:bg-indigo-700">
                            <span>＋</span>
                            Buat Tugas Pertama
                        </a>
                    </div>
                @endforelse
            </section>

            {{-- ==========================================
                 STUDENT DIRECTORY (DIREKTORI SISWA KELAS INI - LENGKAP DENGAN SEARCH dan AKSI)
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-white px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <h2 class="lms-section-title">Direktori Siswa (Daftar Siswa Kelas)</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Daftar seluruh siswa yang terdaftar aktif dalam kelas <span class="font-bold text-[#123B60]">{{ $fullClassTitle }}</span>.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                            {{ $studentCount }} Siswa Terdaftar
                        </span>
                    </div>
                </div>

                {{-- FILTER SEARCH SISWA --}}
                <div class="border-b border-slate-100 bg-slate-50/60 p-4">
                    <div class="relative max-w-md">
                        <input type="text"
                               id="student-search-input"
                               placeholder="Cari siswa berdasarkan nama atau NIS..."
                               class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-4 text-xs text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <span class="pointer-events-none absolute left-3 top-2.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3.5 font-bold">No.</th>
                                <th class="px-6 py-3.5 font-bold">Nama Lengkap</th>
                                <th class="px-6 py-3.5 font-bold">NIS / NISN</th>
                                <th class="px-6 py-3.5 font-bold">Jenis Kelamin</th>
                                <th class="px-6 py-3.5 font-bold">Tanggal Bergabung</th>
                                <th class="px-6 py-3.5 font-bold">Status</th>
                                <th class="px-6 py-3.5 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="student-table-body" class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($students as $index => $student)
                                @php
                                    $studentName = $student->user->name ?? $student->name ?? 'Siswa';
                                    $genderLabel = match(strtoupper($student->gender ?? '')) {
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        default => '-'
                                    };
                                    $joinedDate = $student->pivot->joined_at ? \Illuminate\Support\Carbon::parse($student->pivot->joined_at)->format('d M Y, H:i') : '-';
                                @endphp
                                <tr class="student-row hover:bg-slate-50/80 transition">
                                    <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-400">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-800">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xs font-extrabold text-blue-700">
                                                {{ strtoupper(substr($studentName, 0, 1)) }}
                                            </span>
                                            <div>
                                                <p class="font-bold text-slate-800 student-name">{{ $studentName }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $student->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-mono text-slate-600 student-nis">
                                        {{ $student->nis }}
                                        @if($student->nisn)
                                            <span class="block text-[10px] text-slate-400">NISN: {{ $student->nisn }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-sky-50 text-sky-700' : 'bg-pink-50 text-pink-700' }}">
                                            {{ $genderLabel }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-500">
                                        {{ $joinedDate }} WIB
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <form action="{{ route('guru.classes.students.remove', ['cst' => $cst, 'nis' => $student->nis]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Keluarkan siswa {{ $studentName }} (NIS: {{ $student->nis }}) dari kelas ini?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-[11px] font-bold text-rose-600 hover:bg-rose-100 transition"
                                                    title="Keluarkan siswa dari kelas">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>Keluarkan</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-600">Belum ada siswa yang bergabung di kelas ini.</p>
                                        <p class="text-xs text-slate-400 mt-1">Bagikan kode kelas <strong>{{ $class->join_code }}</strong> kepada siswa agar mereka dapat mendaftar ke kelas ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- ==========================================
                 LATEST GRADES (REKAP NILAI SISWA KELAS INI - DIPERBAIKI)
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-amber-50/70 to-white px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <h2 class="lms-section-title">Rekap Nilai Siswa</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Rekap perolehan nilai tugas dan ujian untuk kelas <span class="font-bold text-[#123B60]">{{ $fullClassTitle }}</span>.
                        </p>
                    </div>

                    <span class="rounded-full bg-amber-50 px-3.5 py-1.5 text-xs font-bold text-amber-800 border border-amber-200">
                        {{ $gradebook->count() }} Data Nilai
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3.5 font-bold">Nama Siswa</th>
                                <th class="px-6 py-3.5 font-bold">Tugas / Ujian</th>
                                <th class="px-6 py-3.5 font-bold">Kategori Nilai</th>
                                <th class="px-6 py-3.5 font-bold">Nilai</th>
                                <th class="px-6 py-3.5 font-bold">Predikat</th>
                                <th class="px-6 py-3.5 font-bold">Tanggal Dinilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($gradebook as $grade)
                                @php
                                    $predBadge = match($grade->predicate) {
                                        'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'B' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'C' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'D' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        default => 'bg-rose-50 text-rose-700 border-rose-200',
                                    };
                                    $typeBadge = match(strtolower($grade->grade_type)) {
                                        'tugas' => 'bg-indigo-50 text-indigo-700',
                                        'kuis' => 'bg-violet-50 text-violet-700',
                                        'uts' => 'bg-sky-50 text-sky-700',
                                        'uas' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ $grade->student_name }}</p>
                                        <p class="text-[10px] text-slate-400">NIS: {{ $grade->nis }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-[#123B60]">
                                        {{ $grade->source_title }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $typeBadge }}">
                                            {{ $grade->grade_type }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-base font-extrabold text-[#123B60]">
                                            {{ $grade->score }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">/ 100</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full font-bold text-xs border {{ $predBadge }}">
                                            {{ $grade->predicate }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-400">
                                        {{ $grade->graded_at ? \Illuminate\Support\Carbon::parse($grade->graded_at)->format('d M Y, H:i') : '-' }} WIB
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">
                                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-600">Belum ada nilai yang tercatat untuk kelas ini.</p>
                                        <p class="text-xs text-slate-400 mt-1">Nilai akan otomatis muncul di tabel ini setelah Anda menilai tugas atau menyelesaikan sesi ujian siswa.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- ==========================================
                 DAFTAR UJIAN KELAS INI
            =========================================== --}}
            <section class="lms-card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-[#4338A8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11M7 10h4M7 14h6M7 18h8"/>
                            </svg>
                            <h2 class="lms-section-title">Ujian dan Kuis</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">
                            Daftar ujian dan kuis online untuk kelas <span class="font-bold text-[#123B60]">{{ $fullClassTitle }}</span>.
                        </p>
                    </div>

                    <a href="{{ route('guru.exams.index') }}"
                       class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">
                        Buka Semua di E-Exam →
                    </a>
                </div>

                @forelse ($exams as $e)
                    @php
                        [$label, $cls] = $status($e);
                    @endphp

                    <div class="border-b border-slate-100 p-6 last:border-0 hover:bg-slate-50/50 transition">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800">
                                    {{ $e->title }}
                                </h3>
                                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    {{ $e->exam_type }} · KKM: {{ $e->pass_score }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold border {{ $cls }}">
                                    {{ $label }}
                                </span>

                                <a href="{{ route('guru.exams.show', $e) }}"
                                   class="inline-flex items-center gap-1 rounded-xl bg-indigo-50 px-3 py-1.5 text-xs font-bold text-[#4338A8] hover:bg-indigo-100 transition">
                                    Kelola Soal →
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Jumlah Soal</p>
                                <p class="mt-1 text-sm font-extrabold text-slate-800">
                                    {{ $e->questions->count() }} Butir
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Durasi</p>
                                <p class="mt-1 text-sm font-extrabold text-slate-800">
                                    {{ $e->duration_minutes }} Menit
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Waktu Buka</p>
                                <p class="mt-1 text-xs font-bold text-slate-800">
                                    {{ \Illuminate\Support\Carbon::parse($e->start_time)->format('d M Y, H:i') }} WIB
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Waktu Tutup</p>
                                <p class="mt-1 text-xs font-bold text-slate-800">
                                    {{ \Illuminate\Support\Carbon::parse($e->end_time)->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-[#4338A8] mb-3">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11M7 10h4M7 14h6M7 18h8"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-bold text-slate-700">
                            Belum Ada Ujian untuk Kelas Ini
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Ujian yang dibuat untuk kelas ini akan ditampilkan di bagian ini.
                        </p>
                        <a href="{{ route('guru.exams.create') }}"
                           class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white hover:bg-[#0B2B48] transition">
                            <span>＋</span>
                            Buat Ujian Baru
                        </a>
                    </div>
                @endforelse
            </section>

            <div class="pb-4 text-center">
                <p class="text-[11px] text-slate-400">
                    Methodist Learning Space · Ruang Kerja Guru · SMAS Methodist Banda Aceh
                </p>
            </div>

        </div>
    </div>

    {{-- MODAL EDIT MATERI --}}
    <div id="modal-material"
         class="lms-modal fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/50 p-4">

        <div class="my-8 w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Edit Materi</h2>
                    <p class="mt-1 text-xs text-slate-400">Perbarui informasi materi pembelajaran.</p>
                </div>

                <button type="button"
                        data-close-modal
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500 hover:bg-slate-200">
                    &times;
                </button>
            </div>

            <form id="form-material-edit"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="lms-label">Judul Materi</label>
                    <input name="title"
                           id="edit-material-title"
                           required
                           maxlength="150"
                           class="lms-input">
                </div>

                <div>
                    <label class="lms-label">Deskripsi</label>
                    <div id="editor-material-edit"></div>
                    <input type="hidden"
                           name="description"
                           id="input-material-edit">
                </div>

                <div>
                    <label class="lms-label">Ganti Berkas Materi</label>
                    <input type="file"
                           name="file"
                           data-allowed="pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,mp4,zip"
                           data-max-mb="20"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4,.zip"
                           class="block w-full rounded-xl border border-slate-200 p-3 text-xs">
                    <p class="mt-2 text-[10px] text-slate-400">
                        Biarkan kosong jika tidak ingin mengganti berkas yang sudah ada.
                    </p>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button"
                            data-close-modal
                            class="lms-btn border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>

                    <button type="submit"
                            class="lms-btn lms-btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT TUGAS --}}
    <div id="modal-assignment"
         class="lms-modal fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/50 p-4">

        <div class="my-8 w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Edit Tugas</h2>
                    <p class="mt-1 text-xs text-slate-400">Perbarui instruksi dan batas pengumpulan tugas.</p>
                </div>

                <button type="button"
                        data-close-modal
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500 hover:bg-slate-200">
                    &times;
                </button>
            </div>

            <form id="form-assignment-edit"
                  method="POST"
                  class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="lms-label">Judul Tugas</label>
                    <input name="title"
                           id="edit-assignment-title"
                           required
                           maxlength="150"
                           class="lms-input">
                </div>

                <div>
                    <label class="lms-label">Instruksi Tugas</label>
                    <div id="editor-assignment-edit"></div>
                    <input type="hidden"
                           name="description"
                           id="input-assignment-edit">
                </div>

                <div>
                    <label class="lms-label">Batas Pengumpulan (Tenggat)</label>
                    <input type="datetime-local"
                           name="due_date"
                           id="edit-assignment-due"
                           required
                           class="lms-input">
                </div>

                <div>
                    <label class="lms-label">Format File yang Diizinkan</label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3"
                         data-format-group>
                        @foreach ($formatOptions as $ext => $label)
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-600">
                                <input type="checkbox"
                                       name="allowed_formats[]"
                                       value="{{ $ext }}"
                                       class="edit-fmt rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button"
                            data-close-modal
                            class="lms-btn border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>

                    <button type="submit"
                            class="lms-btn lms-btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="{{ asset('js/file-validator.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // PENCARIAN SISWA (LIVE FILTER DI TABEL DIREKTORI SISWA)
            const studentSearchInput = document.getElementById('student-search-input');
            if (studentSearchInput) {
                studentSearchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#student-table-body .student-row');
                    rows.forEach(row => {
                        const nameEl = row.querySelector('.student-name');
                        const nisEl = row.querySelector('.student-nis');
                        const nameText = nameEl ? nameEl.textContent.toLowerCase() : '';
                        const nisText = nisEl ? nisEl.textContent.toLowerCase() : '';
                        if (nameText.includes(query) || nisText.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // COPY KODE KELAS
            window.copyJoinCode = function () {
                const code = document.getElementById('join-code').innerText.trim();

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(code).then(() => {
                        alert('Kode kelas berhasil disalin: ' + code);
                    });
                } else {
                    const temp = document.createElement('textarea');
                    temp.value = code;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    temp.remove();
                    alert('Kode kelas berhasil disalin: ' + code);
                }
            };

            // KONFIGURASI UPLOAD GAMBAR
            const uploadUrl = @json(route('guru.classes.editor-images.store', $cst));
            const csrfToken = @json(csrf_token());

            function uploadQuillImage(quill) {
                const fileInput = document.createElement('input');

                fileInput.type = 'file';
                fileInput.accept = 'image/jpeg,image/png,image/gif,image/webp';

                fileInput.click();

                fileInput.onchange = async function () {
                    const file = fileInput.files[0];

                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran gambar maksimal 5 MB.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Gagal mengunggah gambar.');
                        }

                        const range = quill.getSelection(true);

                        quill.insertEmbed(range.index, 'image', result.url, 'user');
                        quill.setSelection(range.index + 1);

                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Terjadi kesalahan saat upload gambar.');
                    }
                };
            }

            function makeEditor(selector) {
                const quill = new Quill(selector, {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['link', 'image', 'blockquote'],
                                ['clean']
                            ],
                            handlers: {
                                image: function () {
                                    uploadQuillImage(quill);
                                }
                            }
                        }
                    }
                });

                return quill;
            }

            // SINKRONISASI EDITOR
            function bindSync(form, quill, input) {
                if (!form || !quill || !input) return;
                form.addEventListener('submit', () => {
                    const empty =
                        quill.getText().trim() === '' &&
                        !quill.root.querySelector('img');

                    input.value = empty ? '' : quill.root.innerHTML;
                });
            }

            // VALIDASI FORMAT
            function requireFormat(form) {
                if (!form) return;
                form.addEventListener('submit', (event) => {
                    const checked = form.querySelector(
                        '[data-format-group] input:checked'
                    );

                    if (!checked) {
                        event.preventDefault();
                        alert('Pilih minimal satu format file untuk pengumpulan tugas.');
                    }
                });
            }

            // EDITOR MODAL
            const qMatEdit = makeEditor('#editor-material-edit');
            const qAsgEdit = makeEditor('#editor-assignment-edit');

            const formMatEdit = document.getElementById('form-material-edit');
            const formAsgEdit = document.getElementById('form-assignment-edit');

            bindSync(
                formMatEdit,
                qMatEdit,
                document.getElementById('input-material-edit')
            );

            bindSync(
                formAsgEdit,
                qAsgEdit,
                document.getElementById('input-assignment-edit')
            );

            requireFormat(formAsgEdit);

            // MODAL HANDLING
            const openModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = (modal) => {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            document.querySelectorAll('[data-close-modal]').forEach(button => {
                button.addEventListener('click', () => {
                    closeModal(button.closest('[id^=modal-]'));
                });
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    document.querySelectorAll('[id^=modal-]').forEach(closeModal);
                }
            });

            // EDIT MATERI
            document.querySelectorAll('.js-edit-material').forEach(button => {
                button.addEventListener('click', () => {
                    if (!button.dataset.action) return;

                    formMatEdit.action = button.dataset.action;

                    document.getElementById('edit-material-title').value =
                        button.dataset.title || '';

                    qMatEdit.setContents([]);

                    qMatEdit.clipboard.dangerouslyPasteHTML(
                        button.dataset.description || ''
                    );

                    formMatEdit.querySelector('[type=file]').value = '';

                    openModal('modal-material');
                });
            });

            // EDIT TUGAS
            document.querySelectorAll('.js-edit-assignment').forEach(button => {
                button.addEventListener('click', () => {
                    formAsgEdit.action = button.dataset.action;

                    document.getElementById('edit-assignment-title').value =
                        button.dataset.title || '';

                    document.getElementById('edit-assignment-due').value =
                        button.dataset.due || '';

                    qAsgEdit.setContents([]);

                    qAsgEdit.clipboard.dangerouslyPasteHTML(
                        button.dataset.description || ''
                    );

                    let formats = [];

                    try {
                        formats = JSON.parse(button.dataset.formats || '[]');
                    } catch (error) {
                        console.error('Format tugas tidak valid:', error);
                    }

                    formAsgEdit.querySelectorAll('.edit-fmt').forEach(checkbox => {
                        checkbox.checked = formats.includes(checkbox.value);
                    });

                    openModal('modal-assignment');
                });
            });

        });
    </script>
</x-app-layout>
