<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    Profil Akun
                </h2>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Akun Terverifikasi</span>
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $user = Auth::user();
        $initial = strtoupper(mb_substr($user->name ?? 'U', 0, 1));
        $roleName = $isStudent ? 'Siswa Aktif' : ($user->isGuru() ? 'Guru Pengampu' : 'Administrator');
        $handle = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->user_id ?? $user->name));
        $classesList = $classes ?? collect();
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap');

        .profile-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-serif-title {
            font-family: 'Playfair Display', serif;
        }

        /* BANNER YOUTUBE / INSTAGRAM GRADIENT */
        .profile-banner {
            background-color: #091a30;
            position: relative;
            overflow: hidden;
        }

        .profile-banner-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 30%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-banner:hover .profile-banner-img {
            transform: scale(1.04);
        }

        .profile-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(9, 26, 48, 0.90) 0%, rgba(16, 42, 78, 0.78) 50%, rgba(18, 59, 96, 0.65) 100%);
        }

        .profile-banner-grid {
            position: absolute;
            inset: 0;
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            mask-image: linear-gradient(to bottom, black 30%, transparent 100%);
        }

        /* AVATAR RING INSTAGRAM STORY STYLE */
        .instagram-avatar-ring {
            background: linear-gradient(135deg, #2563EB, #38BDF8, #818CF8, #C084FC);
            padding: 4px;
            border-radius: 9999px;
            display: inline-block;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
        }

        /* CARD HOVER TRANSITION */
        .feed-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .feed-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(16, 42, 78, 0.08);
        }
    </style>

    <div class="profile-container pb-16 pt-2"
         x-data="{
             activeTab: 'grid',
             copied: false,
             copyToClipboard(text) {
                 navigator.clipboard.writeText(text);
                 this.copied = true;
                 setTimeout(() => this.copied = false, 2200);
             }
         }">

        <div class="mx-auto max-w-6xl space-y-6">

            {{-- NOTIFIKASI SUKSES / ERROR --}}
            @if(session('status') === 'profile-updated')
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">Informasi akun berhasil diperbarui.</span>
                </div>
            @elseif(session('status') === 'password-updated')
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">Kata sandi akun Anda berhasil diperbarui.</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ========================================================
                 CREATOR CHANNEL HEADER (YOUTUBE BANNER + INSTAGRAM PROFILE)
            ======================================================== --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-xl shadow-slate-200/50">

                {{-- WIDE CINEMATIC BANNER (YOUTUBE STYLE) --}}
                <div class="profile-banner h-48 sm:h-56 md:h-64 flex flex-col justify-between p-6 sm:p-8 text-white relative overflow-hidden group">
                    @if($isStudent && file_exists(public_path('images/methodist-students.jpg')))
                        <img src="{{ asset('images/methodist-students.jpg') }}"
                             alt="Siswa SMAS Methodist Banda Aceh"
                             class="profile-banner-img">
                    @else
                        <img src="{{ asset('images/methodist-teachers-activity.jpg') }}"
                             alt="Aktivitas Guru SMAS Methodist Banda Aceh"
                             class="profile-banner-img">
                    @endif

                    <div class="profile-banner-overlay"></div>
                    <div class="profile-banner-grid"></div>

                    {{-- TOP BANNER BADGE --}}
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-black/35 px-3.5 py-1 text-[11px] font-bold backdrop-blur-md shadow-sm">
                            <svg class="h-3.5 w-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                            <span class="tracking-wider">SMAS METHODIST BANDA ACEH</span>
                        </div>

                        <div class="hidden sm:inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/25 px-3.5 py-1 text-[11px] font-semibold text-slate-100 backdrop-blur-md shadow-sm">
                            <span>Portal Akademik Resmi</span>
                        </div>
                    </div>

                    {{-- BOTTOM BANNER WATERMARK / MOTTO --}}
                    <div class="relative z-10 hidden sm:flex items-end justify-between">
                        <div>
                            <p class="text-xs font-semibold tracking-wider text-sky-200 uppercase drop-shadow-sm">Methodist Learning Space</p>
                            <p class="font-serif-title text-xl font-bold tracking-tight text-white drop-shadow-md">
                                Belajar &bull; Bertumbuh &bull; Berprestasi
                            </p>
                        </div>
                        <div class="font-serif-title text-5xl font-extrabold text-white/25 select-none pointer-events-none drop-shadow-sm">
                            M.
                        </div>
                    </div>
                </div>

                {{-- CREATOR IDENTITY ROW (INSTAGRAM PROFILE STYLE) --}}
                <div class="relative px-6 pb-6 pt-3 sm:px-10">

                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 border-b border-slate-100 pb-7">
                        
                        {{-- AVATAR dan NAME BLOCK --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
                            
                            {{-- INSTAGRAM STORY RING AVATAR --}}
                            <div class="-mt-20 sm:-mt-24 relative shrink-0">
                                <div class="instagram-avatar-ring">
                                    <div class="h-28 w-28 sm:h-32 sm:w-32 rounded-full bg-slate-900 border-4 border-white flex items-center justify-center text-white text-4xl sm:text-5xl font-extrabold font-serif-title shadow-inner overflow-hidden">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="bg-gradient-to-br from-[#123B60] to-[#267BB5] h-full w-full flex items-center justify-center">
                                                {{ $initial }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- BADGE AKUN RESMI TERVERIFIKASI --}}
                                <div title="Akun Resmi Terverifikasi SMAS Methodist Banda Aceh"
                                     class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white shadow-md ring-4 ring-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- NAME, HANDLE, VERIFIED BADGE --}}
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 truncate">
                                        {{ $user->name }}
                                    </h1>
                                    
                                    {{-- VERIFIED CHECKMARK --}}
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-500 text-white shadow-sm" title="Terverifikasi SMAS Methodist">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>

                                    {{-- ROLE BADGE --}}
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-0.5 text-xs font-bold text-blue-700 border border-blue-200">
                                        {{ $roleName }}
                                    </span>
                                </div>

                                {{-- USERNAME HANDLE dan EMAIL --}}
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                    <span class="font-bold text-slate-700">@<span>{{ $handle }}</span></span>
                                    <span>&bull;</span>
                                    <span>{{ $user->email }}</span>
                                    @if($user->phone)
                                        <span>&bull;</span>
                                        <span>{{ $user->phone }}</span>
                                    @endif
                                </div>

                                {{-- BIO LINE --}}
                                <div class="text-xs sm:text-sm text-slate-600 max-w-xl pt-1 space-y-1">
                                    @if($isStudent && $profile)
                                        <p>
                                            Siswa aktif SMAS Methodist Banda Aceh rombel <strong class="text-slate-800">{{ $profile->schoolClass->name ?? 'Kelas Aktif' }}</strong>.
                                        </p>
                                        @if($profile->nis)
                                            <p class="text-xs text-slate-500">
                                                NIS: <strong class="text-slate-800 font-mono tracking-wide">{{ $profile->nis }}</strong>
                                            </p>
                                        @endif
                                    @elseif($user->isGuru() && $profile)
                                        <p>
                                            Guru pendidik SMAS Methodist Banda Aceh bidang <strong class="text-slate-800">{{ $profile->specialization ?? 'Mata Pelajaran' }}</strong>.
                                        </p>
                                        @if($profile->nip)
                                            <p class="text-xs text-slate-500">
                                                NIP: <strong class="text-slate-800 font-mono tracking-wide">{{ $profile->nip }}</strong>
                                            </p>
                                        @endif
                                    @else
                                        <p>Anggota terdaftar platform pembelajaran Methodist Learning Space.</p>
                                    @endif
                                </div>
                            </div>

                        </div>

                        {{-- QUICK ACTION BUTTONS (INSTAGRAM/YOUTUBE STYLE) --}}
                        <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0">
                            
                            {{-- BUTTON INFORMASI AKUN --}}
                            <button type="button"
                                    @click="activeTab = 'info'"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-400 active:scale-95">
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Informasi Akun</span>
                            </button>

                            {{-- BUTTON KE RUANG BELAJAR --}}
                            @if($isStudent)
                                <a href="{{ route('siswa.classes.index') }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0B2B48] active:scale-95">
                                    <span>E-Class Siswa</span>
                                    <span>&rarr;</span>
                                </a>
                            @elseif($user->isGuru())
                                <a href="{{ route('guru.classes.index') }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0B2B48] active:scale-95">
                                    <span>E-Class Guru</span>
                                    <span>&rarr;</span>
                                </a>
                            @endif

                            {{-- COPY ID BUTTON --}}
                            <button type="button"
                                    @click="copyToClipboard('{{ $user->user_id }}')"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition active:scale-90"
                                    :title="copied ? 'Tersalin!' : 'Salin Nomor ID Akun'">
                                <svg x-show="!copied" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" x-cloak class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- STATS COUNTER BAR (INSTAGRAM POSTS / FOLLOWERS COUNTER STYLE) --}}
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center sm:text-left">
                        @if($isStudent)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['classes_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Kelas Bergabung
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['submissions_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Tugas Diserahkan
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['grades_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Nilai Tercatat
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#267BB5] truncate">
                                    {{ $profile->schoolClass->name ?? 'Reguler' }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Rombongan Belajar
                                </p>
                            </div>
                        @else
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['classes_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Kelas Diampu
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['students_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Siswa Terbina
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#123B60]">
                                    {{ $stats['assignments_count'] ?? 0 }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Tugas Diberikan
                                </p>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                                <p class="text-xl sm:text-2xl font-extrabold text-[#267BB5] truncate">
                                    {{ $profile->education_level ?? 'Pendidik' }}
                                </p>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                    Kualifikasi Akademik
                                </p>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

            {{-- ========================================================
                 YOUTUBE / INSTAGRAM NAVIGATION TABS
            ======================================================== --}}
            <div class="flex items-center justify-center sm:justify-start border-b border-slate-200/90 gap-2 sm:gap-8 px-2 overflow-x-auto">
                
                {{-- TAB 1: GRID AKTIVITAS --}}
                <button type="button"
                        @click="activeTab = 'grid'"
                        class="flex items-center gap-2 py-3 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap"
                        :class="activeTab === 'grid' 
                            ? 'border-[#267BB5] text-[#123B60]' 
                            : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Kelas dan Aktivitas</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">
                        {{ $classesList->count() }}
                    </span>
                </button>

                {{-- TAB 2: BIODATA RESMI --}}
                <button type="button"
                        @click="activeTab = 'biodata'"
                        class="flex items-center gap-2 py-3 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap"
                        :class="activeTab === 'biodata' 
                            ? 'border-[#267BB5] text-[#123B60]' 
                            : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <span>Biodata Resmi</span>
                </button>

                {{-- TAB 3: INFORMASI AKUN (READ ONLY) --}}
                <button type="button"
                        @click="activeTab = 'info'"
                        class="flex items-center gap-2 py-3 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap"
                        :class="activeTab === 'info' 
                            ? 'border-[#267BB5] text-[#123B60]' 
                            : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Informasi Akun</span>
                </button>

                {{-- TAB 4: KEAMANAN --}}
                <button type="button"
                        @click="activeTab = 'security'"
                        class="flex items-center gap-2 py-3 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 transition whitespace-nowrap"
                        :class="activeTab === 'security' 
                            ? 'border-[#267BB5] text-[#123B60]' 
                            : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Keamanan dan Sandi</span>
                </button>

            </div>

            {{-- ========================================================
                 TAB CONTENT 1: GRID FEED KELAS (INSTAGRAM / YOUTUBE CARD GRID)
            ======================================================== --}}
            <div x-show="activeTab === 'grid'" x-transition class="space-y-6">
                
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-serif-title text-xl font-bold text-slate-900">
                            {{ $isStudent ? 'Kelas yang Anda Ikuti' : 'Kelas yang Anda Ampu' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Daftar rombongan belajar aktif pada tahun ajaran ini.
                        </p>
                    </div>

                    @if($isStudent)
                        <a href="{{ route('siswa.classes.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                            Buka Semua Kelas &rarr;
                        </a>
                    @else
                        <a href="{{ route('guru.classes.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                            Kelola Semua Kelas &rarr;
                        </a>
                    @endif
                </div>

                @if($classesList->isNotEmpty())
                    @php
                        $getSubjectCover = function($title, $code = null) {
                            $name = strtolower($title ?? '');
                            $c = strtolower($code ?? '');

                            $map = [
                                'matematika' => 'matematika.jpg', 'mtk' => 'matematika.jpg',
                                'biologi' => 'biologi.jpg', 'bio' => 'biologi.jpg',
                                'kimia' => 'kimia.jpg', 'kim' => 'kimia.jpg',
                                'fisika' => 'fisika.jpg', 'fis' => 'fisika.jpg',
                                'indonesia' => 'indonesia.jpg', 'bin' => 'indonesia.jpg', 'bahasa indonesia' => 'indonesia.jpg',
                                'inggris' => 'inggris.svg', 'big' => 'inggris.svg', 'bahasa inggris' => 'inggris.svg',
                                'informatika' => 'informatika.jpg', 'tik' => 'informatika.jpg', 'inf' => 'informatika.jpg', 'komputer' => 'informatika.jpg',
                                'sejarah' => 'sejarah.svg', 'sej' => 'sejarah.svg',
                                'geografi' => 'geografi.svg', 'geo' => 'geografi.svg',
                                'ekonomi' => 'ekonomi.svg', 'eko' => 'ekonomi.svg',
                                'sosiologi' => 'sosiologi.svg', 'sos' => 'sosiologi.svg',
                                'agama' => 'agama.svg', 'agm' => 'agama.svg', 'pendidikan agama' => 'agama.svg',
                                'pjok' => 'pjok.svg', 'penjas' => 'pjok.svg',
                                'seni' => 'seni-budaya.svg', 'sbd' => 'seni-budaya.svg', 'seni budaya' => 'seni-budaya.svg',
                                'pkn' => 'ppkn.svg', 'ppkn' => 'ppkn.svg',
                            ];

                            foreach ($map as $key => $file) {
                                if (str_contains($name, $key) || str_contains($c, $key)) {
                                    $jpg = preg_replace('/\.(svg|png)$/i', '.jpg', $file);
                                    $svg = preg_replace('/\.(jpg|png)$/i', '.svg', $file);
                                    if (file_exists(public_path('images/subjects/' . $jpg))) {
                                        return asset('images/subjects/' . $jpg);
                                    } elseif (file_exists(public_path('images/subjects/' . $svg))) {
                                        return asset('images/subjects/' . $svg);
                                    }
                                }
                            }

                            if (file_exists(public_path('images/hero-school.jpg'))) {
                                return asset('images/hero-school.jpg');
                            }
                            return null;
                        };

                        $getSubjectSymbol = function($title, $code = null) {
                            $name = strtolower($title ?? '');
                            $c = strtolower($code ?? '');
                            $symbols = [
                                'matematika' => 'MAT', 'mtk' => 'MAT',
                                'fisika' => 'FIS', 'fis' => 'FIS',
                                'biologi' => 'BIO', 'bio' => 'BIO',
                                'kimia' => 'KIM', 'kim' => 'KIM',
                                'indonesia' => 'IDN', 'bin' => 'IDN',
                                'inggris' => 'ENG', 'big' => 'ENG',
                                'informatika' => 'TIK', 'inf' => 'TIK',
                                'sejarah' => 'SEJ', 'sej' => 'SEJ',
                                'geografi' => 'GEO', 'geo' => 'GEO',
                                'ekonomi' => 'EKO', 'eko' => 'EKO',
                                'sosiologi' => 'SOS', 'sos' => 'SOS',
                                'agama' => 'AGM', 'agm' => 'AGM',
                                'pjok' => 'PJK', 'penjas' => 'PJK',
                                'seni' => 'SBD', 'sbd' => 'SBD',
                                'pkn' => 'PKN', 'ppkn' => 'PKN',
                            ];
                            foreach ($symbols as $k => $sym) {
                                if (str_contains($name, $k) || str_contains($c, $k)) return $sym;
                            }
                            return strtoupper(substr($title, 0, 3));
                        };
                    @endphp

                    <div id="profileClassesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($classesList as $item)
                            @php
                                $subjectTitle = $item->subject->name ?? ($item->subject_code ?? 'Mata Pelajaran');
                                $className = $item->schoolClass->name ?? ($item->class_id ?? 'Kelas');
                                $teacherName = $item->teacher->user->name ?? 'Guru Pengampu';
                                $cstId = $item->cst_id;
                                $targetUrl = $isStudent 
                                    ? route('siswa.classes.show', $cstId) 
                                    : route('guru.classes.show', $cstId);

                                $subjectCoverImage = $getSubjectCover($subjectTitle, $item->subject_code ?? null);
                                $subjectSymbol = $getSubjectSymbol($subjectTitle, $item->subject_code ?? null);
                            @endphp

                            <a href="{{ $targetUrl }}" data-profile-class-card class="feed-card rounded-2xl border border-slate-200 bg-white overflow-hidden flex flex-col justify-between shadow-sm hover:shadow-md hover:border-[#BBD7E9] hover:-translate-y-1 transition group">
                                <div>
                                    {{-- CARD THUMBNAIL / TOP BANNER --}}
                                    <div class="h-32 relative overflow-hidden p-4 text-white flex flex-col justify-between bg-[#123B60]">
                                        @if($subjectCoverImage)
                                            <img src="{{ $subjectCoverImage }}"
                                                 alt="{{ $subjectTitle }}"
                                                 class="absolute inset-0 h-full w-full object-cover transition transform group-hover:scale-105 duration-300">
                                            <div class="absolute inset-0 bg-gradient-to-t from-[#091a30]/85 via-[#10243E]/45 to-transparent"></div>
                                        @else
                                            <div class="absolute -right-4 -top-6 text-7xl font-serif-title font-extrabold text-white/10 select-none">
                                                {{ strtoupper(substr($subjectTitle, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between relative z-10">
                                            <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase backdrop-blur-sm shadow-sm border border-white/20">
                                                {{ $className }}
                                            </span>
                                            <span class="rounded-lg bg-white/20 px-2 py-0.5 text-[10px] font-extrabold tracking-wider backdrop-blur-sm border border-white/25">
                                                {{ $subjectSymbol }}
                                            </span>
                                        </div>

                                        <h4 class="font-serif-title text-base font-bold truncate relative z-10 drop-shadow-md text-white group-hover:text-cyan-200 transition">
                                            {{ $subjectTitle }}
                                        </h4>
                                    </div>

                                    {{-- CARD BODY --}}
                                    <div class="p-4 space-y-3">
                                        <div class="flex items-center gap-2.5 text-xs text-slate-600">
                                            <div class="h-6 w-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-[10px] shrink-0">
                                                {{ strtoupper(substr($teacherName, 0, 1)) }}
                                            </div>
                                            <span class="truncate font-medium">{{ $teacherName }}</span>
                                        </div>

                                        @if(!$isStudent && isset($item->joined_students_count))
                                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                                                <span>Siswa Terdaftar</span>
                                                <span class="font-bold text-slate-800">{{ $item->joined_students_count }} Siswa</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- CARD FOOTER BUTTON --}}
                                <div class="p-4 pt-0">
                                    <div class="w-full flex items-center justify-center gap-1.5 rounded-xl bg-slate-100 group-hover:bg-[#123B60] group-hover:text-white py-2 text-xs font-bold text-slate-700 transition">
                                        <span>Buka Ruang Kelas</span>
                                        <span>&rarr;</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- NAVIGASI SLIDE / PAGINASI KELAS PROFIL (MAKSIMAL 6 PER SLIDE) --}}
                    <div id="profileClassesPagination" class="hidden"></div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
                            </svg>
                        </div>
                        <p class="font-bold text-slate-700">Belum Ada Kelas yang Diikuti</p>
                        <p class="text-xs mt-1">Anda belum tergabung ke dalam rombongan belajar aktif.</p>
                    </div>
                @endif

            </div>

            {{-- ========================================================
                 TAB CONTENT 2: BIODATA RESMI (DATA POKOK PENDIDIKAN)
            ======================================================== --}}
            <div x-show="activeTab === 'biodata'" x-cloak x-transition class="space-y-6">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                        <div>
                            <h3 class="font-serif-title text-xl font-bold text-slate-900">
                                Biodata Resmi Akademik
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Informasi data pokok yang tercatat pada sistem database sekolah.
                            </p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                            Status: Aktif
                        </span>
                    </div>

                    @if($isStudent && $profile)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- IDENTITAS SISWA --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Data Pribadi Siswa
                                </h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Nama Lengkap</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $user->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">NIS</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->nis }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">NISN</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->nisn ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Jenis Kelamin</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            {{ $profile->gender === 'L' ? 'Laki-laki' : ($profile->gender === 'P' ? 'Perempuan' : ($profile->gender ?? '-')) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Tempat, Tanggal Lahir</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            {{ $profile->pob ?? '-' }}, {{ $profile->dob ? $profile->dob->translatedFormat('d F Y') : '-' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Tahun Masuk</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->entry_year ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- KONTAK dan ORANG TUA --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Kontak dan Domisili
                                </h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Kelas Saat Ini</span>
                                        <span class="font-bold text-blue-700 text-right">{{ $profile->schoolClass->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Nama Orang Tua/Wali</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->parent_name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">No. Telepon Wali</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->parent_phone ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Alamat Tempat Tinggal</span>
                                        <span class="font-bold text-slate-800 text-right max-w-[200px] truncate">{{ $profile->address ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Kota / Provinsi</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            {{ $profile->city ?? 'Banda Aceh' }}, {{ $profile->province ?? 'Aceh' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @elseif($profile)
                        {{-- DATA GURU --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Informasi Tenaga Pendidik
                                </h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Nama Lengkap</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $user->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">NIP / Kode Guru</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->nip }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Spesialisasi Mapel</span>
                                        <span class="font-bold text-blue-700 text-right">{{ $profile->specialization ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Pendidikan Terakhir</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->education_level ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Kepegawaian dan Kontak
                                </h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Tanggal Mulai Tugas</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            {{ $profile->hire_date ? $profile->hire_date->translatedFormat('d F Y') : '-' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Alamat Domisili</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $profile->address ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100 text-xs">
                                        <span class="text-slate-500">Alamat Email</span>
                                        <span class="font-bold text-slate-800 text-right">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

            {{-- ========================================================
                 TAB CONTENT 3: INFORMASI AKUN (HANYA DITAMPILKAN / READ-ONLY)
            ======================================================== --}}
            <div x-show="activeTab === 'info'" x-cloak x-transition class="space-y-6">
                
                {{-- PEMBERITAHUAN WEWENANG ADMINISTRATOR --}}
                <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50/90 via-sky-50/70 to-blue-50/90 p-5 sm:p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#123B60] text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-[#101C35]">
                                Hak Akses Perubahan Data Dikelola Administrator
                            </h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Data informasi akun resmi sekolah (nama lengkap, alamat email terdaftar, nomor identitas induk, dan penugasan rombel/mapel) dikelola dan diperbarui secara terpusat oleh <strong>Administrator SMAS Methodist Banda Aceh</strong>. Apabila terdapat ketidaksesuaian data atau keperluan pembaruan informasi, silakan berkoordinasi dengan pihak Tata Usaha atau Tim IT Administrator.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- KARTU DETAIL INFORMASI AKUN RESMI --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-5 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-serif-title text-xl font-bold text-slate-900">
                                    Informasi Akun Anda
                                </h3>
                                <p class="text-xs text-slate-500">
                                    Rincian identitas akun yang terdaftar pada sistem akademik sekolah.
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1.5 self-start sm:self-auto rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Status: Akun Aktif
                        </span>
                    </div>

                    {{-- GRID PROFIL LENGKAP --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</span>
                            <p class="mt-1 text-base font-extrabold text-slate-800">{{ $user->name }}</p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Sesuai data resmi dapodik/sekolah</span>
                        </div>

                        {{-- Alamat Email --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Alamat Email</span>
                            <p class="mt-1 text-base font-extrabold text-slate-800 font-mono">{{ $user->email }}</p>
                            <span class="text-[10px] text-emerald-600 mt-0.5 block font-medium">✓ Terverifikasi resmi</span>
                        </div>

                        {{-- Peran / Role --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Peran Akun</span>
                            <p class="mt-1 text-base font-extrabold text-[#123B60]">{{ $roleName }}</p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Hak akses portal pembelajaran</span>
                        </div>

                        {{-- Nomor Induk --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $isStudent ? 'Nomor Induk Siswa (NIS)' : 'Nomor Induk Pegawai (NIP)' }}
                            </span>
                            <p class="mt-1 text-base font-extrabold text-slate-800 font-mono">
                                {{ $isStudent ? ($profile->nis ?? '-') : ($profile->nip ?? '-') }}
                            </p>
                            @if($isStudent && $profile?->nisn)
                                <span class="text-[10px] text-slate-500 mt-0.5 block">NISN: <span class="font-mono font-semibold">{{ $profile->nisn }}</span></span>
                            @else
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Nomor identitas pokok</span>
                            @endif
                        </div>

                        {{-- Kelas / Spesialisasi --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $isStudent ? 'Rombongan Belajar (Kelas)' : 'Bidang / Mata Pelajaran' }}
                            </span>
                            <p class="mt-1 text-base font-extrabold text-slate-800">
                                {{ $isStudent ? ($profile->schoolClass->name ?? 'Belum Ditugaskan') : ($profile->specialization ?? 'Guru Pengampu') }}
                            </p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Tahun Ajaran {{ $profile->schoolClass->academic_year ?? '2025/2026' }}</span>
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nomor Telepon / WhatsApp</span>
                            <p class="mt-1 text-base font-extrabold text-slate-800 font-mono">
                                {{ $user->phone ?? 'Belum ditambahkan' }}
                            </p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Kontak komunikasi terdaftar</span>
                        </div>
                    </div>

                    {{-- METADATA PENDAFTARAN --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Terdaftar pada: <strong class="text-slate-600">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pembaruan terakhir: <strong class="text-slate-600">{{ $user->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</strong></span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================================================
                 TAB CONTENT 4: KEAMANAN dan SANDI
            ======================================================== --}}
            <div x-show="activeTab === 'security'" x-cloak x-transition class="space-y-6">
                
                {{-- UPDATE PASSWORD --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-serif-title text-xl font-bold text-slate-900">
                                Keamanan dan Sandi
                            </h3>
                            <p class="text-xs text-slate-500">
                                Gunakan kombinasi kata sandi yang kuat untuk menjaga privasi akun Anda.
                            </p>
                        </div>
                    </div>

                    @include('profile.partials.update-password-form')
                </div>

            </div>

        </div>

    </div>

    {{-- PROFILE CLASSES SLIDE / PAGINATION SCRIPT (6 PER SLIDE) --}}
    @if($classesList->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cards = Array.from(document.querySelectorAll('[data-profile-class-card]'));
                const pagination = document.getElementById('profileClassesPagination');
                const grid = document.getElementById('profileClassesGrid');
                const perPage = 6;
                let currentPage = 1;

                function render() {
                    const total = cards.length;
                    const totalPages = Math.ceil(total / perPage) || 1;

                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    // Sembunyikan semua kartu
                    cards.forEach(c => c.classList.add('hidden'));

                    // Tampilkan hanya kartu pada slide saat ini
                    cards.forEach((c, idx) => {
                        if (idx >= start && idx < end) {
                            c.classList.remove('hidden');
                        }
                    });

                    // Render pagination
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
                                    class="profile-class-page-btn flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold transition shadow-sm ${
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
                                Menampilkan <strong class="text-slate-800">${startItem} - ${endItem}</strong> dari <strong class="text-slate-800">${total}</strong> kelas
                                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                    Slide ${currentPage} dari ${totalPages}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                        id="profileClassPrevBtn"
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
                                        id="profileClassNextBtn"
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
                    document.getElementById('profileClassPrevBtn')?.addEventListener('click', function () {
                        if (currentPage > 1) {
                            currentPage--;
                            render();
                            grid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });

                    document.getElementById('profileClassNextBtn')?.addEventListener('click', function () {
                        if (currentPage < totalPages) {
                            currentPage++;
                            render();
                            grid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });

                    // Bind page buttons
                    pagination.querySelectorAll('.profile-class-page-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const p = parseInt(this.getAttribute('data-page'), 10);
                            if (p && p !== currentPage) {
                                currentPage = p;
                                render();
                                grid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    });
                }

                // Inisialisasi awal
                render();
            });
        </script>
    @endif

</x-app-layout>
