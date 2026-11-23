@extends('layouts.admin')

@section('title', 'Detail Enrollment Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Rincian Enrollment Siswa</div>
            <h2 class="fw-bold mb-1 text-dark">{{ $student->user->name ?? 'Siswa #' . $student->nis }}</h2>
            <p class="text-muted-portal mb-0">
                NIS: <code>{{ $student->nis }}</code>
                @if($student->schoolClass)
                    · Kelas Induk: <strong>{{ $student->schoolClass->name }} ({{ $student->schoolClass->academic_year }})</strong>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-portal-primary btn-sm px-3 py-2 rounded-3"
                    data-bs-toggle="modal" data-bs-target="#addStudentCstModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
            </button>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
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

        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-journal-bookmark text-accent me-2"></i> Daftar Kelas Mata Pelajaran ({{ $student->classEnrollments->count() }})
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas dan Tahun Ajaran</th>
                            <th>Guru Pengampu</th>
                            <th class="text-center">Status</th>
                            <th>Tgl Masuk / Keluar</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->classEnrollments as $idx => $enr)
                            @php
                                $cst = $enr->classSubjectTeacher;
                                $badgeClass = match($enr->status) {
                                    'aktif'         => 'bg-success-subtle text-success',
                                    'naik_kelas', 'lulus' => 'bg-primary-subtle text-primary',
                                    'tinggal_kelas', 'pindah' => 'bg-warning-subtle text-warning-emphasis',
                                    'drop_out'      => 'bg-danger-subtle text-danger',
                                    default         => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-center text-muted-portal">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $cst->subject->name ?? '-' }}</div>
                                    <small class="text-muted-portal">Kode: <code>{{ $cst->subject_code ?? '-' }}</code> · CST #{{ $enr->cst_id }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $cst->schoolClass->name ?? '-' }}</div>
                                    <small class="text-muted-portal">{{ $enr->academic_year ?? ($cst->academic_year ?? '-') }}</small>
                                </td>
                                <td class="text-dark small">
                                    {{ $cst->teacher->user->name ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }} px-3 py-1 rounded-pill text-capitalize">
                                        {{ str_replace('_', ' ', $enr->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted-portal">
                                    <div>Masuk: {{ $enr->enrolled_at ? \Carbon\Carbon::parse($enr->enrolled_at)->format('d M Y') : '-' }}</div>
                                    @if($enr->left_at)
                                        <div>Keluar: {{ \Carbon\Carbon::parse($enr->left_at)->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.enrollments.edit', $enr->enrollment_id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Hapus: dikunci jika siswa sudah punya nilai/tugas/ujian di kelas mapel ini --}}
                                        @if($terkunci->has($enr->enrollment_id))
                                            <span class="d-inline-block"
                                                  title="Tidak bisa dihapus: sudah ada nilai/tugas/ujian. Ubah status lewat Edit.">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                        style="pointer-events:none;">
                                                    <i class="bi bi-lock-fill"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form action="{{ route('admin.enrollments.destroy', $enr->enrollment_id) }}" method="POST"
                                                  onsubmit="return confirm('Keluarkan siswa dari mata pelajaran ini?')">
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
                                    Siswa ini belum terdaftar pada mata pelajaran apa pun.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RINGKASAN NILAI SISWA (Database View: v_student_grade_summary) --}}
        <div class="portal-card mt-4 overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-award text-accent me-2"></i> Ringkasan Nilai Akademik Siswa
                </h5>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">
                    {{ count($gradeSummaries ?? []) }} Mapel Ternilai
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Penilaian / Ujian</th>
                            <th>Kelas</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Semester / T.A</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Predikat</th>
                            <th class="pe-4">Tanggal Dinilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gradeSummaries ?? [] as $gIdx => $grade)
                            @php
                                $predBadge = match($grade->predicate ?? null) {
                                    'A' => 'bg-success text-white',
                                    'B' => 'bg-primary text-white',
                                    'C' => 'bg-warning text-dark',
                                    'D' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white',
                                };
                                $typeBadge = match(strtolower($grade->grade_type ?? '')) {
                                    'tugas' => 'bg-info-subtle text-info-emphasis',
                                    'kuis'  => 'bg-warning-subtle text-warning-emphasis',
                                    'uts'   => 'bg-primary-subtle text-primary',
                                    'uas'   => 'bg-danger-subtle text-danger',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted-portal">{{ $gIdx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $grade->subject_name }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $grade->title ?? '-' }}</div>
                                </td>
                                <td>{{ $grade->class_name }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $typeBadge }} px-2 py-1 text-uppercase">
                                        {{ $grade->grade_type }}
                                    </span>
                                </td>
                                <td class="text-center text-muted-portal small">
                                    {{ ucfirst($grade->semester) }} / {{ $grade->academic_year }}
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $grade->score }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $predBadge }} px-2 py-1">
                                        {{ $grade->predicate ?? '-' }}
                                    </span>
                                </td>
                                <td class="small text-muted-portal pe-4">
                                    {{ $grade->graded_at ? \Carbon\Carbon::parse($grade->graded_at)->format('d M Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted-portal fst-italic">
                                    Belum ada rekap nilai untuk siswa ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KELAS MAPEL UNTUK SISWA INI --}}
<div class="modal fade" id="addStudentCstModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.enrollments.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="students_id" value="{{ $student->nis }}">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Kelas Mapel untuk {{ $student->user->name ?? $student->nis }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="cst_id" class="form-select" required>
                        <option value="">-- Pilih Kelas dan Mapel --</option>
                        @foreach($csts as $cst)
                            <option value="{{ $cst->cst_id }}">
                                {{ $cst->schoolClass->name ?? 'Kelas #' . $cst->class_id }} —
                                {{ $cst->subject->name ?? $cst->subject_code }} ({{ $cst->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['aktif', 'naik_kelas', 'tinggal_kelas', 'pindah', 'lulus', 'drop_out'] as $st)
                            <option value="{{ $st }}">{{ ucwords(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">Tanggal Masuk</label>
                    <input type="date" name="enrolled_at" value="{{ now()->toDateString() }}" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-portal-primary px-4">Simpan</button>
            </div>
        </form>
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