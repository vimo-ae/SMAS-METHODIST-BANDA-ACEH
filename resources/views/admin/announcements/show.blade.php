@extends('layouts.admin')
@section('title', 'Detail Pengumuman')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Rincian Pengumuman</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    @foreach($announcement->target_roles ?? [] as $role)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 text-uppercase fw-bold me-1">
                            {{ $role == 'teacher' ? 'Khusus Guru' : 'Khusus Siswa' }}
                        </span>
                    @endforeach
                </div>
                <span class="text-muted small"><i class="bi bi-calendar-event me-1"></i> {{ $announcement->created_at->format('d M Y, H:i') }}</span>
            </div>
            <h3 class="fw-bold text-dark mb-3">{{ $announcement->title }}</h3>
            <p class="text-muted small mb-4 pb-3 border-bottom">Dipublikasikan oleh: <span class="fw-semibold text-dark">{{ $announcement->creator->name ?? 'Admin' }}</span></p>
            
            <div class="text-dark lh-lg" style="white-space: pre-line;">
                {{ $announcement->content }}
            </div>

            <div class="mt-5 pt-3 border-top d-flex justify-content-end">
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection