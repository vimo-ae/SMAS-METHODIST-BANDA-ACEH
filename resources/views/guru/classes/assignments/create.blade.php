@php
    $className = $cst->schoolClass->name ?? 'Kelas ' . $cst->class_id;
    $subjectName = $cst->subject->name ?? $cst->subject_code;
    $fullClassTitle = strtoupper($className) . ' - ' . strtoupper($subjectName);
    $oldFormats = old('allowed_formats', ['pdf']);
@endphp

@section('title', 'Buat Tugas Baru - ' . $fullClassTitle . ' | E-Class Guru')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-extrabold text-slate-800 leading-tight">
                    Buat Tugas Baru
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $fullClassTitle }} (T.A. {{ $cst->academic_year }})
                </span>
            </div>
        </div>
    </x-slot>

    {{-- QUILL STYLE --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');

        .lms-page {
            font-family: 'DM Sans', sans-serif;
        }

        .lms-card {
            border: 1px solid #e8eef6;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(15, 53, 100, .045);
        }

        .lms-input {
            width: 100%;
            border: 1px solid #dce5f0;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 13px;
            color: #1e293b;
            transition: .2s;
        }

        .lms-input:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99,102,241,.1);
        }

        .lms-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 12.5px;
            font-weight: 700;
        }

        .lms-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            padding: 11px 20px;
            font-size: 13px;
            font-weight: 700;
            transition: .2s;
        }

        .lms-btn-primary {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 14px rgba(79,70,229,.25);
        }

        .lms-btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .ql-editor {
            min-height: 200px;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .ql-container {
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            border-color: #dce5f0 !important;
        }

        .ql-toolbar {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-color: #dce5f0 !important;
            background: #f8fafc;
        }
    </style>

    <div class="lms-page min-h-screen bg-[#f5f8fc] py-6 sm:py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB dan TOMBOL KEMBALI (DI BAWAH HEADER) --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('guru.classes.index') }}" class="hover:text-blue-600 transition">E-Class</a>
                    <span>/</span>
                    <a href="{{ route('guru.classes.show', $cst) }}" class="hover:text-blue-600 transition">{{ $fullClassTitle }}</a>
                    <span>/</span>
                    <span class="text-indigo-700 font-bold">Buat Tugas</span>
                </div>

                <a href="{{ route('guru.classes.show', $cst) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                    <span>←</span>
                    <span>Kembali ke Kelas</span>
                </a>
            </div>

            {{-- NOTIFIKASI ERROR --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700">
                    <div class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-bold">Gagal membuat tugas, periksa isian Anda:</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CARD FORM BUAT TUGAS --}}
            <section class="lms-card overflow-hidden">
                <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50/80 to-white px-7 py-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-800">Buat Tugas Baru untuk Siswa</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Atur topik tugas, rincian instruksi kerja, batas akhir pengumpulan, dan jenis berkas yang diperbolehkan.
                            </p>
                        </div>
                    </div>
                </div>

                <form id="form-assignment-create"
                      action="{{ route('guru.classes.assignments.store', $cst) }}"
                      method="POST"
                      class="space-y-6 p-7">
                    @csrf

                    <input type="hidden" name="max_score" value="100">

                    <div>
                        <label class="lms-label">Judul Tugas <span class="text-rose-500">*</span></label>
                        <input name="title"
                               value="{{ old('title') }}"
                               required
                               maxlength="150"
                               class="lms-input"
                               placeholder="Contoh: Latihan 1 - Analisis Struktur dan Tata Ruang Wilayah">
                        <p class="mt-1.5 text-[11px] text-slate-400">Tentukan judul tugas yang spesifik dan mudah dipahami siswa.</p>
                    </div>

                    <div>
                        <label class="lms-label">Instruksi dan Petunjuk Pengerjaan</label>
                        <div id="editor-assignment-create"></div>
                        <input type="hidden"
                               name="description"
                               id="input-assignment-create">
                        <p class="mt-1.5 text-[11px] text-slate-400">Jelaskan langkah-langkah kerja, kriteria penilaian, atau format penulisan yang diharapkan.</p>
                    </div>

                    <div>
                        <label class="lms-label">Batas Akhir Pengumpulan (Tenggat Waktu) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local"
                               name="due_date"
                               required
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               value="{{ old('due_date') }}"
                               class="lms-input">
                        <p class="mt-1.5 text-[11px] text-slate-400">Siswa yang mengumpulkan melewati batas waktu ini akan ditandai terlambat.</p>
                    </div>

                    <div>
                        <label class="lms-label">Format Berkas yang Diizinkan <span class="text-rose-500">*</span></label>
                        <p class="mb-3 text-[11px] text-slate-400">
                            Pilih minimal satu jenis format file yang diizinkan untuk diunggah siswa.
                        </p>

                        <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4"
                             data-format-group>
                            @foreach ($formatOptions as $ext => $label)
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 px-3.5 py-3 text-xs font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50/50">
                                    <input type="checkbox"
                                           name="allowed_formats[]"
                                           value="{{ $ext }}"
                                           @checked(in_array($ext, $oldFormats))
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                        <a href="{{ route('guru.classes.show', $cst) }}"
                           class="lms-btn border border-slate-200 text-slate-600 hover:bg-slate-50">
                            Batal
                        </a>

                        <button type="submit"
                                class="lms-btn lms-btn-primary">
                            <span>＋</span>
                            Terbitkan Tugas Baru
                        </button>
                    </div>
                </form>
            </section>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const uploadUrl = @json(route('guru.classes.editor-images.store', $cst));
            const csrfToken = @json(csrf_token());

            function uploadQuillImage(quill) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/jpeg,image/png,image/gif,image/webp';
                fileInput.click();

                fileInput.onchange = async function () {
                    const file = fileInput.files[0];
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran gambar maksimal 5 MB.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (!response.ok) {
                            throw new Error(result.message || 'Gagal mengunggah gambar.');
                        }

                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', result.url, 'user');
                        quill.setSelection(range.index + 1);
                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Terjadi kesalahan saat upload gambar.');
                    }
                };
            }

            const quill = new Quill('#editor-assignment-create', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link', 'image', 'blockquote'],
                            ['clean']
                        ],
                        handlers: {
                            image: function () {
                                uploadQuillImage(quill);
                            }
                        }
                    }
                }
            });

            const form = document.getElementById('form-assignment-create');
            const inputDesc = document.getElementById('input-assignment-create');
            const oldDesc = @json(old('description'));

            if (oldDesc) {
                quill.clipboard.dangerouslyPasteHTML(oldDesc);
            }

            form.addEventListener('submit', (event) => {
                const checked = form.querySelector('[data-format-group] input:checked');
                if (!checked) {
                    event.preventDefault();
                    alert('Pilih minimal satu format berkas untuk pengumpulan tugas.');
                    return;
                }

                const empty = quill.getText().trim() === '' && !quill.root.querySelector('img');
                inputDesc.value = empty ? '' : quill.root.innerHTML;
            });
        });
    </script>
</x-app-layout>
