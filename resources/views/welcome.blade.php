<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Methodist Banda Aceh') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4">
                <a href="{{ url('/') }}" class="text-lg font-bold tracking-tight text-indigo-950">
                    Methodist Banda Aceh
                </a>
                <a href="{{ route('login') }}" class="rounded-lg bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Masuk
                </a>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden bg-indigo-950">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-950"></div>
                <div class="relative mx-auto grid max-w-7xl gap-12 px-6 py-20 sm:py-28 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="mb-5 text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">
                            Sistem Pembelajaran Sekolah
                        </p>
                        <h1 class="max-w-2xl text-4xl font-bold leading-tight text-white sm:text-5xl">
                            Belajar, mengajar, dan berkembang bersama.
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-indigo-100">
                            Selamat datang di portal pembelajaran SMAS Methodist Banda Aceh. Akses kelas, materi, tugas, dan ujian dalam satu tempat.
                        </p>
                        <div class="mt-9 flex flex-wrap items-center gap-4">
                            <a href="{{ route('login') }}" class="rounded-lg bg-white px-6 py-3 text-sm font-semibold text-indigo-950 shadow transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-950">
                                Masuk ke akun
                            </a>
                            <span class="text-sm text-indigo-200">Untuk siswa, guru, dan admin</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/15 bg-white/10 p-8 shadow-2xl backdrop-blur-sm sm:p-10">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-200">Portal Akademik</p>
                        <h2 class="mt-3 text-2xl font-bold text-white">Semua kegiatan belajar lebih mudah diakses.</h2>
                        <p class="mt-4 leading-7 text-indigo-100">
                            Gunakan akun sekolah untuk membuka ruang belajar digital dan melihat aktivitas akademik sesuai peran Anda.
                        </p>
                        <a href="{{ route('login') }}" class="mt-8 inline-flex items-center gap-2 font-semibold text-white hover:text-indigo-200">
                            Lanjut ke halaman login <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-6 py-16 sm:py-20">
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Satu portal untuk kegiatan akademik</h2>
                    <p class="mt-4 leading-7 text-slate-600">
                        Platform ini membantu warga sekolah terhubung dengan kegiatan pembelajaran secara teratur dan praktis.
                    </p>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">E-Class</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Akses kelas, materi pembelajaran, dan aktivitas bersama guru.</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">E-Exam</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Lihat jadwal dan ikuti ujian atau kuis yang tersedia.</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Informasi Akademik</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Pantau kegiatan belajar dan informasi sekolah melalui akun Anda.</p>
                    </article>
                </div>
            </section>

            <section class="bg-indigo-50">
                <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-6 py-12 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-xl font-bold text-indigo-950">Siap melanjutkan kegiatan belajar?</h2>
                        <p class="mt-2 text-sm text-slate-600">Masuk menggunakan akun sekolah Anda.</p>
                    </div>
                    <a href="{{ route('login') }}" class="rounded-lg bg-indigo-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Login sekarang
                    </a>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-6 py-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} SMAS Methodist Banda Aceh
            </div>
        </footer>
    </body>
</html>