@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('content')
<h4>Tambah Guru</h4>
<p class="text-muted">Akun login otomatis dibuat dengan password default <code>password123</code>.</p>
<form method="POST" action="{{ route('admin.teachers.store') }}" class="bg-white p-3 rounded">
    @csrf
    @include('admin.teachers._form')
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection
