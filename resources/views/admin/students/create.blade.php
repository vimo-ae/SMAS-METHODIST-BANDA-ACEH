@extends('layouts.admin')
@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.students.index') }}" class="btn btn-white border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-arrow-left text-dark fs-5"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1 text-dark">Tambah Siswa Baru</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.students.store') }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold text-primary mb-3">1. Informasi Akun & Identitas Utama</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control bg-light @error('nis') is-invalid @enderror" value="{{ old('nis') }}" required>
                            @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control bg-light @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}">
                            @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control bg-light @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">No. Telepon / WhatsApp Siswa</label>
                            <input type="text" name="phone" class="form-control bg-light @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Kelas Induk (Ruangan)</label>
                            <select name="class_id" class="form-select bg-light @error('class_id') is-invalid @enderror">
                                <option value="">-- Pilih Kelas Induk --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->class_id }}" {{ old('class_id') == $c->class_id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4">2. Detail Pribadi & Alamat</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select bg-light @error('gender') is-invalid @enderror" required>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="pob" class="form-control bg-light" value="{{ old('pob') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="dob" class="form-control bg-light" value="{{ old('dob') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Kecamatan (District)</label>
                            <input type="text" name="district" class="form-control bg-light" value="{{ old('district') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Kota / Kabupaten (City)</label>
                            <input type="text" name="city" class="form-control bg-light" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Provinsi (Province)</label>
                            <input type="text" name="province" class="form-control bg-light" value="{{ old('province') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Tahun Masuk</label>
                            <input type="number" name="entry_year" class="form-control bg-light" value="{{ old('entry_year', date('Y')) }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="address" class="form-control bg-light" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4">3. Data Orang Tua & Status Akademik</h6>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Nama Orang Tua / Wali</label>
                            <input type="text" name="parent_name" class="form-control bg-light" value="{{ old('parent_name') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">No. Telepon Orang Tua</label>
                            <input type="text" name="parent_phone" class="form-control bg-light" value="{{ old('parent_phone') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Status Siswa</label>
                            <select name="status" class="form-select bg-light" required>
                                <option value="aktif" selected>Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="drop_out">Drop Out</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light border px-4 rounded-3 fw-medium">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Simpan Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection