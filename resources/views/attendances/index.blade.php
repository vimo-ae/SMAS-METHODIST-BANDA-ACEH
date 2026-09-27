@extends('layouts.app')
@section('title', 'Absensi')
@section('content')
<h4>Data Absensi</h4>
<table class="table table-bordered bg-white">
    <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($attendances as $a)
        <tr>
            <td>{{ $a->date->format('d M Y') }}</td>
            <td>{{ $a->student->user->name }}</td>
            <td>{{ $a->classSubjectTeacher->schoolClass->name ?? '-' }}</td>
            <td>{{ $a->classSubjectTeacher->subject->name ?? '-' }}</td>
            <td>
                <span class="badge bg-{{ $a->status == 'hadir' ? 'success' : ($a->status == 'alpha' ? 'danger' : 'warning') }}">
                    {{ ucfirst($a->status) }}
                </span>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada data absensi.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $attendances->links() }}
@endsection


@if(auth()->user()->role === 'guru')
<div class="card mt-4">
    <div class="card-header">Input Absensi Siswa</div>
    <div class="card-body">
        <form method="POST" action="{{ route('attendances.store') }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Kelas & Mata Pelajaran</label><select id="cst-select" name="cst_id" class="form-select" required><option value="">-- Pilih --</option>@foreach($csts as $cst)<option value="{{ $cst->id }}">{{ $cst->schoolClass->name }} — {{ $cst->subject->name }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            </div>
            <div id="student-list"></div>
            <button class="btn btn-primary mt-3" type="submit">Simpan Absensi</button>
        </form>
    </div>
</div>
<script>
const cstStudents = @json($csts->mapWithKeys(fn($cst)=>[$cst->id=>$cst->schoolClass->students->map(fn($s)=>['nis'=>$s->nis,'name'=>$s->user?->name ?? $s->nis])])->toArray());
const select=document.getElementById('cst-select'), list=document.getElementById('student-list');
select.addEventListener('change',()=>{ const rows=cstStudents[select.value]||[]; list.innerHTML=rows.length?'<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>NIS</th><th>Nama</th><th>Status</th></tr></thead><tbody>'+rows.map(s=>`<tr><td>${s.nis}</td><td>${s.name}</td><td><select name="attendance[${s.nis}]" class="form-select" required><option value="hadir">Hadir</option><option value="izin">Izin</option><option value="sakit">Sakit</option><option value="alpha">Alpha</option></select></td></tr>`).join('')+'</tbody></table></div>':'<div class="alert alert-secondary">Pilih kelas dan mata pelajaran terlebih dahulu.</div>'; });
</script>
@endif
