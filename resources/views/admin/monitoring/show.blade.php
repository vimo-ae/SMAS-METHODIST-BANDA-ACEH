@extends('layouts.admin')

@section('title', 'Detail Monitoring Ujian - Portal Akademik')

@section('content')

<div class="theme-portal rounded-4 shadow-sm">
    {{-- HEADER INFORMASI UJIAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="text-uppercase tracking-wide text-accent fw-bold small mb-1">Monitoring Ujian {{ $session->class_name }}</div>
            <h2 class="fw-bold mb-1 text-dark">{{ $session->exam_title }}</h2>
            <p class="text-muted-portal mb-0">
                Mata Pelajaran: <strong>{{ $session->subject_name }}</strong>
                @if(config('exam.token_enabled'))
                    · Token Ujian:
                    @if($session->exam_token)
                        <code id="admin-exam-token-value" class="text-primary fw-bold fs-5">{{ $session->exam_token }}</code>
                        <small class="text-muted-portal ms-2" data-admin-token-expiry>
                            Berlaku sampai {{ $session->exam_token_expires_at?->format('H:i:s') }}
                        </small>
                    @else
                        <span class="text-muted-portal">Belum tersedia / ujian belum aktif</span>
                    @endif
                @else
                    · <span class="text-muted-portal">Token dinonaktifkan sementara</span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.monitoring.class.exams', $session->class_id ?? 1) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Ujian
        </a>
    </div>

    <div class="p-4 pt-0">
        
        {{-- TABEL 1: DAFTAR PESERTA SISWA (MENAMPILKAN NIS dan NILAI) --}}
        <div class="portal-card overflow-hidden mb-5">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-accent me-2"></i> Daftar Peserta Siswa ({{ count($participants) }} Siswa)
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th style="width: 140px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" style="width: 140px;">Status Sesi</th>
                            <th class="text-center" style="width: 120px;">Mulai</th>
                            <th class="text-center" style="width: 120px;">Selesai</th>
                            <th class="text-center" style="width: 140px;">Pelanggaran</th>
                            <th class="text-end pe-4" style="width: 120px;">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $idx => $p)
                            @php
                                $statusBadge = match($p->session_status ?? 'belum_mulai') {
                                    'selesai'   => 'bg-success-subtle text-success',
                                    'mengerjakan', 'aktif' => 'bg-primary-subtle text-primary',
                                    default     => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-center text-muted-portal">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $p->nis }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->student_name }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusBadge }} text-capitalize px-3 py-1 rounded-pill">
                                        {{ str_replace('_', ' ', $p->session_status ?? 'belum_mulai') }}
                                    </span>
                                </td>
                                <td class="text-center small text-muted-portal font-monospace">
                                    {{ $p->start_time }}
                                </td>
                                <td class="text-center small text-muted-portal font-monospace">
                                    {{ $p->finish_time }}
                                </td>
                                <td class="text-center">
                                    @if(($p->violation_count ?? 0) > 0)
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold">
                                            {{ $p->violation_count }}x (Terdeteksi)
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Aman (0)</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    {{ $p->score !== null ? number_format($p->score, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted-portal">
                                    Belum ada peserta siswa yang masuk ke sesi ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 2: CATATAN PELANGGARAN SISWA (DI BAGIAN BAWAH) --}}
        <div class="portal-card overflow-hidden">
            <div class="card-header-portal d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i> Catatan Pelanggaran Siswa (Pindah Tab / Menyontek)
                </h5>
                <span class="badge bg-danger-subtle text-danger px-3 py-1">{{ count($violations) }} Catatan</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead class="text-muted-portal small border-bottom border-secondary bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th style="width: 140px;">NIS</th>
                            <th>Nama Siswa dan Keterangan</th>
                            <th style="width: 180px;">Jenis Pelanggaran</th>
                            <th class="text-end pe-4" style="width: 200px;">Waktu Kejadian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violations as $vIdx => $v)
                            <tr>
                                <td class="ps-4 text-center text-muted-portal">{{ $vIdx + 1 }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $v->nis }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $v->student_name ?? 'Siswa' }}</div>
                                    <small class="text-muted-portal">
                                        {{ $v->details ?? 'Terdeteksi meninggalkan halaman ujian (pindah tab browser).' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1">
                                        {{ $v->violation_type ?? $v->type ?? 'Pindah Tab / Keluar Fokus' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 small text-muted-portal font-monospace">
                                    {{ $v->last_occurred_at ?? $v->created_at ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted-portal">
                                    <i class="bi bi-shield-check text-success fs-4 d-block mb-1"></i>
                                    <span class="fw-semibold text-success">Bersih! Tidak ada catatan pelanggaran (menyontek / pindah tab) pada ujian ini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@if($session->exam_token && $session->exam_token_expires_at)
<script>
    (() => {
        let expiresAt = new Date(@json($session->exam_token_expires_at->toIso8601String())).getTime();
        let refreshed = false;

        const refreshToken = async () => {
            if (refreshed) return;
            refreshed = true;

            try {
                const response = await fetch(@json(route('admin.monitoring.token', $session->exam_id)), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();
                if (data.token) {
                    document.getElementById('admin-exam-token-value').textContent = data.token;
                    expiresAt = new Date(data.expires_at).getTime();
                    document.querySelector('[data-admin-token-expiry]')?.replaceChildren(
                        document.createTextNode('Berlaku sampai ' + data.expires_at_display)
                    );
                    refreshed = false;
                }
            } catch (error) {
                refreshed = false;
            }
        };

        const tick = () => {
            const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
            if (remaining === 0 && !refreshed) {
                refreshToken();
            }
        };

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endif

<style>
    .theme-portal { background: #ffffff; color: #1e293b; min-height: 80vh; border: 1px solid #e2e8f0; }
    .text-accent { color: #4f46e5 !important; }
    .text-muted-portal { color: #64748b !important; }
    .border-secondary { border-color: #e2e8f0 !important; }
    .portal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .card-header-portal { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .custom-table { --bs-table-bg: transparent; --bs-table-color: #1e293b; }
</style>

@endsection