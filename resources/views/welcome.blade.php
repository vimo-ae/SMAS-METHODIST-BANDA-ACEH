@extends('layouts.public')

@section('content')

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-navy">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-softblue/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-softblue/10 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8 lg:py-24">
            <div class="min-w-0">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.2em] text-softblue sm:text-sm">
                    Portal E-Learning & E-Exam
                </p>
                <h1 class="font-serif text-3xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Belajar dan ujian daring, lebih tertata dan adil.
                </h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-white/80 sm:text-lg sm:leading-8">
                    Materi, tugas, ujian, dan nilai siswa SMAS Methodist Banda Aceh dalam satu platform terpadu.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
                    <a href="{{ route('login') }}"
                       class="rounded-lg bg-cream px-6 py-3 text-center text-sm font-semibold text-navy shadow transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-navy">
                        Masuk ke akun
                    </a>
                    <a href="#fitur" class="py-2 text-center text-sm font-semibold text-softblue transition hover:text-white sm:text-left">
                        Lihat fitur &darr;
                    </a>
                </div>
            </div>

            {{-- KARTU CEK NILAI --}}
            <div id="cek-nilai" class="min-w-0 scroll-mt-28 rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-softblue sm:text-sm">Untuk Orang Tua</p>
                <h2 class="mt-2 font-serif text-2xl font-bold text-navy">Cek Nilai Siswa</h2>
                <p class="mt-2 text-sm leading-6 text-navy/70">
                    Pantau hasil belajar anak tanpa perlu membuat akun. Cukup masukkan data siswa untuk verifikasi.
                </p>

                {{-- TODO: hubungkan action ke route Cek Nilai kalau backend sudah ada --}}
                <form action="#" method="GET" class="mt-6 space-y-4">
                    <div>
                        <label for="nisn" class="block text-sm font-semibold text-navy">NISN</label>
                        <input id="nisn" name="nisn" type="text" inputmode="numeric" maxlength="10" autocomplete="off"
                               placeholder="Masukkan NISN siswa" required
                               class="mt-1 w-full rounded-lg border border-navy/20 bg-white px-4 py-3 text-base focus:border-softblue focus:outline-none focus:ring-2 focus:ring-softblue/40 sm:text-sm">
                    </div>
                    <div>
                        <label for="tgl_lahir" class="block text-sm font-semibold text-navy">Tanggal Lahir</label>
                        <input id="tgl_lahir" name="tgl_lahir" type="date" required
                               class="mt-1 w-full rounded-lg border border-navy/20 bg-white px-4 py-3 text-base focus:border-softblue focus:outline-none focus:ring-2 focus:ring-softblue/40 sm:text-sm">
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-navy px-5 py-3 text-sm font-semibold text-white transition hover:bg-navy/90 focus:outline-none focus:ring-2 focus:ring-softblue focus:ring-offset-2">
                        Lihat Nilai
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- STATISTIK --}}
    <section class="border-b border-navy/10 bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-3 divide-x divide-navy/10 px-4 py-6 text-center sm:px-6 sm:py-8 lg:px-8">
            <div class="px-2">
                <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">97</p>
                <p class="mt-1 text-xs font-medium text-navy/70 sm:text-sm">Siswa</p>
            </div>
            <div class="px-2">
                <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">18</p>
                <p class="mt-1 text-xs font-medium text-navy/70 sm:text-sm">Guru Mapel</p>
            </div>
            <div class="px-2">
                <p class="font-serif text-2xl font-bold text-navy sm:text-3xl">5</p>
                <p class="mt-1 text-xs font-medium text-navy/70 sm:text-sm">Tipe Soal Ujian</p>
            </div>
        </div>
    </section>

    {{-- FITUR --}}
    <section id="fitur" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-14 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-widest text-softblue sm:text-sm">Fitur Utama</p>
            <h2 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">Semua kebutuhan belajar dalam satu portal</h2>
            <p class="mt-4 leading-7 text-navy/70">Dari pembagian materi sampai rekap nilai, semuanya terhubung dan tersimpan terpusat.</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <article class="rounded-2xl border border-softblue/40 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-navy text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/></svg>
                </div>
                <h3 class="font-serif text-2xl font-semibold text-navy">E-Class</h3>
                <p class="mt-2 text-sm leading-6 text-navy/70">Ruang kelas digital untuk guru dan siswa.</p>
                <ul class="mt-5 space-y-2 text-sm text-navy/80">
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Materi dalam bentuk dokumen dan foto</li>
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Tugas dengan tenggat waktu dan pengumpulan online</li>
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Forum diskusi antara guru dan siswa</li>
                </ul>
            </article>

            <article class="rounded-2xl border border-softblue/40 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-navy text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-serif text-2xl font-semibold text-navy">E-Exam</h3>
                <p class="mt-2 text-sm leading-6 text-navy/70">Ujian daring dengan koreksi otomatis.</p>
                <ul class="mt-5 space-y-2 text-sm text-navy/80">
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Pilihan ganda, benar/salah, mencocokkan, dan esai</li>
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Soal dan pilihan jawaban diacak tiap siswa</li>
                    <li class="flex gap-2"><span class="text-softblue">&#10003;</span> Nilai objektif dihitung otomatis oleh sistem</li>
                </ul>
            </article>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-xl bg-white p-6 shadow-sm">
                <h3 class="font-serif text-lg font-semibold text-navy">Ujian yang jujur</h3>
                <p class="mt-2 text-sm leading-6 text-navy/70">Pindah tab, keluar layar penuh, atau salin-tempel tercatat dan dapat ditinjau guru.</p>
            </article>
            <article class="rounded-xl bg-white p-6 shadow-sm">
                <h3 class="font-serif text-lg font-semibold text-navy">Pantau langsung</h3>
                <p class="mt-2 text-sm leading-6 text-navy/70">Guru dapat melihat progres pengerjaan ujian siswa secara real-time.</p>
            </article>
            <article class="rounded-xl bg-white p-6 shadow-sm sm:col-span-2 lg:col-span-1">
                <h3 class="font-serif text-lg font-semibold text-navy">Buku nilai terpusat</h3>
                <p class="mt-2 text-sm leading-6 text-navy/70">Nilai tugas, kuis, UTS, dan UAS terekap rapi tanpa input manual berulang.</p>
            </article>
        </div>
    </section>

    {{-- UNTUK SIAPA --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-softblue sm:text-sm">Untuk Siapa</p>
                <h2 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">Setiap peran punya ruangnya sendiri</h2>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach ([
                    ['Siswa', 'Akses materi, kumpulkan tugas, dan ikuti ujian.'],
                    ['Guru', 'Kelola materi, tugas, bank soal, dan penilaian.'],
                    ['Admin', 'Kelola data siswa, guru, kelas, dan mata pelajaran.'],
                    ['Orang Tua', 'Lihat nilai anak lewat Cek Nilai, tanpa akun.'],
                ] as [$role, $desc])
                    <div class="rounded-xl border border-softblue/40 bg-cream p-6">
                        <p class="font-serif text-lg font-semibold text-navy">{{ $role }}</p>
                        <p class="mt-2 text-sm leading-6 text-navy/70">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PENGUMUMAN & BERITA --}}
    <section id="pengumuman" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-14 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-softblue sm:text-sm">Info Terbaru</p>
                <h2 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">Pengumuman & Berita</h2>
            </div>
            <a href="#" class="text-sm font-semibold text-navy transition hover:text-softblue">Lihat semua &rarr;</a>
        </div>

        {{-- TODO: ganti dengan @forelse ($announcements as $announcement) setelah fitur pengumuman dibuat --}}
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Pengumuman', '28 Okt 2026', 'Contoh judul pengumuman ujian', 'from-navy to-softblue'],
                ['Berita', '27 Okt 2026', 'Contoh judul berita kegiatan sekolah', 'from-softblue to-navy'],
                ['Pengumuman', '25 Okt 2026', 'Contoh judul pengumuman jadwal', 'from-navy via-navy/80 to-softblue'],
            ] as [$category, $date, $title, $gradient])
                <article class="flex flex-col overflow-hidden rounded-xl border border-softblue/40 bg-white shadow-sm transition motion-safe:hover:-translate-y-1 hover:shadow-md">
                    <div class="h-40 bg-gradient-to-br {{ $gradient }}"></div>
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            <span class="rounded-full bg-softblue/30 px-3 py-1 font-semibold text-navy">{{ $category }}</span>
                            <time class="text-navy/60">{{ $date }}</time>
                        </div>
                        <h3 class="mt-3 font-serif text-lg font-semibold text-navy">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-navy/70">Ringkasan singkat isi akan tampil di sini.</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-navy">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-4 py-12 sm:flex-row sm:items-center sm:px-6 sm:py-14 lg:px-8">
            <div>
                <h2 class="font-serif text-2xl font-bold text-white">Siap melanjutkan kegiatan belajar?</h2>
                <p class="mt-2 text-sm text-white/70">Masuk menggunakan akun sekolah Anda.</p>
            </div>
            <a href="{{ route('login') }}"
               class="w-full rounded-lg bg-cream px-6 py-3 text-center text-sm font-semibold text-navy transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-navy sm:w-auto">
                Login sekarang
            </a>
        </div>
    </section>

@endsection