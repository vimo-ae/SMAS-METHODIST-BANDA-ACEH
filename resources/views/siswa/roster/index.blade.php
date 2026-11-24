<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    Roster Pelajaran Siswa
                </h2>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-bold text-[#123B60] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Kelas: <strong>{{ $rawClassName }}</strong> &bull; TP {{ $metadata['academic_year'] }}
                </span>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            viewMode: 'class_schedule', // 'class_schedule' atau 'matrix'
            selectedClass: '{{ $selectedClass }}',
            activeDay: 'all',
            showLegend: false,
        }"
        class="space-y-6 py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    >
        {{-- BREADCRUMB dan AKSI CEPAT --}}
        <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
            <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                <a href="{{ route('siswa.dashboard') }}" class="transition hover:text-blue-600">
                    Dashboard
                </a>
                <span>/</span>
                <span class="font-bold text-blue-700">Roster Pelajaran</span>
                <span>/</span>
                <span class="text-slate-600 font-semibold">{{ $selectedClass }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('siswa.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>&larr;</span>
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
             BANNER KOP RESMI ROSTER KELAS
             ======================================================== --}}
        <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-r from-[#101C35] via-[#123B60] to-[#1B4D7E] p-6 sm:p-8 text-white shadow-lg print:border-none print:bg-white print:p-0 print:text-slate-900 print:shadow-none">
            {{-- Aksen Latar --}}
            <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
            <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-blue-400/10 blur-2xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white p-2.5 shadow-inner print:h-12 print:w-12">
                        <img src="{{ asset('images/methodist-logo.png') }}" alt="Logo Methodist" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-md bg-white/10 px-2.5 py-0.5 text-[10px] font-bold tracking-widest uppercase text-cyan-200 print:hidden">
                            Dokumen Resmi Kurikulum &bull; SMAS Methodist Banda Aceh
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight print:text-xl print:text-[#101C35]">
                            ROSTER PELAJARAN KELAS {{ strtoupper($selectedClass) }}
                        </h1>
                        <p class="text-xs text-slate-200 sm:text-sm print:text-xs print:text-slate-600 mt-1">
                            Tahun Ajaran {{ $metadata['academic_year'] }} &bull; Berlaku Efektif Semester Ganjil dan Genap
                        </p>
                    </div>
                </div>

                {{-- SWITCH VIEW MODE dan PILIHAN KELAS --}}
                <div class="flex flex-wrap items-center gap-2.5 border-t border-white/10 pt-4 md:border-t-0 md:pt-0 print:hidden">
                    <button
                        type="button"
                        @click="viewMode = 'class_schedule'"
                        :class="viewMode === 'class_schedule' ? 'bg-white text-[#123B60] shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20 font-bold'"
                        class="rounded-xl px-4 py-2.5 text-xs transition flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        <span>Jadwal Mingguan Kelas</span>
                    </button>

                    <button
                        type="button"
                        @click="viewMode = 'matrix'"
                        :class="viewMode === 'matrix' ? 'bg-white text-[#123B60] shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20 font-bold'"
                        class="rounded-xl px-4 py-2.5 text-xs transition flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                        <span>Matrix Seluruh Kelas</span>
                    </button>
                </div>
            </div>

            {{-- SELECTOR PILIH KELAS LAIN --}}
            <div class="mt-6 pt-5 border-t border-white/15 flex flex-wrap items-center gap-2 text-xs print:hidden">
                <span class="text-xs text-blue-200 font-semibold mr-1">Pilih Rombel Kelas:</span>
                @foreach($classes as $cKey => $cInfo)
                    @php
                        $isCurrent = (strtoupper($selectedClass) === strtoupper($cInfo['label']) || strtoupper($selectedClass) === strtoupper($cKey));
                    @endphp
                    <a href="{{ route('siswa.roster', ['class' => $cInfo['label']]) }}"
                       class="rounded-xl px-3.5 py-1.5 font-bold transition shadow-sm {{ $isCurrent ? 'bg-cyan-400 text-[#091a30] shadow-cyan-400/30' : 'bg-white/15 text-white hover:bg-white/25' }}">
                        {{ $cInfo['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ========================================================
             BANNER JADWAL HARI INI (TODAY'S HIGHLIGHT)
             ======================================================== --}}
        @if(!empty($todaySchedule['items']))
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm print:hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                            Jadwal Belajar Hari Ini ({{ $todaySchedule['day_name'] }}) &bull; Kelas {{ $selectedClass }}
                        </h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">
                        {{ count($todaySchedule['items']) }} Sesi Terjadwal
                    </span>
                </div>

                @php
                    $todayGrouped = App\Services\RosterService::groupConsecutiveSlots($todaySchedule['items'] ?? []);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    @foreach($todayGrouped as $item)
                        @if(!empty($item['is_break']) || !empty($item['is_special']))
                            <div class="rounded-xl border border-dashed border-amber-200 bg-amber-50/60 p-3 text-center flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">{{ $item['time'] }}</span>
                                <p class="text-xs font-bold text-amber-900 mt-0.5">{{ $item['activity'] ?? 'Kegiatan Khusus / Istirahat' }}</p>
                            </div>
                        @else
                            @php
                                $sCode = $item['subject'];
                                $style = $subjectStyles[$sCode] ?? ['bg' => '#EFF6FF', 'text' => '#1E40AF', 'border' => '#BFDBFE'];
                            @endphp
                            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 hover:bg-white hover:shadow-sm transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold mb-1">
                                        <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[9px] text-slate-700 font-mono">{{ $item['les_label'] }}</span>
                                        <span>{{ $item['time'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase shrink-0"
                                              style="background-color: {{ $style['bg'] ?? '#EFF6FF' }}; color: {{ $style['text'] ?? '#1E40AF' }}; border: 1px solid {{ $style['border'] ?? $style['bg'] ?? '#BFDBFE' }};">
                                            {{ $item['subject'] }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-800 truncate" title="{{ $item['subject_desc'] ?? $item['subject'] }}">
                                            {{ $item['subject_desc'] ?? $item['subject'] }}
                                        </span>
                                    </div>
                                </div>
                                @if(!empty($item['teacher_name']))
                                    <p class="text-[11px] text-slate-500 mt-2 truncate pt-1 border-t border-slate-100" title="{{ $item['teacher_name'] }}">
                                        Guru: <strong class="text-slate-700">{{ $item['teacher_name'] }}</strong> ({{ $item['teacher'] }})
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ========================================================
             TAMPILAN 1: JADWAL MINGGUAN KELAS (SENIN - SABTU)
             ======================================================== --}}
        <div x-show="viewMode === 'class_schedule'" x-transition class="space-y-6">
            
            {{-- FILTER HARI CEPAT --}}
            <div class="flex flex-wrap items-center gap-2 print:hidden">
                <span class="text-xs font-bold text-slate-500 mr-1">Filter Hari:</span>
                <button
                    type="button"
                    @click="activeDay = 'all'"
                    :class="activeDay === 'all' ? 'bg-[#123B60] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition"
                >
                    Semua Hari (Senin - Sabtu)
                </button>
                @foreach($myClassSchedule as $dKey => $dData)
                    <button
                        type="button"
                        @click="activeDay = '{{ $dKey }}'"
                        :class="activeDay === '{{ $dKey }}' ? 'bg-[#123B60] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition"
                    >
                        {{ $dData['day_name'] }}
                    </button>
                @endforeach
            </div>

            {{-- GRID HARI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($myClassSchedule as $dayKey => $dayData)
                    <div
                        x-show="activeDay === 'all' || activeDay === '{{ $dayKey }}'"
                        class="rounded-3xl border border-slate-200 bg-white overflow-hidden shadow-sm flex flex-col justify-between"
                    >
                        <div>
                            {{-- HEADER HARI --}}
                            <div class="bg-gradient-to-r from-[#123B60] to-[#1B4D7E] p-4 text-white flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <path d="M16 2v4M8 2v4M3 10h18"/>
                                    </svg>
                                    <h3 class="font-black text-base tracking-wide">{{ strtoupper($dayData['day_name']) }}</h3>
                                </div>
                                <span class="rounded-lg bg-white/20 px-2 py-0.5 text-[10px] font-bold text-white uppercase backdrop-blur-sm">
                                    {{ $selectedClass }}
                                </span>
                            </div>

                            {{-- DAFTAR LES dan MAPEL --}}
                            <div class="divide-y divide-slate-100">
                                @php
                                    $groupedSlots = App\Services\RosterService::groupConsecutiveSlots($dayData['items'] ?? []);
                                @endphp
                                @foreach($groupedSlots as $slot)
                                    @if(!empty($slot['is_break']) || !empty($slot['is_special']))
                                        {{-- BARIS ISTIRAHAT / KEGIATAN KHUSUS --}}
                                        <div class="bg-amber-50/70 p-2.5 text-center border-l-4 border-amber-400">
                                            <p class="text-[10px] font-bold text-amber-700 tracking-wider">{{ $slot['time'] }}</p>
                                            <p class="text-xs font-black text-amber-900 mt-0.5">{{ $slot['activity'] ?? 'Kegiatan Khusus / Istirahat' }}</p>
                                        </div>
                                    @else
                                        @php
                                            $sCode = $slot['subject'];
                                            $style = $subjectStyles[$sCode] ?? ['bg' => '#F1F5F9', 'text' => '#334155', 'border' => '#CBD5E1'];
                                        @endphp
                                        <div class="p-3 hover:bg-slate-50/80 transition flex items-start gap-2.5">
                                            <div class="flex flex-col items-center justify-center px-2 py-1 shrink-0 rounded-xl bg-slate-100 text-slate-700 font-mono text-[10px] font-bold border border-slate-200">
                                                <span>{{ $slot['les_label'] }}</span>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wider uppercase"
                                                          style="background-color: {{ $style['bg'] ?? '#F1F5F9' }}; color: {{ $style['text'] ?? '#334155' }}; border: 1px solid {{ $style['border'] ?? $style['bg'] ?? '#CBD5E1' }};">
                                                        {{ $slot['subject'] }}
                                                    </span>
                                                    <span class="text-[11px] font-semibold text-slate-400 font-mono">
                                                        {{ $slot['time'] }}
                                                    </span>
                                                </div>

                                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 mt-1 truncate" title="{{ $slot['subject_desc'] ?? $slot['subject'] }}">
                                                    {{ $slot['subject_desc'] ?? $slot['subject'] }}
                                                </h4>

                                                @if(!empty($slot['teacher_name']))
                                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate" title="{{ $slot['teacher_name'] }}">
                                                        Guru: <strong class="text-slate-700">{{ $slot['teacher_name'] }}</strong>
                                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">({{ $slot['teacher'] }})</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- FOOTER KARTU --}}
                        <div class="bg-slate-50 p-3 text-center border-t border-slate-100 text-[10px] font-semibold text-slate-400">
                            Roster Resmi SMAS Methodist Banda Aceh
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- ========================================================
             TAMPILAN 2: MATRIX ROSTER SELURUH KELAS
             ======================================================== --}}
        <div x-show="viewMode === 'matrix'" x-transition class="space-y-6">
            <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                @foreach($allRosterData as $dayKey => $dayData)
                    <div class="border-b-4 border-slate-100 last:border-b-0">
                        <div class="bg-[#123B60] px-6 py-3 text-white flex items-center justify-between">
                            <h3 class="font-black text-sm uppercase tracking-wider">{{ $dayData['day_name'] }}</h3>
                            <span class="text-xs text-blue-200 font-semibold">{{ count($dayData['rows']) }} Jam Pelajaran</span>
                        </div>

                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 font-extrabold uppercase border-b border-slate-200">
                                    <th class="p-3 w-12 text-center">Les</th>
                                    <th class="p-3 w-28 text-center">Waktu</th>
                                    @foreach($classes as $cKey => $cInfo)
                                        <th class="p-3 text-center border-l border-slate-200 {{ (strtoupper($selectedClass) === strtoupper($cInfo['label']) || strtoupper($selectedClass) === strtoupper($cKey)) ? 'bg-blue-100/70 text-[#123B60]' : '' }}">
                                            {{ $cInfo['label'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($dayData['rows'] as $row)
                                    @if(!empty($row['is_break']) || !empty($row['is_special']))
                                        <tr class="bg-amber-50 text-center text-amber-900 font-bold">
                                            <td class="p-2.5 text-center font-mono">#</td>
                                            <td class="p-2.5 font-mono text-[11px]">{{ $row['time'] }}</td>
                                            <td colspan="{{ count($classes) }}" class="p-2.5 uppercase tracking-wider text-xs">
                                                {{ $row['activity'] ?? $row['special_title'] ?? $row['break_title'] ?? 'Kegiatan Khusus / Istirahat' }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-2.5 text-center font-bold text-slate-500 font-mono">
                                                {{ $row['les'] }}
                                            </td>
                                            <td class="p-2.5 text-center font-mono text-[11px] text-slate-500">
                                                {{ $row['time'] }}
                                            </td>
                                            @foreach($classes as $cKey => $cInfo)
                                                @php
                                                    $slot = $row['slots'][$cKey] ?? null;
                                                    $isMyClass = (strtoupper($selectedClass) === strtoupper($cInfo['label']) || strtoupper($selectedClass) === strtoupper($cKey));
                                                @endphp
                                                <td class="p-2.5 text-center border-l border-slate-100 {{ $isMyClass ? 'bg-blue-50/50 font-bold' : '' }}">
                                                    @if($slot)
                                                        @php
                                                            $style = $subjectStyles[$slot['subject']] ?? ['bg' => '#F1F5F9', 'text' => '#334155', 'border' => '#CBD5E1'];
                                                        @endphp
                                                        <div class="inline-flex flex-col items-center">
                                                            <span class="rounded px-1.5 py-0.5 text-[10px] font-extrabold uppercase"
                                                                  style="background-color: {{ $style['bg'] ?? '#F1F5F9' }}; color: {{ $style['text'] ?? '#334155' }}; border: 1px solid {{ $style['border'] ?? $style['bg'] ?? '#CBD5E1' }};">
                                                                {{ $slot['subject'] }}
                                                            </span>
                                                            <span class="text-[9px] font-semibold text-slate-500 mt-0.5">
                                                                {{ $slot['teacher'] }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-slate-300">&mdash;</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========================================================
             DAFTAR KODE GURU dan MATA PELAJARAN (LEGENDA RESMI)
             ======================================================== --}}
        <div x-data="{ openTeachers: false }" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between cursor-pointer select-none" @click="openTeachers = !openTeachers">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-[#123B60]"></span>
                    <h3 class="font-black text-sm text-slate-800 uppercase tracking-wider">
                        Daftar Guru Pengampu dan Kode Mapel Resmi
                    </h3>
                    <span class="text-[11px] text-slate-400 font-semibold hidden sm:inline">
                        ({{ count($teachers) }} Tenaga Pendidik)
                    </span>
                </div>
                <button type="button" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1.5 transition">
                    <span x-text="openTeachers ? 'Tutup Daftar Guru' : 'Buka Daftar Guru'"></span>
                    <svg :class="openTeachers ? 'rotate-180' : ''" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            <div x-show="openTeachers" x-collapse x-transition class="pt-2 border-t border-slate-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
                    @foreach($teachers as $tCode => $t)
                        <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/60 flex items-start gap-2.5">
                            <span class="h-8 w-8 rounded-xl bg-[#123B60] text-white flex items-center justify-center font-mono font-bold text-xs shrink-0 shadow-sm">
                                {{ $t['code'] }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 truncate" title="{{ $t['name'] }}">{{ $t['name'] }}</p>
                                <p class="text-[11px] text-blue-700 font-semibold">{{ $t['desc'] }} ({{ $t['subject'] }})</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ $t['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
