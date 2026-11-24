@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Pengumuman dan Informasi</h4>
        <p class="text-muted small mb-0">Kelola pengumuman untuk halaman publik (guest), guru, dan siswa.</p>
    </div>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Buat Pengumuman
    </a>
</div>

{{-- Form Pencarian --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.announcements.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           class="form-control bg-light border-start-0"
                           placeholder="Cari judul atau isi pengumuman...">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-3">Cari</button>
                @if(!empty($search))
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Daftar Kartu Pengumuman --}}
<div class="row g-4">
    @forelse($announcements as $announcement)
        @php
            $roles = $announcement->target_roles ?? [];
        @endphp

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
                <div class="card-body p-4 flex-grow-1">
                    {{-- Badge Target Role (Tidak Duplikat) --}}
                    <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                        @if(in_array('all', $roles, true) || empty($roles))
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                SEMUA
                            </span>
                        @else
                            @foreach(array_unique($roles) as $role)
                                @if($role === 'teacher')
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 fw-semibold">
                                        GURU
                                    </span>
                                @elseif($role === 'student')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-semibold">
                                        SISWA
                                    </span>
                                @endif
                            @endforeach
                        @endif

                        @if($announcement->is_public)
                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1 ms-auto" title="Tampil di halaman publik">
                                <i class="bi bi-globe2"></i> Publik
                            </span>
                        @endif
                    </div>

                    {{-- Tanggal Publikasi --}}
                    <div class="text-muted small mb-2">
                        <i class="bi bi-clock me-1"></i>
                        {{ ($announcement->published_at ?? $announcement->created_at)?->format('d M Y, H:i') }}
                    </div>

                    {{-- Judul dan Ringkasan --}}
                    <h5 class="fw-bold text-dark mb-2">{{ $announcement->title }}</h5>
                    <p class="text-muted small mb-3">
                        {{ $announcement->excerpt }}
                    </p>

                    @if($announcement->link)
                        <a href="{{ $announcement->link }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none">
                            <i class="bi bi-link-45deg"></i> Buka Tautan Lampiran
                        </a>
                    @endif
                </div>

                {{-- Footer Aksi --}}
                <div class="card-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted text-truncate" style="max-width: 140px;">
                        <i class="bi bi-person me-1"></i>{{ $announcement->creator->name ?? 'Admin' }}
                    </small>

                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.announcements.show', $announcement) }}"
                           class="btn btn-sm btn-outline-secondary rounded-3"
                           title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.announcements.edit', $announcement) }}"
                           class="btn btn-sm btn-outline-primary rounded-3"
                           title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.announcements.destroy', $announcement) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="bi bi-megaphone text-muted" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold mt-3 text-dark">Belum ada pengumuman</h5>
                <p class="text-muted mb-3">Klik tombol di bawah untuk membuat pengumuman pertama.</p>
                <div>
                    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary rounded-3">
                        <i class="bi bi-plus-lg me-1"></i> Buat Pengumuman
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($announcements->hasPages())
    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
@endif
@endsection