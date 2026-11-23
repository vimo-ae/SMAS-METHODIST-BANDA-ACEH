
@extends('layouts.admin')

@section('title', 'Log ' . $label . ' - Portal Akademik')

@section('content')

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
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                    {{ $label }}
                </li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-2">
            <div>
                <h2 class="fw-bold mb-1 text-dark">Log {{ $label }}</h2>
                <p class="text-muted-portal mb-0">
                    Tabel <code class="table-code px-2 py-0.5 rounded">{{ $table }}</code> &middot;
                    Total <strong class="text-dark">{{ number_format($stats->total ?? $logs->total()) }}</strong> catatan
                </p>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold">
                    +{{ number_format($stats->ins) }} Insert
                </span>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill fw-semibold">
                    ~{{ number_format($stats->upd) }} Update
                </span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold">
                    -{{ number_format($stats->del) }} Delete
                </span>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 ms-1">
                    <i class="bi bi-arrow-left me-1"></i> Semua Tabel
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        {{-- KARTU FILTER dan PENCARIAN --}}
        <div class="portal-card p-3 p-sm-4 mb-4">
            <form method="GET" action="{{ route('admin.activity-logs.table', $table) }}"
                  class="row g-2 align-items-center">
                <div class="col-12 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted-portal">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Cari data atau nama pelaku..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-sm-4 col-lg-2">
                    <select name="action" class="form-select">
                        <option value="">Semua Aksi</option>
                        @foreach ($actions as $a)
                            <option value="{{ $a }}" @selected(request('action') === $a)>
                                {{ str_replace('_', ' ', $a) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-sm-4 col-lg-2">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}" title="Dari tanggal">
                </div>

                <div class="col-6 col-sm-4 col-lg-2">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}" title="Sampai tanggal">
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-accent flex-grow-1 fw-semibold">
                        Filter
                    </button>
                    <a href="{{ route('admin.activity-logs.table', $table) }}" class="btn btn-outline-secondary" title="Reset Filter">
                        Reset
                    </a>
                </div>
            </form>

            @if ($errors->any())
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
                </div>
            @endif
        </div>

        {{-- TABEL DAFTAR LOG AKTIVITAS --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-accent me-2"></i> Riwayat Perubahan ({{ number_format($logs->total()) }})
                </h5>
                <span class="small text-muted-portal">
                    Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 100px;">Log ID</th>
                            <th style="width: 165px;">Waktu</th>
                            <th style="width: 160px;">Aksi</th>
                            @foreach ($cols as $c)
                                <th><code class="text-muted-portal">{{ $c }}</code></th>
                            @endforeach
                            <th style="width: 190px;">Pelaku</th>
                            <th class="text-end pe-4" style="width: 100px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $actionUpper = strtoupper((string) $log->log_action);
                                $badge = match (true) {
                                    $actionUpper === 'INSERT'               => 'bg-success-subtle text-success border border-success-subtle',
                                    str_starts_with($actionUpper, 'UPDATE') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                    default                                 => 'bg-danger-subtle text-danger border border-danger-subtle',
                                };
                                $actionLabel = match ($actionUpper) {
                                    'UPDATE_BEFORE' => 'Update (Sebelum)',
                                    'UPDATE_AFTER'  => 'Update (Sesudah)',
                                    'UPDATE'        => 'Update',
                                    'INSERT'        => 'Insert',
                                    'DELETE'        => 'Delete',
                                    default         => $log->log_action,
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 font-monospace fw-semibold text-dark">
                                    #{{ $log->log_id }}
                                </td>
                                <td class="small text-muted-portal font-monospace">
                                    {{ \Carbon\Carbon::parse($log->log_at)->format('d M Y H:i:s') }}
                                </td>
                                <td>
                                    <span class="badge {{ $badge }} px-2.5 py-1 rounded-pill fw-semibold">
                                        {{ $actionLabel }}
                                    </span>
                                </td>
                                @foreach ($cols as $c)
                                    <td class="small text-dark">
                                        @if (is_null($log->{$c}))
                                            <span class="text-muted-portal fst-italic">NULL</span>
                                        @else
                                            {{ \Illuminate\Support\Str::limit((string) $log->{$c}, 40) }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    @if ($log->log_by && isset($users[$log->log_by]))
                                        <div class="fw-semibold text-dark small">{{ $users[$log->log_by] }}</div>
                                        <span class="badge bg-light text-muted-portal border font-monospace" style="font-size: 0.7rem;">
                                            {{ $log->log_by }}
                                        </span>
                                    @else
                                        <span class="text-muted-portal font-monospace small">{{ $log->log_by ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.activity-logs.show', [$table, $log->log_id]) }}"
                                       class="btn btn-sm btn-outline-accent px-3 rounded-3 fw-semibold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 5 + count($cols) }}" class="text-center py-5 text-muted-portal">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada catatan log untuk <strong>{{ $label }}</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="card-footer-portal px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted-portal">
                        Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} catatan
                    </div>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
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
    .btn-accent {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }
    .btn-accent:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: #ffffff;
    }
    .btn-outline-accent {
        color: #4f46e5;
        border-color: #c7d2fe;
        background-color: #eef2ff;
    }
    .btn-outline-accent:hover {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }
</style>

@endsection