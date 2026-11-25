@extends('layouts.admin')

@section('title', 'Detail Log #' . $row->log_id . ' - Portal Akademik')

@section('content')

@php
    $actionUpper = strtoupper((string) $row->log_action);
    $badge = match (true) {
        $actionUpper === 'INSERT'               => 'bg-success-subtle text-success border border-success-subtle',
        str_starts_with($actionUpper, 'UPDATE') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        default                                 => 'bg-danger-subtle text-danger border border-danger-subtle',
    };
@endphp

<div class="theme-portal rounded-4 shadow-sm">
    {{-- HEADER dan BREADCRUMB --}}
    <div class="p-4 border-bottom">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-decoration-none text-accent fw-semibold">
                        Log Aktivitas
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.activity-logs.table', $table) }}" class="text-decoration-none text-muted-portal">
                        {{ $label }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                    Log #{{ $row->log_id }}
                </li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-2">
            <div>
                <h2 class="fw-bold mb-1 text-dark">Log #{{ $row->log_id }}</h2>
                <p class="text-muted-portal mb-0">
                    Tabel <code class="table-code px-2 py-0.5 rounded">{{ $table }}</code> &middot;
                    <i class="bi bi-clock ms-1 me-1"></i>{{ \Carbon\Carbon::parse($row->log_at)->format('d M Y, H:i:s') }}
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(in_array($actionUpper, ['DELETE', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'UPDATE']))
                    <button type="button" class="btn btn-warning btn-sm px-3 py-2 rounded-3 text-dark fw-semibold shadow-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#recoverRecordModal">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Pulihkan Data Ini (Rollback)</span>
                    </button>

                    {{-- MODAL KONFIRMASI PEMULIHAN REKAMAN --}}
                    <div class="modal fade" id="recoverRecordModal" tabindex="-1" aria-labelledby="recoverRecordLabel" aria-hidden="true">
                        <div class="modal-dialog text-start">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-warning bg-opacity-25 border-bottom border-warning">
                                    <h5 class="modal-title fw-bold text-dark" id="recoverRecordLabel">
                                        <i class="bi bi-arrow-counterclockwise text-warning me-2"></i> Konfirmasi Pemulihan Rekaman
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="mb-2">Anda akan memulihkan data pada tabel <strong>{{ $table }}</strong> menggunakan snapshot data dari:</p>
                                    <div class="p-3 bg-light rounded-3 font-monospace small mb-3 border">
                                        Log #{{ $row->log_id }} &middot; Aksi: <strong>{{ $row->log_action }}</strong> &middot; Waktu: {{ $row->log_at }}
                                    </div>
                                    <div class="alert alert-info mb-0 small">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        Sistem akan memasukkan kembali data yang terhapus atau mengembalikan nilai kolom sebelum perubahan ke tabel utama.
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('admin.activity-logs.recover', [$table, $row->log_id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning text-dark fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Ya, Pulihkan Rekaman Ini
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('admin.activity-logs.table', $table) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke {{ $label }}
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        {{-- FLASH MESSAGES RECOVERY --}}
        @if(session('recovery_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('recovery_success') }}</div>
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
        {{-- KARTU INFORMASI METADATA LOG --}}
        <div class="portal-card p-4 mb-4">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="text-uppercase tracking-wide text-muted-portal small fw-semibold mb-1">Jenis Aksi</div>
                    <span class="badge {{ $badge }} text-uppercase px-3 py-1.5 rounded-pill fw-bold">
                        {{ str_replace('_', ' ', $row->log_action) }}
                    </span>
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-uppercase tracking-wide text-muted-portal small fw-semibold mb-1">Pelaku Perubahan</div>
                    @if ($by)
                        <div class="fw-bold text-dark">
                            {{ $by }}
                            <span class="badge bg-light text-muted-portal border font-monospace ms-1">{{ $row->log_by }}</span>
                        </div>
                    @else
                        <div class="fw-semibold text-dark font-monospace">{{ $row->log_by ?? '-' }}</div>
                    @endif
                </div>

                <div class="col-12 col-md-4">
                    <div class="text-uppercase tracking-wide text-muted-portal small fw-semibold mb-1">Identitas Log</div>
                    <div class="fw-bold text-dark font-monospace">#{{ $row->log_id }}</div>
                </div>
            </div>
        </div>

        {{-- TABEL PERBANDINGAN KOLOM (DATA LAMA VS DATA BARU) --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-layout-split text-accent me-2"></i> Rincian Nilai Kolom
                </h5>
                @if ($hasOld && $hasNew)
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small">
                        <i class="bi bi-pencil-square me-1"></i> Sorotan kuning menandakan perubahan
                    </span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 240px;">Nama Kolom</th>
                            @if ($hasOld)
                                <th>Data Lama</th>
                            @endif
                            @if ($hasNew)
                                <th class="pe-4">Data Baru</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr class="{{ $r['changed'] ? 'row-changed' : '' }}">
                                <td class="ps-4">
                                    <code class="table-code px-2 py-1 rounded fw-semibold">{{ $r['column'] }}</code>
                                    @if ($r['changed'])
                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Diubah</span>
                                    @endif
                                </td>

                                @if ($hasOld)
                                    <td class="font-monospace small" style="white-space: pre-wrap; word-break: break-word;">
                                        @if (is_null($r['old']))
                                            <span class="text-muted-portal fst-italic">NULL</span>
                                        @else
                                            <span class="{{ $r['changed'] ? 'text-danger-emphasis' : 'text-dark' }}">{{ $r['old'] }}</span>
                                        @endif
                                    </td>
                                @endif

                                @if ($hasNew)
                                    <td class="pe-4 font-monospace small" style="white-space: pre-wrap; word-break: break-word;">
                                        @if (is_null($r['new']))
                                            <span class="text-muted-portal fst-italic">NULL</span>
                                        @else
                                            <span class="{{ $r['changed'] ? 'fw-bold text-dark' : 'text-dark' }}">{{ $r['new'] }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer-portal px-4 py-3 small text-muted-portal d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-accent"></i>
                <span>
                    @if ($hasOld && $hasNew)
                        Baris dengan latar kuning menandakan kolom yang mengalami perubahan nilai.
                    @elseif (in_array($actionUpper, ['UPDATE_BEFORE', 'UPDATE_AFTER']))
                        Pasangan data sebelum/sesudah pembaruan tidak ditemukan.
                    @else
                        Snapshot lengkap data saat baris {{ $actionUpper === 'INSERT' ? 'dibuat' : 'dihapus' }}.
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
    }
    .text-accent {
        color: #4f46e5 !important;
    }
    .text-muted-portal {
        color: #64748b !important;
    }
    .tracking-wide {
        letter-spacing: 0.06em;
    }
    .border-secondary {
        border-color: #e2e8f0 !important;
    }
    .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }
    .card-header-portal {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .card-footer-portal {
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .table-code {
        font-size: 0.8rem;
        background-color: #f8fafc;
        color: #4f46e5;
        border: 1px solid #e2e8f0;
    }
    .custom-table {
        --bs-table-bg: transparent;
        --bs-table-color: #1e293b;
    }
    .row-changed {
        background-color: #fefce8 !important;
    }
</style>

@endsection