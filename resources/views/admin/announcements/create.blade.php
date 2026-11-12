@extends('layouts.admin')
@section('title', 'Buat Pengumuman')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Buat Pengumuman Baru</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control bg-light @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Target Penerima <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input @error('target_roles') is-invalid @enderror" type="checkbox" name="target_roles[]" value="teacher" id="roleTeacher" {{ in_array('teacher', old('target_roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="roleTeacher">Guru</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('target_roles') is-invalid @enderror" type="checkbox" name="target_roles[]" value="student" id="roleStudent" {{ in_array('student', old('target_roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="roleStudent">Siswa</label>
                            </div>
                        </div>
                        @error('target_roles')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control bg-light @error('content') is-invalid @enderror" rows="6" required>{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Publikasikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection