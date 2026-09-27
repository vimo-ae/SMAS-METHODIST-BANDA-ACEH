@extends('layouts.app')
@section('title', 'Nilai')
@section('content')
<h4>Data Nilai</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Siswa</th><th>Mapel</th><th>Jenis</th><th>Semester</th><th>Tahun Ajaran</th><th>Nilai</th></tr></thead>
    <tbody>
    @forelse($grades as $g)
        <tr>
            <td>{{ $g->student->user->name }}</td>
            <td>{{ $g->subject->name }}</td>
            <td>{{ strtoupper($g->grade_type) }}</td>
            <td>{{ ucfirst($g->semester) }}</td>
            <td>{{ $g->academic_year }}</td>
            <td><strong>{{ $g->score }}</strong></td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted">Belum ada data nilai.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $grades->links() }}
@endsection
