@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto p-4">
    <h1 class="text-lg font-semibold">{{ $student->user->name }}</h1>
    <p class="text-gray-500 mb-3">{{ $student->schoolClass?->name }}</p>
    <form method="POST" action="{{ route('cek-nilai.show', $student) }}">
        @csrf
        <label>Tanggal lahir anak</label>
        <input type="date" name="dob" class="border p-2 w-full mb-2" required>
        @error('dob') <p class="text-red-600">{{ $message }}</p> @enderror
        <button class="bg-blue-600 text-white px-4 py-2">Lihat Nilai</button>
    </form>
</div>
@endsection