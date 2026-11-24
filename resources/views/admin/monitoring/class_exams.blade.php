@extends('layouts.admin')

@section('title', 'Daftar Ujian ' . $schoolClass->name . ' - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Monitoring Kelas {{ $schoolClass->name }}</div>
            <h2 class="fw-bold mb-1 text-dark">Daftar Mata Pelajaran dan Ujian</h2>
            <p class="text-muted-portal mb-0">Pilih ujian mata pelajaran untuk memantau aktivitas siswa secara real-time.</p>
        </div>
        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pilih Kelas
        </a>
    </div>

    <div class="p-4 pt-0">
        <div class="row g-3">
            @forelse($exams as $exam)
                @php
                    $isOpen = isset($exam->is_open) && $exam->is_open == 1;
                @endphp
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('admin.monitoring.show', $exam->id) }}" class="text-decoration-none">
                        <div class="card border shadow-sm h-100 rounded-4 p-4 bg-white class-box-card transition-all">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-subtle text-primary font-monospace small">
                                    {{ $exam->name }}
                                </span>
                                <span class="badge {{ $isOpen ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1">
                                    {{ $isOpen ? 'Sedang Berlangsung' : 'Selesai / Ditutup' }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $exam->exam_title }}</h5>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-book me-1 text-accent"></i> {{ $exam->subject_name }}
                            </p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center text-muted small font-monospace">
                                <span>Mulai: {{ \Carbon\Carbon::parse($exam->start_time)->format('d M H:i') }}</span>
                                <span class="text-accent fw-semibold">Pantau <i class="bi bi-chevron-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted-portal portal-card p-4 bg-white">
                        Belum ada ujian atau mata pelajaran yang dijadwalkan untuk kelas ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .theme-portal { background: #ffffff; color: #1e293b; min-height: 80vh; border: 1px solid #e2e8f0; }
    .text-accent { color: #4f46e5 !important; }
    .text-muted-portal { color: #64748b !important; }
    .portal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .class-box-card:hover { transform: translateY(-3px); border-color: #4f46e5 !important; box-shadow: 0 .5rem 1rem rgba(79, 70, 229, 0.15) !important; }
    .transition-all { transition: all 0.2s ease-in-out; }
</style>

@endsection