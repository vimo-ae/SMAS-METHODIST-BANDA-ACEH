
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Admin</h4>
        <p class="text-muted mb-0 small">
            Selamat datang, {{ auth()->user()->name }}!
            Berikut ringkasan aktivitas akademik sekolah.
        </p>
    </div>

    <div class="text-muted small">
        <i class="bi bi-calendar3 me-1"></i>
        {{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- STATISTIK --}}
@php
    $stats = $stats ?? [
        'Total Siswa' => 0,
        'Total Guru' => 0,
        'Total Kelas' => 0,
        'Mata Pelajaran' => 0,
        'Total Ujian' => 0,
        'Pengumuman' => 0,
    ];

    $sessionsRunning = $sessionsRunning ?? 0;
    $pendingEssays = $pendingEssays ?? 0;
    $studentsNoClass = $studentsNoClass ?? 0;
    $recentAnnouncements = $recentAnnouncements ?? collect();
    $violations = $violations ?? collect();
    $classes = $classes ?? collect();
@endphp

<div class="row g-3 mb-4">
    @foreach($stats as $label => $total)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">{{ $label }}</div>
                    <div class="fs-3 fw-bold text-primary">{{ $total }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- PERLU PERHATIAN --}}
<h6 class="fw-bold mb-3">Ringkasan Aktivitas</h6>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Sesi ujian berlangsung</div>
                        <div class="fs-3 fw-bold">{{ $sessionsRunning }}</div>
                    </div>
                    <i class="bi bi-clock-history fs-2 text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Esai menunggu penilaian</div>
                        <div class="fs-3 fw-bold">{{ $pendingEssays }}</div>
                    </div>
                    <i class="bi bi-file-earmark-text fs-2 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Siswa belum memiliki kelas</div>
                        <div class="fs-3 fw-bold">{{ $studentsNoClass }}</div>
                    </div>
                    <i class="bi bi-people fs-2 text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- KOLOM KIRI --}}
    <div class="col-lg-7">

        {{-- PENGUMUMAN --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone me-2 text-primary"></i>Pengumuman Terbaru</span>

                <a href="{{ route('announcements.create') }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </a>
            </div>

            <div class="list-group list-group-flush">
                @forelse($recentAnnouncements as $a)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <strong>{{ $a->title }}</strong>
                            <small class="text-muted">
                                {{ $a->created_at?->diffForHumans() }}
                            </small>
                        </div>

                        <p class="text-muted small mt-2 mb-2">
                            {{ \Illuminate\Support\Str::limit($a->content, 120) }}
                        </p>

                        <a href="{{ route('announcements.edit', $a) }}"
                           class="small text-primary text-decoration-none">
                            <i class="bi bi-pencil-square me-1"></i>Edit pengumuman
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-megaphone fs-2 d-block mb-2"></i>
                        Belum ada pengumuman.
                    </div>
                @endforelse
            </div>

            <div class="card-body border-top text-center">
                <a href="{{ route('announcements.index') }}"
                   class="text-decoration-none small fw-semibold">
                    Lihat semua pengumuman
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        {{-- PELANGGARAN --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-exclamation me-2 text-warning"></i>
                Pelanggaran Ujian Terbaru
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Siswa</th>
                            <th>Ujian</th>
                            <th>Jenis</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($violations as $v)
                            <tr>
                                <td>{{ $v->student?->user?->name ?? $v->students_id }}</td>
                                <td>{{ $v->session?->exam?->title ?? '-' }}</td>
                                <td>
                                    <span class="badge text-bg-warning">
                                        {{ $v->type }}
                                    </span>
                                </td>
                                <td class="small">
                                    {{ $v->occurred_at?->format('d M H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada pelanggaran ujian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-lg-5">

        {{-- AKSI CEPAT --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-lightning-charge me-2 text-primary"></i>
                Aksi Cepat
            </div>

            <div class="card-body d-grid gap-2">
                @foreach([
                    ['admin.students.create', 'Tambah Siswa', 'bi-person-plus'],
                    ['admin.teachers.create', 'Tambah Guru', 'bi-person-workspace'],
                    ['admin.classes.create', 'Tambah Kelas', 'bi-building'],
                    ['admin.subjects.create', 'Tambah Mata Pelajaran', 'bi-book'],
                    ['admin.cst.index', 'Tetapkan Pengampu Mapel', 'bi-person-check'],
                    ['admin.enrollments.create', 'Daftarkan Siswa ke Kelas', 'bi-journal-check'],
                ] as [$route, $label, $icon])
                    @if(\Illuminate\Support\Facades\Route::has($route))
                        <a href="{{ route($route) }}"
                           class="btn btn-outline-primary text-start">
                            <i class="bi {{ $icon }} me-2"></i>{{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- JUMLAH SISWA PER KELAS --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people me-2 text-primary"></i>
                Jumlah Siswa per Kelas
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kelas</th>
                            <th class="text-end">Jumlah Siswa</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($classes as $c)
                            <tr>
                                <td>{{ $c->name }}</td>
                                <td class="text-end fw-semibold">
                                    {{ $c->students_count }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    Belum ada data kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
