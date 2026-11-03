@extends('layouts.admin')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Detail Pengumuman</h4>

        <a href="{{ route('announcements.index') }}"
           class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h4 class="fw-bold">{{ $announcement->title }}</h4>

            <small class="text-muted">
                {{ $announcement->created_at?->format('d F Y') }}
            </small>

            <hr>

            <p style="white-space: pre-line; line-height: 1.8;">
                {{ $announcement->content }}
            </p>

            <hr>

            <strong>Target Penerima:</strong>
            <div class="mt-2">
                @foreach($announcement->target_roles ?? [] as $role)
                    <span class="badge bg-primary me-1">
                        {{ ucfirst($role) }}
                    </span>
                @endforeach
            </div>

        </div>
    </div>

</div>
@endsection