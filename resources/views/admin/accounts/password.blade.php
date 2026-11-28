@extends('layouts.admin')

@section('title', 'Reset Password Akun - Portal Akademik')

@section('content')
<div class="theme-portal rounded-4 shadow-sm">
    <div class="d-flex align-items-center gap-3 mb-4 p-4 border-bottom">
        <a href="{{ $user->role === 'guru' ? route('admin.teachers.index') : route('admin.students.index') }}"
           class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center"
           style="width: 42px; height: 42px;" aria-label="Kembali ke daftar">
            <i class="bi bi-arrow-left text-dark fs-5"></i>
        </a>
        <div>
            <div class="text-uppercase text-accent fw-bold small mb-1">Manajemen Pengguna</div>
            <h2 class="fw-bold mb-1 text-dark">Reset Password</h2>
            <p class="text-muted-portal mb-0">Atur password baru untuk akun guru atau siswa.</p>
        </div>
    </div>

    <div class="p-4 pt-0">
        @if (session('success'))
            <div class="alert alert-success rounded-3" role="alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger rounded-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.accounts.password.update', $user->user_id) }}" method="POST"
              class="portal-card p-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <div class="text-muted-portal small">Akun</div>
                <div class="fw-bold text-dark">{{ $user->name }} <span class="text-muted-portal">({{ $user->role }})</span></div>
                <div class="text-muted-portal">{{ $user->user_id }} · {{ $user->email }}</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password Baru</label>
                <input id="password" type="password" name="password" class="form-control"
                       minlength="8" maxlength="128" autocomplete="new-password" required>
                <div class="form-text">Minimal 8 karakter. Password lama tidak dapat dilihat.</div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                       minlength="8" maxlength="128" autocomplete="new-password" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-portal-primary px-4">
                    <i class="bi bi-key me-1"></i> Simpan Password Baru
                </button>
                <a href="{{ $user->role === 'guru' ? route('admin.teachers.index') : route('admin.students.index') }}"
                   class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<style>
    .theme-portal { background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; }
    .theme-portal .text-accent { color: #4f46e5 !important; }
    .theme-portal .text-muted-portal { color: #64748b !important; }
    .theme-portal .portal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .theme-portal .btn-portal-primary { background-color: #4f46e5; border-color: #4f46e5; color: #fff; }
    .theme-portal .btn-portal-primary:hover { background-color: #4338ca; color: #fff; }
</style>
@endsection
