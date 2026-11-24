@extends('layouts.admin')

@section('title', 'Log Aktivitas Database - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Audit dan Riwayat Sistem</div>
            <h2 class="fw-bold mb-1 text-dark">Log Aktivitas Database</h2>
            <p class="text-muted-portal mb-0">
                Pilih tabel di bawah untuk meninjau riwayat perubahan data secara mendetail &middot;
                Total <strong class="text-dark">{{ number_format($grandTotal) }}</strong> catatan tercatat
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-lock me-1"></i> Audit Read-Only
            </span>
        </div>
    </div>

    {{-- DAFTAR KELOMPOK dan KARTU TABEL --}}
    <div class="p-4">
        @foreach ($groups as $groupName => $items)
            <div class="{{ !$loop->first ? 'mt-5' : '' }}">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="group-indicator"></span>
                    <h6 class="text-uppercase tracking-wide fw-bold text-dark mb-0 small">
                        {{ $groupName }}
                    </h6>
                    <span class="badge bg-light text-muted-portal border rounded-pill small">
                        {{ count($items) }} Tabel
                    </span>
                </div>

                <div class="row g-3">
                    @foreach ($items as $t => $s)
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="log-card h-100 position-relative d-flex flex-column justify-content-between p-3 p-sm-4">
                                <div>
                                    {{-- Judul Tabel dan Total Catatan --}}
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">{{ $s['label'] }}</h6>
                                            <code class="table-code px-2 py-0.5 rounded">{{ $t }}</code>
                                        </div>
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill fw-bold font-monospace">
                                            {{ number_format($s['total']) }}
                                        </span>
                                    </div>

                                    {{-- Rincian Aksi (Insert, Update, Delete) --}}
                                    <div class="d-flex flex-wrap gap-1.5 mb-3">
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-semibold">
                                            +{{ number_format($s['ins']) }} Insert
                                        </span>
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2.5 py-1 rounded-pill fw-semibold">
                                            ~{{ number_format($s['upd']) }} Update
                                        </span>
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill fw-semibold">
                                            -{{ number_format($s['del']) }} Delete
                                        </span>
                                    </div>
                                </div>

                                {{-- Waktu Aktivitas Terakhir dan Ikon Arah --}}
                                <div class="pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center small text-muted-portal">
                                    <span>
                                        @if ($s['last_at'])
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($s['last_at'])->format('d M Y, H:i') }}
                                        @else
                                            <i class="bi bi-dash-circle me-1"></i>
                                            Belum ada aktivitas
                                        @endif
                                    </span>
                                    <i class="bi bi-arrow-right-short fs-5 text-accent arrow-icon"></i>
                                </div>

                                <a href="{{ route('admin.activity-logs.table', $t) }}"
                                   class="stretched-link"
                                   aria-label="Lihat log {{ $s['label'] }}"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
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
    .group-indicator {
        width: 4px;
        height: 16px;
        border-radius: 999px;
        background-color: #4f46e5;
        display: inline-block;
    }
    .log-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .log-card:hover {
        border-color: #4f46e5;
        box-shadow: 0 10px 24px -6px rgba(79, 70, 229, 0.12);
        transform: translateY(-2px);
    }
    .log-card .arrow-icon {
        transition: transform 0.2s ease;
    }
    .log-card:hover .arrow-icon {
        transform: translateX(3px);
    }
    .table-code {
        font-size: 0.75rem;
        background-color: #f8fafc;
        color: #4f46e5;
        border: 1px solid #e2e8f0;
    }
</style>

@endsection