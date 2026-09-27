@extends('layouts.app')
@section('title', 'Tambah Orang Tua')
@section('content')
<h4>Tambah Orang Tua</h4>
<p class="text-muted">Akun login otomatis dibuat dengan password default <code>password123</code>.</p>
<form method="POST" action="{{ route('admin.guardians.store') }}" class="bg-white p-3 rounded">
    @csrf
    @include('admin.guardians._form')
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection
