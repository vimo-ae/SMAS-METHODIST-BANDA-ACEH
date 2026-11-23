<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">Methodist Learning Space</p>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Edit Ujian / Kuis') }}
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    Perbarui Asesmen Digital
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
                    <span class="font-bold text-blue-700">Edit Ujian</span>
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

                <form action="{{ route('guru.exams.update', $exam) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Kelas dan Mata Pelajaran (Readonly / Disabled saat Edit) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas dan Mata Pelajaran</label>
                        <input type="text" disabled 
                               value="{{ $exam->classSubjectTeacher?->schoolClass?->name ?? 'Kelas' }} — {{ $exam->classSubjectTeacher?->subject?->name ?? 'Mapel' }}"
                               class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-sm text-sm cursor-not-allowed">
                    </div>

                    <!-- Judul Ujian -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Ujian / Kuis</label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required placeholder="Contoh: UTS Matematika Semester Ganjil"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                    </div>

                    <!-- Grid Waktu Mulai dan Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" 
                                   value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" 
                                   value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                    </div>

                    <!-- Grid Durasi dan KKM -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Durasi Pengerjaan (Menit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="300" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nilai Kelulusan (KKM)</label>
                            <input type="number" step="0.1" name="pass_score" value="{{ old('pass_score', $exam->pass_score) }}" min="0" max="100" required
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">
                        </div>
                    </div>

                    <!-- Checkbox Pengaturan Acak -->
                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-[#123B60] focus:ring-[#267BB5]">
                            <span class="text-sm text-slate-700">Acak urutan soal untuk setiap siswa</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options', $exam->shuffle_options) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-[#123B60] focus:ring-[#267BB5]">
                            <span class="text-sm text-slate-700">Acak pilihan jawaban (opsi) ganda</span>
                        </label>
                    </div>

                    <!-- Instruksi -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instruksi Ujian (Opsional)</label>
                        <textarea name="instructions" rows="3" placeholder="Tuliskan petunjuk pengerjaan di sini..."
                                  class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5] text-sm">{{ old('instructions', $exam->instructions) }}</textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('guru.exams.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#123B60] text-sm font-semibold text-white shadow-sm hover:bg-[#267BB5]">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>