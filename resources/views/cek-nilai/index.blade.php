@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto p-4">
    <h1 class="text-xl font-semibold mb-3">Cek Nilai Siswa</h1>
    <form method="GET" class="flex gap-2 mb-4">
        <input name="name" value="{{ request('name') }}" placeholder="Nama anak (min. 3 huruf)" class="border p-2 flex-1">
        <button class="bg-blue-600 text-white px-4">Cari</button>
    </form>
    @error('name') <p class="text-red-600">{{ $message }}</p> @enderror

    @foreach ($students as $s)
        <a href="{{ route('cek-nilai.form', $s) }}" class="block border p-3 mb-2">
            {{ $s->user->name }} <span class="text-gray-500">- {{ $s->schoolClass?->name }}</span>
        </a>
    @endforeach
    @if (request('name') && $students->isEmpty())
        <p class="text-gray-500">Siswa tidak ditemukan.</p>
    @endif
</div>
@endsection