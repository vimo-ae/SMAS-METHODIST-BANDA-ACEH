@extends('layouts.admin')
@section('title', 'Tambah Guru Baru')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Tambah Guru Baru</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.teachers.store') }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold text-primary mb-3">1. Informasi Akun & Identitas</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIP (Nomor Induk Pegawai) <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="form-control bg-light" value="{{ old('nip') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control bg-light" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4">2. Detail Profil</h6>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select bg-light" required>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Status Kepegawaian <span class="text-danger">*</span></label>
                            <select name="status" class="form-select bg-light" required>
                                <option value="aktif" selected>Aktif</option>
                                <option value="cuti">Cuti</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="address" class="form-control bg-light" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Guru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection