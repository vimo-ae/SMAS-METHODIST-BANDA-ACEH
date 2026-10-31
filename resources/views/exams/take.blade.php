//sesuaikan lagi nnt yoo
@php
    $routeUrl = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : null;

    $config = [
        'title' => $exam->title,
        'questions' => $questions,
        'saved' => $saved,
        'remaining' => (int) $remaining,
        'maxViolations' => (int) $maxViolations,
        'token' => $token,
        'csrf' => csrf_token(),
        'seed' => (int) $session->exam_session_id,
        'shuffleQuestions' => (bool) $exam->shuffle_questions,
        'shuffleOptions' => (bool) $exam->shuffle_options,
        'urls' => [
            'answer' => $routeUrl('siswa.exams.answer', $exam),
            'essay' => $routeUrl('siswa.exams.essay', [$exam, 0]),   // angka 0 diganti id soal oleh JS
            'violation' => $routeUrl('siswa.exams.violation', $exam),
            'leave' => $routeUrl('siswa.exams.leave', $exam),
            'finish' => $routeUrl('siswa.exams.finish', $exam),
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $exam->title }}</title>
<style>
    *{box-sizing:border-box} body{margin:0;font-family:system-ui,Segoe UI,Arial,sans-serif;background:#f3f4f6;color:#111827;user-select:none}
    header{position:sticky;top:0;z-index:5;background:#fff;border-bottom:1px solid #e5e7eb;padding:10px 16px;display:flex;gap:16px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    header h1{font-size:16px;margin:0}
    .pill{padding:4px 10px;border-radius:999px;font-weight:600;font-size:14px;background:#e0f2fe;color:#075985}
    .pill.warn{background:#fee2e2;color:#991b1b}
    .wrap{max-width:1000px;margin:16px auto;padding:0 12px;display:grid;grid-template-columns:1fr 220px;gap:16px}
    @media(max-width:760px){.wrap{grid-template-columns:1fr}}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px}
    .qtext{font-size:17px;line-height:1.5;margin:6px 0 14px;white-space:pre-wrap}
    .opt{display:flex;gap:10px;align-items:flex-start;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:8px;cursor:pointer}
    .opt:hover{background:#f9fafb}
    .row{display:flex;gap:10px;align-items:center;justify-content:space-between;border:1px solid #e5e7eb;border-radius:8px;padding:10px;margin-bottom:8px;flex-wrap:wrap}
    select{padding:6px;border:1px solid #d1d5db;border-radius:6px}
    .nav{display:grid;grid-template-columns:repeat(5,1fr);gap:6px}
    .nav button{padding:8px 0;border:1px solid #d1d5db;background:#fff;border-radius:6px;cursor:pointer}
    .nav button.done{background:#dcfce7;border-color:#86efac}
    .nav button.cur{outline:2px solid #2563eb}
    .btn{padding:9px 16px;border:0;border-radius:8px;cursor:pointer;font-size:15px;background:#2563eb;color:#fff}
    .btn.gray{background:#e5e7eb;color:#111827} .btn.red{background:#dc2626}
    .actions{display:flex;justify-content:space-between;gap:8px;margin-top:16px;flex-wrap:wrap}
    #gate{position:fixed;inset:0;background:#fff;z-index:20;display:flex;align-items:center;justify-content:center;padding:20px}
    #gate .box{max-width:520px;text-align:center}
    #toast{position:fixed;bottom:16px;left:50%;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 16px;border-radius:8px;display:none;z-index:30}
    img.qimg{max-width:100%;border-radius:8px;margin-bottom:12px}
</style>
</head>
<body>

<div id="gate">
    <div class="box">
        <h2>{{ $exam->title }}</h2>
        <p>{{ $exam->classSubjectTeacher->subject->name ?? '' }} &middot; {{ $exam->duration_minutes }} menit</p>
        @if ($exam->instructions)<p style="color:#4b5563">{{ $exam->instructions }}</p>@endif
        <p style="color:#991b1b;font-size:14px">Ujian berjalan dalam layar penuh. Berpindah tab/jendela, keluar layar penuh, atau menyalin-menempel dicatat sebagai pelanggaran. Pelanggaran lebih dari {{ $maxViolations }} kali mengakhiri ujian otomatis. Waktu sudah berjalan sejak Anda membuka halaman ini.</p>
        <button class="btn" id="startBtn">Mulai Mengerjakan</button>
    </div>
</div>

<header>
    <h1>{{ $exam->title }}</h1>
    <div style="display:flex;gap:8px">
        <span class="pill warn" id="vio">Pelanggaran: 0/{{ $maxViolations }}</span>
        <span class="pill" id="timer">--:--</span>
    </div>
</header>

<div class="wrap">
    <section class="card" id="qbox"></section>
    <aside class="card"><strong>Nomor soal</strong><div class="nav" id="nav" style="margin-top:10px"></div></aside>
</div>

<form id="finishForm" method="POST" action="{{ $config['urls']['finish'] }}">@csrf</form>
<div id="toast"></div>

<script>
(function () {
    const C = @json($config);
    const saved = Array.isArray(C.saved) ? {} : C.saved;
    const Q = C.questions;
    const answers = {}, essayDone = {};
    Object.keys(saved).forEach(k => {
        if (saved[k].answer !== null && saved[k].answer !== undefined) answers[k] = saved[k].answer;
        if (saved[k].file) essayDone[k] = true;
    });

    // ---------- acak deterministik (urutan tetap saat refresh, beda tiap siswa) ----------
    function rng(seed) { let a = seed >>> 0; return function () { a |= 0; a = a + 0x6D2B79F5 | 0; let t = Math.imul(a ^ a >>> 15, 1 | a); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }; }
    function shuffled(arr, seed) { const r = rng(seed), a = arr.slice(); for (let i = a.length - 1; i > 0; i--) { const j = Math.floor(r() * (i + 1)); [a[i], a[j]] = [a[j], a[i]]; } return a; }

    let order = Q.map((_, i) => i);
    if (C.shuffleQuestions) order = shuffled(order, C.seed);
    let cur = 0, started = false, finishing = false, finished = false, ignoring = false, lastV = 0;
    const endAt = Date.now() + C.remaining * 1000;

    // ---------- util ----------
    const $ = id => document.getElementById(id);
    function el(tag, attrs, ...kids) {
        const e = document.createElement(tag);
        Object.entries(attrs || {}).forEach(([k, v]) => { if (k === 'class') e.className = v; else if (k.startsWith('on')) e.addEventListener(k.slice(2), v); else e.setAttribute(k, v); });
        kids.flat().forEach(c => e.append(c instanceof Node ? c : document.createTextNode(c ?? '')));
        return e;
    }
    function toast(msg) { const t = $('toast'); t.textContent = msg; t.style.display = 'block'; clearTimeout(t._h); t._h = setTimeout(() => t.style.display = 'none', 3500); }
    function post(url, body) {
        return fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': C.csrf, 'X-Exam-Token': C.token }, body: JSON.stringify(body) });
    }
    function isAnswered(q) {
        if (q.type === 'esai') return !!essayDone[q.id];
        const a = answers[q.id];
        if (a === null || a === undefined) return false;
        if (Array.isArray(a)) return a.some(x => x !== null);
        if (typeof a === 'object') return Object.keys(a).length > 0;
        return true;
    }
    function saveAnswer(q, value) {
        answers[q.id] = value; renderNav();
        if (!C.urls.answer) return;
        post(C.urls.answer, { question_id: q.id, answer: value })
            .then(r => { if (!r.ok) toast('Jawaban gagal disimpan. Periksa koneksi, lalu pilih ulang.'); })
            .catch(() => toast('Jawaban gagal disimpan. Periksa koneksi, lalu pilih ulang.'));
    }

    // ---------- render soal ----------
    function optionOrder(q, n) {
        const idx = Array.from({ length: n }, (_, i) => i);
        return C.shuffleOptions ? shuffled(idx, C.seed + Number(q.id)) : idx;
    }
    function renderQuestion() {
        const q = Q[order[cur]], box = $('qbox');
        box.replaceChildren();
        box.append(el('div', { style: 'color:#6b7280;font-size:14px' }, `Soal ${cur + 1} dari ${Q.length}` + (q.weight > 0 ? ` · bobot ${q.weight}` : '')));
        if (q.image) box.append(el('img', { class: 'qimg', src: q.image, alt: '' }));
        box.append(el('div', { class: 'qtext' }, q.text));

        if (q.type === 'pilihan_ganda') {
            optionOrder(q, q.options.length).forEach(o => {
                const r = el('input', { type: 'radio', name: 'q' + q.id, value: o });
                if (String(answers[q.id]) === String(o)) r.checked = true;
                r.addEventListener('change', () => saveAnswer(q, Number(o)));
                box.append(el('label', { class: 'opt' }, r, el('span', {}, q.options[o])));
            });
        } else if (q.type === 'pilihan_ganda_kompleks') {
            const now = Array.isArray(answers[q.id]) ? answers[q.id].map(Number) : [];
            box.append(el('div', { style: 'color:#6b7280;font-size:14px;margin-bottom:8px' }, 'Pilih satu atau lebih jawaban.'));
            optionOrder(q, q.options.length).forEach(o => {
                const c = el('input', { type: 'checkbox', value: o });
                if (now.includes(o)) c.checked = true;
                c.addEventListener('change', () => {
                    const vals = [...box.querySelectorAll('input[type=checkbox]:checked')].map(x => Number(x.value)).sort((a, b) => a - b);
                    saveAnswer(q, vals);
                });
                box.append(el('label', { class: 'opt' }, c, el('span', {}, q.options[o])));
            });
        } else if (q.type === 'benar_salah') {
            const vals = Array.isArray(answers[q.id]) ? answers[q.id].slice() : new Array(q.options.length).fill(null);
            q.options.forEach((text, i) => {
                const mk = (label, v) => { const r = el('input', { type: 'radio', name: `bs${q.id}_${i}` }); if (vals[i] === v) r.checked = true; r.addEventListener('change', () => { vals[i] = v; saveAnswer(q, vals.slice()); }); return el('label', { style: 'margin-left:12px' }, r, ' ' + label); };
                box.append(el('div', { class: 'row' }, el('span', {}, text), el('span', {}, mk('Benar', true), mk('Salah', false))));
            });
        } else if (q.type === 'menjodohkan') {
            const now = answers[q.id] && typeof answers[q.id] === 'object' ? Object.assign({}, answers[q.id]) : {};
            q.options.left.forEach((text, i) => {
                const s = el('select', {}, el('option', { value: '' }, '-- pilih --'), q.options.right.map((rt, j) => el('option', { value: j }, rt)));
                if (now[i] !== undefined && now[i] !== null) s.value = String(now[i]);
                s.addEventListener('change', () => { if (s.value === '') delete now[i]; else now[i] = Number(s.value); saveAnswer(q, Object.assign({}, now)); });
                box.append(el('div', { class: 'row' }, el('span', {}, text), s));
            });
        } else if (q.type === 'esai') {
            const status = el('div', { style: 'margin:8px 0;color:#065f46' }, essayDone[q.id] ? 'Foto jawaban sudah diunggah. Unggah lagi untuk menggantinya.' : '');
            const file = el('input', { type: 'file', accept: 'image/*' });
            file.addEventListener('click', () => { ignoring = true; });
            file.addEventListener('change', () => { setTimeout(() => ignoring = false, 800); });
            file.addEventListener('cancel', () => { setTimeout(() => ignoring = false, 800); });
            const btn = el('button', { class: 'btn', type: 'button' }, 'Unggah foto jawaban');
            btn.addEventListener('click', () => {
                if (!file.files[0]) return toast('Pilih foto jawaban terlebih dahulu.');
                if (!C.urls.essay) return toast('Fitur unggah belum tersedia.');
                const fd = new FormData(); fd.append('photo', file.files[0]); fd.append('_token', C.csrf);
                btn.disabled = true; btn.textContent = 'Mengunggah...';
                fetch(C.urls.essay.replace(/\/0(?=$|\?)/, '/' + q.id), { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': C.csrf, 'X-Exam-Token': C.token }, body: fd })
                    .then(r => { if (!r.ok) throw 0; essayDone[q.id] = true; status.textContent = 'Foto jawaban berhasil diunggah.'; renderNav(); })
                    .catch(() => toast('Unggah gagal. Pastikan file gambar maksimal 6 MB.'))
                    .finally(() => { btn.disabled = false; btn.textContent = 'Unggah foto jawaban'; });
            });
            box.append(el('p', { style: 'color:#6b7280;font-size:14px' }, 'Kerjakan di lembar jawaban, lalu foto dan unggah di sini.'), file, el('div', { style: 'margin-top:10px' }, btn), status);
        }

        box.append(el('div', { class: 'actions' },
            el('button', { class: 'btn gray', type: 'button', onclick: () => go(cur - 1) }, '‹ Sebelumnya'),
            cur === Q.length - 1
                ? el('button', { class: 'btn red', type: 'button', onclick: () => finish(false) }, 'Selesai & Kumpulkan')
                : el('button', { class: 'btn', type: 'button', onclick: () => go(cur + 1) }, 'Berikutnya ›')));
        renderNav();
    }
    function go(i) { if (i < 0 || i >= Q.length) return; cur = i; renderQuestion(); window.scrollTo(0, 0); }
    function renderNav() {
        const nav = $('nav'); nav.replaceChildren();
        order.forEach((qi, pos) => nav.append(el('button', { type: 'button', class: (isAnswered(Q[qi]) ? 'done ' : '') + (pos === cur ? 'cur' : ''), onclick: () => go(pos) }, pos + 1)));
    }

    // ---------- timer & selesai ----------
    function finish(auto) {
        if (finishing) return;
        if (!auto && !confirm('Kumpulkan ujian sekarang? Jawaban tidak dapat diubah setelah dikumpulkan.')) return;
        finishing = true; finished = true;
        $('finishForm').submit();
    }
    function tick() {
        const s = Math.max(0, Math.round((endAt - Date.now()) / 1000));
        $('timer').textContent = String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
        if (s <= 0) finish(true);
    }

    // ---------- pelanggaran ----------
    function setVio(n) { $('vio').textContent = `Pelanggaran: ${n}/${C.maxViolations}`; }
    function violation(type, detail) {
        if (!started || finished || ignoring || !C.urls.violation) return;
        const now = Date.now(); if (now - lastV < 1500) return; lastV = now;
        post(C.urls.violation, { type, detail }).then(r => r.json()).then(d => {
            setVio(d.count);
            if (d.finished) { finishing = true; finished = true; toast('Ujian diakhiri karena pelanggaran melebihi batas.'); setTimeout(() => location.reload(), 1500); }
            else toast('Pelanggaran tercatat: ' + detail);
        }).catch(() => {});
    }
    document.addEventListener('visibilitychange', () => { if (document.hidden) violation('tab_switch', 'Berpindah tab'); });
    window.addEventListener('blur', () => violation('window_blur', 'Jendela kehilangan fokus'));
    document.addEventListener('fullscreenchange', () => { if (!document.fullscreenElement) violation('fullscreen_exit', 'Keluar dari layar penuh'); });
    ['copy', 'cut', 'paste'].forEach(ev => document.addEventListener(ev, e => { e.preventDefault(); violation('copy_paste', 'Mencoba ' + ev); }));
    document.addEventListener('contextmenu', e => e.preventDefault());

    // ---------- catat keluar dari halaman ----------
    window.addEventListener('pagehide', () => {
        if (finishing || !C.urls.leave) return;
        const fd = new FormData(); fd.append('token', C.token); fd.append('_token', C.csrf);
        navigator.sendBeacon(C.urls.leave, fd);
    });

    // ---------- mulai ----------
    $('startBtn').addEventListener('click', async () => {
        try { await document.documentElement.requestFullscreen(); } catch (e) {}
        started = true; $('gate').style.display = 'none';
        setInterval(tick, 1000); tick(); renderQuestion();
    });
    setVio(0);
})();
</script>
</body>
</html>