@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h3>Selamat datang, {{ $user->name }}</h3>
<p class="text-muted">Peran: {{ ucfirst($user->role) }}</p>

<div class="card mt-4">
    <div class="card-header">Pengumuman Terbaru</div>
    <div class="card-body">
        @forelse($announcements as $a)
            <div class="mb-3 pb-3 border-bottom">
                <strong>{{ $a->title }}</strong>
                <p class="mb-0">{{ $a->content }}</p>
                <small class="text-muted">{{ $a->created_at->diffForHumans() }}</small>
            </div>
        @empty
            <p class="text-muted mb-0">Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection
