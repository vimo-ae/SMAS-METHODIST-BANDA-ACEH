@extends('layouts.admin')

@section('title', 'Tambah Enrollment Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Akademik</div>
            <h2 class="fw-bold mb-1 text-dark">Tambah Enrollment Siswa</h2>
            <p class="text-muted-portal mb-0">Daftarkan siswa ke dalam kelas mata pelajaran tertentu.</p>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="p-4 pt-0">
        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.enrollments.store') }}" method="POST" class="portal-card p-4">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                    <select name="students_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->nis }}" {{ (string) old('students_id') === (string) $st->nis ? 'selected' : '' }}>
                                {{ $st->nis }} — {{ $st->user->name ?? 'Siswa' }}
                                @if($st->schoolClass) ({{ $st->schoolClass->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kelas Mata Pelajaran (CST) <span class="text-danger">*</span></label>
                    <select name="cst_id" class="form-select" required>
                        <option value="">-- Pilih Kelas dan Mapel --</option>
                        @foreach($csts as $cst)
                            <option value="{{ $cst->cst_id }}" {{ (string) old('cst_id') === (string) $cst->cst_id ? 'selected' : '' }}>
                                {{ $cst->schoolClass->name ?? 'Kelas #' . $cst->class_id }} —
                                {{ $cst->subject->name ?? $cst->subject_code }}
                                ({{ $cst->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Enrollment <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['aktif', 'naik_kelas', 'tinggal_kelas', 'pindah', 'lulus', 'drop_out'] as $st)
                            <option value="{{ $st }}" {{ old('status', 'aktif') === $st ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $st)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Masuk</label>
                    <input type="date" name="enrolled_at" value="{{ old('enrolled_at', now()->toDateString()) }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Keluar (Opsional)</label>
                    <input type="date" name="left_at" value="{{ old('left_at') }}" class="form-control">
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-portal-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Enrollment
                </button>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<style>
    .theme-portal { background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; }
    .text-accent { color: #4f46e5 !important; }
    .text-muted-portal { color: #64748b !important; }
    .portal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .btn-portal-primary { background-color: #4f46e5; border-color: #4f46e5; color: white; font-weight: 500; }
    .btn-portal-primary:hover { background-color: #4338ca; color: white; }
</style>

@endsection