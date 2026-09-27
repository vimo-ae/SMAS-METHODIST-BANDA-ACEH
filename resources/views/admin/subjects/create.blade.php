@extends('layouts.app')
@section('title', 'Tambah Mapel')
@section('content')
<h4>Tambah Mapel</h4>
<form method="POST" action="{{ route('admin.subjects.store') }}" class="bg-white p-3 rounded">
    @csrf
    @include('admin.subjects._form')
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection
