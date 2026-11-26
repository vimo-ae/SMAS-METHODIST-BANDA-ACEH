<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">Methodist Learning Space</p>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Buat Ujian / Kuis Baru') }}
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    Pembuatan Asesmen Digital
                </span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#FAF9F6] py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-4">
            {{-- BREADCRUMB dan TOMBOL KEMBALI --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('guru.exams.index') }}" class="transition hover:text-blue-600">
                        E-Exam
                    </a>
                    <span>/</span>
                    <span class="font-bold text-blue-700">Buat Ujian Baru</span>
                </div>

                <a href="{{ route('guru.exams.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>←</span>
                    <span>Kembali ke Daftar Ujian</span>
                </a>
            </div>

            <div class="rounded-3xl border border-[#E7EDF2] bg-white p-8 shadow-sm">
                
                <!-- Menampilkan Error Validasi jika ada -->
                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                        <font class="font-semibold">Terjadi kesalahan:</font>
                        <ul class="mt-1 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('guru.exams.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Pilih Kelas dan Mata Pelajaran -->
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-sm font-semibold text-slate-700">Kelas dan Mata Pelajaran</label>
                            <button type="button" id="add-class-row" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">
                                + Tambah Kelas
                            </button>
                        </div>
                        <div id="class-rows" class="space-y-3">
                            @foreach(old('cst_ids', ['']) as $selectedCstId)
                                <div class="flex items-center gap-2" data-class-row>
                                    <select name="cst_ids[]" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                                        <option value="">-- Pilih Kelas dan Mapel --</option>
                                        @foreach($csts as $cst)
                                            <option value="{{ $cst->cst_id }}" {{ $selectedCstId == $cst->cst_id ? 'selected' : '' }}>
                                                {{ $cst->schoolClass->name }} — {{ $cst->subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="remove-class-row shrink-0 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50" aria-label="Hapus pilihan kelas">
                                        Hapus
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <template id="class-row-template">
                        <div class="flex items-center gap-2" data-class-row>
                            <select name="cst_ids[]" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                                <option value="">-- Pilih Kelas dan Mapel --</option>
                                @foreach($csts as $cst)
                                    <option value="{{ $cst->cst_id }}">
                                        {{ $cst->schoolClass->name }} — {{ $cst->subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="remove-class-row shrink-0 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50" aria-label="Hapus pilihan kelas">
                                Hapus
                            </button>
                        </div>
                    </template>

                    <!-- Judul Ujian -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Ujian / Kuis</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: UTS Matematika Semester Ganjil"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                    </div>

                    <!-- Jenis Ujian -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jenis Evaluasi</label>
                        <select name="exam_type" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                            <option value="kuis" {{ old('exam_type') == 'kuis' ? 'selected' : '' }}>Kuis / Ulangan Harian</option>
                            <option value="uts" {{ old('exam_type') == 'uts' ? 'selected' : '' }}>UTS (Ujian Tengah Semester)</option>
                            <option value="uas" {{ old('exam_type') == 'uas' ? 'selected' : '' }}>UAS (Ujian Akhir Semester)</option>
                        </select>
                    </div>

                    <!-- Grid Waktu Mulai dan Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                    </div>

                    <!-- Grid Durasi dan KKM -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Durasi Pengerjaan (Menit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 90) }}" min="5" max="300" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nilai Kelulusan (KKM)</label>
                            <input type="number" step="0.1" name="pass_score" value="{{ old('pass_score', 75) }}" min="0" max="100" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                    </div>

                    <!-- Checkbox Pengaturan Acak -->
                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-[#123B60] focus:ring-[#267BB5]">
                            <span class="text-sm text-slate-700">Acak urutan soal untuk setiap siswa</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-[#123B60] focus:ring-[#267BB5]">
                            <span class="text-sm text-slate-700">Acak pilihan jawaban (opsi) ganda</span>
                        </label>
                    </div>

                    <!-- Instruksi -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instruksi Ujian (Opsional)</label>
                        <textarea name="instructions" rows="3" placeholder="Tuliskan petunjuk pengerjaan di sini..."
                                  class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">{{ old('instructions') }}</textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('guru.exams.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#123B60] text-sm font-semibold text-white shadow-sm hover:bg-[#267BB5]">
                            Simpan dan Lanjutkan Tambah Soal
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const classRows = document.getElementById('class-rows');
        const classRowTemplate = document.getElementById('class-row-template');

        document.getElementById('add-class-row').addEventListener('click', () => {
            classRows.appendChild(classRowTemplate.content.cloneNode(true));
        });

        classRows.addEventListener('click', (event) => {
            const removeButton = event.target.closest('.remove-class-row');
            if (removeButton && classRows.querySelectorAll('[data-class-row]').length > 1) {
                removeButton.closest('[data-class-row]').remove();
            }
        });
    </script>
</x-app-layout>