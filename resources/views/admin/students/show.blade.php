@extends('layouts.admin')

@section('title', 'Detail Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Data Siswa</div>
            <h2 class="fw-bold mb-1 text-dark">{{ $student->user->name ?? 'Siswa #' . $student->nis }}</h2>
            <p class="text-muted-portal mb-0">
                NIS: <code>{{ $student->nis }}</code>
                @if($student->nisn)
                    · NISN: <code>{{ $student->nisn }}</code>
                @endif
                @if($student->schoolClass)
                    · Kelas: <strong>{{ $student->schoolClass->name }} ({{ $student->schoolClass->academic_year }})</strong>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.students.edit', $student->nis) }}" class="btn btn-portal-primary btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Edit Data Siswa
            </a>
            <a href="{{ route('admin.enrollments.show', $student->nis) }}" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-journal-bookmark me-1"></i> Kelola Enrollment
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="p-4 pt-0">

        {{-- INFO BIODATA SISWA --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="portal-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-person-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted-portal small">Jenis Kelamin</div>
                            <div class="fw-bold text-dark fs-6">
                                {{ $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="portal-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-check-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted-portal small">Status Siswa</div>
                            <div class="fw-bold fs-6">
                                <span class="badge {{ strtolower($student->status ?? 'aktif') === 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-1 rounded-pill text-capitalize">
                                    {{ str_replace('_', ' ', $student->status ?? 'aktif') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="portal-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info-subtle text-info-emphasis p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-building fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted-portal small">Kelas Induk</div>
                            <div class="fw-bold text-dark fs-6">
                                {{ $student->schoolClass->name ?? 'Belum ditentukan' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RINCIAN PROFIL DETAIL --}}
        <div class="portal-card p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                <i class="bi bi-info-circle text-accent me-2"></i> Biodata Lengkap
            </h5>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <div class="text-muted-portal">Nama Lengkap:</div>
                    <div class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-portal">Email Akun:</div>
                    <div class="fw-semibold text-dark">{{ $student->user->email ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-portal">Tempat, Tanggal Lahir:</div>
                    <div class="fw-semibold text-dark">
                        {{ $student->pob ?? '-' }}, {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d F Y') : '-' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-portal">Nomor HP / WhatsApp:</div>
                    <div class="fw-semibold text-dark">{{ $student->user->phone ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-portal">Nama Orang Tua / Wali:</div>
                    <div class="fw-semibold text-dark">{{ $student->parent_name ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-portal">Kontak Orang Tua:</div>
                    <div class="fw-semibold text-dark">{{ $student->parent_phone ?? '-' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted-portal">Alamat Domisili:</div>
                    <div class="fw-semibold text-dark">
                        {{ $student->address ?? '-' }}
                        @if($student->district || $student->city || $student->province)
                            ({{ implode(', ', array_filter([$student->district, $student->city, $student->province])) }})
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- DAFTAR KELAS MATA PELAJARAN (Database View: v_class_roster dan Stored Function: fn_student_average) --}}
        <div class="portal-card mb-4 overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-mortarboard text-accent me-2"></i> Partisipasi Kelas Mata Pelajaran
                </h5>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">
                    {{ count($rosters ?? []) }} Rombel Mapel
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Guru Pengampu</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Rata-rata Nilai (fn_student_average)</th>
                            <th class="pe-4">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rosters ?? [] as $rIdx => $roster)
                            @php
                                $avg = $averages[$roster->cst_id] ?? null;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted-portal">{{ $rIdx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $roster->subject_name }}</div>
                                    <small class="text-muted-portal">CST #{{ $roster->cst_id }}</small>
                                </td>
                                <td>{{ $roster->class_name }}</td>
                                <td>{{ $roster->teacher_name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ strtolower($roster->status ?? 'aktif') === 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-1 rounded-pill text-capitalize">
                                        {{ $roster->status }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold {{ $avg !== null && $avg >= 75 ? 'text-success' : 'text-dark' }}">
                                    {{ $avg !== null ? number_format($avg, 2) : '-' }}
                                </td>
                                <td class="text-muted-portal small pe-4">
                                    {{ $roster->joined_at ? \Carbon\Carbon::parse($roster->joined_at)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted-portal fst-italic">
                                    Siswa belum terdaftar pada kelas mata pelajaran apa pun.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REKAP NILAI AKADEMIK (Database View: v_student_grade_summary) --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-award text-accent me-2"></i> Rekapitulasi Nilai Siswa
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
                                    Belum ada catatan nilai untuk siswa ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
