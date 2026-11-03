
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('announcements.index') }}"
           class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Pengumuman
        </a>

        <h3 class="fw-bold mt-3 mb-1">Detail Pengumuman</h3>
        <p class="text-muted">
            Informasi lengkap mengenai pengumuman.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">

                <div>
                    <h4 class="fw-bold mb-3">
                        {{ $announcement->title }}
                    </h4>

                    <div>
                        @forelse ($announcement->target_roles ?? [] as $role)
                            <span class="badge bg-info text-dark me-1">
                                {{ ucfirst($role) }}
                            </span>
                        @empty
                            <span class="text-muted">Tidak ada penerima</span>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('announcements.edit', ['announcement' => $announcement->getKey()]) }}"
                       class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Edit
                    </a>

                    <form action="{{ route('announcements.destroy', ['announcement' => $announcement->getKey()]) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>
                    </form>
                </div>

            </div>

            <hr>

            <div class="mb-4">
                <h6 class="fw-bold mb-3">Isi Pengumuman</h6>

                <div class="text-secondary" style="white-space: pre-wrap; line-height: 1.8;">{{ $announcement->content }}</div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Dibuat Oleh</small>
                    <div class="fw-semibold">
                        {{ $announcement->creator?->name ?? 'Admin' }}
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <small class="text-muted">Tanggal Dibuat</small>
                    <div class="fw-semibold">
                        {{ $announcement->created_at?->format('d F Y, H:i') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <small class="text-muted">Terakhir Diperbarui</small>
                    <div class="fw-semibold">
                        {{ $announcement->updated_at?->format('d F Y, H:i') ?? '-' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
