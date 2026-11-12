@extends('layouts.admin')
@section('title', 'Buat Kelas Mapel Baru')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.cst.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Buat Kelas Mapel Baru</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.cst.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="subject_code" class="form-select bg-light" required>
                            <option value="" selected disabled>-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->code }}" {{ old('subject_code') == $sub->code ? 'selected' : '' }}>{{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Guru Pengampu <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select bg-light" required>
                            <option value="" selected disabled>-- Pilih Guru --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->nip }}" {{ old('teacher_id') == $teacher->nip ? 'selected' : '' }}>{{ $teacher->user->name ?? 'Tanpa Nama' }} (NIP: {{ $teacher->nip }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Alokasi Ruangan (Label) <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select bg-light" required>
                                <option value="" selected disabled>-- Pilih Ruangan Kelas --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->class_id }}" {{ old('class_id') == $room->class_id ? 'selected' : '' }}>{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year" class="form-control bg-light" value="{{ old('academic_year', '2025/2026') }}" required>
                        </div>
                    </div>
                    <hr class="border-secondary mb-4 opacity-10">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.cst.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan & Buat Join Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection