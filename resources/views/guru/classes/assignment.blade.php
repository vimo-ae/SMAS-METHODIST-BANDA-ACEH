<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('guru.dashboard') }}" class="hover:text-[#123B60] transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('guru.classes.index') }}" class="hover:text-[#123B60] transition">E-Class</a>
                    <span>/</span>
                    <a href="{{ route('guru.classes.show', $cst) }}" class="hover:text-[#123B60] transition">{{ $cst->schoolClass->name ?? 'Kelas' }}</a>
                    <span>/</span>
                    <span class="text-slate-600">Pengumpulan Tugas</span>
                </nav>
                <h1 class="mt-1 text-2xl font-black text-[#101C35]">
                    Pengumpulan Tugas Siswa
                </h1>
            </div>

            <a href="{{ route('guru.classes.show', $cst) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Ruang Kelas</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- NOTIFIKASI SUKSES / ERROR --}}
            @if(session('success'))
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Gagal memperbarui nilai:</p>
                        <ul class="mt-1 list-inside list-disc text-xs">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- HERO KARTU TUGAS --}}
            @php
                $subjectName = $cst->subject->name ?? $cst->subject_code ?? 'Mata Pelajaran';
                $subjectLower = strtolower($subjectName . ' ' . ($cst->subject_code ?? ''));
                $subjectCoverImage = null;
                $subjectCoverMap = [
                    'matematika' => 'matematika.jpg', 'mtk' => 'matematika.jpg',
                    'biologi' => 'biologi.jpg', 'bio' => 'biologi.jpg',
                    'kimia' => 'kimia.jpg', 'kim' => 'kimia.jpg',
                    'fisika' => 'fisika.jpg', 'fis' => 'fisika.jpg',
                    'indonesia' => 'indonesia.jpg', 'bin' => 'indonesia.jpg', 'bahasa indonesia' => 'indonesia.jpg',
                    'informatika' => 'informatika.jpg', 'tik' => 'informatika.jpg', 'inf' => 'informatika.jpg',
                    'inggris' => 'inggris.svg', 'big' => 'inggris.svg',
                    'sejarah' => 'sejarah.svg', 'sej' => 'sejarah.svg',
                    'geografi' => 'geografi.svg', 'geo' => 'geografi.svg',
                    'ekonomi' => 'ekonomi.svg', 'eko' => 'ekonomi.svg',
                    'sosiologi' => 'sosiologi.svg', 'sos' => 'sosiologi.svg',
                    'agama' => 'agama.svg', 'agm' => 'agama.svg',
                    'pjok' => 'pjok.svg', 'penjas' => 'pjok.svg',
                    'seni' => 'seni-budaya.svg', 'sbd' => 'seni-budaya.svg',
                    'ppkn' => 'ppkn.svg', 'pkn' => 'ppkn.svg',
                ];
                foreach ($subjectCoverMap as $k => $f) {
                    if (str_contains($subjectLower, $k)) {
                        $jpg = preg_replace('/\.(svg|png)$/i', '.jpg', $f);
                        $svg = preg_replace('/\.(jpg|png)$/i', '.svg', $f);
                        if (file_exists(public_path('images/subjects/' . $jpg))) {
                            $subjectCoverImage = asset('images/subjects/' . $jpg);
                            break;
                        } elseif (file_exists(public_path('images/subjects/' . $svg))) {
                            $subjectCoverImage = asset('images/subjects/' . $svg);
                            break;
                        }
                    }
                }
                if (!$subjectCoverImage && file_exists(public_path('images/hero-school.jpg'))) {
                    $subjectCoverImage = asset('images/hero-school.jpg');
                }
            @endphp

            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-[#10243E] p-6 sm:p-8 text-white shadow-xl">
                @if($subjectCoverImage)
                    <img src="{{ $subjectCoverImage }}" alt="{{ $subjectName }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/85 via-[#102a4e]/65 to-[#164273]/45"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123B60] via-[#1A4C7A] to-[#267BB5]"></div>
                @endif

                {{-- DEKORATIF AKSEN --}}
                <div class="pointer-events-none absolute -right-16 -top-28 h-80 w-80 rounded-full border-[35px] border-white/5"></div>
                <div class="pointer-events-none absolute -bottom-36 right-52 h-72 w-72 rounded-full bg-blue-400/10 blur-2xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="max-w-3xl space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-cyan-200 backdrop-blur-sm">
                                {{ $cst->schoolClass->name ?? 'Kelas' }}
                            </span>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold text-white/95 backdrop-blur-sm">
                                {{ $subjectName }}
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                            {{ $assignment->title }}
                        </h2>

                        @if($assignment->description)
                            <div class="text-xs sm:text-sm text-blue-100/90 whitespace-pre-line leading-relaxed max-w-2xl bg-white/10 p-4 rounded-2xl border border-white/15 backdrop-blur-md">
                                {{ $assignment->description }}
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-blue-100 pt-2">
                            <div class="flex items-center gap-1.5 bg-black/25 px-3 py-1.5 rounded-xl border border-white/10 backdrop-blur-sm">
                                <svg class="h-4 w-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                </svg>
                                <span>Batas: <strong>{{ $assignment->due_date?->format('d M Y, H:i') ?? 'Tidak ditentukan' }} WIB</strong></span>
                            </div>

                            <div class="flex items-center gap-1.5 bg-black/25 px-3 py-1.5 rounded-xl border border-white/10 backdrop-blur-sm">
                                <svg class="h-4 w-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                <span>Skor Maksimal: <strong>{{ $assignment->max_score ?? 100 }}</strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- STAT KILAT PENGUMPULAN --}}
                    @php
                        $totalJoined = $cst->joinedStudents->count();
                        $submittedCount = $subs->count();
                        $gradedCount = $subs->whereNotNull('score')->count();
                    @endphp
                    <div class="grid grid-cols-2 gap-3 w-full md:w-auto shrink-0">
                        <div class="rounded-2xl bg-white/10 p-4 border border-white/15 backdrop-blur-md text-center min-w-[110px]">
                            <span class="text-2xl font-black text-white">{{ $submittedCount }}/{{ $totalJoined }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-1">Mengumpulkan</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4 border border-white/15 backdrop-blur-md text-center min-w-[110px]">
                            <span class="text-2xl font-black text-white">{{ $gradedCount }}/{{ $submittedCount }}</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200 mt-1">Sudah Dinilai</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR SISWA dan PENGUMPULAN --}}
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-6 py-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#123B60] text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-[#101C35]">
                                Berkas Pengumpulan dan Penilaian Siswa
                            </h3>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Unduh jawaban yang telah dikirim oleh siswa, berikan evaluasi nilai dan feedback per siswa.
                        </p>
                    </div>

                    <span class="self-start sm:self-auto rounded-full bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-[#123B60] border border-blue-200">
                        {{ $totalJoined }} Siswa Terdaftar
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($cst->joinedStudents as $student)
                        @php
                            $submission = $subs->get($student->nis);
                            $studentName = $student->user->name ?? $student->name ?? 'Siswa';
                        @endphp
                        <div class="p-6 transition hover:bg-slate-50/70">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                {{-- INFORMASI SISWA dan STATUS PENGUMPULAN --}}
                                <div class="flex items-start gap-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#123B60] to-[#267BB5] text-sm font-black text-white shadow-sm">
                                        {{ strtoupper(substr($studentName, 0, 2)) }}
                                    </div>

                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-sm font-extrabold text-slate-800">
                                                {{ $studentName }}
                                            </h4>
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 font-mono">
                                                NIS: {{ $student->nis }}
                                            </span>
                                        </div>

                                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                            @if($submission)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                                    <svg class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Sudah Mengumpulkan
                                                </span>
                                                <span class="text-[11px] text-slate-400">
                                                    Dikirim: {{ $submission->submitted_at?->format('d M Y, H:i') }} WIB
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 border border-amber-200">
                                                    <svg class="h-3 w-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                                                    </svg>
                                                    Belum Mengumpulkan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- AKSI UNDUH & LIHAT JAWABAN JIKA ADA --}}
                                @if($submission)
                                    <div class="flex items-center gap-2 self-start lg:self-center flex-wrap">
                                        <a href="{{ route('guru.classes.assignments.view', ['assignment' => $assignment->assignment_id, 'nis' => $student->nis]) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-2xs transition hover:bg-slate-50 hover:border-slate-300">
                                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Buka / Pratinjau</span>
                                        </a>

                                        <a href="{{ route('guru.classes.assignments.download', ['assignment' => $assignment->assignment_id, 'nis' => $student->nis]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-[#123B60] shadow-2xs transition hover:bg-blue-100">
                                            <svg class="h-3.5 w-3.5 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            <span>Unduh Berkas</span>
                                        </a>

                                        @if($submission->score !== null)
                                            <div class="flex items-center gap-1.5 rounded-xl bg-blue-50 px-3.5 py-1.5 border border-blue-200 text-xs">
                                                <span class="text-slate-500 font-semibold">Nilai:</span>
                                                <span class="font-extrabold text-[#123B60] text-sm">{{ $submission->score }}</span>
                                                <span class="text-[10px] text-slate-400">/ {{ $assignment->max_score ?? 100 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- FORMULIR PENILAIAN JIKA SUDAH MENGUMPULKAN --}}
                            @if($submission)
                                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                    <form action="{{ route('guru.classes.assignments.grade', [$assignment, $student->nis]) }}"
                                          method="POST"
                                          class="flex flex-col sm:flex-row sm:items-end gap-3">
                                        @csrf
                                        @method('PATCH')

                                        <div class="w-full sm:w-32">
                                            <label class="mb-1 block text-xs font-bold text-slate-700">
                                                Beri Nilai (0 - {{ $assignment->max_score ?? 100 }})
                                            </label>
                                            <input type="number"
                                                   name="score"
                                                   min="0"
                                                   max="{{ $assignment->max_score ?? 100 }}"
                                                   step="0.01"
                                                   value="{{ old('score', $submission->score) }}"
                                                   required
                                                   placeholder="Contoh: 85"
                                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-800 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5]">
                                        </div>

                                        <div class="flex-1">
                                            <label class="mb-1 block text-xs font-bold text-slate-700">
                                                Catatan / Feedback untuk Siswa
                                            </label>
                                            <input type="text"
                                                   name="feedback"
                                                   maxlength="1000"
                                                   value="{{ old('feedback', $submission->feedback) }}"
                                                   placeholder="Contoh: Jawaban sangat rapi dan lengkap. Pertahankan!"
                                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5]">
                                        </div>

                                        <div>
                                            <button type="submit"
                                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-1.5 rounded-xl bg-[#123B60] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5]">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>{{ $submission->score !== null ? 'Perbarui Nilai' : 'Simpan Nilai' }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-600">Belum ada siswa yang bergabung di kelas ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Siswa akan muncul di halaman ini setelah mendaftar ke kelas.</p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
