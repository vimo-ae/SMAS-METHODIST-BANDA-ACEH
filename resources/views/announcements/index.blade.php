@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<h4>Pengumuman</h4>

@if(in_array(auth()->user()->role, ['superadmin', 'admin']))
<div class="card mb-4">
    <div class="card-header">Buat Pengumuman Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf
            <div class="mb-2">
                <input type="text" name="title" class="form-control" placeholder="Judul" required>
            </div>
            <div class="mb-2">
                <textarea name="content" class="form-control" placeholder="Isi pengumuman" required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Ditujukan untuk:</label><br>
                @foreach(['superadmin' => 'Super Admin', 'admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa', 'orangtua' => 'Orang Tua'] as $val => $label)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" name="target_roles[]" value="{{ $val }}" id="role_{{ $val }}">
                        <label class="form-check-label" for="role_{{ $val }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
            <button class="btn btn-primary">Kirim</button>
        </form>
    </div>
</div>
@endif

@forelse($announcements as $a)
    <div class="card mb-2">
        <div class="card-body">
            <h5 class="card-title">{{ $a->title }}</h5>
            <p class="card-text">{{ $a->content }}</p>
            <small class="text-muted">Oleh {{ $a->creator->name }} - {{ $a->created_at->diffForHumans() }}</small>
        </div>
    </div>
@empty
    <p class="text-muted">Belum ada pengumuman untuk Anda.</p>
@endforelse
@endsection
