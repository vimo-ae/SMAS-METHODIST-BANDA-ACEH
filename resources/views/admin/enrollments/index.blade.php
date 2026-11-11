@extends('layouts.admin')

@section('title', 'Enrollment Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Manajemen Enrollment Siswa</h4>
        <p class="text-muted small mb-0">Kelola pendaftaran siswa ke dalam kelas dan mata pelajaran.</p>
    </div>
    <div>
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Daftarkan Siswa
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Filter Card --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <select name="cst_id" class="form-select bg-light">
                    <option value="">Semua Kelas & Mapel</option>
                    @foreach($csts as $cst)
                        <option value="{{ $cst->cst_id }}" {{ request('cst_id') == $cst->cst_id ? 'selected' : '' }}>
                            {{ $cst->schoolClass->name ?? '-' }} — {{ $cst->subject->name ?? '-' }} ({{ $cst->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="academic_year" class="form-control bg-light" placeholder="Filter Tahun Ajaran (Cth: 2025/2026)" value="{{ request('academic_year') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-medium">Filter</button>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-light border w-100 rounded-3 text-secondary d-flex align-items-center justify-content-center">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">NIS</th>
                    <th class="py-3">Nama Siswa</th>
                    <th class="py-3">Kelas & Mapel</th>
                    <th class="py-3">Tahun Ajaran</th>
                    <th class="py-3">Status</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $item)
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-dark">{{ $item->students_id }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->student->user->name ?? 'Tidak ditemukan' }}</div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $item->classSubjectTeacher->schoolClass->name ?? '-' }}</div>
                            <small class="text-muted">{{ $item->classSubjectTeacher->subject->name ?? '-' }}</small>
                        </td>
                        <td class="text-muted small">{{ $item->academic_year }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-capitalize">
                                {{ str_replace('_', ' ', $item->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.enrollments.edit', $item->enrollment_id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.enrollments.destroy', $item->enrollment_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus enrollment ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x display-4 text-muted d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Belum ada data enrollment</h6>
                            <p class="small mb-0">Silakan daftarkan siswa ke kelas menggunakan tombol di atas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($enrollments->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection