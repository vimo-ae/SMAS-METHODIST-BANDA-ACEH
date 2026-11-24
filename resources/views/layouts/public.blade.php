
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SMAS Methodist Banda Aceh | Portal Akademik')</title>

    <meta name="description" content="Portal E-Learning dan E-Exam SMAS Methodist Banda Aceh.">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" href="{{ asset('images/methodist-logo.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/methodist-logo.png') }}?v=2">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|playfair-display:600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $navLinks = [
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Fitur', 'href' => url('/') . '#fitur'],
        ['label' => 'Cek Nilai', 'href' => route('cek-nilai-ujian.index')],
        ['label' => 'Pengumuman', 'href' => url('/') . '#pengumuman'],
        ['label' => 'Kontak', 'href' => '#kontak'],
    ];
@endphp

<body class="flex min-h-screen flex-col bg-cream font-sans text-navy antialiased">

    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">
        Lewati ke konten
    </a>

    {{-- HEADER --}}
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur-xl">

        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">

            {{-- BRAND --}}
            <a href="{{ url('/') }}" class="group flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-50 ring-1 ring-slate-200 transition group-hover:bg-blue-50">
                    <img src="{{ asset('images/methodist-logo.png') }}"
                         alt="Logo SMAS Methodist Banda Aceh"
                         class="h-9 w-9 object-contain">
                </div>

                <div class="min-w-0">
                    <p class="truncate text-sm font-extrabold tracking-tight text-navy sm:text-base">
                        SMAS Methodist
                    </p>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500 sm:text-xs">
                        Banda Aceh
                    </p>
                </div>
            </a>

            {{-- DESKTOP NAVIGATION --}}
            <nav aria-label="Menu utama" class="hidden items-center gap-1 lg:flex">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       class="rounded-lg px-3.5 py-2.5 text-[13px] font-semibold text-slate-600 transition hover:bg-blue-50 hover:text-navy focus:outline-none focus:ring-2 focus:ring-softblue">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- LOGIN dan MOBILE BUTTON --}}
            <div class="flex shrink-0 items-center gap-3">
                <a href="{{ route('login') }}"
                   class="hidden items-center gap-2 rounded-lg bg-navy px-5 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-softblue focus:ring-offset-2 sm:inline-flex">
                    Masuk Portal
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                    </svg>
                </a>

                <button id="menu-btn"
                        type="button"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                        aria-label="Buka menu"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-navy transition hover:bg-slate-50 lg:hidden">

                    <svg id="menu-open-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>

                    <svg id="menu-close-icon" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- MOBILE NAVIGATION --}}
        <nav id="mobile-menu"
             aria-label="Menu seluler"
             class="hidden border-t border-slate-100 bg-white px-4 pb-5 pt-3 shadow-lg lg:hidden">

            <div class="mx-auto max-w-7xl space-y-1">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-blue-50 hover:text-navy">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <a href="{{ route('login') }}"
                   class="mt-3 flex items-center justify-center rounded-lg bg-navy px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                    Masuk Portal
                </a>
            </div>
        </nav>
    </header>

    {{-- MAIN CONTENT --}}
    <main id="main" class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer id="kontak" class="scroll-mt-24 bg-[#101C35] text-white">

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">

            <div class="grid gap-8 md:grid-cols-2 md:gap-10 lg:grid-cols-12 lg:gap-12">

                {{-- SCHOOL IDENTITY --}}
                <div class="lg:col-span-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-1.5">
                            <img src="{{ asset('images/methodist-logo.png') }}"
                                 alt="Logo sekolah"
                                 class="h-full w-full object-contain">
                        </div>

                        <div>
                            <h2 class="font-serif text-lg font-bold text-white">
                                SMAS Methodist
                            </h2>
                            <p class="text-xs font-medium tracking-wider text-softblue">
                                BANDA ACEH
                            </p>
                        </div>
                    </div>

                    <p class="mt-6 max-w-sm text-sm leading-7 text-slate-300">
                        Portal pembelajaran digital yang mengintegrasikan kegiatan belajar, pengumpulan tugas, ujian, dan pengelolaan nilai dalam satu platform.
                    </p>

                    <div class="mt-6 inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-slate-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Portal Akademik Sekolah
                    </div>
                </div>

                {{-- CONTACT --}}
                <div class="lg:col-span-4">
                    <h3 class="text-sm font-bold tracking-wide text-white">
                        Informasi Kontak
                    </h3>

                    <div class="mt-6 space-y-5">

                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-softblue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-12a7 7 0 10-14 0c0 6.5 7 12 7 12z"/>
                                    <circle cx="12" cy="9" r="2"/>
                                </svg>
                            </div>

                            <p class="text-sm leading-6 text-slate-300">
                                Jl. Pocut Baren No. 3, Kelurahan Mulia / Laksana, Kecamatan Kuta Alam, Banda Aceh, Aceh 23123.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-softblue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.3a1 1 0 01.95.68l1.2 3.6a1 1 0 01-.5 1.2l-2.1 1.1a11 11 0 005.57 5.57l1.1-2.1a1 1 0 011.2-.5l3.6 1.2a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.16 21 3 14.84 3 7V5z"/>
                                </svg>
                            </div>

                            <p class="text-sm text-slate-300">0852-6010-9442</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-softblue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
                                </svg>
                            </div>

                            <a href="mailto:official_smasmethodist@gmail.com"
                               class="break-all text-sm text-slate-300 transition hover:text-white">
                                official_smasmethodist@gmail.com
                            </a>
                        </div>

                    </div>
                </div>

                {{-- QUICK LINKS --}}
                <div class="lg:col-span-3">
                    <h3 class="text-sm font-bold tracking-wide text-white">
                        Navigasi
                    </h3>

                    <ul class="mt-6 space-y-3">
                        @foreach ($navLinks as $link)
                            @if ($link['label'] !== 'Kontak')
                                <li>
                                    <a href="{{ $link['href'] }}"
                                       class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                        <span class="text-softblue">→</span>
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach

                        <li>
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                <span class="text-softblue">→</span>
                                Masuk Portal
                            </a>
                        </li>

                        <li>
                            <a href="https://smasmethodistbna.sch.id/"
                               target="_blank"
                               rel="noopener"
                               class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                <span class="text-softblue">→</span>
                                Website Sekolah
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- FOOTER BOTTOM --}}
            <div class="mt-10 flex flex-col items-center gap-2 border-t border-white/10 pt-6 text-center text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:text-left lg:mt-14">
                <p>
                    &copy; {{ date('Y') }} SMAS Methodist Banda Aceh. All rights reserved.
                </p>

                <p>
                    Portal E-Learning dan E-Exam
                </p>
            </div>

        </div>
    </footer>

    {{-- MOBILE MENU SCRIPT --}}
    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');

        if (btn && menu) {
            btn.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('hidden') === false;

                btn.setAttribute('aria-expanded', String(isOpen));
                btn.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');

                openIcon?.classList.toggle('hidden', isOpen);
                closeIcon?.classList.toggle('hidden', !isOpen);
            });

            menu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                    btn.setAttribute('aria-label', 'Buka menu');

                    openIcon?.classList.remove('hidden');
                    closeIcon?.classList.add('hidden');
                });
            });
        }
    </script>

</body>
</html>
