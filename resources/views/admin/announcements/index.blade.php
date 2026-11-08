
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Pengumuman</h3>
            <p class="text-muted mb-0">
                Kelola informasi dan pengumuman untuk Guru dan Siswa.
            </p>
        </div>

        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Tambah Pengumuman
        </a>
    </div>

    {{-- Notifikasi Berhasil --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Pengumuman --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="5%">No.</th>
                            <th>Judul Pengumuman</th>
                            <th>Penerima</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal</th>
                            <th class="text-center" width="18%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($announcements as $announcement)

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- Judul --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $announcement->title }}
                                    </div>

                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 70) }}
                                    </small>
                                </td>

                                {{-- Penerima --}}
                                <td>
                                    @forelse ($announcement->target_roles ?? [] as $role)
                                        <span class="badge bg-info text-dark me-1">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>

                                {{-- Pembuat --}}
                                <td>
                                    {{ $announcement->creator?->name ?? 'Admin' }}
                                </td>

                                {{-- Tanggal --}}
                                <td>
                                    {{ $announcement->created_at?->format('d/m/Y') ?? '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- Detail --}}
                                        <a href="{{ route('announcements.show', ['announcement' => $announcement->announcement_id]) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('announcements.edit', ['announcement' => $announcement->announcement_id]) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- Hapus --}}
                                        <form action="{{ route('announcements.destroy', ['announcement' => $announcement->announcement_id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-megaphone fs-1 text-muted"></i>

                                    <p class="text-muted mt-2 mb-0">
                                        Belum ada pengumuman.
                                    </p>

                                    <a href="{{ route('announcements.create') }}"
                                       class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Buat Pengumuman
                                    </a>
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
