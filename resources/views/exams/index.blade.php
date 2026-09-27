@extends('layouts.app')
@section('title', 'Ujian')
@section('content')
<h4>Daftar Ujian / Kuis</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Judul</th><th>Jenis</th><th>Kelas</th><th>Mapel</th><th>Waktu</th><th></th></tr></thead>
    <tbody>
    @forelse($exams as $e)
        <tr>
            <td>{{ $e->title }}</td>
            <td>{{ strtoupper($e->type) }}</td>
            <td>{{ $e->classSubjectTeacher->schoolClass->name ?? '-' }}</td>
            <td>{{ $e->classSubjectTeacher->subject->name ?? '-' }}</td>
            <td>{{ $e->start_time->format('d M Y H:i') }}</td>
            <td><a href="{{ route('exams.show', $e) }}" class="btn btn-sm btn-primary">Detail</a></td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted">Belum ada ujian.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $exams->links() }}
@endsection
