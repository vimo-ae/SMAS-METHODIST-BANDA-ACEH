@extends('layouts.admin')

@section('title', 'Dashboard Admin - Portal Akademik')

@section('content')

@php
    // Variabel dummy fallback jika data belum dikirim dari Controller
    $stats = $stats ?? ['Total Siswa' => 0, 'Total Guru' => 0, 'Total Kelas' => 0, 'Peserta Ujian' => 0];
    $activeSessions = $activeSessions ?? collect(); // Untuk monitoring sesi ujian
    $recentAnnouncements = $recentAnnouncements ?? collect();
    $classes = $classes ?? collect();
@endphp

{{-- WRAPPER TEMA WARNA SESUAI GAMBAR --}}
<div class="theme-portal rounded-4 shadow-lg">

    {{-- HEADER DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom border-light-subtle">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Sistem Pembelajaran Sekolah</div>
            <h2 class="fw-bold mb-1 text-white">Portal Admin Akademik</h2>
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
            <div class="col-6 col-md-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Total Siswa</div>
                        <div class="icon-box text-info"><i class="bi bi-people-fill"></i></div>
                    </div>
                    <h3 class="fw-bold text-white mb-0">{{ number_format($stats['Total Siswa'] ?? 0) }}</h3>
                </div>
            </div>
            
            {{-- Total Guru --}}
            <div class="col-6 col-md-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Data Guru</div>
                        <div class="icon-box text-success"><i class="bi bi-person-workspace"></i></div>
                    </div>
                    <h3 class="fw-bold text-white mb-0">{{ number_format($stats['Total Guru'] ?? 0) }}</h3>
                </div>
            </div>

            {{-- Total Kelas --}}
            <div class="col-6 col-md-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Total Kelas</div>
                        <div class="icon-box text-warning"><i class="bi bi-building"></i></div>
                    </div>
                    <h3 class="fw-bold text-white mb-0">{{ number_format($stats['Total Kelas'] ?? 0) }}</h3>
                </div>
            </div>

            {{-- Total Peserta Ujian --}}
            <div class="col-6 col-md-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Peserta Ujian</div>
                        <div class="icon-box text-danger"><i class="bi bi-journal-check"></i></div>
                    </div>
                    <h3 class="fw-bold text-white mb-0">{{ number_format($stats['Peserta Ujian'] ?? 0) }}</h3>
                </div>
            </div>
        </div>


        <div class="row g-4">
            {{-- KOLOM KIRI (KONTEN UTAMA: UJIAN & PENGUMUMAN) --}}
            <div class="col-lg-7">
                
                {{-- MONITORING SESI UJIAN --}}
                <div class="portal-card mb-4">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-display text-accent me-2"></i> Monitoring Sesi Ujian</h5>
                    </div>
                    <div class="p-3">
                        <p class="text-muted-portal small mb-3">Pilih sesi ujian per kelas yang sedang berlangsung untuk memantau aktivitas peserta.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-borderless align-middle custom-table">
                                <thead class="text-muted-portal small border-bottom border-secondary">
                                    <tr>
                                        <th>Kelas & Mata Pelajaran</th>
                                        <th>Sesi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeSessions as $session)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-white">{{ $session->exam->title ?? 'Ujian' }}</div>
                                                <small class="text-muted-portal">{{ $session->cst->schoolClass->name ?? 'Kelas' }} - {{ $session->cst->subject->name ?? 'Mapel' }}</small>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $session->name ?? 'Sesi 1' }}</span></td>
                                            <td>
                                                <span class="text-success small"><i class="bi bi-circle-fill me-1"></i> Berlangsung</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ Route::has('admin.monitoring.session') ? route('admin.monitoring.session', $session->id) : '#' }}" class="btn btn-sm btn-portal-primary">
                                                    Masuk Sesi
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted-portal">
                                                Tidak ada sesi ujian yang sedang berlangsung saat ini.
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
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-megaphone text-accent me-2"></i> Papan Pengumuman</h5>
                    </div>
                    <div class="p-0">
                        @forelse($recentAnnouncements as $announcement)
                            <div class="p-3 border-bottom border-secondary">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold text-white mb-1">{{ $announcement->title }}</h6>
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

            {{-- KOLOM KANAN (MANAJEMEN & DATA KELAS) --}}
            <div class="col-lg-5">
                
                {{-- NAVIGASI MANAJEMEN UTAMA --}}
                <div class="portal-card mb-4">
                    <div class="card-header-portal">
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-grid-1x2 text-accent me-2"></i> Manajemen Sistem</h5>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        
                        {{-- Manajemen Akun --}}
                        <a href="{{ Route::has('admin.accounts.index') ? route('admin.accounts.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-primary-subtle text-primary"><i class="bi bi-key-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-white">Manajemen Akun</div>
                                <div class="text-muted-portal small">Kelola password & akses pengguna</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        {{-- Akademik (CST & Enrollment) --}}
                        <a href="{{ Route::has('admin.cst.index') ? route('admin.cst.index') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-success-subtle text-success"><i class="bi bi-journal-bookmark-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-white">Akademik (Kelas & CST)</div>
                                <div class="text-muted-portal small">Buat kelas mapel & integrasi guru</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                        {{-- Tambah Pengumuman --}}
                        <a href="{{ Route::has('announcements.create') ? route('announcements.create') : '#' }}" class="action-btn">
                            <div class="icon-wrapper bg-warning-subtle text-warning"><i class="bi bi-megaphone-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-white">Buat Pengumuman</div>
                                <div class="text-muted-portal small">Kirim info ke guru & siswa</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-portal"></i>
                        </a>

                    </div>
                </div>

                {{-- DATA SISWA PER KELAS --}}
                <div class="portal-card">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-building text-accent me-2"></i> Data Siswa per Kelas</h5>
                        <a href="{{ Route::has('admin.classes.index') ? route('admin.classes.index') : '#' }}" class="small text-accent text-decoration-none">Lihat Semua</a>
                    </div>
                    <div class="p-3">
                        @forelse($classes as $class)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary">
                                <div>
                                    <div class="fw-semibold text-white">{{ $class->name ?? 'Nama Kelas' }}</div>
                                    <div class="text-muted-portal small">Total pendaftar (Enrollment)</div>
                                </div>
                                <div class="text-end">
                                    <span class="fs-5 fw-bold text-accent">{{ number_format($class->students_count ?? 0) }}</span>
                                    <span class="text-muted-portal small d-block" style="margin-top:-5px;">Siswa</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted-portal">Belum ada data kelas yang dibuat.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS TEMA PORTAL DARK MODE --}}
<style>
    /* Basis Tema */
    .theme-portal {
        background: linear-gradient(145deg, #131235 0%, #1e1b4b 100%); /* Sesuai warna gambar */
        color: #ffffff;
        min-height: 80vh;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    /* Warna Kustom Teks */
    .text-accent {
        color: #6366f1 !important; /* Warna ungu/indigo khas button Masuk */
    }
    .text-muted-portal {
        color: #94a3b8 !important;
    }
    .border-secondary {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Komponen Card */
    .portal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: transform 0.2s;
    }
    .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(0, 0, 0, 0.1);
        border-radius: 12px 12px 0 0;
    }

    /* Badge & Ikon Header */
    .portal-badge {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        font-size: 0.875rem;
    }
    .icon-box {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        font-size: 1.2rem;
    }

    /* Tabel Kustom */
    .custom-table {
        --bs-table-bg: transparent;
        --bs-table-color: #fff;
        margin-bottom: 0;
    }
    .custom-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* Tombol Utama (Sesuai Tombol "Masuk" di gambar) */
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
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
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
</style>

@endsection