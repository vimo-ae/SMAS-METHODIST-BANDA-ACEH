
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('announcements.index') }}"
           class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Pengumuman
        </a>

        <h3 class="fw-bold mt-3 mb-1">Edit Pengumuman</h3>
        <p class="text-muted">
            Perbarui informasi pengumuman.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('announcements.update', ['announcement' => $announcement->getKey()]) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        Judul Pengumuman
                    </label>

                    <input type="text"
                           name="title"
                           id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $announcement->title) }}"
                           placeholder="Masukkan judul pengumuman"
                           required>

                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">
                        Isi Pengumuman
                    </label>

                    <textarea name="content"
                              id="content"
                              rows="6"
                              class="form-control @error('content') is-invalid @enderror"
                              placeholder="Tuliskan isi pengumuman..."
                              required>{{ old('content', $announcement->content) }}</textarea>

                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Pengumuman Ditujukan Kepada
                    </label>

                    <p class="text-muted small">
                        Pilih satu atau lebih penerima pengumuman.
                    </p>

                    @php
                        $selectedRoles = old('target_roles', $announcement->target_roles ?? []);
                    @endphp

                    <div class="d-flex flex-wrap gap-4">

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="target_roles[]"
                                   value="guru"
                                   id="role_guru"
                                   {{ in_array('guru', $selectedRoles) ? 'checked' : '' }}>

                            <label class="form-check-label" for="role_guru">
                                Guru
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="target_roles[]"
                                   value="siswa"
                                   id="role_siswa"
                                   {{ in_array('siswa', $selectedRoles) ? 'checked' : '' }}>

                            <label class="form-check-label" for="role_siswa">
                                Siswa
                            </label>
                        </div>

                    </div>

                    @error('target_roles')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @error('target_roles.*')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('announcements.index') }}"
                       class="btn btn-light border">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
