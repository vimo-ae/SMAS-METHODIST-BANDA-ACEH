<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    Roster Pelajaran dan Jadwal Mengajar
                </h2>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-bold text-[#123B60] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Tahun Pelajaran {{ $metadata['academic_year'] }}
                </span>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            viewMode: 'matrix', // 'matrix' atau 'my_schedule'
            activeDay: 'all',
            activeClass: 'all',
            selectedTeacher: '{{ !empty($currentTeacherCodes) ? $currentTeacherCodes[0] : '' }}',
            myTeacherCodes: {{ json_encode($currentTeacherCodes) }},
            showLegend: false,
            highlightTeacher(code) {
                if (!this.selectedTeacher) return false;
                return this.selectedTeacher === code;
            },
            isMySlot(code) {
                return this.myTeacherCodes.includes(code);
            }
        }"
        class="space-y-6 py-4"
    >
        {{-- BREADCRUMB dan TOMBOL AKSI (DI BAWAH HEADER) --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-2 print:hidden">
            <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                <a href="{{ route('guru.dashboard') }}" class="transition hover:text-blue-600">
                    Dashboard
                </a>
                <span>/</span>
                <span class="font-bold text-blue-700">Roster Pelajaran</span>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('guru.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>←</span>
                    <span>Kembali ke Dashboard</span>
                </a>
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5] focus:outline-none"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Roster Resmi</span>
                </button>
            </div>
        </div>
        {{-- ========================================================
             BANNER DOKUMEN RESMI (KOP ROSTER)
             ======================================================== --}}
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-r from-[#101C35] via-[#123B60] to-[#1B4D7E] p-6 text-white shadow-md print:border-none print:bg-white print:p-0 print:text-slate-900 print:shadow-none">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-inner print:h-12 print:w-12">
                        <img src="{{ asset('images/methodist-logo.png') }}" alt="Logo Methodist" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-md bg-white/10 px-2.5 py-0.5 text-[10px] font-bold tracking-widest uppercase text-[#8eadd0] print:hidden">
                            Dokumen Resmi Kurikulum
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight print:text-xl print:text-[#101C35]">
                            ROSTER PELAJARAN TAHUN {{ $metadata['academic_year'] }}
                        </h1>
                        <p class="text-xs text-slate-200 sm:text-sm print:text-xs print:text-slate-600">
                            {{ $metadata['school_name'] }} &bull; Berlaku Efektif Semester Ganjil dan Genap
                        </p>
                    </div>
                </div>

                {{-- STATISTIK KILAT GURU --}}
                <div class="flex flex-wrap items-center gap-2 border-t border-white/10 pt-3 md:border-t-0 md:pt-0 print:hidden">
                    <button
                        type="button"
                        @click="viewMode = 'matrix'"
                        :class="viewMode === 'matrix' ? 'bg-white text-[#123B60] shadow-sm' : 'bg-white/10 text-white hover:bg-white/20'"
                        class="rounded-xl px-3.5 py-2 text-xs font-bold transition flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                        <span>Matrix Roster</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'duties'"
                        :class="viewMode === 'duties' ? 'bg-white text-[#123B60] shadow-sm' : 'bg-white/10 text-white hover:bg-white/20'"
                        class="rounded-xl px-3.5 py-2 text-xs font-bold transition flex items-center gap-1.5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        <span>Pembagian Tugas Guru</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'my_schedule'"
                        :class="viewMode === 'my_schedule' ? 'bg-[#8eadd0] text-[#101C35] font-extrabold shadow-sm' : 'bg-white/10 text-white hover:bg-white/20'"
                        class="rounded-xl px-3.5 py-2 text-xs font-bold transition flex items-center gap-1.5"
                    >
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>Jadwal Saya</span>
                    </button>
                </div>
            </div>

            {{-- Watermark background print/display --}}
            <div class="absolute -right-6 -bottom-10 pointer-events-none opacity-5 text-9xl font-black print:hidden">
                METHODIST
            </div>
        </div>

        {{-- ========================================================
             TOOLBAR FILTER dan KONTROL INTERAKTIF (TIDAK TERCETAK DI PRINT)
             ======================================================== --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm print:hidden">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                {{-- TAB FILTER HARI --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="mr-2 text-xs font-bold uppercase tracking-wider text-slate-400">Hari:</span>
                    <button
                        type="button"
                        @click="activeDay = 'all'"
                        :class="activeDay === 'all' ? 'bg-[#123B60] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                    >
                        Semua Hari
                    </button>
                    @foreach (['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu'] as $dayKey => $dayLabel)
                        <button
                            type="button"
                            @click="activeDay = '{{ $dayKey }}'"
                            :class="activeDay === '{{ $dayKey }}' ? 'bg-[#123B60] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        >
                            {{ $dayLabel }}
                        </button>
                    @endforeach
                </div>

                {{-- FILTER KELAS dan HIGHLIGHT GURU --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filter Kelas --}}
                    <div class="flex items-center gap-2">
                        <label for="filter-class" class="text-xs font-bold text-slate-500 whitespace-nowrap">Filter Kelas:</label>
                        <select
                            id="filter-class"
                            x-model="activeClass"
                            class="rounded-xl border-slate-200 bg-slate-50 py-1.5 pl-3 pr-8 text-xs font-semibold text-slate-700 focus:border-[#267BB5] focus:ring focus:ring-blue-100"
                        >
                            <option value="all">Semua Kelas (6 Kelas)</option>
                            @foreach ($classes as $classKey => $classInfo)
                                <option value="{{ $classKey }}">{{ $classKey }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Highlight Guru --}}
                    <div class="flex items-center gap-2">
                        <label for="highlight-teacher" class="text-xs font-bold text-slate-500 whitespace-nowrap">Sorot Guru:</label>
                        <select
                            id="highlight-teacher"
                            x-model="selectedTeacher"
                            class="rounded-xl border-slate-200 bg-slate-50 py-1.5 pl-3 pr-8 text-xs font-semibold text-slate-700 focus:border-[#267BB5] focus:ring focus:ring-blue-100"
                        >
                            <option value="">-- Tanpa Sorotan --</option>
                            @if (!empty($currentTeacherCodes))
                                <optgroup label="Akun Anda ({{ auth()->user()->name }})">
                                    @foreach ($currentTeacherCodes as $myCode)
                                        <option value="{{ $myCode }}">{{ $myCode }} - {{ $teachers[$myCode]['desc'] ?? $teachers[$myCode]['name'] }} (Jadwal Anda)</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <optgroup label="Semua Kode Guru Resmi">
                                @foreach ($teachers as $tCode => $tData)
                                    <option value="{{ $tCode }}">{{ $tCode }} - {{ $tData['name'] }} ({{ $tData['subject'] }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    {{-- Tombol Bantuan Legenda --}}
                    <button
                        type="button"
                        @click="showLegend = !showLegend"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100"
                    >
                        <span x-text="showLegend ? 'Tutup Legenda' : 'Lihat Kode Guru dan Mapel'"></span>
                    </button>
                </div>
            </div>

            {{-- KOTAK KETERANGAN GURU YANG DISOROT --}}
            <template x-if="selectedTeacher">
                <div class="mt-3 flex items-center justify-between rounded-xl bg-blue-50/80 px-4 py-2.5 text-xs border border-blue-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#123B60] text-amber-300">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                        <span class="text-slate-600">
                            Menyorot jadwal untuk kode guru:
                            <strong class="font-bold text-[#123B60]" x-text="selectedTeacher"></strong>
                            <template x-if="isMySlot(selectedTeacher)">
                                <span class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                    Jadwal Mengajar Anda
                                </span>
                            </template>
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="selectedTeacher = ''"
                        class="text-[11px] font-bold text-slate-400 hover:text-slate-700 underline"
                    >
                        Hapus Sorotan
                    </button>
                </div>
            </template>

            {{-- KOTAK KETERANGAN KELAS YANG DIFILTER --}}
            <template x-if="activeClass !== 'all'">
                <div class="mt-2.5 flex items-center justify-between rounded-xl bg-sky-50 px-4 py-2.5 text-xs border border-sky-200">
                    <div class="flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#267BB5] text-white">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        <span class="text-slate-700">
                            Menampilkan jadwal khusus kelas:
                            <strong class="font-bold text-[#123B60]" x-text="activeClass"></strong>
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="activeClass = 'all'"
                        class="text-[11px] font-bold text-[#267BB5] hover:text-[#123B60] underline"
                    >
                        Tampilkan Semua Kelas (Reset)
                    </button>
                </div>
            </template>
        </div>

        {{-- ========================================================
             MODAL / COLLAPSIBLE LEGENDA KODE GURU dan MATA PELAJARAN
             ======================================================== --}}
        <div
            x-show="showLegend"
            x-collapse
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm print:hidden"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg class="h-4 w-4 text-[#123B60]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Daftar Kode Guru dan Mata Pelajaran Resmi SMAS Methodist</span>
                </h3>
                <button type="button" @click="showLegend = false" class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                    <span>Tutup</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mt-4 grid gap-6 md:grid-cols-2">
                {{-- Daftar Kode Guru --}}
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Kode Singkatan Guru</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach ($teachers as $code => $info)
                            <div
                                @click="selectedTeacher = '{{ $code }}'; showLegend = false;"
                                class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-100 bg-slate-50/60 p-2 transition hover:border-[#267BB5] hover:bg-blue-50/50"
                            >
                                <span class="font-black text-[#123B60]">{{ $code }}</span>
                                <span class="text-slate-600 truncate ml-2 text-[11px]">{{ $info['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Daftar Mata Pelajaran dan Palet Warna --}}
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Kode dan Warna Mata Pelajaran</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach ($subjectStyles as $subCode => $subStyle)
                            <div class="flex items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 p-2">
                                <span class="inline-block rounded px-2 py-0.5 text-[10px] font-black uppercase text-white shadow-xs" style="background-color: {{ $subStyle['bg'] }}; color: {{ $subStyle['text'] }};">
                                    {{ $subCode }}
                                </span>
                                <span class="text-slate-600 truncate text-[11px]">{{ $subStyle['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAMPILAN 1: MATRIX ROSTER SEKOLAH (SESUAI DOKUMEN RESMI)
             ======================================================== --}}
        <div x-show="viewMode === 'matrix'" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm print:rounded-none print:border-black print:shadow-none">
            <div class="overflow-x-auto">
                <table
                    class="w-full border-collapse text-center text-xs print:text-[9px]"
                    :class="activeClass === 'all' ? 'min-w-[1200px] table-fixed' : 'min-w-[620px] max-w-4xl mx-auto'"
                >
                    <thead class="sticky top-0 z-20">
                        <tr class="border-b border-white/20 bg-[#123B60] text-white print:bg-slate-200 print:text-black">
                            <th rowspan="2" class="border-r border-white/20 px-2 py-3 font-bold uppercase tracking-wider print:border-black w-20 min-w-[80px]">
                                Hari
                            </th>
                            <th rowspan="2" class="border-r border-white/20 px-2 py-3 font-bold uppercase tracking-wider print:border-black w-12 min-w-[45px]">
                                Les
                            </th>
                            <th rowspan="2" class="border-r border-white/20 px-2 py-3 font-bold uppercase tracking-wider print:border-black w-28 min-w-[105px]">
                                Waktu
                            </th>
                            @foreach ($classes as $classKey => $classMeta)
                                <th
                                    colspan="2"
                                    x-show="activeClass === 'all' || activeClass === '{{ $classKey }}'"
                                    class="border-r border-white/20 px-2 py-2 font-black uppercase tracking-wider last:border-r-0 print:border-black"
                                    style="min-width: 145px;"
                                >
                                    {{ $classKey }}
                                </th>
                            @endforeach
                        </tr>
                        <tr class="border-b border-slate-200 bg-[#1B4D7E] text-[10px] font-bold text-slate-100 print:bg-slate-100 print:text-black">
                            @foreach ($classes as $classKey => $classMeta)
                                <th
                                    x-show="activeClass === 'all' || activeClass === '{{ $classKey }}'"
                                    class="border-r border-white/20 px-1 py-1.5 print:border-black w-20 min-w-[80px]"
                                >
                                    Bid Studi
                                </th>
                                <th
                                    x-show="activeClass === 'all' || activeClass === '{{ $classKey }}'"
                                    class="border-r border-white/20 px-1 py-1.5 last:border-r-0 print:border-black w-16 min-w-[65px]"
                                >
                                    Kode Guru
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 print:divide-black">
                        @foreach ($rosterData as $dayKey => $day)
                            {{-- Baris Pemisah Hari --}}
                            <tr
                                x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                                class="bg-[#123B60] text-white print:bg-slate-200 print:text-black border-y-2 border-[#123B60]"
                            >
                                <td :colspan="activeClass === 'all' ? 15 : 5" class="py-2.5 px-4 text-left font-black tracking-wider uppercase text-xs">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 text-sky-300 print:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                                        </svg>
                                        <span>HARI {{ $day['day_name'] }}</span>
                                        <span class="text-[10px] font-normal text-slate-300 ml-2 print:text-slate-700">SMAS Methodist Banda Aceh</span>
                                    </span>
                                </td>
                            </tr>

                            @foreach ($day['rows'] as $row)
                                @if (!empty($row['is_special']))
                                    {{-- Baris Khusus: Upacara / Kebaktian --}}
                                    <tr
                                        x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                                        class="bg-amber-50/60 print:bg-slate-100"
                                    >
                                        <td class="border-r border-slate-200 px-2 py-2.5 font-bold uppercase text-[#123B60] print:border-black">
                                            {{ $day['day_name'] }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2.5 font-black text-slate-800 print:border-black">
                                            {{ $row['les'] }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2.5 font-bold whitespace-nowrap text-slate-600 print:border-black">
                                            {{ $row['time'] }}
                                        </td>
                                        <td
                                            :colspan="activeClass === 'all' ? 12 : 2"
                                            class="py-2.5 text-center text-xs font-black uppercase tracking-[.3em] text-[#123B60] bg-gradient-to-r from-blue-50/60 via-indigo-50/70 to-blue-50/60 border-y border-blue-200/50 print:border-black"
                                        >
                                            {{ $row['special_title'] }}
                                        </td>
                                    </tr>
                                @elseif (!empty($row['is_break']))
                                    {{-- Baris Istirahat --}}
                                    <tr
                                        x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                                        class="bg-slate-100/80 print:bg-slate-200"
                                    >
                                        <td class="border-r border-slate-200 px-2 py-1.5 font-bold text-slate-400 print:border-black">
                                            —
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-1.5 font-bold text-slate-400 print:border-black">
                                            —
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-1.5 font-bold whitespace-nowrap text-slate-500 print:border-black">
                                            {{ $row['time'] }}
                                        </td>
                                        <td
                                            :colspan="activeClass === 'all' ? 12 : 2"
                                            class="py-1.5 text-center text-[11px] font-black uppercase tracking-[.3em] text-slate-500 print:border-black"
                                        >
                                            {{ $row['break_title'] }}
                                        </td>
                                    </tr>
                                @else
                                    {{-- Baris Pelajaran Reguler --}}
                                    <tr
                                        x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                                        class="hover:bg-blue-50/30 transition print:hover:bg-transparent"
                                    >
                                        <td class="border-r border-slate-200 px-2 py-2 font-bold uppercase text-slate-500 print:border-black align-middle">
                                            {{ $day['day_name'] }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 font-black text-slate-800 print:border-black align-middle">
                                            {{ $row['les'] }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 font-semibold whitespace-nowrap text-slate-600 print:border-black align-middle text-[11px]">
                                            {{ $row['time'] }}
                                        </td>

                                        @foreach ($classes as $classKey => $classMeta)
                                            @php
                                                $slot = $row['slots'][$classKey] ?? null;
                                                $subjectCode = $slot['subject'] ?? '';
                                                $teacherCode = $slot['teacher'] ?? '';
                                                $style = $subjectStyles[$subjectCode] ?? [
                                                    'bg' => '#64748B',
                                                    'text' => '#FFFFFF',
                                                ];
                                            @endphp

                                            {{-- Kolom Bid Studi --}}
                                            <td
                                                x-show="activeClass === 'all' || activeClass === '{{ $classKey }}'"
                                                class="border-r border-slate-200 px-1.5 py-2 font-bold transition print:border-black align-middle"
                                                :class="{
                                                    'ring-2 ring-blue-500 ring-inset bg-blue-100 font-black': highlightTeacher('{{ $teacherCode }}'),
                                                    'opacity-30': selectedTeacher && !highlightTeacher('{{ $teacherCode }}')
                                                }"
                                            >
                                                @if ($slot)
                                                    <span
                                                        class="inline-block w-full max-w-[70px] truncate rounded px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-xs print:border print:border-black"
                                                        style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }};"
                                                        title="{{ $subjectStyles[$subjectCode]['name'] ?? $subjectCode }}"
                                                    >
                                                        {{ $subjectCode }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-300 font-normal">—</span>
                                                @endif
                                            </td>

                                            {{-- Kolom Kode Guru --}}
                                            <td
                                                x-show="activeClass === 'all' || activeClass === '{{ $classKey }}'"
                                                class="border-r border-slate-200 px-1 py-2 font-black text-slate-800 last:border-r-0 transition print:border-black align-middle"
                                                :class="{
                                                    'ring-2 ring-blue-500 ring-inset bg-blue-100 text-blue-900 scale-105 font-black': highlightTeacher('{{ $teacherCode }}'),
                                                    'opacity-30': selectedTeacher && !highlightTeacher('{{ $teacherCode }}')
                                                }"
                                            >
                                                @if ($slot)
                                                    <button
                                                        type="button"
                                                        @click="selectedTeacher = (selectedTeacher === '{{ $teacherCode }}' ? '' : '{{ $teacherCode }}')"
                                                        title="{{ $teachers[$teacherCode]['name'] ?? $teacherCode }} (Klik untuk sorot)"
                                                        class="rounded px-1.5 py-0.5 text-[11px] font-black text-slate-800 hover:bg-slate-200 transition focus:outline-none"
                                                    >
                                                        {{ $teacherCode }}
                                                    </button>
                                                @else
                                                    <span class="text-slate-300 font-normal">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ========================================================
             TAMPILAN 2: JADWAL SAYA (KARTU MENGAJAR BUDI SANTOSO / USER)
             ======================================================== --}}
        <div x-show="viewMode === 'my_schedule'" class="space-y-6">
            <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">
                            <svg class="h-3.5 w-3.5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Jadwal Khusus Guru</span>
                        </div>
                        <h3 class="text-xl font-black text-[#101C35] mt-2">
                            Jadwal Mengajar Anda: {{ auth()->user()->name }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">
                            Kode Guru Terdaftar:
                            @foreach ($currentTeacherCodes as $c)
                                <span class="font-extrabold text-[#123B60] underline">{{ $c }} ({{ $teachers[$c]['desc'] ?? $teachers[$c]['subject'] }})</span>@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>

                    <a
                        href="{{ route('guru.classes.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5]"
                    >
                        <span>Buka E-Class (Ruang Belajar)</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            @if (empty($mySchedule))
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400">
                    <p class="text-base font-bold text-slate-600">Tidak ada jadwal mengajar yang terdaftar untuk akun ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Silakan gunakan tab "Matrix Roster Sekolah" untuk melihat jadwal seluruh kelas.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($mySchedule as $dayKey => $group)
                        <div
                            x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="border-b border-slate-100 bg-gradient-to-r from-blue-50/80 to-white px-5 py-3.5 flex items-center justify-between">
                                <h4 class="font-black text-sm uppercase text-[#123B60] flex items-center gap-2">
                                    <svg class="h-4 w-4 text-[#123B60]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                                    </svg>
                                    <span>Hari {{ $group['day_name'] }}</span>
                                </h4>
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-blue-800">
                                    {{ count($group['items']) }} Jam / Sesi
                                </span>
                            </div>

                            <div class="divide-y divide-slate-100 p-2">
                                @foreach ($group['items'] as $item)
                                    @php
                                        $subStyle = $subjectStyles[$item['subject']] ?? ['bg' => '#123B60', 'text' => '#fff'];
                                    @endphp
                                    <div
                                        x-show="activeClass === 'all' || activeClass === '{{ $item['class'] }}'"
                                        class="flex items-center justify-between rounded-xl p-3 hover:bg-slate-50 transition"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-extrabold text-xs text-[#123B60]">
                                                Les {{ $item['les'] }}
                                            </span>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold text-xs text-slate-800">{{ $item['class'] }}</span>
                                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-black uppercase text-white" style="background-color: {{ $subStyle['bg'] }}; color: {{ $subStyle['text'] }};">
                                                        {{ $item['subject'] }}
                                                    </span>
                                                </div>
                                                <p class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 mt-0.5">
                                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                                    </svg>
                                                    <span>{{ $item['time'] }} WIB</span>
                                                </p>
                                            </div>
                                        </div>

                                        <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] font-bold text-slate-600">
                                            Kode: {{ $item['teacher'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ========================================================
             TAMPILAN 3: PEMBAGIAN TUGAS GURU (SK BEBAN MENGAJAR RESMI)
             ======================================================== --}}
        <div x-show="viewMode === 'duties'" class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#123B60]">
                            <svg class="h-3.5 w-3.5 text-[#123B60]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>SK Pembagian Tugas Guru TP {{ $metadata['academic_year'] }}</span>
                        </span>
                        <h3 class="text-xl font-black text-[#101C35] mt-2">
                            Pembagian Tugas Guru SMA Methodist Banda Aceh
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Alokasi jam mengajar resmi per kelas (X IPAS A s/d XII B) dan tugas tambahan guru.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-slate-50 px-4 py-2 border border-slate-200 text-right">
                            <span class="block text-[10px] font-bold uppercase text-slate-400">Total Jam Mengajar</span>
                            <span class="text-lg font-black text-[#123B60]">296 Jam / Minggu</span>
                        </div>
                    </div>
                </div>

                {{-- TABEL PEMBAGIAN TUGAS GURU --}}
                <div class="overflow-x-auto mt-4">
                    <table class="w-full min-w-[1050px] table-fixed border-collapse text-xs print:text-[10px]">
                        <colgroup>
                            <col style="width: 45px;">   {{-- No --}}
                            <col style="width: 220px;">  {{-- Nama Guru --}}
                            <col style="width: 190px;">  {{-- Jabatan --}}
                            <col style="width: 150px;">  {{-- Mapel --}}
                            <col style="width: 65px;">   {{-- X IPAS A --}}
                            <col style="width: 65px;">   {{-- X IPAS B --}}
                            <col style="width: 65px;">   {{-- XI A --}}
                            <col style="width: 65px;">   {{-- XI B --}}
                            <col style="width: 65px;">   {{-- XII A --}}
                            <col style="width: 65px;">   {{-- XII B --}}
                            <col style="width: 70px;">   {{-- Jumlah --}}
                        </colgroup>
                        <thead>
                            <tr class="bg-[#123B60] text-white print:bg-slate-200 print:text-black">
                                <th rowspan="2" class="border border-white/20 px-2 py-3 font-bold text-center">No</th>
                                <th rowspan="2" class="border border-white/20 px-3 py-3 font-bold text-left">Nama Guru dan Tenaga Pendidik</th>
                                <th rowspan="2" class="border border-white/20 px-3 py-3 font-bold text-left">Jabatan / Tugas Tambahan</th>
                                <th rowspan="2" class="border border-white/20 px-3 py-3 font-bold text-left">Mata Pelajaran</th>
                                <th colspan="6" class="border border-white/20 px-2 py-2 font-bold text-center">Jam Mengajar / Kelas</th>
                                <th rowspan="2" class="border border-white/20 px-2 py-3 font-bold text-center bg-[#0B2B48]">Jumlah</th>
                            </tr>
                            <tr class="bg-[#1B4D7E] text-[10px] font-bold text-slate-100 print:bg-slate-100 print:text-black text-center">
                                <th class="border border-white/20 px-1 py-1.5">X IPAS A</th>
                                <th class="border border-white/20 px-1 py-1.5">X IPAS B</th>
                                <th class="border border-white/20 px-1 py-1.5">XI A</th>
                                <th class="border border-white/20 px-1 py-1.5">XI B</th>
                                <th class="border border-white/20 px-1 py-1.5">XII A</th>
                                <th class="border border-white/20 px-1 py-1.5">XII B</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($teacherDuties as $duty)
                                <tr class="hover:bg-blue-50/40 transition {{ $duty['no'] % 2 === 0 ? 'bg-slate-50/50' : 'bg-white' }}">
                                    <td class="border border-slate-200 px-2 py-2.5 text-center font-bold text-slate-500">
                                        {{ $duty['no'] }}
                                    </td>
                                    <td class="border border-slate-200 px-3 py-2.5 font-bold text-[#101C35]">
                                        <div class="flex items-center justify-between gap-2">
                                            <span>{{ $duty['name'] }}</span>
                                            @if ($duty['code'])
                                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-black text-[#123B60] border border-slate-200">
                                                    {{ $duty['code'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="border border-slate-200 px-3 py-2.5 text-slate-600 font-medium">
                                        {{ $duty['role'] }}
                                    </td>
                                    <td class="border border-slate-200 px-3 py-2.5 font-semibold text-[#123B60]">
                                        {{ $duty['subject'] }}
                                    </td>
                                    @foreach (['X IPAS A', 'X IPAS B', 'XI A', 'XI B', 'XII A', 'XII B'] as $cName)
                                        <td class="border border-slate-200 px-2 py-2.5 text-center font-bold {{ $duty['hours'][$cName] ? 'text-slate-900 bg-blue-50/20' : 'text-slate-300' }}">
                                            {{ $duty['hours'][$cName] ?? '—' }}
                                        </td>
                                    @endforeach
                                    <td class="border border-slate-200 px-2 py-2.5 text-center font-black text-[#123B60] bg-slate-50">
                                        {{ $duty['total'] ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-[#123B60] text-white font-black text-xs print:bg-slate-300 print:text-black text-center">
                                <td colspan="4" class="border border-white/20 px-4 py-3 text-right uppercase tracking-wider">
                                    Total Jam Pelajaran / Minggu:
                                </td>
                                <td class="border border-white/20 px-2 py-3">50</td>
                                <td class="border border-white/20 px-2 py-3">50</td>
                                <td class="border border-white/20 px-2 py-3">50</td>
                                <td class="border border-white/20 px-2 py-3">50</td>
                                <td class="border border-white/20 px-2 py-3">48</td>
                                <td class="border border-white/20 px-2 py-3">48</td>
                                <td class="border border-white/20 px-2 py-3 bg-[#0B2B48] text-sm text-yellow-300">296</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
