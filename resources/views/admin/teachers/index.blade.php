@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Data Guru</h4>
        <p class="text-muted small mb-0">Kelola informasi tenaga pendidik dan pengajar.</p>
    </div>
    <div>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Guru Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari berdasarkan Nama atau NIP..." value="{{ request('search') }}">
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
                    <th class="ps-4 py-3">NIP</th>
                    <th class="py-3">Nama Lengkap</th>
                    <th class="py-3">No. Telepon</th>
                    <th class="py-3">JK</th>
                    <th class="py-3">Status</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-dark">{{ $teacher->nip }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $teacher->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $teacher->user->email ?? '-' }}</small>
                        </td>
                        <td>{{ $teacher->user->phone ?? '-' }}</td>
                        <td>{{ $teacher->gender }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success border px-2 py-1 text-uppercase small">
                                {{ $teacher->status }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.teachers.edit', $teacher->nip) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->nip) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data guru dan akun ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person display-4 d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Belum ada data guru</h6>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($teachers->hasPages())
        <div class="card-footer bg-white border-top p-3">{{ $teachers->links() }}</div>
    @endif
</div>
@endsection