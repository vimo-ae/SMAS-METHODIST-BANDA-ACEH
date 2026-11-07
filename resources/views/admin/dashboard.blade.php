
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

@php
    $stats = $stats ?? [];
    $recentAnnouncements = $recentAnnouncements ?? collect();
    $violations = $violations ?? collect();
    $classes = $classes ?? collect();

    $statCards = [
        [
            'label' => 'Total Siswa',
            'value' => $stats['Total Siswa'] ?? 0,
            'icon' => 'bi-people-fill',
            'color' => 'primary',
        ],
        [
            'label' => 'Total Guru',
            'value' => $stats['Total Guru'] ?? 0,
            'icon' => 'bi-person-workspace',
            'color' => 'success',
        ],
        [
            'label' => 'Total Kelas',
            'value' => $stats['Total Kelas'] ?? 0,
            'icon' => 'bi-building',
            'color' => 'info',
        ],
        [
            'label' => 'Mata Pelajaran',
            'value' => $stats['Mata Pelajaran'] ?? 0,
            'icon' => 'bi-book-half',
            'color' => 'warning',
        ],
        [
            'label' => 'Total Ujian',
            'value' => $stats['Total Ujian'] ?? 0,
            'icon' => 'bi-file-earmark-text',
            'color' => 'danger',
        ],
        [
            'label' => 'Peserta Ujian',
            'value' => $stats['Peserta Ujian'] ?? 0,
            'icon' => 'bi-person-check-fill',
            'color' => 'primary',
        ],
    ];
@endphp


{{-- HEADER DASHBOARD --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

    <div>
        <h4 class="fw-bold mb-1">Dashboard Admin</h4>

        <p class="text-muted mb-0">
            Selamat datang, {{ auth()->user()->name ?? 'Administrator' }}!
            Berikut ringkasan aktivitas akademik MALAS.
        </p>
    </div>

    <div class="bg-white border rounded-3 px-3 py-2">
        <div class="small text-muted">
            <i class="bi bi-calendar3 me-1"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

</div>


{{-- STATISTIK UTAMA --}}
<div class="row g-3 mb-4">

    @foreach($statCards as $card)

        <div class="col-6 col-md-4 col-xl-2">

            <div class="card stat-card h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>

                    </div>

                    <h4 class="fw-bold mb-1">
                        {{ number_format((int) $card['value']) }}
                    </h4>

                    <div class="text-muted small">
                        {{ $card['label'] }}
                    </div>

                </div>
            </div>

        </div>

    @endforeach

</div>


{{-- MONITORING DAN AKTIVITAS --}}
<div class="mb-3">
    <h5 class="fw-bold mb-1">Ringkasan Aktivitas</h5>
    <p class="text-muted small mb-0">
        Pantau aktivitas ujian dan pengelolaan akademik sekolah.
    </p>
</div>

<div class="row g-3 mb-4">

    {{-- SESI UJIAN --}}
    <div class="col-md-4">

        <div class="card activity-card h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="text-muted small mb-2">
                            Sesi Ujian Berlangsung
                        </div>

                        <h3 class="fw-bold mb-1">
                            {{ number_format($sessionsRunning ?? 0) }}
                        </h3>

                        <span class="text-success small">
                            <i class="bi bi-circle-fill me-1"></i>
                            Aktivitas ujian
                        </span>
                    </div>

                    <div class="activity-icon bg-primary-subtle text-primary">
                        <i class="bi bi-display"></i>
                    </div>

                </div>

                @if(Route::has('admin.monitoring.index'))
                    <a href="{{ route('admin.monitoring.index') }}"
                       class="small text-primary text-decoration-none d-inline-block mt-3">

                        Lihat monitoring ujian
                        <i class="bi bi-arrow-right ms-1"></i>

                    </a>
                @endif

            </div>
        </div>

    </div>


    {{-- ESAI --}}
    <div class="col-md-4">

        <div class="card activity-card h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="text-muted small mb-2">
                            Esai Menunggu Penilaian
                        </div>

                        <h3 class="fw-bold mb-1">
                            {{ number_format($pendingEssays ?? 0) }}
                        </h3>

                        <span class="text-warning small">
                            <i class="bi bi-clock me-1"></i>
                            Perlu diperiksa
                        </span>
                    </div>

                    <div class="activity-icon bg-warning-subtle text-warning">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                </div>

            </div>
        </div>

    </div>


    {{-- SISWA BELUM PUNYA KELAS --}}
    <div class="col-md-4">

        <div class="card activity-card h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="text-muted small mb-2">
                            Siswa Belum Memiliki Kelas
                        </div>

                        <h3 class="fw-bold mb-1">
                            {{ number_format($studentsNoClass ?? 0) }}
                        </h3>

                        <span class="text-danger small">
                            <i class="bi bi-info-circle me-1"></i>
                            Perlu enrollment
                        </span>
                    </div>

                    <div class="activity-icon bg-danger-subtle text-danger">
                        <i class="bi bi-person-exclamation"></i>
                    </div>

                </div>

                @if(Route::has('admin.enrollments.index'))
                    <a href="{{ route('admin.enrollments.index') }}"
                       class="small text-primary text-decoration-none d-inline-block mt-3">

                        Kelola enrollment
                        <i class="bi bi-arrow-right ms-1"></i>

                    </a>
                @endif

            </div>
        </div>

    </div>

</div>


{{-- KONTEN BAWAH --}}
<div class="row g-3">

    {{-- KOLOM KIRI --}}
    <div class="col-lg-7">

        {{-- PENGUMUMAN --}}
        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>
                    <i class="bi bi-megaphone-fill text-primary me-2"></i>
                    Pengumuman Terbaru
                </div>

                @if(Route::has('announcements.create'))
                    <a href="{{ route('announcements.create') }}"
                       class="btn btn-sm btn-primary">

                        <i class="bi bi-plus-lg me-1"></i>
                        Buat Pengumuman

                    </a>
                @endif

            </div>

            <div class="card-body p-0">

                @forelse($recentAnnouncements as $announcement)

                    <div class="announcement-item p-3">

                        <div class="d-flex justify-content-between align-items-start gap-2">

                            <div class="flex-grow-1">

                                <h6 class="fw-bold mb-1">
                                    {{ $announcement->title }}
                                </h6>

                                <p class="text-muted small mb-2">
                                    {{ \Illuminate\Support\Str::limit($announcement->content, 130) }}
                                </p>

                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $announcement->created_at?->format('d M Y, H:i') ?? '-' }}
                                </small>

                            </div>

                            <span class="badge bg-primary-subtle text-primary">
                                Pengumuman
                            </span>

                        </div>

                        @if(Route::has('announcements.show'))
                            <a href="{{ route('announcements.show', [
                                'announcement' => $announcement->announcement_id
                            ]) }}"
                               class="small text-decoration-none text-primary">

                                Lihat detail
                                <i class="bi bi-arrow-right ms-1"></i>

                            </a>
                        @endif

                    </div>

                @empty

                    <div class="text-center py-5">

                        <i class="bi bi-megaphone display-6 text-muted"></i>

                        <p class="text-muted mt-2 mb-0">
                            Belum ada pengumuman.
                        </p>

                    </div>

                @endforelse

            </div>

            <div class="card-footer bg-white text-center">

                @if(Route::has('announcements.index'))
                    <a href="{{ route('announcements.index') }}"
                       class="small text-primary text-decoration-none fw-semibold">

                        Lihat Semua Pengumuman
                        <i class="bi bi-arrow-right ms-1"></i>

                    </a>
                @endif

            </div>

        </div>


        {{-- PELANGGARAN UJIAN --}}
        <div class="card">

            <div class="card-header">
                <i class="bi bi-shield-exclamation text-warning me-2"></i>
                Pelanggaran Ujian Terbaru
            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Siswa</th>
                            <th>Ujian</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($violations as $violation)

                            <tr>

                                <td class="ps-3">
                                    {{ data_get($violation, 'student.user.name', '-') }}
                                </td>

                                <td>
                                    {{ data_get($violation, 'session.exam.title', '-') }}
                                </td>

                                <td>
                                    <span class="badge bg-warning-subtle text-warning-emphasis">
                                        {{ data_get($violation, 'type', '-') }}
                                    </span>
                                </td>

                                <td class="small text-muted">
                                    {{ data_get($violation, 'occurred_at')
                                        ? \Illuminate\Support\Carbon::parse(data_get($violation, 'occurred_at'))->format('d M Y H:i')
                                        : '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
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
        <div class="card mb-4">

            <div class="card-header">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                Aksi Cepat
            </div>

            <div class="card-body">

                @php
                    $quickActions = [
                        [
                            'route' => 'admin.students.create',
                            'label' => 'Tambah Data Siswa',
                            'icon' => 'bi-person-plus',
                        ],
                        [
                            'route' => 'admin.teachers.create',
                            'label' => 'Tambah Data Guru',
                            'icon' => 'bi-person-workspace',
                        ],
                        [
                            'route' => 'admin.classes.create',
                            'label' => 'Buat Kelas Akademik',
                            'icon' => 'bi-building-add',
                        ],
                        [
                            'route' => 'admin.subjects.create',
                            'label' => 'Tambah Mata Pelajaran',
                            'icon' => 'bi-book',
                        ],
                        [
                            'route' => 'admin.cst.index',
                            'label' => 'Kelola Guru Pengampu',
                            'icon' => 'bi-person-check',
                        ],
                        [
                            'route' => 'admin.enrollments.index',
                            'label' => 'Enrollment Siswa',
                            'icon' => 'bi-journal-check',
                        ],
                    ];
                @endphp

                <div class="d-grid gap-2">

                    @foreach($quickActions as $action)

                        @if(Route::has($action['route']))

                            <a href="{{ route($action['route']) }}"
                               class="quick-action">

                                <i class="bi {{ $action['icon'] }}"></i>

                                <span>{{ $action['label'] }}</span>

                                <i class="bi bi-arrow-right ms-auto"></i>

                            </a>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>


        {{-- DATA SISWA PER KELAS --}}
        <div class="card">

            <div class="card-header">
                <i class="bi bi-people-fill text-primary me-2"></i>
                Data Siswa per Kelas
            </div>

            <div class="card-body p-0">

                @forelse($classes as $class)

                    <div class="class-item px-3 py-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <div class="fw-semibold">
                                    {{ data_get($class, 'name')
                                        ?? data_get($class, 'class_name')
                                        ?? 'Kelas' }}
                                </div>

                                <small class="text-muted">
                                    Jumlah siswa terdaftar
                                </small>
                            </div>

                            <div class="class-count">
                                {{ number_format(data_get($class, 'students_count', 0)) }}
                                <small>siswa</small>
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-4 text-muted">
                        Belum ada data kelas.
                    </div>

                @endforelse

            </div>

            @if(Route::has('admin.classes.index'))
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('admin.classes.index') }}"
                       class="small text-decoration-none text-primary fw-semibold">

                        Lihat Semua Kelas
                        <i class="bi bi-arrow-right ms-1"></i>

                    </a>
                </div>
            @endif

        </div>

    </div>

</div>


{{-- STYLE KHUSUS DASHBOARD --}}
<style>

    .stat-card {
        transition: .2s;
    }

    .stat-card:hover,
    .activity-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon,
    .activity-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        font-size: 20px;
    }

    .activity-card {
        transition: .2s;
    }

    .announcement-item {
        border-bottom: 1px solid #edf0f5;
    }

    .announcement-item:last-child {
        border-bottom: none;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 13px;
        border: 1px solid #e9edf4;
        border-radius: 9px;
        color: #334155;
        text-decoration: none;
        font-size: 13px;
        transition: .2s;
    }

    .quick-action > i:first-child {
        font-size: 18px;
        color: #2563eb;
    }

    .quick-action:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .class-item {
        border-bottom: 1px solid #edf0f5;
    }

    .class-item:last-child {
        border-bottom: none;
    }

    .class-count {
        font-weight: 700;
        color: #2563eb;
        text-align: right;
    }

    .class-count small {
        display: block;
        font-weight: 400;
        font-size: 10px;
        color: #94a3b8;
    }

</style>

@endsection
