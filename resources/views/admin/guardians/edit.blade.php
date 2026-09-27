@extends('layouts.app')
@section('title', 'Edit Orang Tua')
@section('content')
<h4>Edit Orang Tua</h4>
<form method="POST" action="{{ route('admin.guardians.update', $guardian) }}" class="bg-white p-3 rounded">
    @csrf @method('PUT')
    @include('admin.guardians._form')
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection
