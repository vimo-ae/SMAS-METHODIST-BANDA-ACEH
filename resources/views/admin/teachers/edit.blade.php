@extends('layouts.app')
@section('title', 'Edit Guru')
@section('content')
<h4>Edit Guru</h4>
<form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="bg-white p-3 rounded">
    @csrf @method('PUT')
    @include('admin.teachers._form')
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection
