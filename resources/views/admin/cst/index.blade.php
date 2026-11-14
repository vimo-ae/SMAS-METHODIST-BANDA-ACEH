@extends('layouts.admin')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Manajemen Kelas Mapel</h4>
        <p class="text-muted small mb-0">Kelola kelas, pengampu, dan kode gabung.</p>
    </div>
    <div>
        <a href="{{ route('admin.cst.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Buat Kelas Mapel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-danger text-white">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.cst.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari mapel, guru, ruangan, atau kode gabung..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-medium btn-lg">Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Nama Kelas (Mapel)</th>
                    <th class="py-3">Ruangan</th>
                    <th class="py-3">Guru Pengampu</th>
                    <th class="py-3">Tahun Ajaran</th>
                    <th class="py-3 text-center">Join Code</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($csts as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-primary">{{ $item->subject->name ?? '-' }}</div>
                            <small class="text-muted">{{ $item->subject_code }}</small>
                        </td>
                        <td><span class="fw-semibold text-dark">{{ $item->schoolClass->name ?? '-' }}</span></td>
                        <td><div class="fw-medium text-dark">{{ $item->teacher->user->name ?? 'NIP: '.$item->teacher_id }}</div></td>
                        <td class="text-muted small">{{ $item->academic_year }}</td>
                        <td class="text-center">
                            <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2 font-monospace fs-6">
                                {{ $item->join_code }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.cst.edit', $item->cst_id) }}" class="btn btn-sm btn-light border shadow-sm text-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.cst.destroy', $item->cst_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-building display-4 d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Belum ada Kelas</h6>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($csts->hasPages())
        <div class="card-footer bg-white border-top p-3">{{ $csts->links() }}</div>
    @endif
</div>
@endsection