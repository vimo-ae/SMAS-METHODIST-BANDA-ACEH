<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Kelola Soal: {{ $exam->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $exam->classSubjectTeacher?->schoolClass?->name }} — {{ $exam->classSubjectTeacher?->subject?->name }}
                </p>
            </div>
            <a href="{{ route('guru.exams.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                &larr; Kembali ke Daftar
            </a>
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
            min-height: 200px;
        }
    </style>

    <div class="py-8" 
         x-data="{
            isEdit: false,
            editUrl: '',
            type: 'pilihan_ganda',
            options: ['', '', '', ''],
            singleKey: 1,
            multiKeys: [],
            bsItems: [{ text: '', flag: 'B' }, { text: '', flag: 'S' }],
            matchLeft: [{ text: '', target: 1 }],
            matchRight: [''],

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
                this.editUrl = '';
                this.type = 'pilihan_ganda';
                this.options = ['', '', '', ''];
                this.singleKey = 1;
                this.multiKeys = [];
                this.bsItems = [{ text: '', flag: 'B' }, { text: '', flag: 'S' }];
                this.matchLeft = [{ text: '', target: 1 }];
                this.matchRight = [''];
                if (window.editorInstance) {
                    window.editorInstance.setData('');
                }
            },

            loadEditData(q, updateUrl) {
                this.isEdit = true;
                this.editUrl = updateUrl;
                this.type = q.question_type;

                if (window.editorInstance) {
                    window.editorInstance.setData(q.question_text || '');
                }

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

                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
         }">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                     class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                     class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm font-medium text-rose-700">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <h3 class="text-sm font-bold text-rose-800">Gagal menyimpan soal:</h3>
                    </div>
                    <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- LAYOUT SPLIT VIEW -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ======================================================== -->
                <!-- FORM TAMBAH / EDIT SOAL (7 KOLOM) - TANPA INPUT BOBOT   -->
                <!-- ======================================================== -->
                <div class="lg:col-span-7 bg-white p-6 shadow-sm ring-1 ring-slate-200 rounded-2xl sticky top-6 space-y-5">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800" x-text="isEdit ? 'Edit Soal' : 'Tambah Soal Baru'"></h3>
                        <button type="button" x-show="isEdit" @click="resetForm()" class="text-xs font-semibold text-rose-600 hover:underline">
                            Batal Edit
                        </button>
                    </div>

                    <form x-bind:action="isEdit ? editUrl : '{{ route('guru.exams.questions.store', $exam) }}'" method="POST" class="space-y-5">
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

                        <!-- Tipe Soal (Bobot Dihapus) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Soal</label>
                            <select name="question_type" x-model="type" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                <option value="pilihan_ganda">Pilihan Ganda Biasa</option>
                                <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks</option>
                                <option value="benar_salah">Benar / Salah</option>
                                <option value="menjodohkan">Menjodohkan (Pairing)</option>
                                <option value="esai">Esai / Uraian</option>
                            </select>
                        </div>

                        <!-- Pertanyaan Soal (WYSIWYG) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pertanyaan Soal</label>
                            <textarea name="question_text" id="editor" class="w-full rounded-xl border-slate-300 text-xs">{{ old('question_text') }}</textarea>
                        </div>

                        <!-- UI Opsi Jawaban -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                            
                            <!-- 1. PILIHAN GANDA BIASA -->
                            <div x-show="type === 'pilihan_ganda'" class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700">Pilihan Jawaban</label>
                                    <button type="button" @click="addOption()" class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg hover:bg-indigo-100">
                                        + Tambah Opsi
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    <template x-for="(opt, index) in options" :key="index">
                                        <div class="flex items-center justify-between gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                            <!-- Kata required diganti pakai logika :required -->
                                            <input type="text" x-model="options[index]" placeholder="Isi jawaban..." :required="type === 'pilihan_ganda'" class="w-full rounded-lg border-slate-300 text-xs">
                                            <label class="flex items-center gap-1 cursor-pointer text-[11px] font-semibold text-slate-700 flex-none">
                                                <input type="radio" name="radio_single_key" :value="index + 1" x-model.number="singleKey" class="text-indigo-600 focus:ring-indigo-500">
                                                <span>Kunci</span>
                                            </label>
                                            <button type="button" @click="removeOption(index)" x-show="options.length > 2" class="text-rose-500 text-xs font-bold px-1">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 2. PILIHAN GANDA KOMPLEKS -->
                            <div x-show="type === 'pilihan_ganda_kompleks'" class="space-y-3" x-cloak>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700">Pilihan Jawaban (Multi Kunci)</label>
                                    <button type="button" @click="addOption()" class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg hover:bg-indigo-100">
                                        + Tambah Opsi
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    <template x-for="(opt, index) in options" :key="index">
                                        <div class="flex items-center justify-between gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                            <input type="text" x-model="options[index]" placeholder="Isi jawaban..." class="w-full rounded-lg border-slate-300 text-xs">
                                            <label class="flex items-center gap-1 cursor-pointer text-[11px] font-semibold text-slate-700 flex-none">
                                                <input type="checkbox" :value="index + 1" x-model.number="multiKeys" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                <span>Kunci</span>
                                            </label>
                                            <button type="button" @click="removeOption(index)" x-show="options.length > 2" class="text-rose-500 text-xs font-bold px-1">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 3. BENAR / SALAH -->
                            <div x-show="type === 'benar_salah'" class="space-y-3" x-cloak>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700">Daftar Pernyataan</label>
                                    <button type="button" @click="addBs()" class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg hover:bg-indigo-100">
                                        + Tambah Pernyataan
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    <template x-for="(item, index) in bsItems" :key="index">
                                        <div class="flex items-center justify-between gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                            <input type="text" x-model="item.text" placeholder="Teks pernyataan..." class="w-full rounded-lg border-slate-300 text-xs">
                                            <select x-model="item.flag" class="rounded-lg border-slate-300 text-xs font-bold flex-none">
                                                <option value="B">Benar</option>
                                                <option value="S">Salah</option>
                                            </select>
                                            <button type="button" @click="removeBs(index)" x-show="bsItems.length > 1" class="text-rose-500 text-xs font-bold px-1">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 4. MENJODOHKAN -->
                            <div x-show="type === 'menjodohkan'" class="space-y-4" x-cloak>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[11px] font-bold text-slate-600 uppercase">Target Kanan</label>
                                        <button type="button" @click="addMatchRight()" class="text-[11px] font-semibold text-indigo-600">+ Tambah</button>
                                    </div>
                                    <template x-for="(rText, rIdx) in matchRight" :key="rIdx">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-400 w-4" x-text="(rIdx + 1) + '.'"></span>
                                            <input type="text" x-model="matchRight[rIdx]" placeholder="Opsi kanan..." class="w-full rounded-lg border-slate-300 text-xs">
                                            <button type="button" @click="removeMatchRight(rIdx)" x-show="matchRight.length > 1" class="text-rose-500 text-xs font-bold">&times;</button>
                                        </div>
                                    </template>
                                </div>
                                <div class="space-y-2 pt-2 border-t border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[11px] font-bold text-slate-600 uppercase">Premis Kiri</label>
                                        <button type="button" @click="addMatchLeft()" class="text-[11px] font-semibold text-indigo-600">+ Tambah</button>
                                    </div>
                                    <template x-for="(lItem, lIdx) in matchLeft" :key="lIdx">
                                        <div class="flex items-center gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                            <input type="text" x-model="lItem.text" placeholder="Premis kiri..." class="w-full rounded-lg border-slate-300 text-xs">
                                            <select x-model.number="lItem.target" class="rounded-lg border-slate-300 text-xs font-bold">
                                                <template x-for="(rText, rIdx) in matchRight" :key="rIdx">
                                                    <option :value="rIdx + 1" x-text="'No. ' + (rIdx + 1)"></option>
                                                </template>
                                            </select>
                                            <button type="button" @click="removeMatchLeft(lIdx)" x-show="matchLeft.length > 1" class="text-rose-500 text-xs font-bold">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 5. ESAI -->
                            <div x-show="type === 'esai'" class="text-xs text-slate-500 py-1" x-cloak>
                                Soal esai dikoreksi manual oleh guru.
                            </div>

                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <span x-text="isEdit ? 'Simpan Perubahan Soal' : 'Simpan Soal Baru'"></span>
                            </button>
                        </div>
                    </form>
                </div>


                <!-- ======================================================== -->
                <!-- DAFTAR SOAL (5 KOLOM) - INTEGRASI UPDATE BOBOT          -->
                <!-- ======================================================== -->
                <div class="lg:col-span-5 bg-white p-6 shadow-sm ring-1 ring-slate-200 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800">Daftar Soal ({{ $exam->questions->count() }})</h3>

                        @php
                            $totalQuestions = $exam->questions->count();
                            $essayCount = $exam->questions->where('question_type', 'esai')->count();
                            $objectiveCount = $totalQuestions - $essayCount;

                            // Bobot per soal dihitung dari total semua soal
                            $weightPerQuestion = $totalQuestions > 0 ? 100 / $totalQuestions : 0;
                            
                            // Total poin masing-masing kategori
                            $objectivePoints = round($objectiveCount * $weightPerQuestion, 2);
                            $essayPoints = round($essayCount * $weightPerQuestion, 2);
                        @endphp

                        <!-- Badge Ringkas: Tampilkan poin per soal -->
                        <div class="flex items-center gap-2">
                            @if($totalQuestions === 0)
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-xl">
                                    100 Poin
                                </span>
                            @else
                                @if($objectiveCount > 0)
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-xl">
                                        {{ $objectivePoints }} Poin Objektif
                                    </span>
                                @endif
                                
                                @if($essayCount > 0)
                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1 rounded-xl">
                                        {{ $essayPoints }} Poin Esai
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Item Daftar Soal -->
                    <div class="space-y-4">
                        @forelse($exam->questions as $index => $q)
                            <div class="p-4 border border-slate-200 rounded-xl space-y-3 bg-slate-50/50">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center justify-center rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700">
                                            #{{ $index + 1 }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md bg-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                            {{ Str::headline($q->question_type) }}
                                        </span>
                                        <span class="text-[11px] font-medium text-slate-500">
                                            ({{ round($weightPerQuestion, 2) }} Poin)
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" 
                                                @click='loadEditData(@json($q), "{{ route("guru.exams.questions.update", [$exam, $q]) }}")'
                                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded-lg">
                                            Edit
                                        </button>

                                        <form action="{{ route('guru.exams.questions.destroy', [$exam, $q]) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 px-2 py-1 rounded-lg">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Pertanyaan -->
                                <div class="prose prose-slate max-w-none text-xs text-slate-800">
                                    {!! $q->question_text !!}
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-slate-400 py-8">Belum ada soal yang ditambahkan.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Init CKEditor 5 -->
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'insertTable', 'imageUpload', 'link', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                window.editorInstance = editor;
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return {
                        upload: () => loader.file.then(file => new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onload = () => resolve({ default: reader.result });
                            reader.onerror = error => reject(error);
                            reader.readAsDataURL(file);
                        }))
                    };
                };
            })
            .catch(error => {
                console.error('CKEditor Error:', error);
            });
    </script>
</x-app-layout>