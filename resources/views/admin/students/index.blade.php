@extends('layouts.admin')

@section('title', 'Data Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase text-accent fw-bold small mb-1" style="letter-spacing: .08em;">Data Sekolah</div>
            <h2 class="fw-bold mb-1 text-dark">Data Peserta Didik (Siswa)</h2>
            <p class="text-muted-portal mb-0">
                Manajemen data induk siswa, penempatan rombongan belajar, kontak orang tua, dan status akademik.
            </p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="btn btn-portal-primary px-4 py-2 rounded-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
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

        {{-- Pesan penolakan hapus dari StudentController@destroy dikirim lewat with('error') --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- FORM PENCARIAN dan FILTER --}}
        <form method="GET" action="{{ route('admin.students.index') }}" class="portal-card p-3 mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari NIS, NISN, nama siswa, atau orang tua...">
                    </div>
                </div>
                <div class="col-12 col-md-5 col-lg-3">
                    <select name="class_id" class="form-select">
                        <option value="">-- Semua Kelas --</option>
                        <option value="unassigned" {{ request('class_id') === 'unassigned' ? 'selected' : '' }}>
                            Belum Masuk Kelas
                        </option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->class_id }}" {{ (string) request('class_id') === (string) $cls->class_id ? 'selected' : '' }}>
                                {{ $cls->name }} ({{ $cls->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        @foreach (['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'drop_out' => 'Drop Out'] as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-portal-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL DATA SISWA --}}
        <div class="portal-card">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-mortarboard text-accent me-2"></i> Daftar Siswa ({{ $students->total() }} Siswa)
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>NIS / NISN</th>
                            <th>Nama Siswa dan Gender</th>
                            <th>Kelas</th>
                            <th>Orang Tua / Wali</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                            @php
                                $status      = strtolower(trim((string) ($student->status ?? 'aktif')));
                                $isAktif     = $status === 'aktif';
                                $studentName = $student->user->name ?? $student->nis;

                                $badgeClass = match ($status) {
                                    'aktif'    => 'bg-success-subtle text-success',
                                    'lulus'    => 'bg-primary-subtle text-primary',
                                    'pindah'   => 'bg-warning-subtle text-warning-emphasis',
                                    'drop_out' => 'bg-danger-subtle text-danger',
                                    default    => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted-portal">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="fw-semibold"><code>{{ $student->nis }}</code></div>
                                    <small class="text-muted-portal">NISN: {{ $student->nisn ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $student->user->name ?? '-' }}</div>
                                    <small class="text-muted-portal">
                                        {{ $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-') }}
                                        @if ($student->dob)
                                            · {{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if ($student->schoolClass)
                                        <span class="badge bg-info-subtle text-info-emphasis px-2 py-1">
                                            {{ $student->schoolClass->name }}
                                        </span>
                                        <small class="d-block text-muted-portal">{{ $student->schoolClass->academic_year }}</small>
                                    @else
                                        <span class="text-warning-emphasis small fst-italic">Belum diatur</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium">{{ $student->parent_name ?? '-' }}</div>
                                    @if (! empty($student->parent_phone))
                                        <small class="text-muted-portal"><i class="bi bi-telephone me-1"></i>{{ $student->parent_phone }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }} px-3 py-1 rounded-pill text-capitalize">
                                        {{ str_replace('_', ' ', $status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.students.show', $student->nis) }}"
                                           class="btn btn-sm btn-outline-info" title="Detail Siswa">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.students.edit', $student->nis) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($isAktif)
                                            {{-- Siswa aktif tidak boleh dihapus: ubah status lewat Edit dulu --}}
                                            <span class="d-inline-block" tabindex="0"
                                                  title="Siswa aktif tidak bisa dihapus. Ubah status lewat Edit terlebih dahulu.">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                        style="pointer-events: none;" aria-label="Hapus (tidak tersedia untuk siswa aktif)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form action="{{ route('admin.students.destroy', $student->nis) }}" method="POST"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Yakin ingin menghapus data siswa ' . $studentName . '?') }})">
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
                                    Belum ada data siswa yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
                <div class="p-3 border-top">
                    {{ $students->withQueryString()->links() }}
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