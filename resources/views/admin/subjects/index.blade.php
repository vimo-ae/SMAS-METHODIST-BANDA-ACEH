@extends('layouts.app')
@section('title', 'Data Mapel')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Mata Pelajaran</h4>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">+ Tambah Mapel</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Nama Mapel</th><th>Kode</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($subjects as $subject)
        <tr>
            <td>{{ $subject->name }}</td>
            <td>{{ $subject->code }}</td>
            <td>
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mapel ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center text-muted">Belum ada data mapel.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $subjects->links() }}
@endsection
