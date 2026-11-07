<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Edit Ujian / Kuis') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Perbarui detail informasi ujian.</p>
            </div>
            <a href="{{ route('guru.exams.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm ring-1 ring-slate-200 rounded-2xl">
                
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

                <form action="{{ route('guru.exams.update', $exam) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Kelas & Mata Pelajaran (Readonly / Disabled saat Edit) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas & Mata Pelajaran</label>
                        <input type="text" disabled 
                               value="{{ $exam->classSubjectTeacher?->schoolClass?->name ?? 'Kelas' }} — {{ $exam->classSubjectTeacher?->subject?->name ?? 'Mapel' }}"
                               class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-sm text-sm cursor-not-allowed">
                    </div>

                    <!-- Judul Ujian -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Ujian / Kuis</label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required placeholder="Contoh: UTS Matematika Semester Ganjil"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Grid Waktu Mulai & Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" 
                                   value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" 
                                   value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <!-- Grid Durasi & KKM -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Durasi Pengerjaan (Menit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="300" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nilai Kelulusan (KKM)</label>
                            <input type="number" step="0.1" name="pass_score" value="{{ old('pass_score', $exam->pass_score) }}" min="0" max="100" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <!-- Checkbox Pengaturan Acak -->
                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">Acak urutan soal untuk setiap siswa</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options', $exam->shuffle_options) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">Acak pilihan jawaban (opsi) ganda</span>
                        </label>
                    </div>

                    <!-- Instruksi -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instruksi Ujian (Opsional)</label>
                        <textarea name="instructions" rows="3" placeholder="Tuliskan petunjuk pengerjaan di sini..."
                                  class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('instructions', $exam->instructions) }}</textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('guru.exams.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>