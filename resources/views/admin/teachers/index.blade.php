@extends('layouts.admin')

@section('title', 'Data Guru - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase text-accent fw-bold small mb-1" style="letter-spacing: .08em;">Data Sekolah</div>
            <h2 class="fw-bold mb-1 text-dark">Data Tenaga Pendidik (Guru)</h2>
            <p class="text-muted-portal mb-0">
                Manajemen biodata guru, spesialisasi bidang studi, dan status kepegawaian.
            </p>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-portal-primary px-4 py-2 rounded-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Guru
        </a>
    </div>

    <div class="p-4 pt-0">

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- Pesan penolakan hapus dari TeacherController@destroy dikirim lewat with('error') --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- FORM PENCARIAN dan FILTER --}}
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="portal-card p-3 mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari NIP, nama guru, email, atau bidang studi...">
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-3">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        @foreach (['aktif' => 'Aktif', 'cuti' => 'Cuti', 'pensiun' => 'Pensiun', 'resign' => 'Resign'] as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-portal-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL DATA GURU --}}
        <div class="portal-card">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-badge text-accent me-2"></i> Daftar Guru ({{ $teachers->total() }} Orang)
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>NIP</th>
                            <th>Nama dan Kontak</th>
                            <th>Spesialisasi dan Pendidikan</th>
                            <th>Tgl Bergabung</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $index => $teacher)
                            @php
                                $status    = strtolower(trim((string) ($teacher->status ?? 'aktif')));
                                $isAktif   = $status === 'aktif';
                                $teacherName = $teacher->user->name ?? $teacher->nip;

                                $badgeClass = match ($status) {
                                    'aktif'   => 'bg-success-subtle text-success',
                                    'cuti'    => 'bg-warning-subtle text-warning-emphasis',
                                    'pensiun' => 'bg-secondary-subtle text-secondary',
                                    'resign'  => 'bg-danger-subtle text-danger',
                                    default   => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted-portal">
                                    {{ $teachers->firstItem() + $index }}
                                </td>
                                <td>
                                    <code class="fw-semibold">{{ $teacher->nip }}</code>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $teacher->user->name ?? '-' }}</div>
                                    <small class="text-muted-portal d-block">{{ $teacher->user->email ?? '-' }}</small>
                                    @if (! empty($teacher->user->phone))
                                        <small class="text-muted-portal"><i class="bi bi-telephone me-1"></i>{{ $teacher->user->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-medium">{{ $teacher->specialization ?? '-' }}</div>
                                    <small class="text-muted-portal">Pendidikan: {{ $teacher->education_level ?? '-' }}</small>
                                </td>
                                <td class="text-muted-portal small">
                                    {{ $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }} px-3 py-1 rounded-pill text-capitalize">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.accounts.password.edit', $teacher->nip) }}"
                                           class="btn btn-sm btn-outline-warning" title="Reset password"
                                           aria-label="Reset password akun guru {{ $teacherName }}">
                                            <i class="bi bi-key"></i>
                                        </a>
                                        <a href="{{ route('admin.teachers.edit', $teacher->nip) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($isAktif)
                                            {{-- Guru aktif tidak boleh dihapus: ubah status lewat Edit dulu --}}
                                            <span class="d-inline-block" tabindex="0"
                                                  title="Guru aktif tidak bisa dihapus. Ubah status lewat Edit terlebih dahulu.">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                        style="pointer-events: none;" aria-label="Hapus (tidak tersedia untuk guru aktif)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form action="{{ route('admin.teachers.destroy', $teacher->nip) }}" method="POST"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Yakin ingin menghapus data guru ' . $teacherName . '?') }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted-portal">
                                    Belum ada data guru yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($teachers->hasPages())
                <div class="p-3 border-top">
                    {{ $teachers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- CSS dibatasi di .theme-portal agar tidak memengaruhi halaman lain --}}
<style>
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .theme-portal .text-accent { color: #4f46e5 !important; }
    .theme-portal .text-muted-portal { color: #64748b !important; }
    .theme-portal .border-secondary { border-color: #e2e8f0 !important; }
    .theme-portal .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .theme-portal .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .theme-portal .custom-table {
        --bs-table-bg: transparent;
        --bs-table-color: #1e293b;
        margin-bottom: 0;
    }
    .theme-portal .btn-portal-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
        font-weight: 500;
    }
    .theme-portal .btn-portal-primary:hover {
        background-color: #4338ca;
        color: #fff;
    }
</style>

@endsection