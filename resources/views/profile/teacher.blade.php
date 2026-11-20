<x-app-layout>
    <x-slot name="header">
        <div class="teacher-profile-topbar">
            <div>
                <span class="teacher-eyebrow">SMAS METHODIST BANDA ACEH</span>
                <h2>Profil Guru</h2>
            </div>

            <span class="teacher-topbar-icon">
                <i class="bi bi-person-workspace"></i>
            </span>
        </div>
    </x-slot>

    @php
        $initial = mb_strtoupper(mb_substr($user->name ?? 'G', 0, 1));

        $employmentStatusLabels = [
            'aktif' => 'Aktif',
            'cuti' => 'Cuti',
            'pensiun' => 'Pensiun',
            'resign' => 'Mengundurkan diri',
        ];

        $employmentStatus = $employmentStatusLabels[$teacher?->status ?? ''] ?? ($teacher?->status ?? 'Belum diatur');

        $teacherDetails = $teacher ? [
            ['label' => 'Nama lengkap', 'value' => $user->name],
            ['label' => 'Nomor Induk Pegawai (NIP)', 'value' => $teacher->nip],
            ['label' => 'Spesialisasi / bidang studi', 'value' => $teacher->specialization],
            ['label' => 'Pendidikan terakhir', 'value' => $teacher->education_level],
            ['label' => 'Tanggal bergabung', 'value' => $teacher->hire_date?->format('d-m-Y')],
            ['label' => 'Status kepegawaian', 'value' => $employmentStatus],
        ] : [];
    @endphp

    <div class="teacher-profile-page">

        @if (! $teacher)

            <section class="teacher-panel teacher-empty-state">
                <span class="teacher-empty-icon">
                    <i class="bi bi-person-exclamation"></i>
                </span>

                <h3>Data profil guru belum tersedia</h3>

                <p>
                    Akun Anda belum terhubung dengan data guru.
                    Silakan hubungi administrator sekolah untuk pemeriksaan data.
                </p>
            </section>

        @else

            <section class="teacher-hero">
                <div class="teacher-hero-pattern"></div>

                <div class="teacher-hero-content">
                    <div class="teacher-avatar">{{ $initial }}</div>

                    <div class="teacher-identity">
                        <span class="teacher-hero-kicker">TEACHER PROFILE</span>

                        <h1>{{ $user->name }}</h1>

                        <p>
                            <i class="bi bi-person-badge"></i>
                            NIP {{ $teacher->nip }}
                        </p>

                        <div class="teacher-hero-tags">
                            <span>
                                <i class="bi bi-book-half"></i>
                                {{ $teacher->specialization ?: 'Bidang studi belum diisi' }}
                            </span>

                            <span class="teacher-status-pill {{ $teacher->status === 'aktif' ? 'is-active' : '' }}">
                                <i class="bi bi-circle-fill"></i>
                                {{ $employmentStatus }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="teacher-profile-grid">

                <section class="teacher-panel" id="teacher-official-data">

                    <div class="teacher-panel-heading">
                        <span class="teacher-panel-icon icon-navy">
                            <i class="bi bi-person-vcard-fill"></i>
                        </span>

                        <div>
                            <h3>Identitas dan Data Kepegawaian</h3>
                            <p>
                                Data resmi guru yang tercatat pada sistem administrasi sekolah.
                            </p>
                        </div>

                        <span class="teacher-lock-label">
                            <i class="bi bi-lock-fill"></i> Admin saja
                        </span>
                    </div>

                    <div class="teacher-readonly-grid">
                        @foreach ($teacherDetails as $detail)
                            <div class="teacher-readonly-item">
                                <span>{{ $detail['label'] }}</span>

                                <strong>
                                    {{ filled($detail['value']) ? $detail['value'] : 'Belum diisi' }}
                                </strong>

                                <small>
                                    <i class="bi bi-lock-fill"></i>
                                    Tidak dapat diubah guru
                                </small>
                            </div>
                        @endforeach
                    </div>

                    <div class="teacher-protected-note">
                        <i class="bi bi-shield-lock-fill"></i>

                        <p>
                            NIP, nama resmi, spesialisasi, pendidikan terakhir,
                            tanggal bergabung, dan status kepegawaian hanya dapat
                            diperbarui oleh administrator sekolah.
                        </p>
                    </div>

                </section>

                <section class="teacher-panel" id="teacher-contact-data">

                    <div class="teacher-panel-heading">
                        <span class="teacher-panel-icon icon-blue">
                            <i class="bi bi-pencil-square"></i>
                        </span>

                        <div>
                            <h3>Informasi Kontak</h3>
                            <p>
                                Informasi berikut dapat diperbarui sendiri oleh guru.
                            </p>
                        </div>

                        <span class="teacher-edit-label">
                            <i class="bi bi-unlock-fill"></i> Bisa diedit
                        </span>
                    </div>

                    @if ($errors->any())
                        <div class="teacher-alert teacher-alert-error">
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
                        class="teacher-profile-form">

                        @csrf
                        @method('PATCH')

                        <div class="teacher-form-grid">

                            <div class="teacher-form-field">
                                <label for="teacher_email">
                                    Email akun <span>*</span>
                                </label>

                                <input
                                    id="teacher_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    maxlength="255"
                                    required
                                    autocomplete="email"
                                >

                                @error('email')
                                    <small class="teacher-field-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="teacher-form-field">
                                <label for="teacher_phone">Nomor HP</label>

                                <input
                                    id="teacher_phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone', $user->phone) }}"
                                    maxlength="20"
                                    autocomplete="tel"
                                    placeholder="Masukkan nomor HP"
                                >

                                @error('phone')
                                    <small class="teacher-field-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="teacher-form-field teacher-form-field-wide">
                                <label for="teacher_address">Alamat</label>

                                <textarea
                                    id="teacher_address"
                                    name="address"
                                    rows="3"
                                    maxlength="255"
                                    placeholder="Masukkan alamat tempat tinggal"
                                >{{ old('address', $teacher->address) }}</textarea>

                                @error('address')
                                    <small class="teacher-field-error">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                        <div class="teacher-form-footer">
                            <p>
                                <i class="bi bi-info-circle"></i>
                                Perubahan hanya berlaku untuk email, nomor HP,
                                dan alamat. Data kepegawaian tetap dikelola administrator.
                            </p>

                            <button type="submit" class="teacher-save-button">
                                <i class="bi bi-check2-circle"></i>
                                Simpan Perubahan
                            </button>
                        </div>

                        @if (session('status') === 'profile-updated')
                            <div class="teacher-alert teacher-alert-success">
                                <i class="bi bi-check-circle-fill"></i>
                                Informasi profil berhasil diperbarui.
                            </div>
                        @endif

                    </form>
                </section>

                <section class="teacher-panel" id="teacher-password">

                    <div class="teacher-panel-heading">
                        <span class="teacher-panel-icon icon-purple">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>

                        <div>
                            <h3>Keamanan Akun</h3>
                            <p>
                                Ganti kata sandi secara berkala untuk menjaga keamanan akun sekolah.
                            </p>
                        </div>
                    </div>

                    @include('profile.partials.update-password-form')

                </section>

            </div>

        @endif

        <footer class="teacher-profile-footer">
            <span>
                <i class="bi bi-mortarboard-fill"></i>
                SMAS METHODIST BANDA ACEH
            </span>

            <p>Portal Pembelajaran Digital · Profil Guru</p>
        </footer>

    </div>

    <style>
        .teacher-profile-page{max-width:1180px;margin:0 auto;color:#203b5c;font-family:'DM Sans',sans-serif}
        .teacher-profile-topbar{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .teacher-profile-topbar h2{margin:4px 0 0;color:#17365d;font-size:22px;font-weight:800}
        .teacher-eyebrow{color:#6d8eaf;font-size:10px;font-weight:800;letter-spacing:1.6px}
        .teacher-topbar-icon{display:grid;width:44px;height:44px;place-items:center;border-radius:13px;background:#e8f0f8;color:#17365d;font-size:20px}
        .teacher-hero{position:relative;overflow:hidden;margin-bottom:18px;border-radius:22px;background:linear-gradient(115deg,#102744 0%,#173e68 58%,#315f88 100%);color:#fff;box-shadow:0 12px 30px rgba(16,39,68,.13)}
        .teacher-hero-pattern{position:absolute;inset:0;opacity:.12;background-image:linear-gradient(rgba(255,255,255,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.3) 1px,transparent 1px);background-size:32px 32px}
        .teacher-hero-content{position:relative;display:flex;align-items:center;gap:22px;padding:30px;min-height:170px}
        .teacher-avatar{display:grid;width:82px;height:82px;flex:0 0 82px;place-items:center;border:3px solid rgba(255,255,255,.5);border-radius:25px;background:rgba(255,255,255,.15);font-size:34px;font-weight:800}
        .teacher-identity{min-width:0}.teacher-hero-kicker{font-size:10px;font-weight:800;letter-spacing:1.8px;color:#b8d2ed}
        .teacher-identity h1{margin:5px 0 3px;font-size:clamp(22px,3vw,30px);font-weight:800;color:#fff}
        .teacher-identity>p{margin:0;color:#d8e7f6;font-size:13px}.teacher-hero-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:13px}
        .teacher-hero-tags span{display:inline-flex;align-items:center;gap:7px;padding:7px 11px;border:1px solid rgba(255,255,255,.2);border-radius:999px;background:rgba(255,255,255,.1);font-size:11px;font-weight:700;color:#fff}
        .teacher-status-pill.is-active{background:rgba(34,197,94,.18);border-color:rgba(134,239,172,.35)}
        .teacher-profile-grid{display:grid;grid-template-columns:1fr;gap:18px}.teacher-panel{padding:23px;border:1px solid #e1e8f0;border-radius:18px;background:#fff;box-shadow:0 5px 18px rgba(16,39,68,.05)}
        .teacher-panel-heading{display:flex;align-items:center;gap:13px;margin-bottom:19px}.teacher-panel-icon{display:grid;width:42px;height:42px;flex:0 0 42px;place-items:center;border-radius:13px;font-size:18px}
        .teacher-panel-icon.icon-navy{background:#e8eef7;color:#17365d}.teacher-panel-icon.icon-blue{background:#e6f1fb;color:#316b9d}.teacher-panel-icon.icon-purple{background:#f0eafb;color:#7550a5}
        .teacher-panel-heading h3{margin:0;color:#17365d;font-size:16px;font-weight:800}.teacher-panel-heading p{margin:4px 0 0;color:#71849a;font-size:12px}
        .teacher-lock-label,.teacher-edit-label{margin-left:auto;white-space:nowrap;border-radius:999px;padding:6px 9px;font-size:10px;font-weight:800}
        .teacher-lock-label{background:#f1f4f8;color:#64748b}.teacher-edit-label{background:#eaf6ef;color:#27804b}
        .teacher-readonly-grid,.teacher-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
        .teacher-readonly-item{padding:14px;border:1px solid #e8edf3;border-radius:12px;background:#f9fbfd;min-width:0}
        .teacher-readonly-item>span{display:block;color:#7b8da2;font-size:11px;font-weight:700}.teacher-readonly-item strong{display:block;margin-top:6px;color:#233e5c;font-size:14px;overflow-wrap:anywhere}
        .teacher-readonly-item small{display:block;margin-top:8px;color:#9aa8b7;font-size:10px}.teacher-protected-note{display:flex;gap:10px;align-items:flex-start;margin-top:15px;padding:13px;border-radius:11px;background:#f1f5f9;color:#64748b}
        .teacher-protected-note i{margin-top:2px;color:#426487}.teacher-protected-note p{margin:0;font-size:12px;line-height:1.6}
        .teacher-form-field{display:flex;flex-direction:column;gap:7px;min-width:0}.teacher-form-field-wide{grid-column:1/-1}.teacher-form-field label{color:#294663;font-size:12px;font-weight:700}
        .teacher-form-field label span{color:#d34b4b}.teacher-form-field input,.teacher-form-field textarea{width:100%;border:1px solid #d7e1ec;border-radius:10px;background:#f8fbff;padding:11px 13px;color:#203b5c;font:inherit;font-size:13px;outline:none;transition:border-color .2s,box-shadow .2s}
        .teacher-form-field input:focus,.teacher-form-field textarea:focus{border-color:#6f9fc9;box-shadow:0 0 0 3px rgba(111,159,201,.14)}.teacher-form-field textarea{resize:vertical}
        .teacher-field-error{color:#c43e48;font-size:11px}.teacher-form-footer{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-top:18px;padding-top:16px;border-top:1px solid #edf1f5}
        .teacher-form-footer p{margin:0;color:#7c8ea1;font-size:11px;line-height:1.6}.teacher-form-footer p i{margin-right:5px;color:#537ba3}
        .teacher-save-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;flex:0 0 auto;border:0;border-radius:10px;background:#17365d;padding:11px 16px;color:#fff;font-size:12px;font-weight:800;cursor:pointer;transition:background .2s}
        .teacher-save-button:hover{background:#244b77}.teacher-alert{display:flex;gap:10px;align-items:flex-start;margin:0 0 16px;padding:12px 14px;border-radius:11px;font-size:12px}
        .teacher-alert-error{background:#fff1f1;color:#a7353d}.teacher-alert-success{margin-top:15px;background:#edf9f1;color:#287a48}.teacher-alert ul{margin:6px 0 0;padding-left:17px}
        .teacher-empty-state{text-align:center;padding:45px 22px}.teacher-empty-icon{display:inline-grid;width:58px;height:58px;place-items:center;border-radius:18px;background:#edf3f9;color:#426487;font-size:25px}
        .teacher-empty-state h3{margin:15px 0 6px;color:#17365d;font-size:18px;font-weight:800}.teacher-empty-state p{max-width:480px;margin:0 auto;color:#71849a;font-size:13px;line-height:1.7}
        .teacher-profile-footer{display:flex;justify-content:space-between;gap:12px;margin-top:22px;padding:16px 3px;color:#8294a8;font-size:10px}.teacher-profile-footer span{font-weight:800;letter-spacing:.6px}.teacher-profile-footer p{margin:0}
        @media(max-width:700px){.teacher-panel{padding:17px}.teacher-hero-content{padding:22px;gap:15px}.teacher-avatar{width:66px;height:66px;flex-basis:66px;border-radius:20px}.teacher-readonly-grid,.teacher-form-grid{grid-template-columns:1fr}.teacher-form-field-wide{grid-column:auto}.teacher-panel-heading{flex-wrap:wrap}.teacher-lock-label,.teacher-edit-label{margin-left:55px}.teacher-form-footer{align-items:stretch;flex-direction:column}.teacher-save-button{width:100%}.teacher-profile-footer{flex-direction:column}}
    </style>
</x-app-layout>