@extends('layouts.admin')

@section('title', 'Edit Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Data Sekolah</div>
            <h2 class="fw-bold mb-1 text-dark">Edit Data Siswa</h2>
            <p class="text-muted-portal mb-0">NIS: <code>{{ $student->nis }}</code></p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
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

        <form action="{{ route('admin.students.update', $student->nis) }}" method="POST" class="portal-card p-4">
            @csrf
            @method('PUT')

            <h6 class="fw-bold text-accent mb-3">1. Identitas Akun dan Akademik</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">NIS</label>
                    <input type="text" value="{{ $student->nis }}" class="form-control bg-light" disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">NISN (10 Digit)</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="10" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">-- Belum Ditempatkan --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->class_id }}" {{ (string) old('class_id', $student->class_id) === (string) $cls->class_id ? 'selected' : '' }}>
                                {{ $cls->name }} ({{ $cls->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $student->user->name ?? '') }}" maxlength="100" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tahun Masuk</label>
                    <input type="text" name="entry_year" value="{{ old('entry_year', $student->entry_year) }}" maxlength="4" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Siswa</label>
                    <select name="status" class="form-select">
                        @foreach(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'drop_out' => 'Drop Out'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $student->status) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-accent mb-3">2. Biodata, Alamat dan Orang Tua</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tempat Lahir</label>
                    <input type="text" name="pob" value="{{ old('pob', $student->pob) }}" maxlength="50" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                    <input type="date" name="dob"
                           value="{{ old('dob', $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">No. HP Siswa</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->user->phone ?? '') }}" maxlength="20" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <input type="text" name="address" value="{{ old('address', $student->address) }}" maxlength="255" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kecamatan</label>
                    <input type="text" name="district" value="{{ old('district', $student->district) }}" maxlength="100" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kota / Kabupaten</label>
                    <input type="text" name="city" value="{{ old('city', $student->city) }}" maxlength="100" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $student->province) }}" maxlength="100" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}" maxlength="100" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. HP Orang Tua / Wali</label>
                    <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" maxlength="20" class="form-control">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-portal-primary px-4">
                    <i class="bi bi-save me-1"></i> Perbarui Data Siswa
                </button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Batal</a>
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