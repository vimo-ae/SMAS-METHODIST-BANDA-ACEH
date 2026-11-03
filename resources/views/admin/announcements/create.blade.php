
@extends('layouts.admin')

@section('title', 'Tambah Pengumuman')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Tambah Pengumuman</h4>
    <p class="text-muted">Buat informasi baru untuk warga sekolah.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form action="{{ route('announcements.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Pengumuman</label>
                <input type="text"
                       name="title"
                       class="form-control"
                       placeholder="Masukkan judul pengumuman"
                       value="{{ old('title') }}"
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
                          placeholder="Tuliskan isi pengumuman..."
                          required>{{ old('content') }}</textarea>

                @error('content')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Target Penerima</label>
                <p class="text-muted small">
                    Pilih siapa saja yang dapat melihat pengumuman ini.
                </p>

                @foreach(['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $value => $label)
                    <div class="form-check mb-2">
                        <input class="form-check-input"
                               type="checkbox"
                               name="target_roles[]"
                               value="{{ $value }}"
                               id="role_{{ $value }}"
                               @checked(in_array($value, old('target_roles', [])))>

                        <label class="form-check-label" for="role_{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                @endforeach

                @error('target_roles')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i> Terbitkan
                </button>

                <a href="{{ route('announcements.index') }}"
                   class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
