@extends('layouts.public')

@section('title', $announcement->title.' | SMAS Methodist Banda Aceh')

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
    <a href="{{ route('announcements.index') }}" class="text-sm font-semibold text-navy hover:text-softblue">&larr; Semua pengumuman</a>

    <div class="mt-6 flex flex-wrap items-center gap-3 text-xs">
        <span class="rounded-full bg-softblue/30 px-3 py-1 font-semibold text-navy">{{ $announcement->category_label }}</span>
        <time datetime="{{ $announcement->published_at->toDateString() }}" class="text-navy/60">
            {{ $announcement->published_at->translatedFormat('d F Y') }}
        </time>
    </div>

    <h1 class="mt-4 font-serif text-3xl font-bold leading-tight text-navy sm:text-4xl">{{ $announcement->title }}</h1>

    @if ($announcement->image_url)
        <img src="{{ $announcement->image_url }}" alt="" class="mt-8 w-full rounded-xl object-cover">
    @endif

    <div class="mt-8 space-y-4 break-words leading-8 text-navy/90">
        {!! nl2br(e($announcement->content)) !!}
    </div>
</article>

@if ($related->isNotEmpty())
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="font-serif text-2xl font-bold text-navy">Info lainnya</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $announcement)
                    @include('partials.announcement-card', ['announcement' => $announcement])
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection