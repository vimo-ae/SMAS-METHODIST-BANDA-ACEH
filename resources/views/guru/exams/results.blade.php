<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Hasil Ujian: {{ $exam->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $exam->classSubjectTeacher?->schoolClass?->name }} — {{ $exam->classSubjectTeacher?->subject?->name }}
                </p>
            </div>
            <a href="{{ route('guru.exams.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 shadow-sm ring-1 ring-slate-200 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Nilai Siswa</h3>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl">
                        Total Peserta: {{ $sessions->count() }} Siswa
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-700 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center">Waktu & Durasi</th>
                                <th class="px-4 py-3 text-center">Status & Pelanggaran</th>
                                <th class="px-4 py-3 text-center">Nilai Akhir</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($sessions as $session)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    
                                    <!-- NAMA SISWA -->
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $session->student->user->name ?? 'Anonim' }}
                                        <div class="text-[10px] text-slate-400 font-normal mt-0.5">NISN: {{ $session->student->nisn ?? '-' }}</div>
                                    </td>

                                    <!-- WAKTU & DURASI (Sejajar ke samping) -->
                                    <td class="px-4 py-3 text-center text-xs">
                                        <div class="flex items-center justify-center gap-3 flex-wrap">
                                            <div class="text-slate-500">
                                                <span class="font-bold">Mulai:</span> {{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('H:i') : '-' }}
                                            </div>
                                            <div class="text-slate-500">
                                                @php
                                                    // Pakai finished_at (jika ada), atau updated_at sebagai fallback waktu selesai
                                                    $waktuSelesai = $session->finished_at ?? ($session->status === 'selesai' ? $session->updated_at : null);
                                                @endphp
                                                <span class="font-bold">Selesai:</span> {{ $waktuSelesai ? \Carbon\Carbon::parse($waktuSelesai)->format('H:i') : '-' }}
                                            </div>

                                            @if($session->status === 'selesai' && $session->started_at && $waktuSelesai)
                                                @php
                                                    $durasi = \Carbon\Carbon::parse($session->started_at)->diffInMinutes(\Carbon\Carbon::parse($waktuSelesai));
                                                @endphp
                                                <div class="inline-flex items-center bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">
                                                    ⏱️ {{ $durasi }} Menit
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- STATUS & PELANGGARAN (Sejajar ke samping) -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            @if($session->status === 'selesai')
                                                <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Berlangsung</span>
                                            @endif

                                            @if($session->violations_count > 0)
                                                <span class="inline-flex items-center rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">
                                                    ⚠️ {{ $session->violations_count }} Pelanggaran
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                                    Aman (0 Pelanggaran)
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- NILAI AKHIR & STATUS REMEDIAL -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="font-bold text-lg {{ ($session->score ?? 0) >= $exam->pass_score ? 'text-indigo-600' : 'text-rose-600' }}">
                                            {{ $session->score ?? 0 }}
                                        </div>
                                        
                                        @if($session->status === 'selesai')
                                            <div class="mt-1">
                                                @if(($session->score ?? 0) >= $exam->pass_score)
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
                                        <a href="{{ route('guru.exams.session', $session) }}" class="text-[11px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-lg transition-colors inline-block">
                                            Cek Detail Jawaban
                                        </a>
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-2xl mb-2">📭</span>
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