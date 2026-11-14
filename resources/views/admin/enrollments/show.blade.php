@extends('layouts.admin')

@section('title', 'Detail Enrollment Siswa')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-arrow-left text-dark fs-5"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1 text-dark">Rincian Kelas Mapel Siswa</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('admin.enrollments.index') }}" class="text-decoration-none text-muted">Enrollment</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $student->user->name ?? 'Siswa' }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-primary shadow-sm rounded-3 px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="bi bi-plus-lg me-1"></i> Masukkan ke Kelas Baru
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- KOLOM KIRI: PROFIL SINGKAT SISWA --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
            <div class="bg-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold fs-2 mb-3 shadow-sm" style="width: 70px; height: 70px;">
                {{ substr($student->user->name ?? 'S', 0, 1) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $student->user->name ?? 'Tanpa Nama' }}</h5>
            <p class="text-muted small mb-3">NIS: <span class="fw-semibold text-dark font-monospace">{{ $student->nis }}</span></p>
            
            <div class="text-start border-top pt-3">
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted">Kelas Induk:</span>
                    <span class="fw-semibold text-dark">{{ $student->schoolClass->name ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted">Total Mapel Diikuti:</span>
                    <span class="fw-bold text-primary">{{ $student->classEnrollments->count() }} Mapel</span>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: TABEL DAFTAR KELAS MAPEL YANG DIIKUTI --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-bookmark text-primary me-2"></i> Daftar Mata Pelajaran Aktif</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Mata Pelajaran</th>
                            <th class="py-3">Guru Pengampu</th>
                            <th class="py-3">Status</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->classEnrollments as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->classSubjectTeacher->subject->name ?? '-' }}</div>
                                    <small class="text-muted">Ruangan: {{ $item->classSubjectTeacher->schoolClass->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $item->classSubjectTeacher->teacher->user->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border px-2 py-1 text-uppercase small">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.enrollments.destroy', $item->enrollment_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan siswa dari kelas mapel ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger shadow-sm" title="Hapus dari kelas">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">
                                    <i class="bi bi-journal-x display-5 d-block mb-2 opacity-50"></i>
                                    Siswa ini belum dimasukkan ke kelas mata pelajaran apapun.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KELAS UNTUK SISWA INI --}}
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.enrollments.store') }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <input type="hidden" name="students_id" value="{{ $student->nis }}">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark">Masukkan ke Kelas Mapel Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Pilih Kelas & Mapel (CST) <span class="text-danger">*</span></label>
                    <select name="cst_id" class="form-select bg-light" required>
                        <option value="" selected disabled>-- Pilih Kelas Mapel --</option>
                        @foreach($csts as $cst)
                            <option value="{{ $cst->cst_id }}">
                                {{ $cst->subject->name ?? '' }} (Ruangan: {{ $cst->schoolClass->name ?? '' }} - {{ $cst->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Status Siswa <span class="text-danger">*</span></label>
                    <select name="status" class="form-select bg-light" required>
                        <option value="aktif" selected>Aktif</option>
                        <option value="naik_kelas">Naik Kelas</option>
                        <option value="tinggal_kelas">Tinggal Kelas</option>
                        <option value="pindah">Pindah</option>
                        <option value="lulus">Lulus</option>
                        <option value="drop_out">Drop Out</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection