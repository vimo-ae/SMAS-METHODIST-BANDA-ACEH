@extends('layouts.public')

@section('content')

    {{-- HERO SECTION --}}
    <section class="relative isolate overflow-hidden bg-gradient-to-b from-[#0e172a] via-[#101C35] to-[#152542] text-white">
        {{-- Background Image dengan Overlay Gradient Modern --}}
        <div class="absolute inset-0 -z-10">
            <img
                src="{{ asset('images/hero-school.jpg') }}"
                alt="Gedung SMAS Methodist Banda Aceh"
                class="h-full w-full object-cover object-center opacity-30 mix-blend-luminosity filter blur-[1px]"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-[#0e172a]/95 via-[#101C35]/85 to-[#101C35]/70"></div>
            {{-- Ambient Glow Orbs --}}
            <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-softblue/15 blur-3xl"></div>
            <div class="absolute top-1/2 -right-24 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">

                {{-- Kolom Kiri: Copywriting dan CTA Utama --}}
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-white/90 backdrop-blur-md">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Portal Pembelajaran Terintegrasi</span>
                    </div>

                    <h1 class="mt-5 font-serif text-3xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Membangun Generasi Unggul, Berkarakter dan Berprestasi.
                    </h1>

                    <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-200 sm:text-lg">
                        Satu platform terpadu untuk kegiatan belajar daring, pengumpulan tugas, pelaksanaan ujian yang jujur, hingga pemantauan perkembangan hasil belajar siswa SMAS Methodist Banda Aceh.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-[#101C35] shadow-lg shadow-black/20 transition-all duration-200 hover:bg-slate-100 hover:shadow-xl hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-white">
                            Masuk Portal Belajar
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>

                        <a href="#fitur"
                           class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur-md transition-all duration-200 hover:bg-white/15 hover:-translate-y-0.5">
                            Jelajahi Fitur
                            <svg class="h-4 w-4 text-softblue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </a>
                    </div>

                    {{-- Badges Keunggulan Singkat --}}
                    <div class="mt-10 flex flex-wrap items-center gap-4 border-t border-white/10 pt-6 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span>E-Exam Anti-Curang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Rekap Nilai Otomatis</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Akses Wali Murid Cepat</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Card Cek Nilai Interaktif (Glassmorphism Elegan) --}}
                <div class="lg:col-span-5">
                    <div id="cek-nilai" class="relative rounded-2xl border border-white/20 bg-white/10 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex rounded-full bg-softblue/20 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-softblue">
                                Portal Orang Tua / Wali
                            </span>
                            <span class="flex items-center gap-1.5 text-xs text-slate-300">
                                <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Akses Mandiri
                            </span>
                        </div>

                        <h2 class="mt-4 font-serif text-2xl font-bold text-white">
                            Cek Nilai Siswa
                        </h2>
                        <p class="mt-1.5 text-xs leading-relaxed text-slate-300 sm:text-sm">
                            Pantau kemajuan akademik putra-putri Anda secara transparan. Buka halaman Cek Nilai, lalu masukkan NISN dan Tanggal Lahir siswa.
                        </p>

                        {{-- Tombol menuju halaman Cek Nilai (NISN dan tanggal lahir diisi di sana) --}}
                        <a href="{{ Route::has('cek-nilai-ujian.index') ? route('cek-nilai-ujian.index') : '#' }}"
                           class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-softblue to-sky-400 px-5 py-3.5 text-sm font-bold text-[#101C35] shadow-lg transition-all duration-200 hover:brightness-105 hover:shadow-xl active:scale-[0.99]">
                            Buka Halaman Cek Nilai
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- STATISTIK HIGHLIGHT --}}
    <section class="relative z-20 -mt-8 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-xl sm:grid-cols-4 sm:p-8">

            <div class="flex items-center gap-4 p-2 sm:border-r sm:border-slate-100">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-navy">
                    <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">97+</p>
                    <p class="text-xs font-semibold text-slate-500">Siswa Aktif</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-2 sm:border-r sm:border-slate-100">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-navy">
                    <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">18</p>
                    <p class="text-xs font-semibold text-slate-500">Guru Profesional</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-2 sm:border-r sm:border-slate-100">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-navy">
                    <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">5</p>
                    <p class="text-xs font-semibold text-slate-500">Format Ujian</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-2">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="font-serif text-2xl font-bold text-emerald-600 sm:text-3xl">100%</p>
                    <p class="text-xs font-semibold text-slate-500">Digital dan Terpusat</p>
                </div>
            </div>

        </div>
    </section>

    {{-- FITUR UTAMA (BENTO GRID MODERN) --}}
    <section id="fitur" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">

        <div class="mx-auto max-w-3xl text-center">
            <span class="rounded-full bg-softblue/15 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-navy">
                Fitur Unggulan
            </span>
            <h2 class="mt-3 font-serif text-3xl font-bold text-navy sm:text-4xl">
                Semua Kebutuhan Pembelajaran Dalam Satu Ekosistem
            </h2>
            <p class="mt-3 text-base text-slate-600 sm:text-lg">
                Dirancang untuk memudahkan interaksi belajar, menjaga integritas ujian, dan menyederhanakan pelaporan nilai secara digital.
            </p>
        </div>

        {{-- Bento Grid --}}
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-12">

            {{-- Feature Card 1: E-Class (7 Cols) --}}
            <article class="group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl lg:col-span-7">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy text-white shadow-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-navy">
                        Ruang Kelas Digital
                    </span>
                </div>

                <h3 class="mt-6 font-serif text-2xl font-bold text-navy">
                    E-Class: Belajar Fleksibel Tanpa Batas
                </h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Guru dapat membagikan materi terstruktur, memberikan tugas dengan batas pengumpulan otomatis, serta membuka diskusi aktif bersama siswa dalam ruang kelas virtual.
                </p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="flex items-start gap-2.5 rounded-xl bg-slate-50 p-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy/10 text-navy font-bold text-xs">&#10003;</div>
                        <span class="text-xs font-medium text-slate-700">Materi dokumen, video dan foto</span>
                    </div>
                    <div class="flex items-start gap-2.5 rounded-xl bg-slate-50 p-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy/10 text-navy font-bold text-xs">&#10003;</div>
                        <span class="text-xs font-medium text-slate-700">Pengumpulan tugas online</span>
                    </div>
                    <div class="flex items-start gap-2.5 rounded-xl bg-slate-50 p-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy/10 text-navy font-bold text-xs">&#10003;</div>
                        <span class="text-xs font-medium text-slate-700">Forum tanya-jawab interaktif</span>
                    </div>
                    <div class="flex items-start gap-2.5 rounded-xl bg-slate-50 p-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy/10 text-navy font-bold text-xs">&#10003;</div>
                        <span class="text-xs font-medium text-slate-700">Notifikasi batas waktu tugas</span>
                    </div>
                </div>
            </article>

            {{-- Feature Card 2: E-Exam (5 Cols) --}}
            <article class="group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl lg:col-span-5">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy text-white shadow-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Otomatis dan Jujur
                    </span>
                </div>

                <h3 class="mt-6 font-serif text-2xl font-bold text-navy">
                    E-Exam: Evaluasi Teruji dan Akurat
                </h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Sistem ujian daring komprehensif dengan pengacakan soal instan dan penilaian objektif otomatis tanpa hambatan koreksi manual.
                </p>

                <ul class="mt-6 space-y-3">
                    <li class="flex items-center gap-2.5 text-xs font-medium text-slate-700">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold">&#10003;</span>
                        Pilihan Ganda, Benar/Salah, Menjodohkan, dan Esai
                    </li>
                    <li class="flex items-center gap-2.5 text-xs font-medium text-slate-700">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold">&#10003;</span>
                        Pengacakan urutan soal dan opsi per siswa
                    </li>
                    <li class="flex items-center gap-2.5 text-xs font-medium text-slate-700">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold">&#10003;</span>
                        Nilai objektif langsung terhitung otomatis
                    </li>
                </ul>
            </article>

            {{-- 3 Mini Keunggulan --}}
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md lg:col-span-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h4 class="mt-4 font-serif text-lg font-bold text-navy">Integritas dan Anti-Curang</h4>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-600">
                    Deteksi otomatis saat siswa berganti tab browser, keluar dari mode layar penuh, atau melakukan salin-tempel.
                </p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md lg:col-span-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-navy">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h4 class="mt-4 font-serif text-lg font-bold text-navy">Live Monitoring Pengawas</h4>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-600">
                    Guru dapat memantau progres pengerjaan ujian, waktu tersisa, dan status keaktifan siswa secara real-time.
                </p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md lg:col-span-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="mt-4 font-serif text-lg font-bold text-navy">Buku Nilai Terpusat</h4>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-600">
                    Rekapitulasi otomatis untuk seluruh nilai tugas harian, kuis, UTS, dan UAS tanpa perlu input manual yang berulang.
                </p>
            </article>

        </div>
    </section>

    {{-- UNTUK SIAPA (EKOSISTEM SEKOLAH) --}}
    <section class="border-y border-slate-200/80 bg-white/70 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="rounded-full bg-softblue/15 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-navy">
                    Peran Pengguna
                </span>
                <h2 class="mt-3 font-serif text-3xl font-bold text-navy sm:text-4xl">
                    Dirancang Khusus untuk Seluruh Komunitas Sekolah
                </h2>
                <p class="mt-2 text-sm text-slate-600 sm:text-base">
                    Setiap peran mendapatkan ruang kerja terdedikasi sesuai wewenang dan kebutuhannya.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Siswa --}}
                <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-navy/40 hover:shadow-lg">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-navy">
                        <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-serif text-xl font-bold text-navy">Siswa</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        Akses modul pembelajaran, unggah tugas harian tepat waktu, ikuti ujian digital, dan tinjau progres hasil belajar.
                    </p>
                </div>

                {{-- Guru --}}
                <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-navy/40 hover:shadow-lg">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-navy">
                        <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-serif text-xl font-bold text-navy">Guru Mapel</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        Kelola bahan ajar, atur bank soal ujian dengan berbagai tipe, awasi ujian real-time, dan nilai tugas siswa secara praktis.
                    </p>
                </div>

                {{-- Admin --}}
                <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-navy/40 hover:shadow-lg">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-navy">
                        <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-serif text-xl font-bold text-navy">Administrator</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        Manajemen terpusat untuk akun pengguna, plotting kelas, jadwal akademik, pengumuman sekolah, serta sinkronisasi data master.
                    </p>
                </div>

                {{-- Orang Tua --}}
                <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-navy/40 hover:shadow-lg">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-navy">
                        <svg class="h-6 w-6 text-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-serif text-xl font-bold text-navy">Orang Tua / Wali</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        Mendukung pemantauan nilai anak kapan saja tanpa harus memiliki akun khusus melalui fitur Cek Nilai Siswa mandiri.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- PENGUMUMAN dan BERITA SEKOLAH --}}
    @php
        $hasDynamicAnnouncements = isset($announcements) && count($announcements) > 0;
        $sampleAnnouncements = [
            [
                'category' => 'Pengumuman',
                'date' => '28 Okt 2026',
                'title' => 'Jadwal Penilaian Akhir Semester (PAS) Berbasis E-Exam',
                'desc' => 'Informasi lengkap pelaksanaan PAS digital untuk seluruh tingkatan kelas X, XI, dan XII SMAS Methodist.',
                'gradient' => 'from-navy via-[#1f4068] to-softblue',
            ],
            [
                'category' => 'Berita',
                'date' => '27 Okt 2026',
                'title' => 'Prestasi Gemilang Siswa SMAS Methodist di Tingkat Provinsi',
                'desc' => 'Apresiasi kepada siswa yang berhasil menorehkan prestasi membanggakan pada ajang kompetisi sains tahun ini.',
                'gradient' => 'from-softblue via-sky-600 to-navy',
            ],
            [
                'category' => 'Akademik',
                'date' => '25 Okt 2026',
                'title' => 'Sosialisasi E-Learning dan Pembagian Akun Siswa Baru',
                'desc' => 'Panduan pengenalan sistem pembelajaran daring terpadu bagi siswa dan wali murid SMAS Methodist Banda Aceh.',
                'gradient' => 'from-slate-800 via-navy to-softblue',
            ],
        ];
    @endphp

    <section id="pengumuman" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">

        <div class="flex flex-col justify-between gap-4 border-b border-slate-200/80 pb-6 sm:flex-row sm:items-end">
            <div>
                <span class="rounded-full bg-softblue/15 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-navy">
                    Informasi Terkini
                </span>
                <h2 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">
                    Pengumuman dan Berita Sekolah
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Kabar terkini seputar kegiatan akademik dan pengumuman resmi SMAS Methodist Banda Aceh.
                </p>
            </div>

            <a href="{{ Route::has('announcements.index') ? route('announcements.index') : '#pengumuman' }}"
               class="group inline-flex items-center gap-1.5 text-sm font-bold text-navy transition hover:text-softblue">
                <span>Lihat Semua Kabar</span>
                <span class="transition group-hover:translate-x-1">&rarr;</span>
            </a>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @if ($hasDynamicAnnouncements)
                @foreach ($announcements as $announcement)
                    <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        @if (!empty($announcement->image_url))
                            <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="h-44 w-full object-cover">
                        @else
                            <div class="flex h-44 items-center justify-center bg-gradient-to-br from-navy via-slate-800 to-softblue p-4 text-center">
                                <span class="text-xs font-bold tracking-widest text-white/80 uppercase">SMAS Methodist Banda Aceh</span>
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center justify-between text-xs">
                                <span class="rounded-full bg-blue-50 px-3 py-1 font-semibold text-navy">{{ $announcement->category_label ?? 'Pengumuman' }}</span>
                                <time class="text-slate-400">{{ optional($announcement->published_at)->translatedFormat('d M Y') ?? date('d M Y') }}</time>
                            </div>
                            <h3 class="mt-3 font-serif text-lg font-bold text-navy group-hover:text-softblue">
                                {{ $announcement->title }}
                            </h3>
                            <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-600">
                                {{ $announcement->excerpt ?? '' }}
                            </p>
                            <div class="mt-auto pt-4">
                                @if (Route::has('announcements.show'))
                                    <a href="{{ route('announcements.show', $announcement->slug) }}" class="inline-flex items-center gap-1 text-xs font-bold text-navy hover:text-softblue">
                                        Baca selengkapnya &rarr;
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-navy">
                                        Baca selengkapnya &rarr;
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            @else
                @foreach ($sampleAnnouncements as $item)
                    <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="relative flex h-44 flex-col justify-end bg-gradient-to-br {{ $item['gradient'] }} p-5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-white/75">Warta Sekolah</span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center justify-between text-xs">
                                <span class="rounded-full bg-blue-50 px-3 py-1 font-semibold text-navy">{{ $item['category'] }}</span>
                                <time class="text-slate-400">{{ $item['date'] }}</time>
                            </div>
                            <h3 class="mt-3 font-serif text-lg font-bold text-navy group-hover:text-softblue">
                                {{ $item['title'] }}
                            </h3>
                            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                                {{ $item['desc'] }}
                            </p>
                            <div class="mt-auto pt-4">
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-navy group-hover:text-softblue">
                                    Baca selengkapnya &rarr;
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </section>

    {{-- CALL TO ACTION (CTA BANNER MODERN) --}}
    <section class="mx-auto mb-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-r from-[#0e172a] via-[#101C35] to-[#17365D] px-6 py-12 shadow-2xl sm:px-12 sm:py-16 lg:flex lg:items-center lg:justify-between lg:px-16">

            {{-- Ambient light effect --}}
            <div class="absolute -top-24 -right-24 -z-10 h-72 w-72 rounded-full bg-softblue/20 blur-3xl"></div>

            <div class="max-w-xl">
                <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-softblue backdrop-blur-md">
                    Portal Akademik Digital
                </span>
                <h2 class="mt-3 font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl">
                    Siap Memulai Pembelajaran Hari Ini?
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-300 sm:text-base">
                    Masuk dengan akun terdaftar untuk mengakses seluruh materi, jadwal kegiatan belajar, dan evaluasi hasil belajar Anda.
                </p>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0 lg:shrink-0">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-bold text-[#101C35] shadow-lg transition hover:bg-slate-100 hover:shadow-xl hover:-translate-y-0.5">
                    Masuk ke Akun
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>

                <a href="https://smasmethodistbna.sch.id/"
                   target="_blank"
                   rel="noopener"
                   class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">
                    Kunjungi Web Utama Sekolah
                </a>
            </div>

        </div>
    </section>

@endsection