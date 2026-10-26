@extends('layouts.app')
@section('title', $exam->title)
@section('content')
<h4>{{ $exam->title }}</h4>
<p class="text-muted">
    {{ strtoupper($exam->type) }} | {{ $exam->classSubjectTeacher->schoolClass->name ?? '-' }} - {{ $exam->classSubjectTeacher->subject->name ?? '-' }}<br>
    {{ $exam->start_time->format('d M Y H:i') }} s/d {{ $exam->end_time->format('d M Y H:i') }} ({{ $exam->duration_minutes }} menit)
</p>
<div class="card">
    <div class="card-header">Soal ({{ $exam->questions->count() }})</div>
    <ul class="list-group list-group-flush">
        @forelse($exam->questions as $i => $q)
            <li class="list-group-item">{{ $i + 1 }}. {{ $q->question_text }} <span class="badge bg-secondary">{{ $q->question_type }}</span></li>
        @empty
            <li class="list-group-item text-muted">Belum ada soal.</li>
        @endforelse
    </ul>
</div>
@endsection
