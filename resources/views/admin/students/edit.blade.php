@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')
<h4>Edit Siswa</h4>
<form method="POST" action="{{ route('admin.students.update', $student) }}" class="bg-white p-3 rounded">
    @csrf @method('PUT')
    @include('admin.students._form')
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection
