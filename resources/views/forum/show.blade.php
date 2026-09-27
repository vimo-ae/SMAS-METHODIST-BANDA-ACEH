@extends('layouts.app')
@section('title', $thread->title)
@section('content')
<h4>{{ $thread->title }}</h4>
<p class="text-muted">{{ $thread->classSubjectTeacher->schoolClass->name ?? '-' }} - {{ $thread->classSubjectTeacher->subject->name ?? '-' }}</p>

<div class="card mb-3">
    <ul class="list-group list-group-flush">
        @forelse($thread->replies as $r)
            <li class="list-group-item">
                <strong>{{ $r->user->name }}</strong>
                <p class="mb-0">{{ $r->content }}</p>
                <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>
            </li>
        @empty
            <li class="list-group-item text-muted">Belum ada balasan.</li>
        @endforelse
    </ul>
</div>

<form method="POST" action="{{ route('forum.reply', $thread) }}">
    @csrf
    <div class="mb-2">
        <textarea name="content" class="form-control" placeholder="Tulis balasan..." required></textarea>
    </div>
    <button class="btn btn-primary">Kirim Balasan</button>
</form>
@endsection
