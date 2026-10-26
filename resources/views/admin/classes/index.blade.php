@extends('layouts.app')
@section('title', 'Data Kelas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Kelas</h4>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm">+ Tambah Kelas</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Nama Kelas</th><th>Jenjang</th><th>Wali Kelas</th><th>Tahun Ajaran</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($classes as $class)
        <tr>
            <td>{{ $class->name }}</td>
            <td>{{ $class->grade_level }}</td>
            <td>{{ $class->homeroomTeacher?->user?->name ?? '-' }}</td>
            <td>{{ $class->academic_year }}</td>
            <td>
                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada data kelas.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $classes->links() }}
@endsection
