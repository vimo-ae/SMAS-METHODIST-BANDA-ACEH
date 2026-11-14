<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title ?? 'Ujian' }}</title>
    <link rel="stylesheet" href="{{ asset('css/exam.css') }}">
</head>
<body class="no-select">

{{-- ===== Top bar ===== --}}
<header class="take-topbar">
    <div class="take-topbar__title">
        {{ $exam->title ?? 'Ujian' }}
        <small>{{ optional(optional($exam->classSubjectTeacher)->subject)->name }}</small>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <span class="violation-counter">Pelanggaran: <span id="v-count">0</span>/{{ $maxViolations }}</span>
        <span class="timer" id="timer">--:--:--</span>
        <button type="button" class="btn btn-success" id="btn-finish">Selesai</button>
    </div>
</header>

<main class="take-layout">
    {{-- ===== Soal ===== --}}
    <section>
        <div id="q-holder"></div>
    </section>

    {{-- ===== Navigasi nomor soal ===== --}}
    <aside class="question-nav">
        <h3>Daftar Soal</h3>
        <div class="question-nav__grid" id="nav-grid"></div>
        <div class="legend">
            <span class="l-answered">Sudah dijawab</span>
            <span class="l-current">Soal saat ini</span>
        </div>
        <div class="save-status" id="save-status"></div>
    </aside>
</main>

{{-- ===== Modal mulai (fullscreen) ===== --}}
<div class="modal-overlay is-open" id="start-modal">
    <div class="modal">
        <div class="modal__icon">📝</div>
        <h2>Siap mengerjakan?</h2>
        <p>Ujian berjalan dalam layar penuh. Berpindah tab/jendela, keluar dari layar penuh, atau menyalin teks
            akan dicatat sebagai pelanggaran. Maksimal {{ $maxViolations }} pelanggaran, setelah itu ujian
            otomatis diakhiri.</p>
        <div class="modal__actions">
            <button class="btn btn-primary" id="btn-start">Mulai Layar Penuh</button>
        </div>
    </div>
</div>

{{-- ===== Modal pelanggaran ===== --}}
<div class="modal-overlay" id="warn-modal">
    <div class="modal is-danger">
        <div class="modal__icon">⚠️</div>
        <h2>Pelanggaran Terdeteksi</h2>
        <p id="warn-text"></p>
        <div class="modal__actions">
            <button class="btn btn-primary" id="btn-warn-ok">Lanjut Mengerjakan</button>
        </div>
    </div>
</div>

{{-- ===== Modal konfirmasi selesai ===== --}}
<div class="modal-overlay" id="finish-modal">
    <div class="modal">
        <div class="modal__icon">✅</div>
        <h2>Akhiri ujian?</h2>
        <p id="finish-text"></p>
        <div class="modal__actions">
            <button class="btn btn-outline" id="btn-finish-cancel">Kembali</button>
            <button class="btn btn-success" id="btn-finish-ok">Ya, Selesai</button>
        </div>
    </div>
</div>

<form id="finish-form" method="POST" action="{{ route('siswa.exams.finish', $exam) }}" style="display:none">
    @csrf
</form>

<script>
(function () {
    // ---------- Data dari controller ----------
    const QUESTIONS = @json($questions);
    const SAVED     = @json($saved);
    const TOKEN     = @json($token);
    const MAX_VIOL  = {{ (int) $maxViolations }};
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

    const URL_ANSWER    = @json(route('siswa.exams.answer', $exam));
    const URL_VIOLATION = @json(route('siswa.exams.violation', $exam));
    const URL_LEAVE     = @json(route('siswa.exams.leave', $exam));
    const URL_SHOW      = @json(route('siswa.exams.show', $exam));
    const URL_ESSAY_TPL = @json(route('siswa.exams.essay', [$exam, 0]));

    const $ = (id) => document.getElementById(id);
    const endAt = Date.now() + {{ (int) $remaining }} * 1000;

    // ---------- Normalisasi data soal dari database ----------
    // DB: opsi = daftar biasa, kunci/jawaban = indeks mulai 0. Di layar: opsi berhuruf A, B, C...
    const TYPE_MAP = { pilihan_ganda: 'pg', pilihan_ganda_kompleks: 'pg_kompleks' };
    const I2L = (i) => String.fromCharCode(65 + Number(i));   // 0 -> A
    const L2I = (k) => k.charCodeAt(0) - 65;                  // A -> 0
    QUESTIONS.forEach((q) => {
        q.type = TYPE_MAP[q.type] || q.type;
        if (typeof q.options === 'string') {
            try { q.options = JSON.parse(q.options); } catch (e) { q.options = []; }
        }
        if ((q.type === 'pg' || q.type === 'pg_kompleks') && Array.isArray(q.options)) {
            const obj = {};
            q.options.forEach((t, i) => { obj[I2L(i)] = t; });
            q.options = obj;
        }
    });

    let current = 0;
    let finishing = false;
    let examStarted = false;

    // answers[qid] : string (pg) | array (pg_kompleks) | object (menjodohkan) | {file:true} (esai)
    const answers = {};
    QUESTIONS.forEach((q) => {
        const s = (SAVED && SAVED[q.id]) || null;
        if (q.type === 'esai') { answers[q.id] = { file: !!(s && s.file) }; return; }
        let a = s ? s.answer : null;
        if (a !== null && a !== undefined && a !== '') {
            if (q.type === 'pg') {
                a = I2L(a);
            } else if (['pg_kompleks', 'benar_salah', 'menjodohkan'].includes(q.type)) {
                try { a = JSON.parse(a); } catch (e) { a = null; }
                if (q.type === 'pg_kompleks' && Array.isArray(a)) a = a.map(I2L);
            }
        } else {
            a = null;
        }
        answers[q.id] = a;
    });

    // ---------- Util ----------
    function el(tag, cls, text) {
        const e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    }
    function headers() {
        return { 'X-CSRF-TOKEN': CSRF, 'X-Exam-Token': TOKEN, 'Accept': 'application/json' };
    }
    function setStatus(msg, ok) {
        const s = $('save-status');
        s.textContent = msg;
        s.className = 'save-status' + (ok ? ' is-saved' : '');
    }
    function isAnswered(q) {
        const a = answers[q.id];
        if (q.type === 'esai') return !!(a && a.file);
        if (q.type === 'pg_kompleks') return Array.isArray(a) && a.length > 0;
        if (q.type === 'benar_salah') {
            return Array.isArray(a) && a.length === (q.options || []).length && a.every((v) => v === true || v === false);
        }
        if (q.type === 'menjodohkan') return a && Object.values(a).some((v) => v);
        return !!a;
    }

    // ---------- Simpan jawaban ----------
    async function saveAnswer(q) {
        const a = answers[q.id];
        let payload;
        if (q.type === 'pg') payload = String(L2I(a));                       // indeks, mis. "3"
        else if (q.type === 'pg_kompleks') payload = JSON.stringify(a.map(L2I)); // mis. [0,2]
        else payload = JSON.stringify(a);                                    // benar_salah: [true,false,...]
        setStatus('Menyimpan...');
        try {
            const res = await fetch(URL_ANSWER, {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, headers()),
                body: JSON.stringify({ question_id: q.id, answer: payload, token: TOKEN }),
            });
            if (res.status === 403) { return autoFinish(); }
            if (!res.ok) throw new Error();
            setStatus('Tersimpan ✓', true);
        } catch (e) {
            setStatus('Gagal menyimpan, periksa koneksi.');
        }
    }

    async function uploadEssay(q, file, previewBox) {
        const fd = new FormData();
        fd.append('photo', file);
        fd.append('token', TOKEN);
        setStatus('Mengunggah foto...');
        try {
            const res = await fetch(URL_ESSAY_TPL.replace(/\/0$/, '/' + q.id), {
                method: 'POST', headers: headers(), body: fd,
            });
            if (res.status === 403) { return autoFinish(); }
            if (!res.ok) throw new Error();
            answers[q.id] = { file: true };
            previewBox.classList.add('is-uploaded');
            setStatus('Foto terunggah ✓', true);
            renderNav();
        } catch (e) {
            setStatus('Gagal mengunggah (maks 6 MB, jpg/png/webp).');
        }
    }

    // ---------- Render soal ----------
    function render() {
        const q = QUESTIONS[current];
        const holder = $('q-holder');
        holder.innerHTML = '';

        const card = el('div', 'question-card');

        const meta = el('div', 'question-card__meta');
        meta.appendChild(el('span', 'question-card__no', 'Soal ' + (current + 1) + ' / ' + QUESTIONS.length));
        const labels = { pg: 'Pilihan Ganda', pg_kompleks: 'PG Kompleks', benar_salah: 'Benar / Salah', menjodohkan: 'Menjodohkan', esai: 'Esai/Uraian' };
        meta.appendChild(el('span', '', (labels[q.type] || q.type) + ' · bobot ' + q.weight));
        card.appendChild(meta);

        card.appendChild(el('div', 'question-card__text', q.text));
        if (q.image) {
            const img = el('img', 'question-card__image');
            img.src = q.image; img.alt = 'Gambar soal';
            card.appendChild(img);
        }

        if (q.type === 'pg') card.appendChild(renderPg(q));
        else if (q.type === 'pg_kompleks') card.appendChild(renderKompleks(q));
        else if (q.type === 'benar_salah') card.appendChild(renderBenarSalah(q));
        else if (q.type === 'menjodohkan') card.appendChild(renderMatch(q));
        else if (q.type === 'esai') card.appendChild(renderEssay(q));

        const nav = el('div', 'question-card__nav');
        const prev = el('button', 'btn btn-outline', '← Sebelumnya');
        prev.disabled = current === 0;
        prev.onclick = () => go(current - 1);
        const next = el('button', 'btn btn-primary', current === QUESTIONS.length - 1 ? 'Tinjau & Selesai' : 'Berikutnya →');
        next.onclick = () => (current === QUESTIONS.length - 1 ? openFinish() : go(current + 1));
        nav.append(prev, next);
        card.appendChild(nav);

        holder.appendChild(card);
        renderNav();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function renderPg(q) {
        const list = el('div', 'option-list');
        Object.entries(q.options || {}).forEach(([key, text]) => {
            const label = el('label', 'option' + (answers[q.id] === key ? ' is-selected' : ''));
            const input = document.createElement('input');
            input.type = 'radio'; input.name = 'q' + q.id; input.checked = answers[q.id] === key;
            input.onchange = () => {
                answers[q.id] = key;
                list.querySelectorAll('.option').forEach((o) => o.classList.remove('is-selected'));
                label.classList.add('is-selected');
                saveAnswer(q); renderNav();
            };
            label.append(input, el('span', 'option__key', key), el('span', '', text));
            list.appendChild(label);
        });
        return list;
    }

    function renderKompleks(q) {
        const list = el('div', 'option-list');
        const hint = el('div', 'save-status', 'Pilih satu atau lebih jawaban.');
        hint.style.marginBottom = '10px';
        const wrap = el('div');
        wrap.appendChild(hint);
        Object.entries(q.options || {}).forEach(([key, text]) => {
            const chosen = Array.isArray(answers[q.id]) && answers[q.id].includes(key);
            const label = el('label', 'option' + (chosen ? ' is-selected' : ''));
            const input = document.createElement('input');
            input.type = 'checkbox'; input.checked = chosen;
            input.onchange = () => {
                let cur = Array.isArray(answers[q.id]) ? answers[q.id].slice() : [];
                cur = input.checked ? Array.from(new Set(cur.concat(key))) : cur.filter((k) => k !== key);
                answers[q.id] = cur.sort();
                label.classList.toggle('is-selected', input.checked);
                saveAnswer(q); renderNav();
            };
            label.append(input, el('span', 'option__key', key), el('span', '', text));
            list.appendChild(label);
        });
        wrap.appendChild(list);
        return wrap;
    }

    function renderBenarSalah(q) {
        const list = el('div', 'option-list');
        const cur = Array.isArray(answers[q.id]) ? answers[q.id] : [];
        (q.options || []).forEach((text, i) => {
            const row = el('div', 'option');
            row.style.cssText = 'cursor:default; flex-wrap:wrap; justify-content:space-between;';
            const t = el('span', '', text);
            t.style.flex = '1 1 260px';
            row.appendChild(t);

            const grp = el('div');
            grp.style.cssText = 'display:flex; gap:16px;';
            [['Benar', true], ['Salah', false]].forEach(([label, val]) => {
                const lab = el('label');
                lab.style.cssText = 'display:flex; gap:6px; align-items:center; cursor:pointer; font-weight:600;';
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'bs' + q.id + '_' + i;
                input.checked = cur[i] === val;
                input.onchange = () => {
                    const a = Array.isArray(answers[q.id]) ? answers[q.id].slice() : [];
                    while (a.length < q.options.length) a.push(null);
                    a[i] = val;
                    answers[q.id] = a;
                    saveAnswer(q); renderNav();
                };
                lab.append(input, document.createTextNode(label));
                grp.appendChild(lab);
            });
            row.appendChild(grp);
            list.appendChild(row);
        });
        return list;
    }

    function renderMatch(q) {
        const opt = q.options || { left: {}, right: {} };
        const wrap = el('div');

        const right = el('div', 'save-status');
        right.style.cssText = 'margin-bottom:12px; line-height:1.8;';
        Object.entries(opt.right || {}).forEach(([k, t]) => {
            right.appendChild(el('div', '', k + '. ' + t));
        });
        wrap.appendChild(right);

        const list = el('div', 'option-list');
        Object.entries(opt.left || {}).forEach(([id, text]) => {
            const row = el('div', 'option');
            row.style.cursor = 'default';
            row.appendChild(el('span', '', text));
            row.lastChild.style.flex = '1';

            const sel = el('select', 'answer-input');
            sel.style.width = '110px';
            sel.appendChild(new Option('— pilih —', ''));
            Object.keys(opt.right || {}).forEach((k) => sel.appendChild(new Option(k, k)));
            const cur = answers[q.id] || {};
            sel.value = cur[id] || '';
            sel.onchange = () => {
                const a = Object.assign({}, answers[q.id] || {});
                if (sel.value) a[id] = sel.value; else delete a[id];
                answers[q.id] = a;
                saveAnswer(q); renderNav();
            };
            row.appendChild(sel);
            list.appendChild(row);
        });
        wrap.appendChild(list);
        return wrap;
    }

    function renderEssay(q) {
        const box = el('div', 'upload-box' + (answers[q.id] && answers[q.id].file ? ' is-uploaded' : ''));
        box.appendChild(el('div', '', 'Tulis jawaban di kertas, lalu unggah foto lembar jawabanmu (jpg/png/webp, maks 6 MB).'));
        const status = el('div', '', answers[q.id] && answers[q.id].file ? '✓ Foto jawaban sudah terunggah. Pilih file baru untuk mengganti.' : '');
        status.style.cssText = 'margin-top:8px; font-weight:600;';
        const input = document.createElement('input');
        input.type = 'file'; input.accept = 'image/jpeg,image/png,image/webp';
        input.onchange = () => {
            const f = input.files[0];
            if (!f) return;
            if (f.size > 6 * 1024 * 1024) { setStatus('Ukuran foto melebihi 6 MB.'); input.value = ''; return; }
            const old = box.querySelector('.upload-preview');
            if (old) old.remove();
            const img = el('img', 'upload-preview');
            img.src = URL.createObjectURL(f);
            box.appendChild(img);
            status.textContent = 'Mengunggah...';
            uploadEssay(q, f, box).then(() => {
                if (answers[q.id] && answers[q.id].file) status.textContent = '✓ Foto jawaban terunggah.';
            });
        };
        box.append(status, input);
        return box;
    }

    function renderNav() {
        const grid = $('nav-grid');
        grid.innerHTML = '';
        QUESTIONS.forEach((q, i) => {
            const b = el('button', 'nav-btn', String(i + 1));
            b.type = 'button';
            if (isAnswered(q)) b.classList.add('is-answered');
            if (i === current) b.classList.add('is-current');
            b.onclick = () => go(i);
            grid.appendChild(b);
        });
    }

    function go(i) {
        if (i < 0 || i >= QUESTIONS.length) return;
        current = i; render();
    }

    // ---------- Timer ----------
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        const left = Math.max(0, Math.round((endAt - Date.now()) / 1000));
        const h = Math.floor(left / 3600), m = Math.floor((left % 3600) / 60), s = left % 60;
        const t = $('timer');
        t.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        t.classList.toggle('is-warning', left <= 300 && left > 60);
        t.classList.toggle('is-danger', left <= 60);
        if (left <= 0) autoFinish();
    }

    // ---------- Selesai ----------
    function openFinish() {
        const total = QUESTIONS.length;
        const done = QUESTIONS.filter(isAnswered).length;
        const empty = total - done;
        $('finish-text').textContent = empty > 0
            ? 'Masih ada ' + empty + ' soal yang belum dijawab (' + done + '/' + total + ' terjawab). Setelah selesai, jawaban tidak bisa diubah.'
            : 'Semua soal sudah dijawab. Setelah selesai, jawaban tidak bisa diubah.';
        $('finish-modal').classList.add('is-open');
    }
    function submitFinish() {
        if (finishing) return;
        finishing = true;
        $('finish-form').submit();
    }
    function autoFinish() { submitFinish(); }

    // ---------- Pelanggaran ----------
    let lastViolationAt = 0;
    async function reportViolation(type, detail) {
        if (!examStarted || finishing) return;
        const now = Date.now();
        if (now - lastViolationAt < 2000) return; // hindari hitungan ganda (blur + tab_switch)
        lastViolationAt = now;
        try {
            const res = await fetch(URL_VIOLATION, {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, headers()),
                body: JSON.stringify({ type: type, detail: detail || null, token: TOKEN }),
            });
            if (!res.ok) return;
            const d = await res.json();
            $('v-count').textContent = d.count;
            if (d.finished) {
                finishing = true;
                alert('Batas pelanggaran tercapai. Ujian diakhiri otomatis.');
                window.location.href = URL_SHOW;
                return;
            }
            const labels = {
                tab_switch: 'Kamu berpindah tab/jendela.',
                window_blur: 'Jendela ujian kehilangan fokus.',
                fullscreen_exit: 'Kamu keluar dari layar penuh.',
                copy_paste: 'Salin/tempel tidak diperbolehkan.',
            };
            $('warn-text').textContent = (labels[type] || 'Pelanggaran tercatat.') +
                ' Pelanggaran ' + d.count + ' dari ' + d.max + '.';
            $('warn-modal').classList.add('is-open');
        } catch (e) { /* abaikan */ }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) reportViolation('tab_switch');
    });
    window.addEventListener('blur', () => {
        setTimeout(() => { if (!document.hidden) reportViolation('window_blur'); }, 300);
    });
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) reportViolation('fullscreen_exit');
    });
    ['copy', 'cut', 'paste', 'contextmenu'].forEach((ev) => {
        document.addEventListener(ev, (e) => { e.preventDefault(); reportViolation('copy_paste', ev); });
    });

    // catat saat siswa menutup/keluar halaman
    window.addEventListener('pagehide', () => {
        if (finishing || !examStarted) return;
        const fd = new FormData();
        fd.append('token', TOKEN); fd.append('_token', CSRF);
        navigator.sendBeacon(URL_LEAVE, fd);
    });

    // ---------- Tombol & modal ----------
    $('btn-start').onclick = () => {
        const root = document.documentElement;
        const req = root.requestFullscreen ? root.requestFullscreen() : Promise.resolve();
        Promise.resolve(req).catch(() => {}).then(() => {
            examStarted = true;
            $('start-modal').classList.remove('is-open');
        });
    };
    $('btn-warn-ok').onclick = () => {
        $('warn-modal').classList.remove('is-open');
        if (!document.fullscreenElement && document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(() => {});
        }
    };
    $('btn-finish').onclick = openFinish;
    $('btn-finish-cancel').onclick = () => $('finish-modal').classList.remove('is-open');
    $('btn-finish-ok').onclick = submitFinish;

    // ---------- Mulai ----------
    render();
    tick();
    setInterval(tick, 1000);
})();
</script>
</body>
</html>