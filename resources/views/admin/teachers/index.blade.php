@extends('layouts.app')
@section('title', 'Data Guru')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Guru</h4>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">+ Tambah Guru</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Nama</th><th>Email</th><th>NIP</th><th>Spesialisasi</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($teachers as $teacher)
        <tr>
            <td>{{ $teacher->user->name }}</td>
            <td>{{ $teacher->user->email }}</td>
            <td>{{ $teacher->nip }}</td>
            <td>{{ $teacher->specialization }}</td>
            <td>
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus guru ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada data guru.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $teachers->links() }}
@endsection
