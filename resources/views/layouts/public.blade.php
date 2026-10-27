<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'SMAS Methodist Banda Aceh | Portal E-Learning & E-Exam')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|playfair-display:600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    @php
        $navLinks = [
            ['label' => 'Fitur',       'href' => url('/') . '#fitur'],
            ['label' => 'Cek Nilai',   'href' => url('/') . '#cek-nilai'],
            ['label' => 'Pengumuman',  'href' => url('/') . '#pengumuman'],
            ['label' => 'Kontak',      'href' => '#kontak'],
        ];
    @endphp

    <body class="flex min-h-screen flex-col bg-cream font-sans text-navy antialiased">

        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">
            Lewati ke konten
        </a>

        {{-- TOP BAR --}}
        <div class="hidden bg-navy text-xs text-white/80 md:block">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
                <span>Jl. Pocut Baren No. 3, Kota Banda Aceh, Aceh</span>
                <span>official_smasmethodist@gmail.com</span>
            </div>
        </div>

        {{-- NAVBAR --}}
        <header class="sticky top-0 z-50 border-b border-navy/10 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-4 lg:px-8">
                <a href="{{ url('/') }}" class="font-serif text-lg font-bold text-navy sm:text-xl">
                    SMAS Methodist <span class="hidden text-softblue sm:inline">Banda Aceh</span>
                </a>

                <nav aria-label="Menu utama" class="hidden items-center gap-8 text-sm font-medium md:flex">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}" class="transition hover:text-softblue">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('login') }}"
                       class="rounded-lg bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-navy/90 focus:outline-none focus:ring-2 focus:ring-softblue focus:ring-offset-2 sm:px-5">
                        Masuk
                    </a>
                    <button id="menu-btn" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu"
                            class="flex h-11 w-11 items-center justify-center rounded-lg border border-navy/20 md:hidden">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <nav id="mobile-menu" aria-label="Menu seluler" class="hidden border-t border-navy/10 bg-white px-4 pb-4 pt-2 md:hidden">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-cream">{{ $link['label'] }}</a>
                @endforeach
            </nav>
        </header>

        <main id="main" class="flex-1">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer id="kontak" class="scroll-mt-24 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div>
                    <p class="font-serif text-lg font-bold text-navy">SMAS Methodist Banda Aceh</p>
                    <p class="mt-3 text-sm leading-6 text-navy/70">Portal e-learning dan ujian daring untuk siswa, guru, dan orang tua.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy">Kontak</p>
                    <ul class="mt-3 space-y-2 text-sm text-navy/70">
                        <li>Jl. Pocut Baren No. 3, Kota Banda Aceh, Aceh</li>
                        <li>0852-6010-9442</li>
                        <li class="break-all">official_smasmethodist@gmail.com</li>
                        <li>NSPN 10105395</li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy">Tautan</p>
                    <ul class="mt-3 space-y-2 text-sm text-navy/70">
                        @foreach ($navLinks as $link)
                            @if ($link['label'] !== 'Kontak')
                                <li><a href="{{ $link['href'] }}" class="hover:text-softblue">{{ $link['label'] }}</a></li>
                            @endif
                        @endforeach
                        <li><a href="{{ route('login') }}" class="hover:text-softblue">Masuk</a></li>
                        <li><a href="https://smasmethodistbna.sch.id/" target="_blank" rel="noopener" class="hover:text-softblue">Website Sekolah</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-navy/10 px-4 py-5 text-center text-xs text-navy/60">
                &copy; {{ date('Y') }} SMAS Methodist Banda Aceh
            </div>
        </footer>

        <script>
            const btn = document.getElementById('menu-btn');
            const menu = document.getElementById('mobile-menu');

            btn.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('hidden') === false;
                btn.setAttribute('aria-expanded', isOpen);
            });

            menu.querySelectorAll('a').forEach((a) => {
                a.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                });
            });
        </script>
    </body>
</html>