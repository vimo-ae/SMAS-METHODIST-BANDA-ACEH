<section
    x-data="{
        photoPreview: '{{ $user->photo ? asset('storage/' . $user->photo) : '' }}',
        removePhoto: false,
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.photoPreview = e.target.result;
                this.removePhoto = false;
            };
            reader.readAsDataURL(file);
        },
        clearPhoto() {
            this.photoPreview = '';
            this.removePhoto = true;
            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }
        }
    }"
>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        {{-- Hidden input for photo removal --}}
        <input type="hidden" name="remove_photo" :value="removePhoto ? 1 : 0">

        {{-- FOTO PROFIL SECTION --}}
        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-5 sm:p-6 transition hover:border-slate-200">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3">
                Foto Profil Akun
            </label>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                {{-- PREVIEW AVATAR --}}
                <div class="relative group shrink-0">
                    <div class="h-20 w-20 sm:h-24 sm:w-24 rounded-full border-2 border-white shadow-md overflow-hidden bg-slate-900 flex items-center justify-center">
                        <template x-if="photoPreview && !removePhoto">
                            <img :src="photoPreview" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!photoPreview || removePhoto">
                            <span class="bg-gradient-to-br from-[#123B60] to-[#267BB5] text-white font-serif font-extrabold text-2xl sm:text-3xl h-full w-full flex items-center justify-center select-none">
                                {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                            </span>
                        </template>
                    </div>

                    {{-- QUICK CLICK OVERLAY --}}
                    <button
                        type="button"
                        @click="$refs.photoInput.click()"
                        class="absolute inset-0 rounded-full bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-150"
                        title="Klik untuk memilih foto"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                {{-- ACTION BUTTONS dan HINT --}}
                <div class="flex-1 space-y-2">
                    <input
                        type="file"
                        x-ref="photoInput"
                        name="photo"
                        id="photo"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="hidden"
                        @change="fileChosen($event)"
                    >

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            @click="$refs.photoInput.click()"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-400 active:scale-95"
                        >
                            <svg class="h-4 w-4 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Pilih Foto Baru</span>
                        </button>

                        <button
                            type="button"
                            x-show="photoPreview && !removePhoto"
                            @click="clearPhoto()"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 active:scale-95"
                        >
                            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Foto</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Mendukung format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB. Foto akan tampil di profil, dashboard, dan sidebar navigasi Anda.
                    </p>

                    @error('photo')
                        <p class="text-xs font-semibold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- NAMA LENGKAP --}}
        <div>
            <label for="name" class="block text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                Nama Lengkap
            </label>
            <input
                id="name"
                name="name"
                type="text"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition focus:border-[#267BB5] focus:outline-none focus:ring-2 focus:ring-[#267BB5]/20"
                value="{{ old('name', $user->name) }}"
                required
                autocomplete="name"
            />
            @error('name')
                <p class="text-xs font-semibold text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- EMAIL TERDAFTAR --}}
        <div>
            <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-1.5">
                Alamat Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition focus:border-[#267BB5] focus:outline-none focus:ring-2 focus:ring-[#267BB5]/20"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            />
            @error('email')
                <p class="text-xs font-semibold text-rose-600 mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-xs">
                    <p class="text-amber-800">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="underline font-bold text-amber-900 hover:text-black">
                            {{ __('Klik di sini untuk mengirim ulang tautan verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-emerald-600">
                            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="flex items-center gap-4 pt-2 border-t border-slate-100">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#0B2B48] active:scale-95"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Perubahan</span>
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Profil berhasil diperbarui.</span>
                </div>
            @endif
        </div>
    </form>
</section>
