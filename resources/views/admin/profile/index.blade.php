@extends('layouts.admin')

@section('title', 'Profil Admin - Portal Akademik')

@section('content')

{{-- WRAPPER TEMA PORTAL --}}
<div class="theme-portal rounded-4 shadow-sm">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-4 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted-portal small">
                    <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                </a>
                <span class="text-muted-portal small">/</span>
                <span class="text-accent small fw-semibold">Profil Saya</span>
            </div>
            <h2 class="fw-bold mb-1 text-dark">Profil Administrator</h2>
            <p class="text-muted-portal mb-0">
                Kelola informasi akun pribadi, kontak, dan keamanan kredensial administrator Anda.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-lock-fill me-1"></i> Hak Akses: Administrator
            </span>
        </div>
    </div>

    <div class="p-4 pt-0">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('password_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-shield-check fs-5"></i>
                <div>{{ session('password_success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any() && !$errors->updatePassword->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Terjadi kesalahan saat memperbarui profil:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- KOLOM KIRI: KARTU IDENTITAS PROFIL --}}
            <div class="col-lg-4">
                
                {{-- KARTU UTAMA PROFIL --}}
                <div class="portal-card overflow-hidden mb-4">
                    {{-- Cover Banner --}}
                    <div style="height: 100px; background: linear-gradient(135deg, #101c35 0%, #1e293b 60%, #4f46e5 100%); position: relative;" class="p-3 d-flex align-items-start justify-content-between text-white">
                        <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="bi bi-mortarboard-fill me-1"></i> SMAS METHODIST
                        </span>
                        <span class="badge bg-white bg-opacity-10 text-white-50" style="font-size: 0.7rem;">
                            Banda Aceh
                        </span>
                    </div>

                    {{-- Body Kartu Profil --}}
                    <div class="p-4 pt-0 text-center" style="margin-top: -50px;">
                        {{-- Foto Profil (Logo SMAS Methodist / Foto Upload) --}}
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                 id="profileAvatarPreview"
                                 class="rounded-circle border border-4 border-white shadow-sm bg-white p-1"
                                 style="width: 105px; height: 105px; object-fit: contain;">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" 
                                  title="Online" style="width: 18px; height: 18px;"></span>
                        </div>

                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                        <div class="text-muted-portal small mb-2">{{ $user->email }}</div>
                        
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-3 flex-wrap">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                <i class="bi bi-shield-check me-1"></i> Admin SMAS Methodist
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill small">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Aktif
                            </span>
                        </div>

                        <hr class="border-secondary my-3">

                        {{-- Rincian Data Akun --}}
                        <div class="text-start d-flex flex-column gap-3 small">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted-portal"><i class="bi bi-building me-2"></i>Unit Sekolah</span>
                                <span class="fw-semibold text-dark">SMAS Methodist Banda Aceh</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted-portal"><i class="bi bi-person-badge me-2"></i>User ID</span>
                                <span class="fw-bold font-monospace text-dark">{{ $user->user_id }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted-portal"><i class="bi bi-telephone me-2"></i>No. Telepon</span>
                                <span class="fw-semibold text-dark">{{ $user->phone ?? 'Belum diatur' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted-portal"><i class="bi bi-calendar-check me-2"></i>Terdaftar Sejak</span>
                                <span class="fw-semibold text-dark">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted-portal"><i class="bi bi-megaphone me-2"></i>Pengumuman Dibuat</span>
                                <span class="badge bg-secondary-subtle text-dark fw-bold rounded-pill px-2.5 py-1">
                                    {{ $totalAnnouncements ?? 0 }} Pengumuman
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- KARTU INFORMASI HAK AKSES SISTEM --}}
                <div class="portal-card p-3">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-secondary">
                        <i class="bi bi-info-circle-fill text-accent"></i>
                        <span class="fw-bold small text-dark">Informasi Hak Akses</span>
                    </div>
                    <ul class="list-unstyled mb-0 small text-muted-portal d-flex flex-column gap-2">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-success fs-6"></i>
                            <span>Akses penuh konfigurasi sistem akademik</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-success fs-6"></i>
                            <span>Pengelolaan data siswa, guru, dan kelas</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-success fs-6"></i>
                            <span>Monitoring ujian dan riwayat aktivitas sistem</span>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- KOLOM KANAN: FORM EDIT INFORMASI dan GANTI PASSWORD --}}
            <div class="col-lg-8">

                {{-- FORM 1: INFORMASI PROFIL --}}
                <div class="portal-card mb-4">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box text-accent" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Informasi Pribadi dan Kontak</h5>
                        </div>
                        <span class="text-muted-portal small">Data Akun Admin</span>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- User ID (Readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">
                                    User ID / NIP / NIK
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0" 
                                           value="{{ $user->user_id }}" disabled readonly>
                                </div>
                                <small class="text-muted-portal" style="font-size: 0.72rem;">
                                    User ID bersifat permanen dan tidak dapat diubah secara langsung.
                                </small>
                            </div>

                            {{-- Role Akun (Readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">
                                    Peran Akun
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="bi bi-shield-fill"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0" 
                                           value="Administrator Sistem" disabled readonly>
                                </div>
                                <small class="text-muted-portal" style="font-size: 0.72rem;">
                                    Memiliki hak akses tertinggi dalam portal akademik.
                                </small>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-semibold small text-dark">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           maxlength="100" required placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small text-dark">
                                    Alamat Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           maxlength="255" required placeholder="nama@sekolah.sch.id">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nomor Telepon --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold small text-dark">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-telephone"></i>
                                    </span>
                                    <input type="tel" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}" 
                                           maxlength="20" placeholder="Contoh: 081234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Unggah Foto Profil --}}
                            <div class="col-12">
                                <label for="photo" class="form-label fw-semibold small text-dark">
                                    Foto Profil (Opsional)
                                </label>
                                <input type="file" name="photo" id="photo" 
                                       class="form-control @error('photo') is-invalid @enderror" 
                                       accept="image/jpeg,image/png,image/jpg,image/webp"
                                       onchange="previewPhoto(this)">
                                <small class="text-muted-portal" style="font-size: 0.72rem;">
                                    Format: JPG, JPEG, PNG, atau WEBP. Maksimal ukuran berkas 2MB.
                                </small>
                                @error('photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-secondary d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-3">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-portal-primary px-4">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>


                {{-- FORM 2: GANTI PASSWORD --}}
                <div class="portal-card">
                    <div class="card-header-portal d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box text-warning" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Keamanan dan Kata Sandi</h5>
                        </div>
                        <span class="text-muted-portal small">Perbarui Password</span>
                    </div>

                    <form action="{{ route('admin.profile.password') }}" method="POST" class="p-4">
                        @csrf
                        @method('PUT')

                        @if($errors->updatePassword->any())
                            <div class="alert alert-danger rounded-3 mb-3 p-3">
                                <div class="fw-semibold small mb-1"><i class="bi bi-exclamation-circle me-1"></i> Gagal memperbarui kata sandi:</div>
                                <ul class="mb-0 small ps-3">
                                    @foreach($errors->updatePassword->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            {{-- Password Saat Ini --}}
                            <div class="col-12">
                                <label for="current_password" class="form-label fw-semibold small text-dark">
                                    Kata Sandi Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" 
                                           required placeholder="Masukkan kata sandi saat ini">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('current_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Password Baru --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold small text-dark">
                                    Kata Sandi Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="password" 
                                           class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" 
                                           required minlength="8" placeholder="Minimal 8 karakter">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted-portal" style="font-size: 0.72rem;">
                                    Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.
                                </small>
                            </div>

                            {{-- Konfirmasi Password Baru --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold small text-dark">
                                    Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" 
                                           required minlength="8" placeholder="Ulangi kata sandi baru">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password_confirmation', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-secondary d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-warning text-dark fw-semibold px-4">
                                <i class="bi bi-shield-lock me-1"></i> Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>

</div>

{{-- CSS KHUSUS PORTAL ADMIN --}}
<style>
    .theme-portal {
        background: #ffffff;
        color: #1e293b;
        min-height: 80vh;
        border: 1px solid #e2e8f0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .text-accent {
        color: #4f46e5 !important;
    }
    .text-muted-portal {
        color: #64748b !important;
    }
    .border-secondary {
        border-color: #e2e8f0 !important;
    }
    .portal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .card-header-portal {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
    }
    .btn-portal-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-portal-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: white;
    }
</style>

{{-- JAVASCRIPT PREVIEW FOTO dan TOGGLE PASSWORD --}}
@push('scripts')
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewEl = document.getElementById('profileAvatarPreview');
                if (previewEl.tagName.toLowerCase() === 'img') {
                    previewEl.src = e.target.result;
                } else {
                    // Jika sebelumnya elemen div inisial, ganti dengan img
                    const img = document.createElement('img');
                    img.id = 'profileAvatarPreview';
                    img.src = e.target.result;
                    img.alt = 'Foto Profil';
                    img.className = 'rounded-circle border border-4 border-white shadow-sm';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    previewEl.parentNode.replaceChild(img, previewEl);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

@endsection
