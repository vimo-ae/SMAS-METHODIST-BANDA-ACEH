<x-app-layout>
    <x-slot name="header">
        <div class="student-profile-topbar">
            <div>
                <span class="student-eyebrow">SMAS METHODIST BANDA ACEH</span>
                <h2>Profil Siswa</h2>
            </div>
            <span class="student-topbar-icon">
                <i class="bi bi-person-vcard-fill"></i>
            </span>
        </div>
    </x-slot>

    @php
        $initial = mb_strtoupper(mb_substr($user->name ?? 'S', 0, 1));

        $className = $student?->schoolClass?->name ?? 'Belum ditentukan';

        if ($student?->schoolClass?->academic_year) {
            $className .= ' · ' . $student->schoolClass->academic_year;
        }

        $genderLabel = match ($student?->gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Belum diisi',
        };

        $statusLabels = [
            'aktif' => 'Aktif',
            'lulus' => 'Lulus',
            'pindah' => 'Pindah',
            'drop_out' => 'Drop Out',
        ];

        $statusLabel = $statusLabels[$student?->status ?? ''] ?? 'Belum diatur';

        $studentDetails = $student ? [
            ['label' => 'Nama lengkap', 'value' => $user->name],
            ['label' => 'NIS', 'value' => $student->nis],
            ['label' => 'NISN', 'value' => $student->nisn],
            ['label' => 'Kelas', 'value' => $className],
            ['label' => 'Jenis kelamin', 'value' => $genderLabel],
            ['label' => 'Tempat lahir', 'value' => $student->pob],
            ['label' => 'Tanggal lahir', 'value' => $student->dob?->format('d-m-Y')],
            ['label' => 'Tahun masuk', 'value' => $student->entry_year],
            ['label' => 'Status siswa', 'value' => $statusLabel],
        ] : [];
    @endphp

    <div class="student-profile-page">

        @if (! $student)

            <section class="student-profile-panel student-empty-state">
                <span class="student-empty-icon">
                    <i class="bi bi-person-exclamation"></i>
                </span>

                <h3>Data profil siswa belum tersedia</h3>

                <p>
                    Akun Anda belum terhubung dengan data siswa.
                    Silakan hubungi administrator sekolah untuk pemeriksaan data.
                </p>
            </section>

        @else

            <section class="student-profile-hero">
                <div class="student-hero-pattern"></div>

                <div class="student-hero-content">
                    <div class="student-avatar">
                        {{ $initial }}
                    </div>

                    <div class="student-identity">
                        <span class="student-hero-kicker">STUDENT PROFILE</span>

                        <h1>{{ $user->name }}</h1>

                        <p>
                            <i class="bi bi-person-badge"></i>
                            NIS {{ $student->nis }}
                        </p>

                        <div class="student-hero-tags">
                            <span>
                                <i class="bi bi-mortarboard-fill"></i>
                                {{ $className }}
                            </span>

                            <span class="student-status-pill {{ $student->status === 'aktif' ? 'is-active' : 'is-other' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <a href="#student-editable-data" class="student-hero-action">
                        <i class="bi bi-pencil-square"></i>
                        Ubah Informasi Kontak
                    </a>
                </div>
            </section>

            <div class="student-profile-notice">
                <i class="bi bi-shield-lock-fill"></i>

                <div>
                    <strong>Data identitas dan akademik dilindungi.</strong>

                    <p>
                        NIS, NISN, nama, kelas, jenis kelamin, tempat/tanggal lahir,
                        tahun masuk, dan status hanya dapat diperbarui oleh administrator sekolah.
                    </p>
                </div>
            </div>

            <section class="student-profile-panel" id="student-identity-data">

                <div class="student-panel-heading">
                    <div class="student-panel-icon icon-navy">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>

                    <div>
                        <h3>Identitas dan Data Akademik</h3>
                        <p>
                            Informasi resmi siswa yang tercatat pada halaman Data Siswa administrator.
                        </p>
                    </div>

                    <span class="student-lock-label">
                        <i class="bi bi-lock-fill"></i> Admin saja
                    </span>
                </div>

                <div class="student-readonly-grid">
                    @foreach ($studentDetails as $detail)
                        <div class="student-readonly-item">
                            <span class="student-field-label">
                                {{ $detail['label'] }}
                            </span>

                            <strong>
                                {{ filled($detail['value']) ? $detail['value'] : 'Belum diisi' }}
                            </strong>

                            <small>
                                <i class="bi bi-lock-fill"></i>
                                Tidak dapat diubah siswa
                            </small>
                        </div>
                    @endforeach
                </div>

            </section>

            <section class="student-profile-panel" id="student-editable-data">

                <div class="student-panel-heading">
                    <div class="student-panel-icon icon-blue">
                        <i class="bi bi-pencil-square"></i>
                    </div>

                    <div>
                        <h3>Informasi Kontak dan Alamat</h3>
                        <p>
                            Bagian ini dapat diperbarui sendiri oleh siswa jika ada perubahan.
                        </p>
                    </div>

                    <span class="student-edit-label">
                        <i class="bi bi-unlock-fill"></i> Bisa diedit
                    </span>
                </div>

                @if ($errors->any())
                    <div class="student-alert student-alert-error">
                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div>
                            <strong>Periksa kembali data yang dimasukkan.</strong>

                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST"
                    action="{{ route('profile.update') }}"
                    class="student-profile-form">

                    @csrf
                    @method('PATCH')

                    <div class="student-form-grid">

                        <div class="student-form-field">
                            <label for="email">
                                Email akun <span>*</span>
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                maxlength="255"
                                required
                                autocomplete="email"
                            >

                            @error('email')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field">
                            <label for="phone">Nomor HP siswa</label>

                            <input
                                id="phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone', $user->phone) }}"
                                maxlength="20"
                                autocomplete="tel"
                                placeholder="Masukkan nomor HP siswa"
                            >

                            @error('phone')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field student-form-field-wide">
                            <label for="address">Alamat lengkap</label>

                            <input
                                id="address"
                                name="address"
                                type="text"
                                value="{{ old('address', $student->address) }}"
                                maxlength="255"
                                placeholder="Nama jalan, desa/kelurahan"
                            >

                            @error('address')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field">
                            <label for="district">Kecamatan</label>

                            <input
                                id="district"
                                name="district"
                                type="text"
                                value="{{ old('district', $student->district) }}"
                                maxlength="100"
                            >

                            @error('district')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field">
                            <label for="city">Kota / Kabupaten</label>

                            <input
                                id="city"
                                name="city"
                                type="text"
                                value="{{ old('city', $student->city) }}"
                                maxlength="100"
                            >

                            @error('city')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field student-form-field-wide">
                            <label for="province">Provinsi</label>

                            <input
                                id="province"
                                name="province"
                                type="text"
                                value="{{ old('province', $student->province) }}"
                                maxlength="100"
                            >

                            @error('province')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field">
                            <label for="parent_name">Nama orang tua / wali</label>

                            <input
                                id="parent_name"
                                name="parent_name"
                                type="text"
                                value="{{ old('parent_name', $student->parent_name) }}"
                                maxlength="100"
                            >

                            @error('parent_name')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="student-form-field">
                            <label for="parent_phone">Nomor HP orang tua / wali</label>

                            <input
                                id="parent_phone"
                                name="parent_phone"
                                type="text"
                                value="{{ old('parent_phone', $student->parent_phone) }}"
                                maxlength="20"
                            >

                            @error('parent_phone')
                                <small class="student-field-error">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <div class="student-form-footer">
                        <p>
                            <i class="bi bi-info-circle"></i>
                            Perubahan hanya berlaku untuk informasi kontak dan alamat.
                            Data identitas resmi tetap dikelola administrator.
                        </p>

                        <button type="submit" class="student-save-button">
                            <i class="bi bi-check2-circle"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                    @if (session('status') === 'profile-updated')
                        <div class="student-alert student-alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Informasi profil berhasil diperbarui.
                        </div>
                    @endif

                </form>
            </section>

            <section class="student-profile-panel" id="student-password">

                <div class="student-panel-heading">
                    <div class="student-panel-icon icon-purple">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <div>
                        <h3>Keamanan Akun</h3>
                        <p>
                            Ganti kata sandi secara berkala untuk menjaga keamanan akun sekolah.
                        </p>
                    </div>
                </div>

                @include('profile.partials.student-update-password-form')

            </section>

        @endif

        <footer class="student-profile-footer">
            <span>
                <i class="bi bi-mortarboard-fill"></i>
                SMAS METHODIST BANDA ACEH
            </span>

            <p>Portal Pembelajaran Digital · Profil Siswa</p>
        </footer>

    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');

        .student-profile-topbar{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .student-profile-topbar h2{margin:4px 0 0;color:#17365d;font-size:22px;font-weight:800}
        .student-eyebrow{color:#6d8eaf;font-size:10px;font-weight:800;letter-spacing:1.6px}
        .student-topbar-icon{display:grid;width:44px;height:44px;place-items:center;border-radius:13px;background:#e8f0f8;color:#17365d;font-size:20px}
        .student-profile-page{max-width:1180px;margin:0 auto;color:#203b5c;font-family:'DM Sans',sans-serif}
        .student-profile-hero{position:relative;overflow:hidden;margin-bottom:18px;border-radius:22px;background:linear-gradient(115deg,#102744 0%,#173e68 58%,#315f88 100%);color:#fff;box-shadow:0 12px 30px rgba(16,39,68,.13)}
        .student-hero-pattern{position:absolute;inset:0;opacity:.12;background-image:linear-gradient(rgba(255,255,255,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.3) 1px,transparent 1px);background-size:32px 32px;mask-image:linear-gradient(90deg,#000,transparent)}
        .student-hero-content{position:relative;display:flex;align-items:center;gap:22px;padding:32px;min-height:190px}
        .student-avatar{display:grid;width:88px;height:88px;flex:0 0 88px;place-items:center;border:3px solid rgba(255,255,255,.75);border-radius:25px;background:#e4edf7;color:#17365d;font-size:36px;font-weight:800;box-shadow:0 8px 24px rgba(0,0,0,.15)}
        .student-identity{min-width:0;flex:1}
        .student-hero-kicker{color:#b9d1ea;font-size:10px;font-weight:800;letter-spacing:1.7px}
        .student-identity h1{margin:5px 0;color:#fff;font-size:clamp(22px,3vw,30px);font-weight:800;overflow-wrap:anywhere}
        .student-identity>p{margin:0;color:#d7e5f3;font-size:13px}
        .student-identity>p i{margin-right:5px}
        .student-hero-tags{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-top:13px}
        .student-hero-tags>span{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border:1px solid rgba(255,255,255,.2);border-radius:30px;background:rgba(255,255,255,.1);color:#fff;font-size:11px;font-weight:700}
        .student-hero-tags .student-status-pill.is-active{background:#dcfce7;border-color:#dcfce7;color:#166534}
        .student-hero-tags .student-status-pill.is-other{background:#fff7ed;border-color:#ffedd5;color:#9a3412}
        .student-hero-action{display:inline-flex;flex-shrink:0;align-items:center;gap:8px;padding:11px 15px;border:1px solid rgba(255,255,255,.35);border-radius:11px;color:#fff;text-decoration:none;font-size:12px;font-weight:800;transition:.2s}
        .student-hero-action:hover{background:rgba(255,255,255,.13);color:#fff;transform:translateY(-1px)}
        .student-profile-notice{display:flex;gap:13px;margin:18px 0;padding:16px 18px;border:1px solid #d6e3f0;border-radius:14px;background:#eef5fb;color:#25476c}
        .student-profile-notice>i{margin-top:2px;color:#315f88;font-size:19px}
        .student-profile-notice strong{font-size:13px}
        .student-profile-notice p{margin:4px 0 0;color:#617994;font-size:12px;line-height:1.65}
        .student-profile-panel{margin-bottom:18px;padding:25px 27px;border:1px solid #e0e8f1;border-radius:19px;background:#fff;box-shadow:0 7px 24px rgba(20,48,88,.045)}
        .student-panel-heading{display:flex;align-items:center;gap:13px;margin-bottom:22px;padding-bottom:17px;border-bottom:1px solid #edf1f6}
        .student-panel-heading>div:nth-child(2){min-width:0;flex:1}
        .student-panel-heading h3{margin:0;color:#17365d;font-size:16px;font-weight:800}
        .student-panel-heading p{margin:4px 0 0;color:#8291a7;font-size:11px;line-height:1.6}
        .student-panel-icon{display:grid;width:43px;height:43px;flex:0 0 43px;place-items:center;border-radius:13px;font-size:18px}
        .icon-navy{background:#e8f0f8;color:#17365d}.icon-blue{background:#e8f1ff;color:#2563a6}.icon-purple{background:#f0eaff;color:#7952d8}
        .student-lock-label,.student-edit-label{display:inline-flex;align-items:center;gap:6px;white-space:nowrap;padding:7px 10px;border-radius:30px;font-size:10px;font-weight:800}
        .student-lock-label{background:#f1f5f9;color:#64748b}.student-edit-label{background:#e9f9f1;color:#16845c}
        .student-readonly-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
        .student-readonly-item{min-width:0;padding:14px 15px;border:1px solid #e7edf4;border-radius:12px;background:#fbfcfe}
        .student-field-label{display:block;margin-bottom:6px;color:#8291a7;font-size:10px;font-weight:800;letter-spacing:.35px;text-transform:uppercase}
        .student-readonly-item strong{display:block;min-height:20px;color:#203b5c;font-size:13px;font-weight:700;overflow-wrap:anywhere}
        .student-readonly-item small{display:block;margin-top:8px;color:#9aa7b7;font-size:10px}
        .student-readonly-item small i{margin-right:3px}
        .student-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}
        .student-form-field{min-width:0}
        .student-form-field-wide{grid-column:1/-1}
        .student-form-field label{display:block;margin-bottom:7px;color:#344968;font-size:12px;font-weight:800}
        .student-form-field label span{color:#dc4444}
        .student-form-field input{width:100%;min-height:44px;padding:10px 13px;border:1px solid #dce5f1;border-radius:10px;background:#f8faff;color:#19365e;font:13px 'DM Sans',sans-serif;transition:.2s}
        .student-form-field input:focus{border-color:#3b82f6;outline:0;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.1)}
        .student-field-error{display:block;margin-top:5px;color:#dc4444;font-size:11px}
        .student-form-footer{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-top:22px;padding-top:17px;border-top:1px solid #edf1f6}
        .student-form-footer p{max-width:600px;margin:0;color:#8291a7;font-size:11px;line-height:1.6}
        .student-form-footer p i{margin-right:4px;color:#4775a5}
        .student-save-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:43px;padding:11px 17px;border:0;border-radius:10px;background:linear-gradient(120deg,#17365d,#285b86);color:#fff;font:700 12px 'DM Sans',sans-serif;cursor:pointer;transition:.2s}
        .student-save-button:hover{transform:translateY(-1px);box-shadow:0 7px 16px rgba(23,54,93,.18)}
        .student-alert{display:flex;align-items:flex-start;gap:9px;margin-bottom:17px;padding:12px 14px;border-radius:11px;font-size:12px}
        .student-alert-error{border:1px solid #fecaca;background:#fef2f2;color:#991b1b}.student-alert-success{margin:16px 0 0;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534}
        .student-alert ul{margin:5px 0 0;padding-left:17px}
        .student-password-form{max-width:650px}
        .student-password-form .student-form-grid{grid-template-columns:1fr}
        .student-password-form .student-form-field{margin-bottom:14px}
        .student-profile-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:10px 3px 2px}
        .student-profile-footer span{color:#315f88;font-size:11px;font-weight:800;letter-spacing:.7px}
        .student-profile-footer span i{margin-right:6px}
        .student-profile-footer p{margin:0;color:#91a0b3;font-size:10px}
        .student-empty-state{text-align:center;padding:55px 24px}
        .student-empty-icon{display:grid;width:60px;height:60px;margin:0 auto 15px;place-items:center;border-radius:18px;background:#eef5fb;color:#315f88;font-size:26px}
        .student-empty-state h3{margin:0 0 7px;color:#17365d;font-size:18px;font-weight:800}
        .student-empty-state p{max-width:520px;margin:0 auto;color:#8291a7;font-size:13px;line-height:1.7}
        @media(max-width:800px){.student-hero-content{flex-wrap:wrap;padding:25px}.student-hero-action{margin-left:110px}.student-readonly-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:560px){.student-profile-panel{padding:19px 16px}.student-hero-content{gap:15px}.student-avatar{width:70px;height:70px;flex-basis:70px;border-radius:20px;font-size:29px}.student-hero-action{margin-left:0;width:100%;justify-content:center}.student-readonly-grid,.student-form-grid{grid-template-columns:1fr}.student-form-field-wide{grid-column:auto}.student-panel-heading{align-items:flex-start;flex-wrap:wrap}.student-lock-label,.student-edit-label{margin-left:56px}.student-form-footer{align-items:stretch;flex-direction:column}.student-save-button{width:100%}.student-profile-footer{align-items:flex-start;flex-direction:column}}
    </style>
</x-app-layout>