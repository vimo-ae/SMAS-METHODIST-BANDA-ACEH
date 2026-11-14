@extends('layouts.admin')

@section('title', 'Enrollment Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Enrollment Kelas Siswa</h4>
        <p class="text-muted small mb-0">Kelola pendaftaran kelas mata pelajaran per siswa.</p>
    </div>
    <div>
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Enrollment Baru
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
        <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari nama siswa atau NIS..." value="{{ request('search') }}">
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
                    <th class="ps-4 py-3">NIS</th>
                    <th class="py-3">Nama Siswa</th>
                    <th class="py-3">Kelas Induk (Ruangan)</th>
                    <th class="py-3 text-center">Jml Kelas Mapel</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="ps-4 font-monospace text-muted">{{ $student->nis }}</td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $student->user->name ?? 'Tanpa Nama' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border">{{ $student->schoolClass->name ?? 'Belum ada' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-bold">
                                {{ $student->classEnrollments->count() }} Kelas
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.enrollments.show', $student->nis) }}" class="btn btn-sm btn-light border shadow-sm text-primary px-3 rounded-3 fw-medium">
                                <i class="bi bi-eye me-1"></i> Kelola Kelas <i class="bi bi-chevron-right ms-1 small"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-4 d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Belum ada data enrollment</h6>
                            <p class="small mb-0">Silakan tambahkan pendaftaran kelas untuk siswa.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection