@extends('layouts.app')
@section('title', 'Forum Diskusi')
@section('content')
<h4>Forum Diskusi</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Dibuat oleh</th><th></th></tr></thead>
    <tbody>
    @forelse($threads as $t)
        <tr>
            <td>{{ $t->title }}</td>
            <td>{{ $t->classSubjectTeacher->schoolClass->name ?? '-' }}</td>
            <td>{{ $t->classSubjectTeacher->subject->name ?? '-' }}</td>
            <td>{{ $t->creator->name }}</td>
            <td><a href="{{ route('forum.show', $t) }}" class="btn btn-sm btn-primary">Buka</a></td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada diskusi.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $threads->links() }}
@endsection
