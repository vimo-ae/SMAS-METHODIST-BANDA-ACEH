@extends('layouts.public')

@section('title', 'Pengumuman & Berita | SMAS Methodist Banda Aceh')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <p class="text-xs font-semibold uppercase tracking-widest text-softblue sm:text-sm">Info Terbaru</p>
    <h1 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">Pengumuman & Berita</h1>

    <div class="mt-6 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('announcements.index') }}"
           class="rounded-full px-4 py-2 font-medium {{ $category ? 'bg-white text-navy hover:bg-softblue/20' : 'bg-navy text-white' }}">
            Semua
        </a>
        @foreach (\App\Models\Announcement::CATEGORIES as $key => $label)
            <a href="{{ route('announcements.index', ['kategori' => $key]) }}"
               class="rounded-full px-4 py-2 font-medium {{ $category === $key ? 'bg-navy text-white' : 'bg-white text-navy hover:bg-softblue/20' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($announcements as $announcement)
            @include('partials.announcement-card', ['announcement' => $announcement])
        @empty
            <p class="col-span-full rounded-xl border border-dashed border-softblue bg-white p-10 text-center text-navy/60">
                Belum ada pengumuman di kategori ini.
            </p>
        @endforelse
    </div>

    <div class="mt-10">{{ $announcements->links() }}</div>
</section>
@endsection