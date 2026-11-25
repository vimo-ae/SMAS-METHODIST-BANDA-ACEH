@extends('layouts.public')

@section('title', 'Pengumuman dan Berita | SMAS Methodist Banda Aceh')

@section('content')
@php
    $categories = \App\Models\Announcement::CATEGORIES;
    $activeLabel = $category ? ($categories[$category] ?? null) : null;
@endphp

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">

    {{-- Tombol kembali --}}
    <a href="{{ url('/') }}"
       class="mb-6 inline-flex items-center gap-1.5 text-sm font-semibold text-navy transition hover:text-softblue">
        <span aria-hidden="true">&larr;</span> Kembali ke Beranda
    </a>

    {{-- Header: sama dengan bagian Pengumuman di halaman depan --}}
    <div class="flex flex-col justify-between gap-4 border-b border-slate-200/80 pb-6 sm:flex-row sm:items-end">
        <div>
            <span class="rounded-full bg-softblue/15 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-navy">
                Informasi Terkini
            </span>
            <h1 class="mt-2 font-serif text-3xl font-bold text-navy sm:text-4xl">
                Pengumuman dan Berita Sekolah
            </h1>
            <p class="mt-1 text-sm text-slate-600">
                Kabar terkini seputar kegiatan akademik dan pengumuman resmi SMAS Methodist Banda Aceh.
            </p>
        </div>
        <p class="text-sm text-navy/60">{{ $announcements->total() }} kabar{{ $activeLabel ? ' · '.$activeLabel : '' }}</p>
    </div>

    {{-- Filter kategori --}}
    <nav class="mt-6 flex flex-wrap gap-2 text-sm" aria-label="Kategori pengumuman">
        <a href="{{ route('announcements.index') }}"
           class="rounded-full border px-4 py-2 font-semibold transition {{ $category ? 'border-softblue/40 bg-white text-navy hover:bg-softblue/20' : 'border-navy bg-navy text-white' }}">
            Semua
        </a>
        @foreach ($categories as $key => $label)
            <a href="{{ route('announcements.index', ['kategori' => $key]) }}"
               class="rounded-full border px-4 py-2 font-semibold transition {{ $category === $key ? 'border-navy bg-navy text-white' : 'border-softblue/40 bg-white text-navy hover:bg-softblue/20' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    {{-- Daftar: kartu yang sama dengan halaman depan dan "Info lainnya" --}}
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($announcements as $announcement)
            @include('partials.announcement-card', ['announcement' => $announcement])
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-softblue bg-white px-6 py-14 text-center">
                <p class="font-serif text-lg font-semibold text-navy">Belum ada pengumuman</p>
                <p class="mt-1 text-sm text-navy/60">
                    {{ $activeLabel ? 'Belum ada kabar di kategori '.$activeLabel.'.' : 'Kabar terbaru akan muncul di sini.' }}
                </p>
                @if ($category)
                    <a href="{{ route('announcements.index') }}"
                       class="mt-5 inline-flex rounded-xl bg-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                        Lihat semua pengumuman
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginasi (filter tetap terbawa) --}}
    @if ($announcements->hasPages())
        <div class="mt-10">{{ $announcements->withQueryString()->links() }}</div>
    @endif
</section>
@endsection