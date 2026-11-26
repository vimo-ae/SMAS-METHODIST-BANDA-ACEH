<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">Methodist Learning Space</p>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Kelola Soal: {{ $exam->title }}
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $exam->classSubjectTeacher?->schoolClass?->name }} — {{ $exam->classSubjectTeacher?->subject?->name }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Script CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        .ck-editor__editable img,
        .ck-content img,
        .prose img {
            max-width: 380px !important;
            width: auto !important;
            height: auto !important;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border-radius: 0.75rem;
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        .ck-editor__editable_inline {
            min-height: 220px !important;
        }
        .ck-content figure.table,
        .prose figure.table {
            margin: 1em 0;
        }
        .ck-content table,
        .prose table {
            width: 100%;
            border-collapse: collapse;
        }
        .ck-content table th,
        .ck-content table td,
        .prose table th,
        .prose table td {
            border: 1px solid #94a3b8;
            padding: 0.45em 0.65em;
        }
        .ck-content table th,
        .prose table th {
            background: #f1f5f9;
            font-weight: 700;
        }
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-[#FAF9F6] py-8"
         x-data="{
            showForm: {{ ($errors->any() || $exam->questions->isEmpty()) ? 'true' : 'false' }},
            isEdit: false,
            editIndex: 1,
            editUrl: '',
            type: 'pilihan_ganda',
            options: ['', '', '', ''],
            singleKey: 1,
            multiKeys: [],
            bsItems: [{ text: '', flag: 'B' }, { text: '', flag: 'S' }],
            matchLeft: [{ text: '', target: 1 }],
            matchRight: [''],

            setEditorData(content) {
                if (window.editorInstance) {
                window.editorInstance.setData(content);
                return;
                }

                window.examQuestionEditorReady?.then(editor => {
                if (editor) editor.setData(content);
                });
            },

            toggleForm() {
                this.showForm = !this.showForm;
                if (this.showForm && !this.isEdit) {
                    this.resetForm();
                }
                if (this.showForm) {
                    this.$nextTick(() => {
                        window.dispatchEvent(new Event('resize'));
                        document.getElementById('form-container')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                }
            },

            addOption() { this.options.push(''); },
            removeOption(index) { if (this.options.length > 2) this.options.splice(index, 1); },
            addBs() { this.bsItems.push({ text: '', flag: 'B' }); },
            removeBs(index) { if (this.bsItems.length > 1) this.bsItems.splice(index, 1); },
            addMatchLeft() { this.matchLeft.push({ text: '', target: 1 }); },
            removeMatchLeft(index) { if (this.matchLeft.length > 1) this.matchLeft.splice(index, 1); },
            addMatchRight() { this.matchRight.push(''); },
            removeMatchRight(index) { if (this.matchRight.length > 1) this.matchRight.splice(index, 1); },

            resetForm() {
                this.isEdit = false;
                this.editIndex = 1;
                this.editUrl = '';
                this.type = 'pilihan_ganda';
                this.options = ['', '', '', ''];
                this.singleKey = 1;
                this.multiKeys = [];
                this.bsItems = [{ text: '', flag: 'B' }, { text: '', flag: 'S' }];
                this.matchLeft = [{ text: '', target: 1 }];
                this.matchRight = [''];
                this.setEditorData('');
            },

            loadEditData(q, updateUrl, index) {
                this.showForm = true;
                this.isEdit = true;
                this.editIndex = index || 1;
                this.editUrl = updateUrl;
                this.type = q.question_type;

                this.setEditorData(q.question_text || '');

                if (this.type === 'pilihan_ganda') {
                    this.options = Array.isArray(q.options) ? q.options : ['', '', '', ''];
                    this.singleKey = (q.correct_answer !== null) ? (parseInt(q.correct_answer) + 1) : 1;
                } else if (this.type === 'pilihan_ganda_kompleks') {
                    this.options = Array.isArray(q.options) ? q.options : ['', '', '', ''];
                    this.multiKeys = Array.isArray(q.correct_answer) ? q.correct_answer.map(k => parseInt(k) + 1) : [];
                } else if (this.type === 'benar_salah') {
                    if (Array.isArray(q.options)) {
                        this.bsItems = q.options.map((opt, i) => ({
                            text: opt,
                            flag: q.correct_answer && q.correct_answer[i] ? 'B' : 'S'
                        }));
                    }
                } else if (this.type === 'menjodohkan') {
                    if (q.options && q.options.left && q.options.right) {
                        this.matchRight = q.options.right;
                        this.matchLeft = q.options.left.map((lText, i) => ({
                            text: lText,
                            target: (q.correct_answer && q.correct_answer[i] !== undefined) ? (parseInt(q.correct_answer[i]) + 1) : 1
                        }));
                    }
                }

                this.$nextTick(() => {
                    window.dispatchEvent(new Event('resize'));
                    document.getElementById('form-container')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
         }">

        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">

            @if(isset($examToken) && $examToken)
                <div class="rounded-2xl border border-[#B9D5EA] bg-[#EAF4FB] p-5 shadow-sm"
                     id="exam-token-card"
                     data-expires="{{ $examToken->expires_at->toIso8601String() }}">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#267BB5]">Token Ujian Aktif</p>
                            <p class="mt-1 text-sm text-slate-600">Token diperbarui otomatis setiap 10 menit.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <code id="exam-token-value" class="rounded-xl border border-[#9DC3DE] bg-white px-5 py-3 font-mono text-2xl font-black tracking-[0.35em] text-[#123B60]">
                                {{ $examToken->plainToken() }}
                            </code>
                            <div class="text-right">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Berlaku sampai</p>
                                <p class="text-sm font-bold text-slate-700">{{ $examToken->expires_at->format('H:i:s') }}</p>
                                <p class="text-[10px] font-semibold text-[#267BB5]" id="exam-token-countdown"></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- BREADCRUMB dan TOMBOL KEMBALI --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('guru.exams.index') }}" class="transition hover:text-blue-600">
                        E-Exam
                    </a>
                    <span>/</span>
                    <span class="font-bold text-blue-700">Kelola Soal</span>
                </div>

                <a href="{{ route('guru.exams.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Daftar Ujian</span>
                </a>
            </div>

            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                     class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                     class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm font-semibold text-rose-800 flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <h3 class="text-sm font-bold text-rose-800">Gagal menyimpan soal:</h3>
                    </div>
                    <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($sourceExams->isNotEmpty())
                <div class="rounded-3xl border border-indigo-200 bg-indigo-50/70 p-5 sm:p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-base font-black text-indigo-950">Salin Soal dari Ujian Lain</h2>
                        <p class="mt-1 text-xs text-indigo-800">
                            Semua soal beserta opsi dan kunci jawaban akan ditambahkan. Soal yang sudah ada tetap tersimpan.
                        </p>
                    </div>
                    <form action="{{ route('guru.exams.questions.copy', $exam) }}" method="POST" class="flex flex-col gap-3 sm:flex-row" onsubmit="return confirm('Salin semua soal dari ujian yang dipilih? Soal yang sudah ada tidak akan dihapus.')">
                        @csrf
                        <select name="source_exam_id" required class="w-full rounded-xl border-indigo-200 bg-white text-sm shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5]">
                            <option value="">-- Pilih ujian sumber --</option>
                            @foreach ($sourceExams as $sourceExam)
                                <option value="{{ $sourceExam->exam_id }}" {{ old('source_exam_id') == $sourceExam->exam_id ? 'selected' : '' }}>
                                    {{ $sourceExam->title }} — {{ $sourceExam->classSubjectTeacher?->schoolClass?->name }} / {{ $sourceExam->classSubjectTeacher?->subject?->name }} ({{ $sourceExam->questions_count }} soal)
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="shrink-0 rounded-xl bg-indigo-700 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-800">
                            Salin Semua Soal
                        </button>
                    </form>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- TOP CONTROL BAR: INFORMASI SOAL dan TOMBOL TAMBAH/EDIT     --}}
            {{-- ======================================================== --}}
            @php
                $totalQuestions = $exam->questions->count();
                $essayCount = $exam->questions->where('question_type', 'esai')->count();
                $objectiveCount = $totalQuestions - $essayCount;
                $weightPerQuestion = $totalQuestions > 0 ? 100 / $totalQuestions : 0;
                $objectivePoints = round($objectiveCount * $weightPerQuestion, 2);
                $essayPoints = round($essayCount * $weightPerQuestion, 2);
            @endphp

            <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-[#123B60]">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <h2 class="text-lg font-black text-[#101C35]">
                                Bank Soal Ujian ({{ $totalQuestions }} Butir)
                            </h2>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-lg bg-blue-50 border border-blue-200 px-2.5 py-1 font-bold text-[#123B60]">
                                {{ $objectivePoints }} Poin Objektif
                            </span>
                            @if($essayCount > 0)
                                <span class="rounded-lg bg-amber-50 border border-amber-200 px-2.5 py-1 font-bold text-amber-700">
                                    {{ $essayPoints }} Poin Esai
                                </span>
                            @endif
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">
                                Durasi: {{ $exam->duration_minutes }} Menit
                            </span>
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">
                                KKM: {{ (int)$exam->pass_score }}
                            </span>
                        </div>
                    </div>

                    {{-- TOMBOL UTAMA: BUKA / TUTUP FORM TAMBAH dan EDIT SOAL --}}
                    <div>
                        <button type="button"
                                @click="toggleForm()"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-xs font-black shadow-md transition transform active:scale-95"
                                :class="showForm
                                    ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200'
                                    : 'bg-[#123B60] text-white hover:bg-[#267BB5]'">
                            <template x-if="!showForm">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>+ Tambah Soal Baru</span>
                                </span>
                            </template>
                            <template x-if="showForm">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span x-text="isEdit ? 'Batal Edit Soal #' + editIndex : 'Tutup Formulir Tambah Soal'"></span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- FORMULIR TAMBAH / EDIT SOAL (COLLAPSIBLE DI ATAS SOAL)   --}}
            {{-- ======================================================== --}}
            <div id="form-container"
                 x-show="showForm"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-lg space-y-6">

                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-800"
                                x-text="isEdit ? ('Edit Soal #' + editIndex) : 'Tambah Soal Baru untuk Ujian Ini'">
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                Susun redaksi pertanyaan, lampiran gambar, dan tentukan kunci jawaban yang benar.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            @click="toggleForm()"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        <span x-text="isEdit ? 'Batal Edit' : 'Tutup Formulir'"></span>
                    </button>
                </div>

                <form x-bind:action="isEdit ? editUrl : '{{ route('guru.exams.questions.store', $exam) }}'"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-5">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Hidden Inputs -->
                    <textarea name="options" hidden x-bind:value="
                        type === 'pilihan_ganda' || type === 'pilihan_ganda_kompleks'
                            ? options.join('\n')
                            : (type === 'benar_salah' ? bsItems.map(i => i.text + ' | ' + i.flag).join('\n') : '')
                    "></textarea>

                    <input type="hidden" name="key_single" x-bind:value="singleKey">
                    <input type="hidden" name="key_multi" x-bind:value="multiKeys.join(',')">
                    <textarea name="pair_left" hidden x-bind:value="matchLeft.map(i => i.text + ' | ' + i.target).join('\n')"></textarea>
                    <textarea name="pair_right" hidden x-bind:value="matchRight.join('\n')"></textarea>

                    <!-- Tipe Soal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Tipe Soal
                        </label>
                        <select name="question_type"
                                x-model="type"
                                required
                                class="w-full sm:max-w-md rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm focus:border-[#267BB5] focus:ring-[#267BB5]">
                            <option value="pilihan_ganda">Pilihan Ganda Biasa (1 Kunci)</option>
                            <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks (Banyak Kunci)</option>
                            <option value="benar_salah">Pernyataan Benar / Salah</option>
                            <option value="menjodohkan">Menjodohkan (Pairing Kiri dan Kanan)</option>
                            <option value="esai">Esai / Uraian Bebas</option>
                        </select>
                    </div>

                    <!-- Pertanyaan Soal (CKEditor 5) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Teks Pertanyaan Soal
                        </label>
                        <div class="mb-2 flex flex-wrap gap-2" role="toolbar" aria-label="Format teks">
                            <button type="button" data-editor-format="superscript" aria-pressed="false" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                Pangkat atas <span aria-hidden="true">x<sup>2</sup></span>
                            </button>
                            <button type="button" data-editor-format="subscript" aria-pressed="false" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                Pangkat bawah <span aria-hidden="true">x<sub>2</sub></span>
                            </button>
                        </div>
                        <textarea name="question_text" id="editor" class="w-full rounded-xl border-slate-300 text-xs">{{ old('question_text') }}</textarea>
                    </div>

                    <!-- UI Opsi Jawaban Sesuai Tipe -->
                    <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-5 space-y-4">

                        <!-- 1. PILIHAN GANDA BIASA -->
                        <div x-show="type === 'pilihan_ganda'" class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-xs font-bold text-slate-700">Pilihan Jawaban</label>
                                    <p class="text-[11px] text-slate-400">Pilih salah satu tombol radio sebagai kunci jawaban benar.</p>
                                </div>
                                <button type="button" @click="addOption()" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#123B60] bg-white border border-slate-200 px-3 py-1.5 rounded-xl hover:bg-blue-50 transition">
                                    + Tambah Opsi
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(opt, index) in options" :key="index">
                                    <div class="flex items-center justify-between gap-3 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-extrabold text-slate-700"
                                              x-text="String.fromCharCode(65 + index)">
                                        </span>
                                        <input type="text"
                                               x-model="options[index]"
                                               placeholder="Ketik isi pilihan jawaban..."
                                               :required="type === 'pilihan_ganda'"
                                               class="w-full rounded-lg border-slate-300 text-xs focus:border-[#267BB5] focus:ring-[#267BB5]">
                                        <label class="flex items-center gap-1.5 cursor-pointer text-xs font-bold px-3 py-1.5 rounded-lg border transition shrink-0"
                                               :class="singleKey === (index + 1) ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600'">
                                            <input type="radio"
                                                   name="radio_single_key"
                                                   :value="index + 1"
                                                   x-model.number="singleKey"
                                                   class="text-emerald-600 focus:ring-emerald-500">
                                            <span>Kunci Benar</span>
                                        </label>
                                        <button type="button"
                                                @click="removeOption(index)"
                                                x-show="options.length > 2"
                                                class="text-slate-400 hover:text-rose-600 text-lg font-bold px-1.5 transition">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 2. PILIHAN GANDA KOMPLEKS -->
                        <div x-show="type === 'pilihan_ganda_kompleks'" class="space-y-3" x-cloak>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-xs font-bold text-slate-700">Pilihan Jawaban (Multi Kunci)</label>
                                    <p class="text-[11px] text-slate-400">Centang semua opsi yang merupakan jawaban benar.</p>
                                </div>
                                <button type="button" @click="addOption()" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#123B60] bg-white border border-slate-200 px-3 py-1.5 rounded-xl hover:bg-blue-50 transition">
                                    + Tambah Opsi
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(opt, index) in options" :key="index">
                                    <div class="flex items-center justify-between gap-3 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-extrabold text-slate-700"
                                              x-text="String.fromCharCode(65 + index)">
                                        </span>
                                        <input type="text"
                                               x-model="options[index]"
                                               placeholder="Ketik isi pilihan jawaban..."
                                               class="w-full rounded-lg border-slate-300 text-xs focus:border-[#267BB5] focus:ring-[#267BB5]">
                                        <label class="flex items-center gap-1.5 cursor-pointer text-xs font-bold px-3 py-1.5 rounded-lg border transition shrink-0"
                                               :class="multiKeys.includes(index + 1) ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600'">
                                            <input type="checkbox"
                                                   :value="index + 1"
                                                   x-model.number="multiKeys"
                                                   class="rounded text-emerald-600 focus:ring-emerald-500">
                                            <span>Kunci Benar</span>
                                        </label>
                                        <button type="button"
                                                @click="removeOption(index)"
                                                x-show="options.length > 2"
                                                class="text-slate-400 hover:text-rose-600 text-lg font-bold px-1.5 transition">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 3. BENAR / SALAH -->
                        <div x-show="type === 'benar_salah'" class="space-y-3" x-cloak>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-xs font-bold text-slate-700">Daftar Pernyataan Benar / Salah</label>
                                    <p class="text-[11px] text-slate-400">Tulis pernyataan lalu pilih apakah bernilai Benar atau Salah.</p>
                                </div>
                                <button type="button" @click="addBs()" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#123B60] bg-white border border-slate-200 px-3 py-1.5 rounded-xl hover:bg-blue-50 transition">
                                    + Tambah Pernyataan
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(item, index) in bsItems" :key="index">
                                    <div class="flex items-center justify-between gap-3 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <input type="text"
                                               x-model="item.text"
                                               placeholder="Tuliskan teks pernyataan..."
                                               class="w-full rounded-lg border-slate-300 text-xs focus:border-[#267BB5] focus:ring-[#267BB5]">
                                        <select x-model="item.flag"
                                                class="rounded-lg border-slate-300 text-xs font-bold shrink-0"
                                                :class="item.flag === 'B' ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50'">
                                            <option value="B">Benar</option>
                                            <option value="S">Salah</option>
                                        </select>
                                        <button type="button"
                                                @click="removeBs(index)"
                                                x-show="bsItems.length > 1"
                                                class="text-slate-400 hover:text-rose-600 text-lg font-bold px-1.5 transition">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 4. MENJODOHKAN -->
                        <div x-show="type === 'menjodohkan'" class="space-y-4" x-cloak>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Daftar Pilihan Jawaban Kanan (Target)</label>
                                    <button type="button" @click="addMatchRight()" class="text-xs font-bold text-[#123B60] hover:underline">+ Tambah Opsi Kanan</button>
                                </div>
                                <template x-for="(rText, rIdx) in matchRight" :key="rIdx">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-500 w-5" x-text="(rIdx + 1) + '.'"></span>
                                        <input type="text" x-model="matchRight[rIdx]" placeholder="Opsi jawaban kanan..." class="w-full rounded-lg border-slate-300 text-xs">
                                        <button type="button" @click="removeMatchRight(rIdx)" x-show="matchRight.length > 1" class="text-rose-500 text-xs font-bold px-2">&times;</button>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-2 pt-3 border-t border-slate-200">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Daftar Pernyataan Kiri (Premis) dan Kunci Target</label>
                                    <button type="button" @click="addMatchLeft()" class="text-xs font-bold text-[#123B60] hover:underline">+ Tambah Premis Kiri</button>
                                </div>
                                <template x-for="(lItem, lIdx) in matchLeft" :key="lIdx">
                                    <div class="flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <input type="text" x-model="lItem.text" placeholder="Premis / pertanyaan kiri..." class="w-full rounded-lg border-slate-300 text-xs">
                                        <span class="text-xs text-slate-400 font-bold shrink-0">cocok ke</span>
                                        <select x-model.number="lItem.target" class="rounded-lg border-slate-300 text-xs font-bold shrink-0">
                                            <template x-for="(rText, rIdx) in matchRight" :key="rIdx">
                                                <option :value="rIdx + 1" x-text="'No. ' + (rIdx + 1)"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="removeMatchLeft(lIdx)" x-show="matchLeft.length > 1" class="text-rose-500 text-xs font-bold px-2">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 5. ESAI -->
                        <div x-show="type === 'esai'" class="text-xs text-slate-500 py-2 flex items-center gap-2" x-cloak>
                            <svg class="h-4 w-4 text-[#267BB5] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                            </svg>
                            <span>Soal tipe Esai tidak membutuhkan opsi jawaban. Jawaban siswa nantinya dikoreksi dan dinilai secara manual oleh guru pada menu Hasil Ujian.</span>
                        </div>

                    </div>

                    <!-- TOMBOL SIMPAN -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button"
                                @click="toggleForm()"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-6 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#267BB5] transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="isEdit ? 'Simpan Perubahan Soal' : 'Simpan Soal ke Bank Ujian'"></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ======================================================== --}}
            {{-- DAFTAR SOAL (FULL WIDTH, TAMPIL DULUAN)                  --}}
            {{-- ======================================================== --}}
            <div class="space-y-4">
                @forelse($exam->questions as $index => $q)
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-[#BBD7E9] hover:shadow-md">
                        
                        {{-- HEADER KARTU SOAL: NOMOR, TIPE, BOBOT, DAN TOMBOL AKSI --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-[#123B60] text-xs font-black text-white shadow-sm">
                                    #{{ $index + 1 }}
                                </span>
                                <span class="inline-flex items-center rounded-md bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-[11px] font-bold text-[#123B60]">
                                    {{ Str::headline($q->question_type) }}
                                </span>
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                    {{ number_format((float) $q->score_weight, 2) }} Poin
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click='loadEditData(@json($q), "{{ route("guru.exams.questions.update", [$exam, $q]) }}", {{ $index + 1 }})'
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-[#123B60] transition hover:bg-blue-100">
                                    <svg class="h-3.5 w-3.5 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('guru.exams.questions.destroy', [$exam, $q]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal nomor {{ $index + 1 }} ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100">
                                        <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- ISI PERTANYAAN dan GAMBAR --}}
                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="prose prose-slate max-w-none text-sm text-slate-800 leading-relaxed">
                                {!! $q->question_text !!}
                            </div>

                            @if ($q->image_path)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $q->image_path) }}"
                                         alt="Gambar soal"
                                         class="max-w-full max-h-72 rounded-2xl border border-slate-200 object-contain shadow-sm">
                                </div>
                            @endif

                            {{-- PREVIEW JAWABAN dan KUNCI --}}
                            @if($q->question_type === 'pilihan_ganda' && is_array($q->options))
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pilihan Jawaban:</p>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach($q->options as $optIdx => $optText)
                                            @php
                                                $isCorrect = ((string) $q->correct_answer === (string) $optIdx);
                                                $letter = chr(65 + $optIdx);
                                            @endphp
                                            <div class="flex items-start gap-2.5 rounded-xl p-2.5 text-xs border {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-semibold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md font-bold text-[11px] {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">
                                                    {{ $letter }}
                                                </span>
                                                <span class="flex-1 mt-0.5">{{ $optText }}</span>
                                                @if($isCorrect)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md shrink-0">
                                                        <svg class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span>Kunci Benar</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($q->question_type === 'pilihan_ganda_kompleks' && is_array($q->options))
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pilihan Jawaban (Multi Kunci):</p>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach($q->options as $optIdx => $optText)
                                            @php
                                                $isCorrect = is_array($q->correct_answer) && in_array($optIdx, $q->correct_answer);
                                                $letter = chr(65 + $optIdx);
                                            @endphp
                                            <div class="flex items-start gap-2.5 rounded-xl p-2.5 text-xs border {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-semibold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md font-bold text-[11px] {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">
                                                    {{ $letter }}
                                                </span>
                                                <span class="flex-1 mt-0.5">{{ $optText }}</span>
                                                @if($isCorrect)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md shrink-0">
                                                        <svg class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span>Kunci</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($q->question_type === 'benar_salah' && is_array($q->options))
                                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Daftar Pernyataan dan Kunci:</p>
                                    @foreach($q->options as $bsIdx => $bsText)
                                        @php
                                            $isTrue = !empty($q->correct_answer[$bsIdx]);
                                        @endphp
                                        <div class="flex items-center justify-between rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-xs">
                                            <span class="text-slate-800 font-medium">{{ $bsText }}</span>
                                            <span class="rounded-md px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $isTrue ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                Kunci: {{ $isTrue ? 'Benar' : 'Salah' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'menjodohkan' && isset($q->options['left']) && isset($q->options['right']))
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pasangan Menjodohkan yang Benar:</p>
                                    <div class="space-y-1.5 rounded-xl bg-slate-50 border border-slate-200 p-3 text-xs">
                                        @foreach($q->options['left'] as $lIdx => $lText)
                                            @php
                                                $targetIdx = $q->correct_answer[$lIdx] ?? 0;
                                                $rText = $q->options['right'][$targetIdx] ?? '-';
                                            @endphp
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <span class="font-bold text-[#123B60]">{{ $lText }}</span>
                                                <span class="text-slate-400">→</span>
                                                <span class="rounded bg-blue-100 px-2 py-0.5 font-bold text-blue-900">{{ $rText }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($q->question_type === 'esai')
                                <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-500 italic flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                                    </svg>
                                    <span>Tipe Esai / Uraian (jawaban dinilai manual oleh guru).</span>
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60]">
                            <svg class="h-7 w-7 text-[#123B60]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Soal di Ujian Ini</h3>
                        <p class="mt-1 text-xs text-slate-400 max-w-sm mx-auto">
                            Klik tombol di bawah atau tombol di bagian atas untuk mulai menambahkan butir soal pertama Anda.
                        </p>
                        <button type="button"
                                @click="showForm = true; $nextTick(() => document.getElementById('form-container')?.scrollIntoView({ behavior: 'smooth' }))"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#267BB5] transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat Soal Pertama Sekarang</span>
                        </button>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    @if(isset($examToken) && $examToken)
        <script>
            (() => {
                const card = document.getElementById('exam-token-card');
                const countdown = document.getElementById('exam-token-countdown');
                if (!card) return;

                let expiresAt = new Date(card.dataset.expires).getTime();
                let refreshed = false;
                const refreshToken = async () => {
                    if (refreshed) return;
                    refreshed = true;

                    try {
                        const response = await fetch(@json(route('guru.exams.token', $exam)), {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await response.json();

                        if (data.token) {
                            document.getElementById('exam-token-value').textContent = data.token;
                            const expiryText = document.querySelector('#exam-token-card p.text-sm.font-bold');
                            if (expiryText) expiryText.textContent = data.expires_at_display;

                            expiresAt = new Date(data.expires_at).getTime();
                            refreshed = false;
                        }
                    } catch (error) {
                        refreshed = false;
                    }
                };

                const tick = () => {
                    const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;

                    if (countdown) {
                        countdown.textContent = remaining > 0
                            ? `Refresh dalam ${minutes}:${String(seconds).padStart(2, '0')}`
                            : 'Token kedaluwarsa, memuat token baru...';
                    }

                    if (remaining === 0 && !refreshed) {
                        refreshToken();
                    }
                };

                tick();
                setInterval(tick, 1000);
            })();
        </script>
    @endif

    <!-- Init CKEditor 5 -->
    <script>
        const imageUploadUrl = @json(route('guru.exams.upload-image'));
        const csrfToken = @json(csrf_token());

        window.examQuestionEditorReady = ClassicEditor
            .create(document.querySelector('#editor'), {
                simpleUpload: {
                    uploadUrl: imageUploadUrl,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                },
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'insertTable', 'imageUpload', 'link', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                editor.model.schema.extend('$text', {
                    allowAttributes: ['superscript', 'subscript']
                });

                [
                    ['superscript', 'sup'],
                    ['subscript', 'sub']
                ].forEach(([attribute, tag]) => {
                    editor.conversion.for('upcast').elementToAttribute({
                        view: tag,
                        model: { key: attribute, value: true }
                    });
                    editor.conversion.for('downcast').attributeToElement({
                        model: attribute,
                        view: (value, { writer }) => value
                            ? writer.createAttributeElement(tag)
                            : null
                    });
                });

                window.editorInstance = editor;

                document.querySelectorAll('[data-editor-format]').forEach(button => {
                    button.addEventListener('mousedown', event => event.preventDefault());
                    button.addEventListener('click', () => {
                        const attribute = button.dataset.editorFormat;
                        const selection = editor.model.document.selection;
                        const isActive = selection.hasAttribute(attribute);

                        editor.model.change(writer => {
                            if (selection.isCollapsed) {
                                if (isActive) {
                                    writer.removeSelectionAttribute(attribute);
                                } else {
                                    writer.setSelectionAttribute(attribute, true);
                                }
                            } else if (isActive) {
                                writer.removeAttribute(attribute, selection.getFirstRange());
                            } else {
                                writer.setAttribute(attribute, true, selection.getFirstRange());
                            }
                        });
                        button.setAttribute('aria-pressed', String(!isActive));
                    });
                });

                return editor;
            })
            .catch(error => {
                console.error('CKEditor Error:', error);
                return null;
            });
    </script>
</x-app-layout>
