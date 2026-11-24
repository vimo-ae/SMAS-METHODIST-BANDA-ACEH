@extends('layouts.admin')

@section('title', 'Enrollment Siswa - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Akademik</div>
            <h2 class="fw-bold mb-1 text-dark">Enrollment Siswa ke Kelas Mata Pelajaran</h2>
            <p class="text-muted-portal mb-0">
                Daftar siswa yang telah terdaftar pada rombongan belajar mata pelajaran.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary px-3 py-2 rounded-3"
                    data-bs-toggle="modal" data-bs-target="#bulkEnrollModal">
                <i class="bi bi-people-fill me-1"></i> Masukkan Satu Kelas
            </button>
            <a href="{{ route('admin.enrollments.create') }}" class="btn btn-portal-primary px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg me-1"></i> Tambah Enrollment
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM PENCARIAN FULL-WIDTH RAPI --}}
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="portal-card p-3 mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari NIS, NISN, atau nama siswa...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-portal-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL ENROLLMENT SISWA --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clipboard-check text-accent me-2"></i> Daftar Siswa Terdaftar ({{ $students->total() }} Siswa)
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th style="width: 120px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 190px;">Kelas Induk</th>
                            <th class="text-center" style="width: 170px;">Total Kelas Mapel</th>
                            <th class="text-end pe-4 text-nowrap" style="width: 185px;">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr>
                                <td class="ps-4 text-center text-muted-portal">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $student->nis }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $student->user->name ?? '-' }}</div>
                                    @if(!empty($student->nisn))
                                        <small class="text-muted-portal">NISN: {{ $student->nisn }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($student->schoolClass)
                                        <span class="badge bg-info-subtle text-info-emphasis px-2 py-1 text-nowrap">
                                            {{ $student->schoolClass->name }} ({{ $student->schoolClass->academic_year }})
                                        </span>
                                    @else
                                        <span class="text-muted-portal small fst-italic">Belum ada kelas induk</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill text-nowrap">
                                        {{ $student->classEnrollments->count() }} Mata Pelajaran
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <a href="{{ route('admin.enrollments.show', $student->nis) }}"
                                       class="btn btn-sm btn-outline-primary px-3 text-nowrap">
                                        <i class="bi bi-list-ul me-1"></i> Kelola Mapel
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted-portal">
                                    Belum ada siswa yang terdaftar pada kelas mata pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL MASUKKAN SATU KELAS (BULK ENROLLMENT) --}}
<div class="modal fade" id="bulkEnrollModal" tabindex="-1" aria-labelledby="bulkEnrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ Route::has('admin.enrollments.bulk') ? route('admin.enrollments.bulk') : (Route::has('admin.enrollments.storeBulk') ? route('admin.enrollments.storeBulk') : url('/admin/enrollments/bulk')) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="bulkEnrollModalLabel">
                    <i class="bi bi-people-fill text-accent me-2"></i>Masukkan Siswa Satu Kelas ke Kelas Mapel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Kelas Mata Pelajaran (CST) <span class="text-danger">*</span></label>
                    <select name="cst_id" id="bulkCstSelect" class="form-select" required>
                        <option value="">-- Pilih Kelas dan Mata Pelajaran --</option>
                        @foreach($csts as $cst)
                            <option value="{{ $cst->cst_id }}" data-class-id="{{ $cst->class_id }}">
                                {{ $cst->schoolClass->name ?? 'Kelas #' . $cst->class_id }} —
                                {{ $cst->subject->name ?? $cst->subject_code }}
                                ({{ $cst->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-semibold mb-0">Daftar Siswa di Kelas Induk</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleSelectAllBtn">
                        Pilih Semua
                    </button>
                </div>

                <div id="bulkStudentsContainer" class="border rounded-3 p-3 bg-light" style="min-height: 140px; max-height: 320px; overflow-y: auto;">
                    <p class="text-muted small mb-0 fst-italic">Silakan pilih kelas mata pelajaran di atas terlebih dahulu.</p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-portal-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Daftarkan Siswa Terpilih
                </button>
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

@push('scripts')
<script>
    const studentsByClass = @json($studentsByClass);
    const enrolledByCst = @json($enrolledByCst);

    const cstSelect = document.getElementById('bulkCstSelect');
    const container = document.getElementById('bulkStudentsContainer');
    const toggleAllBtn = document.getElementById('toggleSelectAllBtn');

    cstSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const cstId = this.value;
        const classId = selectedOption ? selectedOption.getAttribute('data-class-id') : null;

        if (!cstId || !classId) {
            container.innerHTML = '<p class="text-muted small mb-0 fst-italic">Silakan pilih kelas mata pelajaran di atas terlebih dahulu.</p>';
            return;
        }

        const classStudents = studentsByClass[classId] || [];
        const enrolledSet = new Set((enrolledByCst[cstId] || []).map(String));

        if (classStudents.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0 fst-italic">Tidak ada siswa yang tergabung di kelas induk ini.</p>';
            return;
        }

        let html = '<div class="list-group list-group-flush bg-white rounded-2 border">';
        classStudents.forEach(st => {
            const isEnrolled = enrolledSet.has(String(st.nis));
            const name = st.user ? st.user.name : ('Siswa #' + st.nis);
            html += `
                <label class="list-group-item d-flex justify-content-between align-items-center ${isEnrolled ? 'bg-light text-muted' : ''}">
                    <div class="form-check mb-0">
                        <input class="form-check-input bulk-student-cb" type="checkbox" name="students_id[]" value="${st.nis}"
                               ${isEnrolled ? 'disabled' : 'checked'}>
                        <span class="form-check-label ms-1">
                            <strong>${name}</strong> <code class="ms-1">(${st.nis})</code>
                        </span>
                    </div>
                    ${isEnrolled ? '<span class="badge bg-success-subtle text-success">Sudah Terdaftar</span>' : '<span class="badge bg-primary-subtle text-primary">Siap Didaftarkan</span>'}
                </label>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    });

    toggleAllBtn.addEventListener('click', function () {
        const checkboxes = container.querySelectorAll('.bulk-student-cb:not(:disabled)');
        if (!checkboxes.length) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    });
</script>
@endpush

@endsection