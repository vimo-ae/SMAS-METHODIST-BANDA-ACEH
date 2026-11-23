@extends('layouts.public')

@section('title', 'Hasil Nilai Ujian | SMAS Methodist Banda Aceh')

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('cek-nilai-ujian.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-navy transition hover:text-softblue">
                    <span aria-hidden="true">&larr;</span> Kembali ke Form Cek Nilai
                </a>
                <p class="mt-6 text-xs font-bold uppercase tracking-[.18em] text-softblue">Hasil Akademik</p>
                <h1 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">{{ $student->student_name ?? 'Siswa' }}</h1>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $student->class_name ?? 'Kelas belum ditentukan' }} · Riwayat ujian yang telah selesai
                </p>
            </div>
            <a href="{{ url('/') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-navy shadow-sm transition hover:bg-slate-50">
                Beranda
            </a>
        </div>

        <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50/70 p-4 text-sm leading-6 text-blue-900">
            Skor objektif mencakup pilihan ganda dan tipe soal non-esai. Nilai akhir berstatus sementara jika masih ada jawaban esai yang menunggu koreksi.
        </div>

        <section aria-labelledby="exam-results-heading" class="mt-8">
            <h2 id="exam-results-heading" class="sr-only">Daftar hasil ujian</h2>
            <div class="space-y-5">
                @forelse ($exams as $exam)
                    @php
                        $essayCount = (int) $exam->essay_count;
                        $pendingEssayCount = (int) $exam->pending_essay_count;
                        $isFinal = $pendingEssayCount === 0;
                        $resultDate = $exam->finished_at ?? $exam->left_at ?? $exam->updated_at;
                    @endphp

                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-4 border-b border-slate-100 bg-white p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-navy">
                                        {{ strtoupper($exam->exam_type) }}
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider {{ $exam->status === 'selesai' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $exam->status === 'selesai' ? 'Selesai' : 'Dihentikan' }}
                                    </span>
                                </div>
                                <h3 class="mt-3 font-serif text-xl font-bold text-navy">{{ $exam->title }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $exam->subject_name }} · {{ $exam->class_name }}</p>
                                <p class="mt-2 text-xs text-slate-500">
                                    Selesai:
                                    {{ $resultDate ? \Carbon\Carbon::parse($resultDate)->translatedFormat('d M Y, H:i') : 'Waktu tidak tersedia' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4 sm:p-6">
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-slate-500">Skor Objektif</p>
                                <p class="mt-1 text-2xl font-bold text-navy">
                                    {{ number_format((float) $exam->objective_score, 2) }}
                                    <span class="text-base font-semibold text-slate-400">/ {{ number_format((float) $exam->objective_max_score, 2) }}</span>
                                </p>
                                <p class="mt-1 text-[11px] text-slate-500">Poin diperoleh / maksimal · Pilihan ganda dan soal non-esai</p>
                            </div>

                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-slate-500">Skor Esai</p>
                                @if ($essayCount === 0)
                                    <p class="mt-1 text-base font-semibold text-slate-500">Tidak ada soal esai</p>
                                @else
                                    <p class="mt-1 text-2xl font-bold text-navy">
                                        {{ number_format((float) $exam->essay_score, 2) }}
                                        <span class="text-base font-semibold text-slate-400">/ {{ number_format((float) $exam->essay_max_score, 2) }}</span>
                                    </p>
                                    <p class="mt-1 text-[11px] {{ $pendingEssayCount > 0 ? 'font-semibold text-amber-700' : 'text-slate-500' }}">
                                        {{ $essayCount - $pendingEssayCount }} dari {{ $essayCount }} soal dikoreksi
                                        @if ($pendingEssayCount > 0)
                                            · {{ $pendingEssayCount }} menunggu
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="rounded-xl border {{ $isFinal ? 'border-blue-100 bg-blue-50/70' : 'border-amber-100 bg-amber-50/70' }} p-4">
                                <p class="text-xs font-semibold {{ $isFinal ? 'text-blue-700' : 'text-amber-700' }}">
                                    {{ $isFinal ? 'Nilai Akhir' : 'Nilai Sementara' }}
                                </p>
                                <p class="mt-1 text-2xl font-bold {{ $isFinal ? 'text-navy' : 'text-amber-800' }}">{{ number_format((float) $exam->final_score, 2) }}</p>
                                <p class="mt-1 text-[11px] text-slate-600">Batas kelulusan: {{ number_format((float) $exam->pass_score, 2) }}</p>
                            </div>

                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-slate-500">Keterangan</p>
                                @if (!$isFinal)
                                    <p class="mt-2 text-sm font-bold text-amber-700">Menunggu koreksi esai</p>
                                @elseif ((float) $exam->final_score >= (float) $exam->pass_score)
                                    <p class="mt-2 text-sm font-bold text-emerald-700">Mencapai batas kelulusan</p>
                                @else
                                    <p class="mt-2 text-sm font-bold text-rose-700">Belum mencapai batas kelulusan</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-navy">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h2 class="mt-4 font-serif text-xl font-bold text-navy">Belum ada hasil ujian</h2>
                        <p class="mt-2 text-sm text-slate-500">Ujian yang sudah selesai akan tampil di halaman ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
