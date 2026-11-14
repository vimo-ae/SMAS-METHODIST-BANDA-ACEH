@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Data Siswa</h4>
        <p class="text-muted small mb-0">Kelola informasi seluruh siswa terdaftar di sekolah.</p>
    </div>
    <div>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Siswa Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.students.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari berdasarkan Nama, NIS, atau NISN..." value="{{ request('search') }}">
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
                    <th class="ps-4 py-3">NIS / NISN</th>
                    <th class="py-3">Nama Lengkap</th>
                    <th class="py-3">No. Telepon</th>
                    <th class="py-3">Kelas Induk</th>
                    <th class="py-3">JK</th>
                    <th class="py-3">Status</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark font-monospace">{{ $student->nis }}</div>
                            <small class="text-muted">{{ $student->nisn ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $student->user?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $student->user?->email ?? '-' }}</small>
                        </td>
                        <td>{{ $student->user?->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border">{{ $student->schoolClass?->name ?? 'Belum ada' }}</span>
                        </td>
                        <td>{{ $student->gender }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success border px-2 py-1 text-uppercase small">
                                {{ $student->status }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.students.edit', $student->nis) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.students.destroy', $student->nis) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data siswa dan akun ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard display-4 d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Belum ada data siswa</h6>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($students, 'hasPages') && $students->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection