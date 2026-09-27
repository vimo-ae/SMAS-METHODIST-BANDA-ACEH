@extends('layouts.app')
@section('title', 'Tugas')
@section('content')
<h4>Daftar Tugas</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Batas Waktu</th><th></th></tr></thead>
    <tbody>
    @forelse($assignments as $a)
        <tr>
            <td>{{ $a->title }}</td>
            <td>{{ $a->classSubjectTeacher->schoolClass->name ?? '-' }}</td>
            <td>{{ $a->classSubjectTeacher->subject->name ?? '-' }}</td>
            <td>{{ $a->due_date->format('d M Y H:i') }}</td>
            <td><a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-primary">Detail</a></td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada tugas.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $assignments->links() }}
@endsection
