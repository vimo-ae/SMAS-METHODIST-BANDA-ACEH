<x-login-layout>
    <div class="sl-card">
        <a href="{{ route('home') }}" class="sl-link inline-flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 12H5m7 7-7-7 7-7"/>
            </svg>
            Kembali ke Beranda
        </a>

        <div class="sl-card-head mt-3">
            <h1 class="sl-welcome">Selamat Datang Kembali</h1>
            <p class="sl-welcome-sub">
                Silakan masuk menggunakan akun sekolah Anda untuk melanjutkan
                aktivitas pembelajaran.
            </p>
        </div>

        {{-- Status sesi (mis. setelah kirim link reset password) --}}
        @if (session('status'))
            <div class="sl-status" role="status">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="sl-form">
            @csrf

            {{-- Email Sekolah --}}
            <div class="sl-field">
                <label for="email" class="sl-label">NIS / NIP / Email Sekolah</label>
                <div class="sl-input-wrap">
                    <svg class="sl-input-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <circle cx="9" cy="10" r="2"/>
                        <path d="M5.5 17a3.5 3.5 0 0 1 7 0M15 9h3M15 13h3"/>
                    </svg>
                    <input id="email" name="email" type="text"
                           value="{{ old('email') }}" required autocomplete="username"
                           placeholder="Masukkan NIS, NIP, atau email sekolah"
                           class="sl-input @error('email') sl-input--invalid @enderror"
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                </div>
                @error('email')
                    <p id="email-error" class="sl-error" role="alert">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password + tombol show/hide (Alpine.js) --}}
            <div class="sl-field" x-data="{ show: false }">
                <label for="password" class="sl-label">Kata Sandi</label>
                <div class="sl-input-wrap">
                    <svg class="sl-input-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="11" width="16" height="10" rx="2"/>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                    <input id="password" name="password" required autocomplete="current-password"
                           placeholder="Masukkan kata sandi"
                           :type="show ? 'text' : 'password'"
                           class="sl-input sl-input--password @error('password') sl-input--invalid @enderror"
                           @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    <button type="button" class="sl-toggle" @click="show = !show"
                            :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            :aria-pressed="show.toString()">
                        {{-- mata (tampilkan) --}}
                        <svg x-show="!show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        {{-- mata dicoret (sembunyikan) --}}
                        <svg x-show="show" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9.9 4.24A9.1 9.1 0 0 1 12 4c6.5 0 10 7 10 7a13.2 13.2 0 0 1-2.16 2.88"/>
                            <path d="M6.5 6.5A13.3 13.3 0 0 0 2 11s3.5 7 10 7a9 9 0 0 0 4-.9"/>
                            <path d="M3 3l18 18"/><path d="M9.5 9.5a3 3 0 0 0 4.2 4.2"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p id="password-error" class="sl-error" role="alert">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Ingat saya + Lupa kata sandi --}}
            <div class="sl-row">
                <label for="remember_me" class="sl-check">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="sl-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>

            {{-- Tombol utama --}}
            <button type="submit" class="sl-btn">
                Masuk
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
        </form>

        {{-- Info akun sekolah: akun dibuat oleh pihak sekolah --}}
        <div class="sl-help">
            <svg class="sl-help-ic" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
            </svg>
            <span>
                Akun pembelajaran dibuat oleh pihak sekolah. Belum memiliki akun?
                Silakan <strong class="sl-help-contact">hubungi administrator sekolah</strong>.
            </span>
        </div>

        <div class="sl-divider"></div>
        <p class="sl-copy">&copy; {{ date('Y') }} {{ config('school.name', 'SMAS Methodist Banda Aceh') }}</p>
    </div>
</x-login-layout>