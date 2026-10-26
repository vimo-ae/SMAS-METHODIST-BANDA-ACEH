@extends('layouts.app')
@section('title', 'Materi')
@section('content')
<h4>Materi Pembelajaran</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Tanggal</th></tr></thead>
    <tbody>
    @forelse($materials as $m)
        <tr>
            <td>{{ $m->title }}</td>
            <td>{{ $m->classSubjectTeacher->schoolClass->name ?? '-' }}</td>
            <td>{{ $m->classSubjectTeacher->subject->name ?? '-' }}</td>
            <td>{{ $m->uploaded_at?->format('d M Y') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted">Belum ada materi.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $materials->links() }}
@endsection
