@extends('layouts.admin')
@section('title', 'Daftar Pengumuman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Kelola Pengumuman</h4>
        <p class="text-muted small mb-0">Publikasikan informasi dan pengumuman penting untuk warga sekolah.</p>
    </div>
    <div>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary shadow-sm rounded-3 fw-medium px-3">
            <i class="bi bi-plus-lg me-1"></i> Buat Pengumuman Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0 bg-success text-white" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.announcements.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 fs-6" placeholder="Cari judul atau isi pengumuman..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-medium btn-lg">Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($announcements as $item)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            {{-- Menggunakan array_unique agar tidak terjadi cetak badge berulang (bug duplikasi) --}}
                            @foreach(array_unique($item->target_roles ?? []) as $role)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-uppercase small fw-bold me-1">
                                    {{ $role == 'teacher' ? 'Guru' : ($role == 'student' ? 'Siswa' : 'Semua') }}
                                </span>
                            @endforeach
                        </div>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $item->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">
                        <a href="{{ route('admin.announcements.show', $item->announcement_id) }}" class="text-dark text-decoration-none">
                            {{ $item->title }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-3">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-4 px-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Oleh: <span class="fw-semibold text-dark">{{ $item->creator->name ?? 'Admin' }}</span></small>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.announcements.show', $item->announcement_id) }}" class="btn btn-sm btn-light border text-primary shadow-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.announcements.edit', $item->announcement_id) }}" class="btn btn-sm btn-light border text-warning shadow-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        <form action="{{ route('admin.announcements.destroy', $item->announcement_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger shadow-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body">
                    <i class="bi bi-megaphone display-4 d-block mb-3 opacity-50 text-muted"></i>
                    <h6 class="fw-semibold text-dark">Belum ada pengumuman</h6>
                    <p class="small text-muted mb-0">Silakan buat pengumuman baru melalui tombol di atas.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($announcements->hasPages())
    <div class="mt-4">{{ $announcements->links() }}</div>
@endif
@endsection