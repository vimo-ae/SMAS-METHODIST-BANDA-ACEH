<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">Methodist Learning Space</p>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    E-Class Siswa
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ auth()->user()->student->schoolClass->name ?? 'Siswa Aktif' }} &bull; TP {{ date('Y') }}/{{ date('Y')+1 }}
                </span>
            </div>
        </div>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .eclass-serif { font-family: 'Playfair Display', Georgia, serif; }

        .eclass-card {
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

        .eclass-card:hover {
            transform: translateY(-6px);
            border-color: #BBD7E9;
            box-shadow: 0 18px 38px rgba(18,59,96,.10);
        }

        .eclass-card-cover {
            position: relative;
            min-height: 160px;
            padding: 20px;
            color: white;
            background-color: #123B60;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .eclass-cover-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eclass-card:hover .eclass-cover-image {
            transform: scale(1.08);
        }

        .eclass-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(8, 22, 40, 0.90) 0%, rgba(18, 59, 96, 0.74) 60%, rgba(18, 59, 96, 0.40) 100%);
            pointer-events: none;
        }

        .eclass-cover-symbol {
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

        .eclass-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: space-between;
        }

        .eclass-card-stat {
            padding: 10px 6px;
            border-radius: 12px;
            background: #F5F8FA;
            text-align: center;
            border: 1px solid #EDF2F6;
        }

        .eclass-card-stat strong {
            display: block;
            color: #123B60;
            font-family: 'Playfair Display', serif;
            font-size: 19px;
            font-weight: 700;
            line-height: 1.1;
        }

        .eclass-card-stat span {
            display: block;
            margin-top: 2px;
            color: #84919E;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .eclass-card:hover .eclass-card-stat {
            background: #EEF4F9;
            border-color: #DDE7F0;
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
                'symbol' => 'MLS', 'category' => 'KELAS AKADEMIK', 'image' => null,
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
    @endphp

    <div class="min-h-screen bg-[#FAF9F6] pb-16">
        <div class="mx-auto max-w-[1400px] space-y-8 px-4 py-6 sm:px-6 lg:px-8">

            {{-- ==========================================
                 HERO SECTION RESMI: E-CLASS SISWA
                 ========================================== --}}
            <section class="relative overflow-hidden rounded-[28px] bg-[#10243E] p-8 sm:p-10 text-white shadow-xl">
                @if(file_exists(public_path('images/hero-school.jpg')))
                    <img src="{{ asset('images/hero-school.jpg') }}" alt="SMAS Methodist" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/95 via-[#102a4e]/85 to-[#164273]/65"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123d80] via-[#1756a8] to-[#2879df]"></div>
                @endif

                <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
                <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-blue-400/10 blur-2xl"></div>

                <div class="relative z-10">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[1.5px] text-blue-100 backdrop-blur-md">
                            PORTAL AKADEMIK SISWA
                        </span>
                        <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-[10px] font-bold text-emerald-100 backdrop-blur-md">
                            ● SEMESTER AKTIF
                        </span>
                    </div>

                    <div class="max-w-3xl">
                        <p class="mb-1 text-sm font-semibold text-blue-100">
                            METHODIST LEARNING SPACE
                        </p>
                        <h1 class="eclass-serif text-3xl font-extrabold tracking-tight sm:text-5xl text-white leading-tight">
                            E-Class.
                        </h1>
                        <p class="mt-2 text-xl font-medium text-cyan-200 eclass-serif italic">
                            Belajar &bull; Bertumbuh &bull; Berprestasi
                        </p>
                        <p class="mt-3 max-w-xl text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                            Akses materi pembelajaran resmi, kerjakan tugas akademik, dan evaluasi capaian belajar Anda di SMAS Methodist Banda Aceh.
                        </p>
                    </div>

                    {{-- STATS PILLS HERO --}}
                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-xl">
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-white">{{ $joined->count() }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Kelas Diikuti</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-white truncate block">
                                {{ auth()->user()->student->schoolClass->name ?? 'Kelas X' }}
                            </span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Rombongan Belajar</p>
                        </div>
                        <div class="col-span-2 sm:col-span-1 rounded-2xl bg-white/10 p-3.5 border border-white/15 backdrop-blur-md">
                            <span class="text-2xl font-black text-white">100%</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-0.5">Akses Digital</p>
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

            {{-- GABUNG KELAS DENGAN KODE (JOIN CLASSROOM) --}}
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60]">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-[.18em] text-[#267BB5]">
                                GABUNG RUANG BELAJAR
                            </span>
                            <h2 class="mt-1 text-lg font-black text-slate-800">
                                Punya Kode Bergabung Kelas?
                            </h2>
                            <p class="mt-1 max-w-xl text-xs sm:text-sm text-slate-500">
                                Masukkan 6-8 digit kode kelas resmi yang dibagikan oleh guru pengampu Anda untuk mulai mengakses materi dan ujian.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('siswa.classes.join') }}" method="POST" class="flex w-full flex-col gap-2.5 sm:flex-row lg:w-auto">
                        @csrf
                        <input
                            id="join_code"
                            name="join_code"
                            value="{{ old('join_code') }}"
                            maxlength="8"
                            required
                            autocomplete="off"
                            placeholder="CONTOH: KIM101"
                            class="min-w-0 rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-extrabold uppercase tracking-[.16em] text-slate-800 placeholder:font-medium placeholder:tracking-normal focus:border-[#267BB5] focus:bg-white focus:ring-[#267BB5] sm:w-56">

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#123B60] px-6 py-3 text-xs font-bold text-white shadow-md transition hover:bg-[#267BB5] active:scale-95">
                            <span>Gabung Kelas</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </section>

            {{-- ==========================================
                 DAFTAR KELAS YANG DIIKUTI
                 ========================================== --}}
            <section id="daftar-kelas" class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-1.5 w-6 rounded-full bg-[#123B60]"></span>
                            <p class="text-[10px] font-extrabold uppercase tracking-[.2em] text-[#267BB5]">Ruang Belajar Anda</p>
                        </div>
                        <h2 class="mt-1 text-2xl font-black text-slate-800 sm:text-3xl">Kelas yang Saya Ikuti</h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">Pilih mata pelajaran untuk melihat materi pelajaran, tugas, dan rekap nilai Anda.</p>
                    </div>

                    @if($joined->isNotEmpty())
                        <div class="relative w-full sm:max-w-xs">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                id="classSearch"
                                type="search"
                                placeholder="Cari mata pelajaran atau guru..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium text-slate-700 shadow-sm transition focus:border-[#267BB5] focus:ring-[#267BB5]">
                        </div>
                    @endif
                </div>

                @if($joined->isEmpty())
                    {{-- EMPTY STATE --}}
                    <div class="overflow-hidden rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60]">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-black text-slate-800">Belum Ada Kelas yang Diikuti</h3>
                        <p class="mx-auto mt-1 max-w-md text-xs sm:text-sm text-slate-400">
                            Anda belum bergabung ke dalam kelas pembelajaran mana pun. Masukkan kode kelas dari guru Anda pada formulir di atas untuk memulai.
                        </p>
                    </div>
                @else
                    {{-- GRID KELAS ELEGAN DENGAN COVER GAMBAR MAPEL --}}
                    <div id="classGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($joined as $c)
                            @php
                                $subjectName = $c->subject->name ?? $c->subject_code ?? 'Mata Pelajaran';
                                $theme = $getSubjectTheme($subjectName, $c->subject_code);
                                $className = $c->schoolClass->name ?? ('Kelas ' . $c->class_id);
                                $teacherName = $c->teacher->user->name ?? $c->teacher->name ?? 'Guru Pengampu';
                            @endphp

                            <a href="{{ route('siswa.classes.show', $c->cst_id) }}"
                               data-class-card
                               data-search="{{ strtolower($subjectName . ' ' . $className . ' ' . $teacherName) }}"
                               class="eclass-card group">

                                {{-- COVER GAMBAR MAPEL --}}
                                <div class="eclass-card-cover" style="{{ empty($theme['imageUrl']) ? 'background: ' . $theme['gradient'] : '' }}">
                                    @if(!empty($theme['imageUrl']))
                                        <img src="{{ $theme['imageUrl'] }}" alt="{{ $subjectName }}" class="eclass-cover-image">
                                        <div class="eclass-cover-overlay"></div>
                                    @endif

                                    {{-- HEADER BAR: KELAS dan SEMESTER --}}
                                    <div class="relative z-10 flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center rounded-lg bg-white/20 backdrop-blur-md px-2.5 py-1 text-[10px] font-bold text-white border border-white/30">
                                            {{ $className }}
                                        </span>
                                        <span class="rounded-full bg-white/15 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-semibold text-white/90 border border-white/20">
                                            Semester {{ ucfirst($c->semester ?? 'Ganjil') }}
                                        </span>
                                    </div>

                                    {{-- FOOTER BAR: KATEGORI dan MONOGRAM --}}
                                    <div class="relative z-10 flex items-end justify-between gap-3 mt-6">
                                        <div>
                                            <p class="text-[9px] font-extrabold uppercase tracking-[.15em] text-white/80">
                                                {{ $theme['category'] }}
                                            </p>
                                            <h3 class="eclass-serif text-xl font-bold text-white leading-tight mt-0.5 drop-shadow">
                                                {{ $subjectName }}
                                            </h3>
                                        </div>

                                        <div class="eclass-cover-symbol drop-shadow-md">
                                            {{ $theme['symbol'] }}
                                        </div>
                                    </div>
                                </div>

                                {{-- BODY KELAS --}}
                                <div class="eclass-card-body space-y-4">
                                    {{-- GURU PENGAJAR --}}
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60] font-black text-xs">
                                            {{ strtoupper(substr($teacherName, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Guru Pengampu</p>
                                            <p class="truncate text-xs font-bold text-slate-800">
                                                {{ $teacherName }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- STATISTIK 3 KOLOM --}}
                                    <div class="grid grid-cols-3 gap-2 pt-1">
                                        <div class="eclass-card-stat">
                                            <strong>{{ $c->materials_count ?? 0 }}</strong>
                                            <span>Materi</span>
                                        </div>
                                        <div class="eclass-card-stat">
                                            <strong>{{ $c->assignments_count ?? 0 }}</strong>
                                            <span>Tugas</span>
                                        </div>
                                        <div class="eclass-card-stat">
                                            <strong>{{ $c->exams_count ?? 0 }}</strong>
                                            <span>Ujian</span>
                                        </div>
                                    </div>

                                    {{-- FOOTER KELAS --}}
                                    <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-xs">
                                        <span class="text-[11px] text-slate-400 font-medium">
                                            Tahun {{ $c->academic_year ?? '2025/2026' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 font-bold text-[#123B60] group-hover:text-[#267BB5] transition">
                                            <span>Buka Ruang Kelas</span>
                                            <span class="transition transform group-hover:translate-x-1">&rarr;</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- SEARCH EMPTY RESULT --}}
                    <div id="noClassResult" class="hidden rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                        <p class="font-bold text-slate-700">Mata pelajaran tidak ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                    </div>

                    {{-- NAVIGASI SLIDE / PAGINASI KARTU KELAS (MAKSIMAL 6 PER SLIDE) --}}
                    <div id="classPagination" class="hidden"></div>
                @endif
            </section>

        </div>
    </div>

    {{-- CLASS SLIDE dan SEARCH SCRIPT --}}
    @if($joined->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('classSearch');
                const cards = Array.from(document.querySelectorAll('[data-class-card]'));
                const empty = document.getElementById('noClassResult');
                const pagination = document.getElementById('classPagination');
                const perPage = 6;
                let currentPage = 1;
                let filteredCards = [...cards];

                function render() {
                    const total = filteredCards.length;
                    const totalPages = Math.ceil(total / perPage) || 1;

                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    // Sembunyikan semua kartu terlebih dahulu
                    cards.forEach(c => c.classList.add('hidden'));

                    // Tampilkan hanya kartu pada slide/halaman saat ini
                    filteredCards.forEach((c, idx) => {
                        if (idx >= start && idx < end) {
                            c.classList.remove('hidden');
                        }
                    });

                    // Tampilkan pesan kosong jika tidak ada kartu yang cocok
                    if (empty) {
                        empty.classList.toggle('hidden', total > 0);
                    }

                    // Tampilkan tombol navigasi slide jika lebih dari 6 kartu
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
                                    title="Buka Slide ${p}"
                                    class="h-9 w-9 rounded-xl text-xs font-black transition flex items-center justify-center ${isActive ? 'bg-[#123B60] text-white shadow-md shadow-blue-900/20 ring-2 ring-[#123B60]/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-[#123B60] shadow-sm'}">
                                ${p}
                            </button>
                        `;
                    }

                    pagination.innerHTML = `
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-200/90">
                            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>Menampilkan <strong class="text-slate-800 font-bold">${startItem}–${endItem}</strong> dari <strong class="text-slate-800 font-bold">${total}</strong> mata pelajaran</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">Slide ${currentPage} / ${totalPages}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        data-page="${currentPage - 1}"
                                        ${currentPage === 1 ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border text-xs font-bold transition ${currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm active:scale-95'}">
                                    <span>&larr;</span>
                                    <span>Sebelumnya</span>
                                </button>

                                <div class="flex items-center gap-1.5">
                                    ${pillsHtml}
                                </div>

                                <button type="button"
                                        data-page="${currentPage + 1}"
                                        ${currentPage === totalPages ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border text-xs font-bold transition ${currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm active:scale-95'}">
                                    <span>Berikutnya</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                        </div>
                    `;

                    pagination.querySelectorAll('button[data-page]').forEach(btn => {
                        btn.addEventListener('click', function () {
                            if (this.disabled) return;
                            currentPage = parseInt(this.getAttribute('data-page'));
                            render();

                            const grid = document.getElementById('classGrid');
                            if (grid) {
                                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    });
                }

                if (search) {
                    search.addEventListener('input', function () {
                        const keyword = this.value.trim().toLowerCase();
                        if (!keyword) {
                            filteredCards = [...cards];
                        } else {
                            filteredCards = cards.filter(card => {
                                const searchableText = card.dataset.search || '';
                                return searchableText.includes(keyword);
                            });
                        }
                        currentPage = 1;
                        render();
                    });
                }

                render();
            });
        </script>
    @endif
</x-app-layout>
