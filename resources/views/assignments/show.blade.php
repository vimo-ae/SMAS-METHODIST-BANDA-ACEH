@extends('layouts.app')
@section('title', $assignment->title)
@section('content')
<h4>{{ $assignment->title }}</h4>
<p>{{ $assignment->description }}</p>
<p class="text-muted">Batas waktu: {{ $assignment->due_date->format('d M Y H:i') }} | Nilai maksimal: {{ $assignment->max_score }}</p>

@if(auth()->user()->role === 'siswa')
    <div class="card mt-3">
        <div class="card-header">Pengumpulan Saya</div>
        <div class="card-body">
            @if($mySubmission)
                <p>Sudah dikumpulkan pada {{ $mySubmission->submitted_at?->format('d M Y H:i') }}</p>
                <p>Nilai: {{ $mySubmission->score ?? 'Belum dinilai' }}</p>
            @else
                <p class="text-muted">Anda belum mengumpulkan tugas ini.</p>
            @endif
        </div>
    </div>
@else
    <div class="card mt-3">
        <div class="card-header">Daftar Pengumpulan Siswa</div>
        <ul class="list-group list-group-flush">
            @forelse($assignment->submissions as $s)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $s->student->user->name }}</span>
                    <span>{{ $s->score ?? 'Belum dinilai' }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada yang mengumpulkan.</li>
            @endforelse
        </ul>
    </div>
@endif
@endsection
