@php
    $user = auth()->user();
    $roleLabel = $user?->roleLabel() ?? 'Portal Akademik';
    $navigation = match ($user?->role) {
        'guru' => [
            ['label' => 'Dashboard', 'route' => 'guru.dashboard', 'active' => 'guru.dashboard', 'icon' => 'dashboard'],
            ['label' => 'E-Class', 'route' => 'guru.classes.index', 'active' => 'guru.classes.*', 'icon' => 'class'],
            ['label' => 'E-Exam', 'route' => 'guru.exams.index', 'active' => 'guru.exams.*', 'icon' => 'exam'],
            ['label' => 'Roster Pelajaran', 'route' => 'guru.roster', 'active' => 'guru.roster*', 'icon' => 'roster'],
        ],
        'siswa' => [
            ['label' => 'Dashboard', 'route' => 'siswa.dashboard', 'active' => 'siswa.dashboard', 'icon' => 'dashboard'],
            ['label' => 'E-Class', 'route' => 'siswa.classes.index', 'active' => 'siswa.classes.*', 'icon' => 'class'],
            ['label' => 'E-Exam', 'route' => 'siswa.exams.index', 'active' => 'siswa.exams.*', 'icon' => 'exam'],
            ['label' => 'Roster Pelajaran', 'route' => 'siswa.roster', 'active' => 'siswa.roster*', 'icon' => 'roster'],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
        ],
        default => [],
    };
    $portalHomeRoute = $navigation[0]['route'] ?? 'home';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $roleLabel.' | SMAS Methodist')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen bg-[#f7f2e8] text-[#263747]">
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-[#0e172a]/60 lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-[#101C35] text-white shadow-2xl transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            aria-label="Navigasi portal"
        >
            <a href="{{ route($portalHomeRoute) }}" class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white p-1.5">
                    <img src="{{ asset('images/methodist-logo.png') }}" alt="" class="h-full w-full object-contain">
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-extrabold tracking-tight">SMAS Methodist</span>
                    <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[.18em] text-[#8eadd0]">Banda Aceh</span>
                </span>
            </a>

            <div class="px-5 pt-6">
                <p class="px-3 text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">Menu {{ $roleLabel }}</p>
                <nav class="mt-3 space-y-1.5">
                    @foreach ($navigation as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            @if (request()->routeIs($item['active'])) aria-current="page" @endif
                            @click="sidebarOpen = false"
                            class="group flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition
                                {{ request()->routeIs($item['active']) ? 'bg-[#8eadd0]/20 text-white ring-1 ring-inset ring-[#8eadd0]/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                        >
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs($item['active']) ? 'text-[#b9d1ea]' : 'text-slate-400 group-hover:text-[#b9d1ea]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                @if ($item['icon'] === 'dashboard')
                                    <rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/>
                                    <rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/>
                                    <rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/>
                                    <rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/>
                                @elseif ($item['icon'] === 'class')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 016.5 3H20v17H6.5A2.5 2.5 0 014 17.5v-12zM4 16a2 2 0 012-2h14M8 7h8M8 10h6"/>
                                @elseif ($item['icon'] === 'roster')
                                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01M16 17h.01"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h7l4 4V20a1.25 1.25 0 01-1.25 1.25h-9.5A1.25 1.25 0 016 20V5a1.25 1.25 0 011.25-1.25zM14 4v4h4M9 13h6M9 16.5h6"/>
                                @endif
                            </svg>
                            <span>{{ $item['label'] }}</span>
                            @if (request()->routeIs($item['active']))
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-[#b9d1ea]"></span>
                            @endif
                        </a>
                    @endforeach

                    @if ($user)
                        <a
                            href="{{ route('profile.edit') }}"
                            @if (request()->routeIs('profile.*')) aria-current="page" @endif
                            @click="sidebarOpen = false"
                            class="group flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition
                                {{ request()->routeIs('profile.*') ? 'bg-[#8eadd0]/20 text-white ring-1 ring-inset ring-[#8eadd0]/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                        >
                            <svg class="h-5 w-5 shrink-0 text-slate-400 group-hover:text-[#b9d1ea]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 20a6.5 6.5 0 0113 0"/>
                            </svg>
                            <span>Profil</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="mt-auto border-t border-white/10 p-4">
                @if ($user)
                    <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="h-10 w-10 shrink-0 rounded-full object-cover border border-white/20">
                        @else
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#8eadd0] font-bold text-[#101C35]">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold">{{ $user->name }}</span>
                            <span class="block text-xs text-slate-400">{{ $roleLabel }}</span>
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 5H6.5A1.5 1.5 0 005 6.5v11A1.5 1.5 0 006.5 19H10m4-3 4-4-4-4m4 4H9"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-xl bg-[#8eadd0] px-4 py-2.5 text-center text-sm font-bold text-[#101C35]">
                        Masuk Portal
                    </a>
                @endif
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72 flex flex-col justify-between">
            {{-- SATU HEADER TUNGGAL --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-6 lg:px-8">
                <div class="flex min-h-[72px] items-center justify-between gap-4 py-2">
                    <div class="flex flex-1 min-w-0 items-center gap-3">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            aria-label="Buka navigasi"
                            :aria-expanded="sidebarOpen"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-[#101C35] hover:bg-slate-50 lg:hidden"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        @isset($header)
                            <div class="min-w-0 flex-1">
                                {{ $header }}
                            </div>
                        @else
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#267bb5]">SMAS Methodist · {{ $roleLabel }}</p>
                                <p class="truncate text-base font-bold text-[#101C35]">Portal Akademik</p>
                            </div>
                        @endisset
                    </div>

                    @if ($user)
                        <div class="hidden shrink-0 items-center gap-3 text-right sm:flex">
                            <div class="text-right">
                                <span class="block text-sm font-bold text-slate-700 leading-tight">{{ $user->name }}</span>
                                <span class="block text-[11px] font-semibold text-slate-400 capitalize">{{ $roleLabel }}</span>
                            </div>
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover shadow-sm border border-slate-200">
                            @else
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e8f1f8] font-bold text-[#17365d] shadow-sm">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 flex-1">
                {{ $slot }}
            </main>

            {{-- FOOTER SEPERTI HOMEPAGE --}}
            <footer class="mt-auto border-t border-slate-800 bg-[#101C35] text-white">
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
                    <div class="grid gap-8 md:grid-cols-2 md:gap-10 lg:grid-cols-12 lg:gap-10">

                        {{-- SCHOOL IDENTITY --}}
                        <div class="lg:col-span-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-1.5 shadow-sm">
                                    <img src="{{ asset('images/methodist-logo.png') }}"
                                         alt="Logo SMAS Methodist"
                                         class="h-full w-full object-contain">
                                </div>

                                <div>
                                    <h2 class="font-serif text-lg font-bold text-white">
                                        SMAS Methodist
                                    </h2>
                                    <p class="text-xs font-semibold tracking-wider text-[#8eadd0]">
                                        BANDA ACEH
                                    </p>
                                </div>
                            </div>

                            <p class="mt-5 max-w-sm text-sm leading-6 text-slate-300">
                                Portal pembelajaran digital yang mengintegrasikan kegiatan belajar, pengumpulan tugas, ujian, dan pengelolaan nilai dalam satu platform.
                            </p>

                            <div class="mt-5 inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-slate-300">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                <span>Methodist Learning Space &bull; {{ $roleLabel }}</span>
                            </div>
                        </div>

                        {{-- CONTACT --}}
                        <div class="lg:col-span-4">
                            <h3 class="text-sm font-bold tracking-wide text-white uppercase">
                                Informasi Kontak
                            </h3>

                            <div class="mt-5 space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#8eadd0]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-12a7 7 0 10-14 0c0 6.5 7 12 7 12z"/>
                                            <circle cx="12" cy="9" r="2"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs sm:text-sm leading-5 text-slate-300">
                                        Jl. Pocut Baren No. 3, Kelurahan Mulia / Laksana, Kecamatan Kuta Alam, Banda Aceh, Aceh 23123.
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#8eadd0]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.3a1 1 0 01.95.68l1.2 3.6a1 1 0 01-.5 1.2l-2.1 1.1a11 11 0 005.57 5.57l1.1-2.1a1 1 0 011.2-.5l3.6 1.2a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.16 21 3 14.84 3 7V5z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-300">0852-6010-9442</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#8eadd0]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
                                        </svg>
                                    </div>
                                    <a href="mailto:official_smasmethodist@gmail.com"
                                       class="break-all text-xs sm:text-sm text-slate-300 transition hover:text-white">
                                        official_smasmethodist@gmail.com
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- QUICK LINKS --}}
                        <div class="lg:col-span-3">
                            <h3 class="text-sm font-bold tracking-wide text-white uppercase">
                                Navigasi
                            </h3>

                            <ul class="mt-5 space-y-2.5">
                                @if ($user?->isGuru())
                                    <li>
                                        <a href="{{ route('guru.dashboard') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            Dashboard Guru
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('guru.classes.index') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            E-Class (Ruang Belajar)
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('guru.exams.index') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            E-Exam (Kelola Ujian)
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('guru.roster') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            Roster Pelajaran Resmi
                                        </a>
                                    </li>
                                @elseif ($user?->isSiswa())
                                    <li>
                                        <a href="{{ route('siswa.dashboard') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            Dashboard Siswa
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('siswa.classes.index') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            E-Class Siswa
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('siswa.exams.index') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            E-Exam Siswa
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('siswa.roster') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            Roster Pelajaran Siswa
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('dashboard') }}"
                                           class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                            <span class="text-[#8eadd0]">→</span>
                                            Dashboard
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                       class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                        <span class="text-[#8eadd0]">→</span>
                                        Profil Akun
                                    </a>
                                </li>

                                <li>
                                    <a href="https://smasmethodistbna.sch.id/"
                                       target="_blank"
                                       rel="noopener"
                                       class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-300 transition hover:translate-x-1 hover:text-white">
                                        <span class="text-[#8eadd0]">→</span>
                                        Website Sekolah ↗
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>

                    {{-- FOOTER BOTTOM --}}
                    <div class="mt-10 flex flex-col items-center gap-2 border-t border-white/10 pt-6 text-center text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:text-left lg:mt-12">
                        <p>
                            &copy; {{ date('Y') }} SMAS Methodist Banda Aceh. All rights reserved.
                        </p>

                        <p>
                            Methodist Learning Space &bull; Portal E-Learning dan E-Exam
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
