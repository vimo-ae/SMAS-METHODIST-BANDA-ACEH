/**
 * file-validator.js  ->  simpan di public/js/file-validator.js
 *
 * Otomatis aktif di setiap <input type="file" data-allowed="pdf,docx,..." data-max-mb="20">
 * Yang dicek:
 *   1. Ekstensi termasuk format yang diizinkan
 *   2. ISI file (magic bytes) cocok dengan ekstensinya
 *      (mencegah file .exe/.php yang cuma di-rename jadi .pdf)
 *   3. Ukuran file (opsional)
 *   4. Menampilkan Pratinjau (Preview) Berkas yang interaktif dan detail saat berkas dipilih
 * Jika gagal: pesan error tampil dan pilihan file dikosongkan.
 */
(function () {
    const ALIAS = { jpeg: 'jpg' };

    const FILE_META = {
        docx: { name: 'Dokumen Word', color: '#2563eb', bg: '#eff6ff', border: '#bfdbfe', badge: '#1d4ed8', icon: 'DOCX' },
        doc:  { name: 'Dokumen Word', color: '#2563eb', bg: '#eff6ff', border: '#bfdbfe', badge: '#1d4ed8', icon: 'DOC' },
        pdf:  { name: 'Dokumen PDF', color: '#e11d48', bg: '#fff1f2', border: '#fecdd3', badge: '#be123c', icon: 'PDF' },
        xlsx: { name: 'Spreadsheet Excel', color: '#059669', bg: '#ecfdf5', border: '#a7f3d0', badge: '#047857', icon: 'XLSX' },
        xls:  { name: 'Spreadsheet Excel', color: '#059669', bg: '#ecfdf5', border: '#a7f3d0', badge: '#047857', icon: 'XLS' },
        pptx: { name: 'Presentasi PowerPoint', color: '#ea580c', bg: '#fff7ed', border: '#fed7aa', badge: '#c2410c', icon: 'PPTX' },
        ppt:  { name: 'Presentasi PowerPoint', color: '#ea580c', bg: '#fff7ed', border: '#fed7aa', badge: '#c2410c', icon: 'PPT' },
        jpg:  { name: 'Gambar JPEG', color: '#7c3aed', bg: '#f5f3ff', border: '#ddd6fe', badge: '#6d28d9', icon: 'JPG', isImage: true },
        png:  { name: 'Gambar PNG', color: '#7c3aed', bg: '#f5f3ff', border: '#ddd6fe', badge: '#6d28d9', icon: 'PNG', isImage: true },
        webp: { name: 'Gambar WebP', color: '#7c3aed', bg: '#f5f3ff', border: '#ddd6fe', badge: '#6d28d9', icon: 'WEBP', isImage: true },
        mp4:  { name: 'Video MP4', color: '#9333ea', bg: '#faf5ff', border: '#e9d5ff', badge: '#7e22ce', icon: 'MP4' },
        zip:  { name: 'Arsip ZIP', color: '#d97706', bg: '#fffbeb', border: '#fde68a', badge: '#b45309', icon: 'ZIP' },
        txt:  { name: 'Dokumen Teks', color: '#475569', bg: '#f8fafc', border: '#cbd5e1', badge: '#334155', icon: 'TXT' },
        default: { name: 'Berkas Dokumen', color: '#0284c7', bg: '#f0f9ff', border: '#bae6fd', badge: '#0369a1', icon: 'FILE' }
    };

    const startsWith = (b, sig, off = 0) => sig.every((v, i) => b[off + i] === v);
    const ascii = (b) => { let s = ''; for (let i = 0; i < b.length; i++) s += String.fromCharCode(b[i]); return s; };

    /** Format ukuran bytes ke teks rapi (KB, MB) */
    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const dm = 1;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    /** Tebak jenis file dari isinya. */
    async function detect(file) {
        const b = new Uint8Array(await file.slice(0, 512 * 1024).arrayBuffer());
        if (b.length === 0) return 'empty';
        if (startsWith(b, [0x25, 0x50, 0x44, 0x46])) return 'pdf';
        if (startsWith(b, [0x89, 0x50, 0x4E, 0x47])) return 'png';
        if (startsWith(b, [0xFF, 0xD8, 0xFF])) return 'jpg';
        if (startsWith(b, [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1])) return 'ole'; // doc/xls/ppt lama
        if (startsWith(b, [0x50, 0x4B, 0x03, 0x04])) {                                   // zip & turunan OOXML
            const t = ascii(b);
            if (t.includes('word/')) return 'docx';
            if (t.includes('xl/')) return 'xlsx';
            if (t.includes('ppt/')) return 'pptx';
            return 'zip';
        }
        if (b.length >= 8 && ascii(b.slice(4, 8)) === 'ftyp') return 'mp4';
        if (!b.slice(0, 4096).includes(0)) return 'txt'; // tanpa byte NUL -> teks biasa
        return 'unknown';
    }

    /** Apakah jenis hasil deteksi cocok dengan ekstensi? */
    function matches(ext, kind) {
        switch (ext) {
            case 'doc': case 'xls': case 'ppt': return kind === 'ole';
            case 'docx': case 'xlsx': case 'pptx': return kind === ext;
            case 'zip': return ['zip', 'docx', 'xlsx', 'pptx'].includes(kind);
            default: return kind === ext; // pdf, png, jpg, mp4, txt
        }
    }

    function errorBox(input) {
        let box = input.parentElement.querySelector('[data-file-error]');
        if (!box) {
            box = document.createElement('p');
            box.setAttribute('data-file-error', '');
            box.className = 'mt-2 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-lg p-2.5 flex items-center gap-2';
            input.insertAdjacentElement('afterend', box);
        }
        return box;
    }

    function clearPreview(input) {
        const preview = input.parentElement.querySelector('[data-file-preview]');
        if (preview) preview.remove();

        const dropzonePrompt = input.parentElement.querySelector('[data-dropzone-prompt]');
        if (dropzonePrompt) {
            dropzonePrompt.style.display = '';
        }
        input.style.display = '';
    }

    function renderPreview(input, file) {
        clearPreview(input);

        const ext = (ALIAS[file.name.split('.').pop().toLowerCase()]) || file.name.split('.').pop().toLowerCase();
        const meta = FILE_META[ext] || FILE_META.default;
        const sizeText = formatBytes(file.size);

        const preview = document.createElement('div');
        preview.setAttribute('data-file-preview', '');
        preview.className = 'mt-3 overflow-hidden rounded-2xl border-2 transition-all p-3.5 sm:p-4 text-left shadow-sm';
        preview.style.backgroundColor = '#ffffff';
        preview.style.borderColor = meta.border;

        let iconOrThumbHtml = '';
        if (meta.isImage && file.type.startsWith('image/')) {
            const objUrl = URL.createObjectURL(file);
            iconOrThumbHtml = `
                <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-xs">
                    <img src="${objUrl}" alt="${file.name}" class="h-full w-full object-cover">
                </div>
            `;
        } else {
            iconOrThumbHtml = `
                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl font-black text-white shadow-xs"
                     style="background: linear-gradient(135deg, ${meta.color} 0%, ${meta.badge} 100%);">
                    <span class="text-[12px] font-mono tracking-tighter">${meta.icon}</span>
                    <span class="text-[8px] font-sans opacity-80 uppercase tracking-widest">FILE</span>
                </div>
            `;
        }

        preview.innerHTML = `
            <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                    ${iconOrThumbHtml}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider text-white"
                                  style="background-color: ${meta.badge};">
                                ${meta.name}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Siap Diunggah
                            </span>
                        </div>
                        <h4 class="mt-1 text-xs sm:text-sm font-extrabold text-slate-800 truncate" title="${file.name}">
                            ${file.name}
                        </h4>
                        <p class="text-[11px] text-slate-400 font-medium">
                            Ukuran Berkas: <strong class="text-slate-600">${sizeText}</strong> &bull; Ekstensi: <span class="uppercase font-mono">${ext}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <button type="button" class="btn-change-file inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:border-slate-300 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Ganti File
                    </button>
                    <button type="button" class="btn-remove-file inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-100 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hapus
                    </button>
                </div>
            </div>
        `;

        // Event listener ganti file
        preview.querySelector('.btn-change-file').addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });

        // Event listener hapus file
        preview.querySelector('.btn-remove-file').addEventListener('click', (e) => {
            e.stopPropagation();
            input.value = '';
            clearPreview(input);
            const box = errorBox(input);
            box.style.display = 'none';
        });

        // Sisipkan preview tepat setelah input atau di dalam container dropzone
        input.insertAdjacentElement('afterend', preview);

        // Jika ada dropzone prompt di dalam container, sembunyikan sementara agar rapi
        const dropzonePrompt = input.parentElement.querySelector('[data-dropzone-prompt]');
        if (dropzonePrompt) {
            dropzonePrompt.style.display = 'none';
        }
        input.style.display = 'none';
    }

    async function check(input) {
        const box = errorBox(input);
        box.textContent = '';
        box.style.display = 'none';

        const file = input.files && input.files[0];
        if (!file) {
            clearPreview(input);
            return true;
        }

        const allowed = (input.dataset.allowed || '').split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
        const maxMb = parseFloat(input.dataset.maxMb || '0');
        const ext = (ALIAS[file.name.split('.').pop().toLowerCase()]) || file.name.split('.').pop().toLowerCase();

        let msg = '';
        if (allowed.length && !allowed.includes(ext)) {
            msg = 'Format .' + ext + ' tidak diizinkan. Gunakan: ' + allowed.join(', ').toUpperCase() + '.';
        } else if (maxMb && file.size > maxMb * 1024 * 1024) {
            msg = 'Ukuran file melebihi ' + maxMb + ' MB (Ukuran file: ' + formatBytes(file.size) + ').';
        } else {
            const kind = await detect(file);
            if (kind === 'empty') msg = 'File kosong.';
            else if (!matches(ext, kind)) msg = 'Isi file tidak sesuai dengan ekstensi .' + ext + '. File mungkin rusak atau hanya diganti namanya.';
        }

        if (msg) {
            box.innerHTML = `
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>${msg}</span>
            `;
            box.style.display = 'flex';
            input.value = '';
            clearPreview(input);
            return false;
        }

        // Tampilkan Pratinjau Berkas
        renderPreview(input, file);
        return true;
    }

    document.addEventListener('change', (e) => {
        const el = e.target;
        if (el instanceof HTMLInputElement && el.type === 'file' && el.hasAttribute('data-allowed')) check(el);
    });

    window.validateUploadInput = check;
})();