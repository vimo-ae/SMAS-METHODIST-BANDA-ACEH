@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')
<h4>Edit Kelas</h4>
<form method="POST" action="{{ route('admin.classes.update', $schoolClass) }}" class="bg-white p-3 rounded">
    @csrf @method('PUT')
    @include('admin.classes._form', ['class' => $schoolClass])
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection
