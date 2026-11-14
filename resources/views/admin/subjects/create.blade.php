@extends('layouts.admin')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Tambah Mata Pelajaran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item text-muted">Akademik</li>
                <li class="breadcrumb-item text-muted">Mata Pelajaran</li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Tambah Baru</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                
                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Kode Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control bg-light @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Contoh: BIND, MTK, IPA" required style="text-transform: uppercase;">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text small mt-1">Gunakan singkatan unik maksimal 20 karakter.</div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold text-dark">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-light @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Bahasa Indonesia" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Mapel</button>
                    </div>

                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection