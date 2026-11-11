@extends('layouts.admin')

@section('title', 'Tambah Akun Baru')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.accounts.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; transition: 0.2s;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Tambah Akun Baru</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item text-muted">Manajemen Akun</li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Buat Baru</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                
                <form action="{{ route('admin.accounts.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">User ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="user_id" class="form-control bg-light border-start-0 @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" placeholder="Masukkan NIP, NIS, atau NIK" required>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text small mt-2"><i class="bi bi-info-circle me-1"></i>Maksimal 10 karakter untuk Siswa, 18 karakter untuk Guru/Admin.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama sesuai identitas" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contoh@methodist.sch.id" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Peran Akses (Role) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shield-lock"></i></span>
                            <select name="role" class="form-select bg-light border-start-0 @error('role') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru Pengajar</option>
                                <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Peserta Didik (Siswa)</option>
                                <option value="orangtua" {{ old('role') == 'orangtua' ? 'selected' : '' }}>Wali / Orang Tua</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold text-dark">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Data Akun</button>
                    </div>

                </form>
                
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-xl-5 d-none d-lg-block">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100 p-4">
            <div class="card-body">
                <i class="bi bi-shield-check display-4 mb-3 d-block opacity-75"></i>
                <h4 class="fw-bold">Keamanan Data</h4>
                <p class="opacity-75 small mb-4">Pastikan Anda memasukkan User ID dengan teliti karena akan digunakan sebagai kunci penghubung ke data siswa dan guru secara permanen.</p>
                <ul class="list-unstyled small opacity-75 d-flex flex-column gap-2">
                    <li><i class="bi bi-check-circle me-2"></i> Role <b>Guru</b> akan otomatis dibuatkan slot kosong di tabel `teachers`.</li>
                    <li><i class="bi bi-check-circle me-2"></i> Role <b>Siswa</b> akan otomatis dibuatkan slot kosong di tabel `students`.</li>
                    <li><i class="bi bi-check-circle me-2"></i> Password dienkripsi secara aman.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection