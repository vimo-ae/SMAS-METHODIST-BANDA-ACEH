@extends('layouts.admin')

@section('title', 'Edit Enrollment Siswa')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit Data Enrollment Siswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item text-muted">Akademik</li>
                <li class="breadcrumb-item text-muted">Enrollment</li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $enrollment->student->user->name ?? 'Edit' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                
                <form action="{{ route('admin.enrollments.update', $enrollment->enrollment_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Siswa</label>
                        <input type="text" class="form-control bg-light" value="{{ $enrollment->students_id }} — {{ $enrollment->student->user->name ?? '' }}" readonly>
                        <div class="form-text small mt-1 text-muted">Siswa tidak dapat diubah pada halaman edit ini.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Kelas & Mata Pelajaran (CST) <span class="text-danger">*</span></label>
                        <select name="cst_id" class="form-select bg-light @error('cst_id') is-invalid @enderror" required>
                            @foreach($csts as $cst)
                                <option value="{{ $cst->cst_id }}" {{ old('cst_id', $enrollment->cst_id) == $cst->cst_id ? 'selected' : '' }}>
                                    {{ $cst->schoolClass->name ?? '-' }} | Mapel: {{ $cst->subject->name ?? '-' }} (Tahun: {{ $cst->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        @error('cst_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Status Siswa <span class="text-danger">*</span></label>
                        <select name="status" class="form-select bg-light @error('status') is-invalid @enderror" required>
                            @php $statuses = ['aktif', 'naik_kelas', 'tinggal_kelas', 'pindah', 'lulus', 'drop_out']; @endphp
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ old('status', $enrollment->status) == $st ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $st)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-dark">Tanggal Masuk (Enrolled At)</label>
                            <input type="date" name="enrolled_at" class="form-control bg-light @error('enrolled_at') is-invalid @enderror" value="{{ old('enrolled_at', optional($enrollment->enrolled_at)->format('Y-m-d')) }}">
                            @error('enrolled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-dark">Tanggal Keluar (Left At)</label>
                            <input type="date" name="left_at" class="form-control bg-light @error('left_at') is-invalid @enderror" value="{{ old('left_at', optional($enrollment->left_at)->format('Y-m-d')) }}">
                            @error('left_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Perubahan</button>
                    </div>

                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection