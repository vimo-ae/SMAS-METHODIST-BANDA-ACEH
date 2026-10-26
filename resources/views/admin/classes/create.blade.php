@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('content')
<h4>Tambah Kelas</h4>
<form method="POST" action="{{ route('admin.classes.store') }}" class="bg-white p-3 rounded">
    @csrf
    @include('admin.classes._form')
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection
