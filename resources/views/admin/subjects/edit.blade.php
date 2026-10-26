@extends('layouts.app')
@section('title', 'Edit Mapel')
@section('content')
<h4>Edit Mapel</h4>
<form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="bg-white p-3 rounded">
    @csrf @method('PUT')
    @include('admin.subjects._form')
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection
