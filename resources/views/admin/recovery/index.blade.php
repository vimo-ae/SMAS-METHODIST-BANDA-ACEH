@extends('layouts.admin')

@section('title', 'Pusat Pemulihan dan Cadangan - Portal Akademik')

@section('content')

{{-- WRAPPER TEMA PORTAL --}}
<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted-portal small">
                    <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                </a>
                <span class="text-muted-portal small">/</span>
                <span class="text-accent small fw-semibold">Pemulihan dan Cadangan</span>
            </div>
            <h2 class="fw-bold mb-1 text-dark">Pusat Pemulihan dan Cadangan Basis Data</h2>
            <p class="text-muted-portal mb-0">
                Kelola strategi backup (Full/Logical Backup), Point-in-Time Recovery, dan pemulihan rekaman terhapus.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.recovery.backup') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-portal-primary px-3 py-2 rounded-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-database-fill-add"></i>
                    <span>Buat Cadangan Baru</span>
                </button>
            </form>
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-flex align-items-center gap-2 shadow-sm bg-white">
                <i class="bi bi-clock-history"></i>
                <span>Log Pemulihan Audit</span>
            </a>
        </div>
    </div>

    <div class="p-4 pt-0">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- METRIK KESEHATAN SISTEM PEMULIHAN --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Engine dan Transaksi</div>
                        <div class="icon-box text-success"><i class="bi bi-shield-check"></i></div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $stats['engine'] }}</h5>
                    <small class="text-success small d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill" style="font-size: 0.65rem;"></i> Crash-Safe Recovery Aktif
                    </small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Kapasitas Basis Data</div>
                        <div class="icon-box text-primary"><i class="bi bi-hdd-stack-fill"></i></div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $stats['total_size_mb'] }} MB</h5>
                    <small class="text-muted-portal small">
                        {{ $stats['total_tables'] }} Tabel &middot; Data: {{ $stats['data_size_mb'] }} MB &middot; Indeks: {{ $stats['index_size_mb'] }} MB
                    </small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Cadangan Tersimpan</div>
                        <div class="icon-box text-warning"><i class="bi bi-archive-fill"></i></div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ count($backups) }} Berkas</h5>
                    <small class="text-muted-portal small">
                        Lokasi: <code>storage/app/backups</code>
                    </small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="portal-card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted-portal small text-uppercase">Audit Trail PITR</div>
                        <div class="icon-box text-info"><i class="bi bi-clock-history"></i></div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ number_format($stats['total_audit_logs']) }} Baris Log</h5>
                    <small class="text-info small">
                        Siap Rollback Granular per Baris
                    </small>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- DAFTAR CADANGAN (BACKUPS) --}}
            <div class="col-lg-8">
                <div class="portal-card overflow-hidden">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-server text-accent fs-5"></i>
                            <h5 class="fw-bold mb-0 text-dark">Daftar Berkas Cadangan Basis Data</h5>
                        </div>
                        <span class="badge bg-secondary-subtle text-dark fw-semibold px-2.5 py-1 rounded-pill">
                            Total: {{ count($backups) }} File
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-table mb-0">
                            <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                                <tr>
                                    <th class="ps-4">Nama Berkas</th>
                                    <th>Ukuran</th>
                                    <th>Waktu Pembuatan</th>
                                    <th class="text-end pe-4">Aksi Pemulihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $b)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-filetype-sql text-primary fs-4"></i>
                                                <div>
                                                    <div class="fw-semibold text-dark font-monospace" style="font-size: 0.88rem;">
                                                        {{ $b['filename'] }}
                                                    </div>
                                                    <small class="text-muted-portal" style="font-size: 0.72rem;">Full Logical Backup</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace">
                                                {{ $b['size_human'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-dark d-block fw-semibold">
                                                {{ $b['created_at']->translatedFormat('d M Y, H:i') }}
                                            </small>
                                            <small class="text-muted-portal" style="font-size: 0.72rem;">
                                                {{ $b['created_at']->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1">
                                                {{-- Tombol Restore Modal --}}
                                                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-semibold" 
                                                        data-bs-toggle="modal" data-bs-target="#restoreModal{{ md5($b['filename']) }}"
                                                        title="Pulihkan Database dari file ini">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                                </button>

                                                {{-- Tombol Download --}}
                                                <a href="{{ route('admin.recovery.download', $b['filename']) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Unduh file SQL">
                                                    <i class="bi bi-download"></i>
                                                </a>

                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('admin.recovery.destroy', $b['filename']) }}" method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas cadangan {{ $b['filename'] }}?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus berkas">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- MODAL KONFIRMASI RESTORE --}}
                                            <div class="modal fade" id="restoreModal{{ md5($b['filename']) }}" tabindex="-1" aria-labelledby="restoreLabel{{ md5($b['filename']) }}" aria-hidden="true">
                                                <div class="modal-dialog text-start">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-warning bg-opacity-25 border-bottom border-warning">
                                                            <h5 class="modal-title fw-bold text-dark" id="restoreLabel{{ md5($b['filename']) }}">
                                                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Konfirmasi Pemulihan Database
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <p class="mb-2">Anda akan memulihkan basis data dari berkas cadangan:</p>
                                                            <div class="p-3 bg-light rounded-3 font-monospace small mb-3 border">
                                                                <strong>{{ $b['filename'] }}</strong> ({{ $b['size_human'] }})
                                                            </div>
                                                            <div class="alert alert-danger mb-0 small">
                                                                <i class="bi bi-info-circle-fill me-1"></i>
                                                                <strong>PERINGATAN KRUSIAL:</strong> Pemulihan database akan menimpa seluruh data tabel yang ada saat ini dengan data dari file cadangan ini. Pastikan Anda telah membuat cadangan terbaru sebelum melanjutkan.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('admin.recovery.restore') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="filename" value="{{ $b['filename'] }}">
                                                                <button type="submit" class="btn btn-warning text-dark fw-bold">
                                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Pulihkan Database Sekarang
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted-portal">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada berkas cadangan. Klik tombol <strong>"Buat Cadangan Baru"</strong> di atas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- DOKUMENTASI SOP RECOVERY dan OPTIMALISASI --}}
            <div class="col-lg-4">
                
                {{-- KARTU RPO dan RTO RECOVERY PLAN --}}
                <div class="portal-card p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary">
                        <i class="bi bi-journal-medical text-accent fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Spesifikasi Recovery Plan</h6>
                    </div>
                    <div class="d-flex flex-column gap-3 small">
                        <div>
                            <span class="text-muted-portal d-block">Recovery Point Objective (RPO):</span>
                            <span class="fw-bold text-dark">< 5 Menit (Audit Triggers dan Log Tables Aktif)</span>
                        </div>
                        <div>
                            <span class="text-muted-portal d-block">Recovery Time Objective (RTO):</span>
                            <span class="fw-bold text-dark">< 2 Menit (Instant Restorasi Berkas SQL)</span>
                        </div>
                        <div>
                            <span class="text-muted-portal d-block">Metode Pemulihan:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill">
                                Dual-Mode: Full SQL + Granular Log Rollback
                            </span>
                        </div>
                    </div>
                </div>

                {{-- KARTU FITUR RECOVERY SISTEM --}}
                <div class="portal-card p-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary">
                        <i class="bi bi-gear-wide-connected text-accent fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Fitur Pemulihan Terintegrasi</h6>
                    </div>
                    <ul class="list-unstyled mb-0 small text-muted-portal d-flex flex-column gap-3">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block">Pencadangan Satu-Klik:</strong>
                                Ekspor skema, data, relasi foreign key, stored procedure, triggers, dan view secara otomatis.
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block">Pemulihan Data Terhapus (Rollback):</strong>
                                Memulihkan rekaman yang tidak sengaja terhapus/terupdate langsung dari halaman <em>Log Aktivitas</em>.
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block">Optimalisasi Query Optimizer:</strong>
                                Dilengkapi indeks komposit pada tabel nilai, ujian, dan absensi untuk respon kueri sub-detik.
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

    </div>

</div>

{{-- CSS KHUSUS PORTAL ADMIN --}}
<style>
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .text-accent {
        color: #4f46e5 !important;
    }
    .text-muted-portal {
        color: #64748b !important;
    }
    .border-secondary {
        border-color: #e2e8f0 !important;
    }
    .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .icon-box {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
        font-size: 1.15rem;
    }
    .btn-portal-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-portal-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: white;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
</style>

@endsection
