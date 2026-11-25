<form method="POST"
    action="{{ route('password.update') }}"
    class="student-password-form">

    @csrf
    @method('PUT')

    <div class="student-form-grid">

        <div class="student-form-field">
            <label for="student_current_password">
                Kata sandi saat ini
            </label>

            <input
                id="student_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                required
            >

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />
        </div>

        <div class="student-form-field">
            <label for="student_new_password">
                Kata sandi baru
            </label>

            <input
                id="student_new_password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
            >

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />
        </div>

        <div class="student-form-field">
            <label for="student_password_confirmation">
                Konfirmasi kata sandi baru
            </label>

            <input
                id="student_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
            >

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />
        </div>

    </div>

    <div class="student-form-footer">
        <p>
            <i class="bi bi-shield-check"></i>
            Gunakan kata sandi yang kuat dan jangan membagikannya kepada orang lain.
        </p>

        <button type="submit" class="student-save-button">
            <i class="bi bi-key-fill"></i>
            Perbarui Kata Sandi
        </button>
    </div>

    @if (session('status') === 'password-updated')
        <div class="student-alert student-alert-success">
            <i class="bi bi-check-circle-fill"></i>
            Kata sandi berhasil diperbarui.
        </div>
    @endif

</form>
