@extends('layouts.admin')

@section('title', 'Manajemen Akun')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Manajemen Akun Pengguna</h4>
        <p class="text-muted small mb-0">Kelola akses login untuk Admin, Guru, Siswa, dan Orang Tua.</p>
    </div>
    <div>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Akun
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-danger text-white" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Filter & Search Card --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.accounts.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari NIP/NIS, nama, atau email..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select form-select-lg fs-6 bg-light">
                    <option value="">Semua Peran (Role)</option>
                    <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="guru" {{ ($role ?? '') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="siswa" {{ ($role ?? '') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="orangtua" {{ ($role ?? '') == 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-medium">Filter</button>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border w-100 rounded-3 text-secondary d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data Akun --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">ID (NIP/NIS/NIK)</th>
                    <th class="py-3">Nama Lengkap</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Peran</th>
                    <th class="py-3">Terdaftar</th>
                    <th class="text-end pe-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark font-monospace">{{ $user->user_id }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $user->name }}</div>
                        </td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Admin</span>
                            @elseif($user->role == 'guru')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Guru</span>
                            @elseif($user->role == 'siswa')
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Siswa</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Orang Tua</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <i class="bi bi-calendar-check me-1"></i> {{ $user->created_at?->format('d M Y') }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.accounts.edit', $user->user_id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($user->user_id !== auth()->id())
                                    <form action="{{ route('admin.accounts.destroy', $user->user_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tindakan ini tidak bisa dibatalkan! Yakin ingin menghapus akun {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-4 text-muted d-block mb-3 opacity-50"></i>
                            <h6 class="fw-semibold text-dark">Data tidak ditemukan</h6>
                            <p class="small mb-0">Belum ada akun yang terdaftar atau sesuai dengan kata kunci pencarian Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
   {{-- Pagination Links --}}
    @if($users->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection