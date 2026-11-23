@extends('layouts.admin')

@section('title', 'Dashboard Admin - Portal Akademik')

@section('content')

@php
    // Variabel dummy fallback jika data belum dikirim dari Controller
    $stats = $stats ?? ['Total Siswa' => 0, 'Total Guru' => 0, 'Total Kelas' => 0];
    $activeSessions = $activeSessions ?? collect(); // Untuk monitoring sesi ujian
    $examMonitors = $examMonitors ?? collect();
    $recentAnnouncements = $recentAnnouncements ?? collect();
    $classes = $classes ?? collect();
@endphp

{{-- WRAPPER TEMA TERANG --}}
<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Sistem Pembelajaran Sekolah</div>
            <h2 class="fw-bold mb-1 text-dark">Portal Admin Akademik</h2>
            <p class="text-muted-portal mb-0">
                Selamat datang, {{ auth()->user()->name ?? 'Administrator' }}. Pantau dan kelola seluruh aktivitas belajar mengajar.
            </p>
        </div>
        <div class="portal-badge px-3 py-2 rounded-3">
            <i class="bi bi-calendar3 me-2"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <div class="p-4 pt-0">

        {{-- STATISTIK UTAMA --}}
        <div class="row g-3 mb-4">
            {{-- Total Siswa --}}
            <div class="col-12 col-sm-4">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Total Siswa</div>
                        <div class="icon-box text-info"><i class="bi bi-people-fill"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['Total Siswa'] ?? 0) }}</h3>
                </div>
            </div>
            
            {{-- Total Guru --}}
            <div class="col-12 col-sm-4">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Data Guru</div>
                        <div class="icon-box text-success"><i class="bi bi-person-workspace"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['Total Guru'] ?? 0) }}</h3>
                </div>
            </div>

            {{-- Total Kelas --}}
            <div class="col-12 col-sm-4">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Total Kelas</div>
                        <div class="icon-box text-warning"><i class="bi bi-building"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['Total Kelas'] ?? 0) }}</h3>
                </div>
            </div>
        </div>


        <div class="row g-4">
            {{-- KOLOM KIRI (KONTEN UTAMA: UJIAN dan PENGUMUMAN) --}}
            <div class="col-lg-7">
                
                {{-- MONITORING SESI UJIAN --}}
                <div class="portal-card mb-4">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-display text-accent me-2"></i> Monitoring Sesi Ujian</h5>
                        <a href="{{ route('admin.monitoring.index') }}" class="small text-accent text-decoration-none">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle custom-table">
                                <thead class="text-muted-portal small border-bottom border-secondary">
                                    <tr>
                                        <th>Kelas dan Mata Pelajaran</th>
                                        <th class="text-center">Peserta</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($examMonitors as $exam)
                                        @php
                                            $now = now();
                                            $isUpcoming = $now->lt($exam->start_time);
                                            $isFinished = $now->gt($exam->end_time);
                                            $isRunning = ! $isUpcoming && ! $isFinished && (int) $exam->running_count > 0;
                                            $isOpen = ! $isUpcoming && ! $isFinished;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $exam->title }}</div>
                                               <small class="text-muted-portal">
                                                    {{ $exam->class_name }} · {{ $exam->subject_name ?? 'Mata Pelajaran' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ (int) $exam->participants_count }} siswa</span>
                                            </td>
                                            <td>
                                                @if($isFinished)
                                                    <span class="text-secondary small"><i class="bi bi-circle-fill me-1"></i> Selesai</span>
                                                @elseif($isUpcoming)
                                                    <span class="text-warning small"><i class="bi bi-circle-fill me-1"></i> Belum dibuka</span>
                                                @elseif($isRunning)
                                                    <span class="text-success small"><i class="bi bi-circle-fill me-1"></i> Berlangsung</span>
                                                @elseif($isOpen)
                                                    <span class="text-primary small"><i class="bi bi-circle-fill me-1"></i> Dibuka</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.monitoring.show', $exam->exam_id) }}" class="btn btn-sm btn-portal-primary">
                                                    Monitoring
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted-portal">
                                                Belum ada ujian yang dipublikasikan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PENGUMUMAN --}}
                <div class="portal-card">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-megaphone text-accent me-2"></i> Papan Pengumuman</h5>
                        <a href="{{ route('admin.announcements.index') }}" class="small text-accent text-decoration-none">Kelola Semua</a>
                    </div>
                    <div class="p-0">
                        @forelse($recentAnnouncements as $announcement)
                            <div class="p-3 border-bottom border-secondary">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold text-dark mb-1">
                                        <a href="{{ route('admin.announcements.show', $announcement->announcement_id) }}" class="text-dark text-decoration-none">
                                            {{ $announcement->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted-portal">{{ $announcement->created_at?->format('d M Y') ?? '-' }}</small>
                                </div>
                                <p class="text-muted-portal small mb-0">{{ \Illuminate\Support\Str::limit($announcement->content, 100) }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted-portal">
                                Belum ada pengumuman terbaru.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN (MANAJEMEN SISTEM dan LOG AKTIVITAS NILAI) --}}
            <div class="col-lg-5">
                
                {{-- NAVIGASI MANAJEMEN UTAMA --}}
                <div class="portal-card mb-4">
                    <div class="card-header-portal">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-1x2 text-accent me-2"></i> Manajemen Sistem</h5>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        
                        <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-primary-subtle text-primary"><i class="bi bi-people-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Data Siswa dan Akun</div>
                                <div class="text-muted-portal small">Kelola data dan akun siswa</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        <a href="{{ Route::has('admin.teachers.index') ? route('admin.teachers.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-success-subtle text-success"><i class="bi bi-person-workspace"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Data Guru dan Akun</div>
                                <div class="text-muted-portal small">Kelola data dan akun pengajar</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        <a href="{{ Route::has('admin.cst.index') ? route('admin.cst.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-warning-subtle text-warning"><i class="bi bi-building"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Manajemen Kelas</div>
                                <div class="text-muted-portal small">Kelola ruangan kelas siswa</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        <a href="{{ Route::has('admin.enrollments.index') ? route('admin.enrollments.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-info-subtle text-info"><i class="bi bi-journal-bookmark-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Enrollment Siswa</div>
                                <div class="text-muted-portal small">Pendaftaran ke kelas mapel</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        <a href="{{ route('admin.announcements.create') }}" class="action-btn">
                            <div class="icon-wrapper bg-danger-subtle text-danger"><i class="bi bi-megaphone-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Buat Pengumuman</div>
                                <div class="text-muted-portal small">Kirim info ke guru dan siswa</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        <a href="{{ route('admin.profile.index') }}" class="action-btn">
                            <div class="icon-wrapper" style="background: #e0e7ff; color: #4338ca;"><i class="bi bi-person-gear"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">Profil dan Keamanan Akun</div>
                                <div class="text-muted-portal small">Kelola data profil admin dan kata sandi</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                    </div>
                </div>

              {{-- LOG AKTIVITAS SISTEM (SEMUA TABEL) --}}
                <div class="portal-card">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history text-accent me-2"></i> Log Aktivitas Terbaru
                            </h5>
                            <small class="text-muted-portal" style="font-size: 0.75rem;">
                                Perubahan data lintas tabel sistem
                            </small>
                        </div>
                        <a href="{{ Route::has('admin.activity-logs.index') ? route('admin.activity-logs.index') : '#' }}" class="small text-accent text-decoration-none fw-semibold">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="p-0">
                        @forelse($recentActivityLogs ?? collect() as $log)
                            @php
                                $actionUpper = strtoupper((string) ($log->action ?? ''));
                                $badge = match(true) {
                                    $actionUpper === 'INSERT'               => 'bg-success-subtle text-success border border-success-subtle',
                                    str_starts_with($actionUpper, 'UPDATE') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                    default                                 => 'bg-danger-subtle text-danger border border-danger-subtle',
                                };
                                $actionLabel = match($actionUpper) {
                                    'UPDATE_BEFORE' => 'Update (Sebelum)',
                                    'UPDATE_AFTER'  => 'Update (Sesudah)',
                                    'UPDATE'        => 'Update',
                                    'INSERT'        => 'Insert',
                                    'DELETE'        => 'Delete',
                                    default         => $actionUpper,
                                };
                            @endphp
                            <div class="p-3 border-bottom border-secondary position-relative log-item-hover">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge {{ $badge }} rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.68rem;">
                                            {{ $actionLabel }}
                                        </span>
                                        <span class="fw-bold text-dark small">
                                            {{ $log->table_label }}
                                        </span>
                                        <code class="small px-1.5 py-0.5 rounded" style="background:#f8fafc; color:#4f46e5; border:1px solid #e2e8f0; font-size:0.7rem;">
                                            {{ $log->table }}
                                        </code>
                                    </div>
                                    <small class="text-muted-portal font-monospace text-nowrap" style="font-size: 0.73rem;">
                                        #{{ $log->log_id }} &middot; {{ $log->logged_at ? \Carbon\Carbon::parse($log->logged_at)->format('d M, H:i') : '-' }}
                                    </small>
                                </div>

                                @if(!empty($log->summary))
                                    <div class="text-muted-portal mb-1 text-truncate" style="font-size: 0.76rem;">
                                        {{ $log->summary }}
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.76rem;">
                                    <span class="text-muted-portal">
                                        <i class="bi bi-person me-1"></i>{{ $log->actor_name }}
                                    </span>

                                    @if(Route::has('admin.activity-logs.show'))
                                        <a href="{{ route('admin.activity-logs.show', [$log->table, $log->log_id]) }}" class="small text-accent text-decoration-none fw-semibold stretched-link">
                                            Detail <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted-portal small">
                                <i class="bi bi-inbox fs-5 d-block mb-1"></i>
                                Belum ada aktivitas perubahan data terbaru.
                            </div>
                        @endforelse
                    </div>
                </div>

    </div>
</div>

{{-- CSS KHUSUS TEMA PORTAL (TERANG) --}}
<style>
    /* Basis Tema */
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    /* Warna Kustom Teks */
    .text-accent {
        color: #4f46e5 !important;
    }
    .text-muted-portal {
        color: #64748b !important;
    }
    .border-secondary {
        border-color: #e2e8f0 !important;
    }

    /* Komponen Card */
    .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s;
    }
    .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }

    /* Badge dan Ikon Header */
    .portal-badge {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.875rem;
    }
    .icon-box {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
        font-size: 1.2rem;
    }

    /* Tabel Kustom */
    .custom-table {
        --bs-table-bg: transparent;
        --bs-table-color: #1e293b;
        margin-bottom: 0;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Tombol Utama */
    .btn-portal-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-portal-primary:hover {
        background-color: #4338ca;
        color: white;
    }

    /* Tombol Aksi Cepat (Navigasi Kanan) */
    .action-btn {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        background: #eef2ff;
        border-color: #c7d2fe;
        transform: translateX(5px);
    }
    .icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
    }

    // Log Aktivitas 
    .log-item-hover {
        transition: background-color 0.15s ease;
    }
    .log-item-hover:hover {
        background-color: #f8fafc;
    }

</style>

@endsection