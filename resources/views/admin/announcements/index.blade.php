
@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Pengumuman</h4>
        <p class="text-muted mb-0">Kelola informasi dan pengumuman sekolah.</p>
    </div>

    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Pengumuman
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <strong>Daftar Pengumuman</strong>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Judul</th>
                    <th>Isi Pengumuman</th>
                    <th>Target</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($announcements as $announcement)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td class="fw-semibold">
                            {{ $announcement->title }}
                        </td>

                        <td>
                            {{ \Illuminate\Support\Str::limit($announcement->content, 70) }}
                        </td>

                        <td>
                            @foreach($announcement->target_roles ?? [] as $role)
                                <span class="badge bg-secondary">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </td>

                        <td>
                            {{ $announcement->created_at?->format('d/m/Y') }}
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('announcements.show', $announcement) }}"
                                   class="btn btn-sm btn-outline-info"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('announcements.edit', $announcement) }}"
                                   class="btn btn-sm btn-outline-warning"
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('announcements.destroy', $announcement) }}"
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
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada pengumuman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
