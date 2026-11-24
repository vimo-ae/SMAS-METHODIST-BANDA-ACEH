@extends('layouts.admin')

@section('title', 'Manajemen Akun - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Manajemen Pengguna</div>
            <h2 class="fw-bold mb-1 text-dark">Daftar Akun Pengguna</h2>
            <p class="text-muted-portal mb-0">Kelola akun administrator, guru dan siswa dalam satu sistem.</p>
        </div>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-portal-primary px-4 py-2 rounded-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Akun Baru
        </a>
    </div>

    <div class="p-4 pt-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM PENCARIAN dan FILTER RAPI --}}
        <form method="GET" action="{{ route('admin.accounts.index') }}" class="portal-card p-3 mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari nama, email, atau User ID...">
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-3">
                    <select name="role" class="form-select">
                        <option value="">-- Semua Role --</option>
                        @foreach(['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $val => $label)
                            <option value="{{ $val }}" {{ (string) request('role') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-portal-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL AKUN --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-accent me-2"></i> Daftar Akun ({{ $users->total() }})
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th style="width: 150px;">User ID</th>
                            <th>Nama dan Email</th>
                            <th style="width: 130px;">Role</th>
                            <th class="text-center" style="width: 150px;">Rata-rata Nilai</th>
                            <th class="text-end pe-4 text-nowrap" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $item)
                            @php
                                $roleBadge = match($item->role) {
                                    'admin'    => 'bg-danger-subtle text-danger',
                                    'guru'     => 'bg-primary-subtle text-primary',
                                    'siswa'    => 'bg-success-subtle text-success',
                                    default    => 'bg-secondary-subtle text-secondary',
                                };
                                $studentStatus = strtolower((string) ($item->student->status ?? $item->student_status ?? $item->status ?? ''));
                                $isActiveStudent = $item->role === 'siswa' && in_array($studentStatus, ['aktif', 'active', '1']);
                            @endphp
                            <tr>
                                <td class="ps-4 text-center text-muted-portal">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $item->user_id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    <small class="text-muted-portal">{{ $item->email }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $roleBadge }} px-3 py-1 rounded-pill text-uppercase small">
                                        {{ $item->role }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->role === 'siswa')
                                        <span class="badge bg-light text-dark border px-3 py-1 fw-bold">
                                            {{ $item->student_avg_score !== null ? number_format($item->student_avg_score, 2) : '-' }}
                                        </span>
                                    @else
                                        <span class="text-muted-portal small">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.accounts.edit', $item->user_id) }}"
                                           class="btn btn-sm btn-outline-primary px-2" title="Edit Akun">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($item->user_id !== Auth::id())
                                            @if($isActiveStudent)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary px-2 opacity-50"
                                                        title="Akun siswa aktif tidak dapat dihapus. Ubah status siswa terlebih dahulu."
                                                        onclick="alert('Akun siswa {{ addslashes($item->name) }} masih berstatus AKTIF dan tidak dapat dihapus. Nonaktifkan status siswa terlebih dahulu.');">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.accounts.destroy', $item->user_id) }}" method="POST"
                                                      onsubmit="return confirm('Yakin ingin menghapus akun {{ addslashes($item->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted-portal">
                                    Belum ada data akun pengguna yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .theme-portal { background: #ffffff; color: #1e293b; min-height: 80vh; border: 1px solid #e2e8f0; }
    .text-accent { color: #4f46e5 !important; }
    .text-muted-portal { color: #64748b !important; }
    .border-secondary { border-color: #e2e8f0 !important; }
    .portal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .card-header-portal { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .custom-table { --bs-table-bg: transparent; --bs-table-color: #1e293b; }
    .btn-portal-primary { background-color: #4f46e5; border-color: #4f46e5; color: white; font-weight: 500; }
    .btn-portal-primary:hover { background-color: #4338ca; color: white; }
</style>

@endsection