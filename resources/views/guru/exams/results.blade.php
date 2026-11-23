<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">Methodist Learning Space</p>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Hasil Ujian: {{ $exam->title }}
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $exam->classSubjectTeacher?->schoolClass?->name }} — {{ $exam->classSubjectTeacher?->subject?->name }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#FAF9F6] py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

            {{-- BREADCRUMB dan TOMBOL KEMBALI --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('guru.exams.index') }}" class="transition hover:text-blue-600">
                        E-Exam
                    </a>
                    <span>/</span>
                    <span class="font-bold text-blue-700">Hasil dan Nilai</span>
                </div>

                <a href="{{ route('guru.exams.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>←</span>
                    <span>Kembali ke Daftar Ujian</span>
                </a>
            </div>

            {{-- RINGKASAN STATISTIK UJIAN (Dari View v_exam_statistics) --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Esai menunggu koreksi</div>
                    <div class="mt-1 text-2xl font-bold text-amber-600">{{ $pendingEssayCount }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="text-xs font-semibold text-slate-500">Total pelanggaran tercatat</div>
                    <div class="mt-1 text-2xl font-bold text-rose-600">
                        {{ $violationSummaries->flatten(1)->sum('total') }}
                    </div>
                </div>
            </div>

            @if(isset($examStats))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-2xl shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold text-slate-500">Rata-rata Nilai</div>
                        <div class="text-2xl font-bold text-[#123B60] mt-1">{{ $examStats->avg_score ?? '0.00' }}</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold text-slate-500">Nilai Tertinggi / Terendah</div>
                        <div class="text-2xl font-bold text-slate-800 mt-1">
                            {{ $examStats->max_score ?? '0' }} <span class="text-sm font-normal text-slate-400">/ {{ $examStats->min_score ?? '0' }}</span>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold text-slate-500">Tingkat Ketuntasan (KKM {{ $exam->pass_score }})</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $examStats->pass_rate ?? '0' }}%</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold text-slate-500">Sesi Selesai</div>
                        <div class="text-2xl font-bold text-slate-800 mt-1">
                            {{ $examStats->finished_sessions ?? 0 }} <span class="text-sm font-normal text-slate-400">/ {{ $examStats->total_sessions ?? $sessions->count() }}</span>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="bg-white p-6 shadow-sm ring-1 ring-slate-200 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Nilai Siswa</h3>
                    <span class="text-xs font-bold text-[#123B60] bg-[#E8F1F8] px-3 py-1.5 rounded-xl">
                        Total Peserta: {{ $sessions->count() }} Siswa
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-700 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center">Waktu dan Durasi</th>
                                <th class="px-4 py-3 text-center">Status dan Pelanggaran</th>
                                <th class="px-4 py-3 text-center">Nilai Akhir</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($sessions as $session)
                                @php
                                    // Ambil hasil kalkulasi dari Database View v_exam_results
                                    $res = isset($examResultsView) ? $examResultsView->get($session->exam_session_id) : null;
                                    $finalScore = $res ? (float) $res->final_score : 0;
                                    $isFinished = in_array($session->status, ['selesai', 'keluar'], true);
                                    $waktuSelesai = $session->finished_at ?? ($isFinished ? $session->updated_at : null);
                                    $gradedEssayCount = (int) $gradedEssayCounts->get($session->student_id, 0);
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    
                                    <!-- NAMA SISWA -->
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $session->student->user->name ?? 'Anonim' }}
                                        <div class="text-[10px] text-slate-400 font-normal mt-0.5">
                                            NIS: {{ $session->student_id }} | NISN: {{ $session->student->nisn ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- WAKTU dan DURASI -->
                                    <td class="px-4 py-3 text-center text-xs">
                                        <div class="flex items-center justify-center gap-3 flex-wrap">
                                            <div class="text-slate-500">
                                                <span class="font-bold">Mulai:</span> {{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('H:i:s') : '-' }}
                                            </div>
                                            <div class="text-slate-500">
                                                <span class="font-bold">Selesai:</span> {{ $waktuSelesai ? \Carbon\Carbon::parse($waktuSelesai)->format('H:i:s') : '-' }}
                                            </div>

                                            @if($isFinished && $session->started_at && $waktuSelesai)
                                                @php
                                                    $durasiDetik = max(0, (int) \Carbon\Carbon::parse($session->started_at)->diffInSeconds(\Carbon\Carbon::parse($waktuSelesai)));
                                                    $durasiMenit = intdiv($durasiDetik, 60);
                                                    $sisaDetik = $durasiDetik % 60;
                                                @endphp
                                                <div class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">
                                                    <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                                    </svg>
                                                    <span>{{ $durasiMenit }}m {{ $sisaDetik }}d</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- STATUS dan PELANGGARAN -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            @if($session->status === 'selesai')
                                                <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Selesai</span>
                                            @elseif($session->status === 'keluar')
                                                <span class="inline-flex items-center rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">Dikeluarkan</span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Berlangsung</span>
                                            @endif

                                            @if($session->violations_count > 0)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">
                                                    <svg class="h-3 w-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <span>{{ $session->violations_count }} Pelanggaran</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                                    Aman (0 Pelanggaran)
                                                </span>
                                            @endif

                                            @if($essayQuestionCount > 0)
                                                @if($gradedEssayCount >= $essayQuestionCount)
                                                    <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                                        Esai dicek {{ $gradedEssayCount }}/{{ $essayQuestionCount }}
                                                    </span>
                                                @elseif($gradedEssayCount > 0)
                                                    <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                        Esai sebagian dicek {{ $gradedEssayCount }}/{{ $essayQuestionCount }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                        Esai belum dicek 0/{{ $essayQuestionCount }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                                    Tidak ada esai
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- NILAI AKHIR dan STATUS REMEDIAL -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="font-bold text-lg {{ $finalScore >= $exam->pass_score ? 'text-[#123B60]' : 'text-rose-600' }}">
                                            {{ number_format($finalScore, 2) }}
                                        </div>
                                        
                                        @if($isFinished)
                                            <div class="mt-1">
                                                @if($finalScore >= $exam->pass_score)
                                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                        TUNTAS
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                        REMEDIAL
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-1 text-[10px] text-slate-400 font-semibold">-</div>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('guru.exams.session', $session->exam_session_id) }}" class="text-[11px] font-bold text-white bg-[#123B60] hover:bg-[#0B2B48] px-3 py-2 rounded-lg transition-colors inline-block">
                                            Cek Detail Jawaban
                                        </a>
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                            </div>
                                            <span>Belum ada siswa yang mengerjakan ujian ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>