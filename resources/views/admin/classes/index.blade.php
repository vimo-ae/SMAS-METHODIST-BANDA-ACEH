@extends('layouts.admin')

@section('title', 'Data Kelas dan Daftar Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Manajemen Akademik</div>
            <h2 class="fw-bold mb-1 text-dark">Data Kelas, Wali Kelas dan Siswa</h2>
            <p class="text-muted-portal mb-0">
                Daftar seluruh kelas beserta wali kelas pengampu dan jumlah siswa terdaftar.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-portal-primary btn-sm px-3 py-2 rounded-3"
                    data-bs-toggle="modal" data-bs-target="#promoteClassModal">
                <i class="bi bi-arrow-up-circle me-1"></i> Naik Kelas Massal
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
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

        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM PENCARIAN dan FILTER --}}
        <form method="GET" action="{{ route('admin.classes.index') }}" class="portal-card p-3 mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari nama kelas, tahun ajaran, NIP, atau nama wali kelas...">
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-3">
                    <select name="academic_year" class="form-select">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        @foreach($academicYears ?? [] as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-portal-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL DAFTAR KELAS --}}
        <div class="portal-card">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-building text-accent me-2"></i> Daftar Rombongan Belajar ({{ $classes->total() }} Kelas)
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Wali Kelas (Homeroom Teacher)</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-end pe-4">Rincian Siswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $index => $class)
                            @php
                                $classStudents = $studentsByClass[(string) $class->class_id]
                                    ?? $studentsByClass[$class->class_id]
                                    ?? collect();
                                $teacherName = $class->homeroom_teacher_name
                                    ?? $class->homeroomTeacher?->user?->name
                                    ?? null;
                                $teacherNip = $class->homeroom_teacher_nip
                                    ?? $class->homeroom_teacher_id
                                    ?? null;
                                $studentTotal = $class->total_students
                                    ?? $class->students_count
                                    ?? $classStudents->count();
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted-portal">
                                    {{ $classes->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $class->class_name ?? $class->name }}</div>
                                </td>
                                <td class="text-muted-portal">
                                    {{ $class->academic_year ?? '-' }}
                                </td>
                                <td>
                                    @if(!empty($teacherName))
                                        <div class="fw-semibold text-dark">
                                            <i class="bi bi-person-badge text-success me-1"></i> {{ $teacherName }}
                                        </div>
                                        @if(!empty($teacherNip))
                                            <small class="text-muted-portal">NIP: {{ $teacherNip }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted-portal fst-italic small">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                        <i class="bi bi-people-fill me-1"></i> {{ $studentTotal }} Siswa
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#siswa-kelas-{{ $class->class_id }}"
                                            aria-expanded="false"
                                            aria-controls="siswa-kelas-{{ $class->class_id }}">
                                        <i class="bi bi-eye me-1"></i> Lihat Siswa
                                    </button>
                                </td>
                            </tr>

                            {{-- BARIS COLLAPSE DAFTAR SISWA DI KELAS INI --}}
                            <tr class="bg-light">
                                <td colspan="6" class="p-0 border-0">
                                    <div class="collapse" id="siswa-kelas-{{ $class->class_id }}">
                                        <div class="p-3 ps-4 pe-4">
                                            <div class="bg-white rounded-3 border p-3">
                                                <div class="fw-bold text-dark small mb-2">
                                                    <i class="bi bi-people text-accent me-1"></i>
                                                    Daftar Siswa Kelas {{ $class->class_name ?? $class->name }} ({{ $classStudents->count() }} orang)
                                                </div>
                                                @if($classStudents->isEmpty())
                                                    <p class="text-muted-portal small mb-0 fst-italic">Belum ada siswa di kelas ini.</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-borderless mb-0 small">
                                                            <thead class="border-bottom text-muted-portal">
                                                                <tr>
                                                                    <th style="width: 50px;">#</th>
                                                                    <th>NIS / NISN</th>
                                                                    <th>Nama Siswa</th>
                                                                    <th>Jenis Kelamin</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($classStudents as $sIdx => $student)
                                                                    <tr>
                                                                        <td class="text-muted-portal">{{ $sIdx + 1 }}</td>
                                                                        <td><code>{{ $student->nis ?? $student->nisn ?? '-' }}</code></td>
                                                                        <td class="fw-medium text-dark">
                                                                            {{ $student->student_name ?? 'Siswa #' . $student->nis }}
                                                                        </td>
                                                                        <td class="text-muted-portal">
                                                                            {{ $student->gender ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge {{ strtolower($student->status ?? 'aktif') === 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                                                {{ ucfirst($student->status ?? 'aktif') }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted-portal">
                                    Tidak ada data kelas yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($classes->hasPages())
                <div class="p-3 border-top">
                    {{ $classes->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- MODAL NAIK KELAS MASSAL (Stored Procedure sp_promote_class) --}}
<div class="modal fade" id="promoteClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.classes.promote') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-up-circle text-accent me-2"></i> Naik Kelas Massal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="small text-muted-portal mb-3">
                    Fitur ini memindahkan semua siswa berstatus <strong>aktif</strong> dari kelas asal ke kelas tujuan sekaligus secara aman menggunakan <code>sp_promote_class</code>.
                </p>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas Asal <span class="text-danger">*</span></label>
                    <select name="from_class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas Asal --</option>
                        @foreach($allClasses ?? [] as $c)
                            <option value="{{ $c->class_id }}">
                                {{ $c->name }} ({{ $c->academic_year ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas Tujuan <span class="text-danger">*</span></label>
                    <select name="to_class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach($allClasses ?? [] as $c)
                            <option value="{{ $c->class_id }}">
                                {{ $c->name }} ({{ $c->academic_year ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-portal-primary px-4"
                        onclick="return confirm('Apakah Anda yakin ingin memindahkan seluruh siswa aktif dari kelas asal ke kelas tujuan?')">
                    <i class="bi bi-arrow-up-circle me-1"></i> Proses Kenaikan Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .text-accent { color: #4f46e5 !important; }
    .text-muted-portal { color: #64748b !important; }
    .border-secondary { border-color: #e2e8f0 !important; }
    .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .custom-table {
        --bs-table-bg: transparent;
        --bs-table-color: #1e293b;
        margin-bottom: 0;
    }
    .btn-portal-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 500;
    }
    .btn-portal-primary:hover {
        background-color: #4338ca;
        color: white;
    }
</style>

@endsection