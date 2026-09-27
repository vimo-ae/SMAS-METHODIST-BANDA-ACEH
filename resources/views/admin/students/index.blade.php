@extends('layouts.app')
@section('title', 'Data Siswa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Siswa</h4>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">+ Tambah Siswa</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Nama</th><th>NIS</th><th>Kelas</th><th>JK</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($students as $student)
        <tr>
            <td>{{ $student->user->name }}</td>
            <td>{{ $student->nis }}</td>
            <td>{{ $student->schoolClass->name ?? '-' }}</td>
            <td>{{ $student->gender }}</td>
            <td>
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus siswa ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada data siswa.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $students->links() }}
@endsection
