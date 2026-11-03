
@extends('layouts.admin')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold">Edit Pengumuman</h4>
    <p class="text-muted">Perbarui informasi pengumuman sekolah.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form action="{{ route('announcements.update', $announcement) }}"
              method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul</label>
                <input type="text"
                       name="title"
                       class="form-control"
                       value="{{ old('title', $announcement->title) }}"
                       maxlength="150"
                       required>
                @error('title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Isi Pengumuman</label>
                <textarea name="content"
                          class="form-control"
                          rows="6"
                          required>{{ old('content', $announcement->content) }}</textarea>
                @error('content')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Target Penerima</label>

                @foreach(['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $value => $label)
                    <div class="form-check mb-2">
                        <input type="checkbox"
                               name="target_roles[]"
                               value="{{ $value }}"
                               class="form-check-input"
                               id="role_{{ $value }}"
                               @checked(in_array($value, old('target_roles', $announcement->target_roles ?? [])))>

                        <label class="form-check-label" for="role_{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>

            <a href="{{ route('announcements.index') }}"
               class="btn btn-outline-secondary">
                Batal
            </a>
        </form>

    </div>
</div>
@endsection
