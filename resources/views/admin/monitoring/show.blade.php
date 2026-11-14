@extends('layouts.admin')

@section('title', 'Detail Monitoring Sesi')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Detail Sesi Ujian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item text-muted">Monitoring</li>
                <li class="breadcrumb-item active fw-semibold" aria-current="page">Sesi Kelas</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Info Sesi --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">{{ $session->exam->title ?? 'Ujian Tengah Semester - Matematika' }}</h5>
            <div class="text-muted small">
                <span class="me-3"><i class="bi bi-building me-1"></i> Kelas: {{ $session->cst->schoolClass->name ?? 'X IPA 1' }}</span>
                <span><i class="bi bi-key me-1"></i> Token: <strong class="text-dark">{{ $session->token ?? 'X7B9Q' }}</strong></span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-3 border border-success border-opacity-25">
                <i class="bi bi-record-circle-fill me-1"></i> Berlangsung
            </span>
        </div>
    </div>
</div>

{{-- Tabel Peserta --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">Daftar Peserta Ujian</h6>
        <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control bg-light border-start-0" placeholder="Cari siswa...">
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Siswa</th>
                    <th>NISN</th>
                    <th>Mulai Mengerjakan</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $p)
                    <tr>
                        <td class="ps-4">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $p->student->user->name ?? 'Nama Siswa' }}</div>
                        </td>
                        <td class="text-muted">{{ $p->student->nisn ?? '-' }}</td>
                        <td class="text-muted small">
                            <i class="bi bi-clock me-1"></i> {{ $p->start_time ?? '-' }}
                        </td>
                        <td>
                            {{-- Contoh Badge Status --}}
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Mengerjakan</span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light border shadow-sm text-danger" title="Reset Login">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Belum ada peserta yang tergabung di sesi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection