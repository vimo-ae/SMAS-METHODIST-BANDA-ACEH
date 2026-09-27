@extends('layouts.app')
@section('title', 'Data Orang Tua')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Orang Tua / Wali</h4>
    <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary btn-sm">+ Tambah Orang Tua</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Nama</th><th>Email</th><th>Hubungan</th><th>Anak</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($guardians as $guardian)
        <tr>
            <td>{{ $guardian->user->name }}</td>
            <td>{{ $guardian->user->email }}</td>
            <td>{{ $guardian->relationship }}</td>
            <td>{{ $guardian->students->pluck('user.name')->join(', ') }}</td>
            <td>
                <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.guardians.destroy', $guardian) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada data orang tua.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $guardians->links() }}
@endsection
