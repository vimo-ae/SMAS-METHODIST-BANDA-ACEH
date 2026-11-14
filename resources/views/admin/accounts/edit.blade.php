@extends('layouts.admin')

@section('title', 'Edit Akun')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.accounts.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; transition: 0.2s;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit Akun Pengguna</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item text-muted">Manajemen Akun</li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                
                <form action="{{ route('admin.accounts.update', $user->user_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">User ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="user_id" class="form-control bg-light border-start-0 @error('user_id') is-invalid @enderror" value="{{ old('user_id', $user->user_id) }}" readonly required>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text small mt-2 text-warning"><i class="bi bi-exclamation-triangle me-1"></i>User ID tidak disarankan untuk diubah karena terhubung ke tabel relasi.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Peran Akses (Role) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shield-lock"></i></span>
                            <select name="role" class="form-select bg-light border-start-0 @error('role') is-invalid @enderror" required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="guru" {{ old('role', $user->role) == 'guru' ? 'selected' : '' }}>Guru Pengajar</option>
                                <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Peserta Didik (Siswa)</option>
                                <option value="orangtua" {{ old('role', $user->role) == 'orangtua' ? 'selected' : '' }}>Wali / Orang Tua</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="border-secondary my-4 opacity-10">

                    <div class="mb-5">
                        <label class="form-label fw-semibold text-dark">Ubah Kata Sandi <span class="text-muted fw-normal small ms-1">(Opsional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah sandi">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text small mt-2">Hanya isi bidang ini jika pengguna lupa sandinya atau ingin mereset akses.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Perubahan</button>
                    </div>

                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection