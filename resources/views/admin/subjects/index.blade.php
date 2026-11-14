@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Data Mata Pelajaran</h4>
        <p class="text-muted small mb-0">Kelola daftar mata pelajaran yang diajarkan di sekolah.</p>
    </div>
    <div>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-danger text-white" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari kode atau nama mata pelajaran..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-medium">Cari</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-light border rounded-3 text-secondary px-3" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">No</th>
                    <th class="py-3" style="width: 150px;">Kode Mapel</th>
                    <th class="py-3">Nama Mata Pelajaran</th>
                    <th class="text-end pe-4 py-3" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $index => $subject)
                    <tr>
                        <td class="ps-4 text-muted">{{ $subjects->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6 font-monospace">
                                {{ $subject->code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark fs-6">{{ $subject->name }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Edit --}}
<a href="{{ route('admin.subjects.edit', $subject->code) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit Mapel">
    <i class="bi bi-pencil-square"></i>
</a>

{{-- Tombol Hapus --}}
<form action="{{ route('admin.subjects.destroy', $subject->code) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran {{ $subject->name }}?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus Mapel">
        <i class="bi bi-trash"></i>
    </button>
</form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-book display-4 text-muted d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Data tidak ditemukan</h6>
                            <p class="small mb-0">Belum ada mata pelajaran yang terdaftar atau sesuai pencarian Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($subjects->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $subjects->links() }}
        </div>
    @endif
</div>
@endsection