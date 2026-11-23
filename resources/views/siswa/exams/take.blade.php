<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title ?? 'Ujian' }}</title>
    <link rel="stylesheet" href="{{ asset('css/exam.css') }}">
    <style>
        /* =========================================================
           CINEMATIC HOLLYWOOD / SCI-FI PROCTORING ALERT STYLES
           ========================================================= */
        #screen-alarm-overlay {
            position: fixed;
            inset: 0;
            z-index: 998;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: inset 0 0 120px rgba(220, 38, 38, 0.85), inset 0 0 40px rgba(255, 0, 0, 0.6);
        }
        #screen-alarm-overlay.is-active {
            opacity: 1;
            animation: cinemaVignettePulse 1.2s infinite alternate ease-in-out;
        }
        @keyframes cinemaVignettePulse {
            0% {
                box-shadow: inset 0 0 80px rgba(220, 38, 38, 0.6), inset 0 0 20px rgba(239, 68, 68, 0.4);
            }
            100% {
                box-shadow: inset 0 0 160px rgba(220, 38, 38, 0.95), inset 0 0 60px rgba(255, 0, 0, 0.8);
            }
        }

        .ai-proctor-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #059669;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
        .ai-proctor-pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: proctorPulse 1.5s infinite;
        }
        @keyframes proctorPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(1.3); }
        }
        .ai-proctor-badge.is-warning {
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.5);
            color: #d97706;
        }
        .ai-proctor-badge.is-warning .ai-proctor-pulse {
            background: #f59e0b;
            box-shadow: 0 0 8px #f59e0b;
        }
        .ai-proctor-badge.is-danger {
            background: rgba(220, 38, 38, 0.2);
            border-color: rgba(220, 38, 38, 0.65);
            color: #dc2626;
            animation: badgeShake 0.4s infinite;
        }
        .ai-proctor-badge.is-danger .ai-proctor-pulse {
            background: #dc2626;
            box-shadow: 0 0 10px #dc2626;
        }
        @keyframes badgeShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-1.5px); }
            75% { transform: translateX(1.5px); }
        }

        .modal-overlay--cinematic {
            background: rgba(4, 7, 15, 0.9) !important;
            backdrop-filter: blur(8px) !important;
            z-index: 9999 !important;
        }
        .cinema-box {
            position: relative;
            background: #090e1a;
            border: 1px solid #1f293d;
            border-radius: 22px;
            box-shadow: 0 0 70px rgba(0, 0, 0, 0.9), 0 0 45px rgba(220, 38, 38, 0.35);
            color: #f1f5f9;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            animation: cinemaPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes cinemaPop {
            from { transform: scale(0.88) translateY(15px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        @keyframes cinemaShakeGlitch {
            0% { transform: scale(1) translate(0, 0); }
            12% { transform: scale(1.03) translate(-8px, 4px); filter: hue-rotate(-20deg) brightness(1.2); }
            25% { transform: scale(1.02) translate(8px, -4px); filter: hue-rotate(20deg) brightness(1.3); }
            38% { transform: scale(1.04) translate(-6px, -3px); }
            50% { transform: scale(1.02) translate(6px, 3px); }
            65% { transform: scale(1.01) translate(-4px, 1px); }
            80% { transform: scale(1.01) translate(3px, -1px); }
            100% { transform: scale(1) translate(0, 0); filter: none; }
        }
        .cinema-box--shake {
            animation: cinemaShakeGlitch 0.55s cubic-bezier(0.36, 0.07, 0.19, 0.97) both !important;
        }

        .hazard-stripe {
            height: 7px;
            width: 100%;
            background: repeating-linear-gradient(
                -45deg,
                #dc2626,
                #dc2626 12px,
                #111827 12px,
                #111827 24px
            );
            background-size: 200% 200%;
            animation: hazardMove 8s linear infinite;
        }
        .hazard-stripe--lockdown {
            background: repeating-linear-gradient(
                -45deg,
                #991b1b,
                #991b1b 12px,
                #000000 12px,
                #000000 24px
            );
        }
        @keyframes hazardMove {
            0% { background-position: 0 0; }
            100% { background-position: 100% 0; }
        }

        .cinema-inner {
            padding: 24px 26px;
        }
        .hud-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
            margin-bottom: 16px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
        .hud-top__left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .live-rec-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            box-shadow: 0 0 10px #ef4444;
            animation: recBlink 0.9s infinite;
        }
        @keyframes recBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.15; }
        }
        .hud-code {
            font-size: 0.68rem;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.08em;
        }
        .hud-threat-badge {
            background: rgba(220, 38, 38, 0.2);
            border: 1px solid rgba(220, 38, 38, 0.5);
            color: #f87171;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.06em;
        }

        .cinema-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .radar-scan-wrapper {
            position: relative;
            width: 62px;
            height: 62px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .radar-ping {
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px solid rgba(239, 68, 68, 0.7);
            animation: radarRipple 1.6s infinite ease-out;
        }
        @keyframes radarRipple {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        .radar-icon {
            position: relative;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: radial-gradient(circle, #7f1d1d 0%, #450a0a 100%);
            border: 2px solid #ef4444;
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .radar-icon svg {
            width: 26px;
            height: 26px;
        }
        .cinema-title {
            font-size: 1.25rem;
            font-weight: 900;
            color: #fee2e2;
            letter-spacing: 0.04em;
            margin: 0 0 4px;
            text-shadow: 0 0 15px rgba(239, 68, 68, 0.6);
        }
        .cinema-subtitle {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #f87171;
            margin: 0;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }

        .terminal-card {
            background: #040711;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 11px 14px;
            margin-bottom: 14px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 0.73rem;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.6);
        }
        .terminal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 3px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .terminal-row:last-child {
            border-bottom: none;
        }
        .terminal-label {
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.05em;
            flex-shrink: 0;
        }
        .terminal-val {
            color: #cbd5e1;
            font-weight: 700;
            text-align: right;
        }
        .terminal-val--code {
            color: #38bdf8;
        }
        .terminal-val--danger {
            color: #ef4444;
            font-weight: 800;
        }

        .strike-container {
            background: rgba(220, 38, 38, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.35);
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }
        .strike-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #fca5a5;
            margin-bottom: 8px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
        .strike-bars {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
        }
        .strike-bar-item {
            flex: 1;
            height: 9px;
            border-radius: 4px;
            background: #1e293b;
            border: 1px solid #334155;
            transition: all 0.3s ease;
        }
        .strike-bar-item.is-hit {
            background: #ef4444;
            border-color: #f87171;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.85);
            animation: hitPulse 0.8s infinite alternate;
        }
        @keyframes hitPulse {
            from { opacity: 0.75; }
            to { opacity: 1; box-shadow: 0 0 14px rgba(239, 68, 68, 1); }
        }
        .strike-bar-item.is-final {
            background: #7f1d1d;
            border-color: #991b1b;
        }
        .strike-warning {
            font-size: 0.78rem;
            color: #fecaca;
            margin: 0;
            line-height: 1.45;
            text-align: center;
        }
        .strike-warning strong {
            color: #ffffff;
        }

        .cinema-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .btn-sound-toggle {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #111827;
            border: 1px solid #374151;
            color: #9ca3af;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            flex-shrink: 0;
        }
        .btn-sound-toggle:hover {
            background: #1f2937;
            color: #f3f4f6;
            border-color: #4b5563;
        }
        .btn-cinema-resume {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            cursor: pointer;
            border: 0;
            transition: all 0.25s;
            font-family: inherit;
        }
        .btn-cinema-resume:disabled {
            background: #162032;
            color: #64748b;
            border: 1px solid #1e293b;
            cursor: not-allowed;
            opacity: 0.9;
        }
        .btn-cinema-resume:not(:disabled) {
            background: #123b60;
            color: #ffffff;
            border: 1px solid #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.45);
        }
        .btn-cinema-resume:not(:disabled):hover {
            background: #0284c7;
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.75);
            transform: translateY(-1px);
        }
        .incident-notice {
            font-size: 0.68rem;
            color: #64748b;
            text-align: center;
            margin: 0;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }

        /* Lockdown Disqualified Modal */
        .cinema-box--lockdown {
            border-color: #991b1b;
            box-shadow: 0 0 80px rgba(153, 27, 27, 0.8);
            background: #05070f;
        }
        .lockdown-badge {
            display: inline-block;
            background: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fee2e2;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            margin-bottom: 14px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            animation: lockBlink 1s infinite alternate;
        }
        @keyframes lockBlink {
            0% { opacity: 0.7; }
            100% { opacity: 1; box-shadow: 0 0 15px rgba(239, 68, 68, 0.8); }
        }
        .lockdown-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            color: #ef4444;
            animation: pulseLockIcon 1.2s infinite alternate;
        }
        @keyframes pulseLockIcon {
            from { transform: scale(0.95); opacity: 0.8; }
            to { transform: scale(1.08); opacity: 1; filter: drop-shadow(0 0 12px #ef4444); }
        }
        .lockdown-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0 0 4px;
            letter-spacing: 0.04em;
            text-shadow: 0 0 15px rgba(239, 68, 68, 0.8);
            text-align: center;
        }
        .lockdown-subtitle {
            font-size: 0.72rem;
            color: #f87171;
            font-weight: 800;
            letter-spacing: 0.1em;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            margin: 0 0 16px;
            text-align: center;
        }
        .lockdown-terminal {
            background: #000000;
            border: 1px solid #7f1d1d;
            border-radius: 12px;
            padding: 14px;
            text-align: left;
            font-size: 0.8rem;
            color: #fca5a5;
            margin-bottom: 16px;
            line-height: 1.5;
        }
        .lockdown-meta {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed rgba(239, 68, 68, 0.3);
            font-size: 0.72rem;
            color: #cbd5e1;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
        .redirect-countdown {
            font-size: 0.82rem;
            color: #94a3b8;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            text-align: center;
        }
        .redirect-countdown span {
            color: #ef4444;
            font-weight: 900;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="no-select">

{{-- Screen Emergency Alarm Red Vignette Flash --}}
<div id="screen-alarm-overlay"></div>

{{-- ===== Top bar ===== --}}
<header class="take-topbar">
    <div class="take-topbar__title">
        {{ $exam->title ?? 'Ujian' }}
        <small>{{ optional(optional($exam->classSubjectTeacher)->subject)->name }}</small>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div class="ai-proctor-badge" id="ai-proctor-pill">
            <span class="ai-proctor-pulse"></span>
            <span id="ai-proctor-text">AI PROCTOR LIVE</span>
        </div>
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
        <div class="modal__icon">
            <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto; color: var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <h2>Siap mengerjakan?</h2>
        <p>Ujian berjalan dalam layar penuh dengan pengawasan AI aktif. Berpindah tab/jendela, keluar layar penuh, atau menyalin teks
            akan dicatat sebagai pelanggaran keamanan. Maksimal {{ $maxViolations }} pelanggaran, setelah itu akun ujian
            otomatis didiskualifikasi.</p>
        <div class="modal__actions">
            <button class="btn btn-primary" id="btn-start">Mulai Layar Penuh</button>
        </div>
    </div>
</div>

{{-- ===== Modal peringatan pelanggaran (HOLLYWOOD / SCI-FI CINEMATIC) ===== --}}
<div class="modal-overlay modal-overlay--cinematic" id="warn-modal">
    <div class="cinema-box">
        <div class="hazard-stripe"></div>
        <div class="cinema-inner">
            <div class="hud-top">
                <div class="hud-top__left">
                    <span class="live-rec-dot"></span>
                    <span class="hud-code">AI-PROCTOR DEFENSE // PROTOCOL 4.2</span>
                </div>
                <div class="hud-top__right">
                    <span class="hud-threat-badge" id="hud-threat-level">THREAT LEVEL: HIGH</span>
                </div>
            </div>

            <div class="cinema-header">
                <div class="radar-scan-wrapper">
                    <div class="radar-ping"></div>
                    <div class="radar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="cinema-title" id="cinema-title-text">PELANGGARAN TERDETEKSI</h2>
                <p class="cinema-subtitle" id="cinema-subtitle-text">SECURITY INTEGRITY BREACH &bull; LOG RECORDED</p>
            </div>

            <div class="terminal-card">
                <div class="terminal-row">
                    <span class="terminal-label">INCIDENT ID:</span>
                    <span class="terminal-val terminal-val--code" id="incident-code">#SEC-9812</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-label">JENIS PELANGGARAN:</span>
                    <span class="terminal-val terminal-val--danger" id="violation-type-label">PERPINDAHAN JENDELA</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-label">WAKTU DETEKSI:</span>
                    <span class="terminal-val" id="incident-timestamp">--:--:-- WIB</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-label">IDENTITAS SISWA:</span>
                    <span class="terminal-val">{{ Auth::user()->name }} (NIS: {{ Auth::user()->user_id }})</span>
                </div>
            </div>

            <div class="strike-container">
                <div class="strike-header">
                    <span>AKUMULASI PELANGGARAN</span>
                    <span id="strike-text-count" style="color: #f87171; font-weight: 800;">1 DARI {{ $maxViolations }}</span>
                </div>
                <div class="strike-bars" id="strike-bars-holder"></div>
                <p class="strike-warning" id="strike-warning-desc">
                    Sisa toleransi: <strong><span id="strike-remaining">2</span> kali lagi</strong> sebelum sistem melakukan <strong>DISKUALIFIKASI OTOMATIS</strong> dan membekukan ujian!
                </p>
            </div>

            <div class="cinema-actions">
                <button type="button" class="btn-sound-toggle" id="btn-sound-toggle" title="Matikan/Nyalakan Suara Alarm">
                    <span id="sound-icon">🔊</span> <span id="sound-text">Sirene Aktif</span>
                </button>
                <button type="button" class="btn-cinema-resume" id="btn-warn-ok" disabled>
                    <span id="resume-btn-text">Mengunci Protokol Keamanan... (3s)</span>
                </button>
            </div>
            <p class="incident-notice">
                Seluruh aktivitas layar dan perpindahan fokus telah dicatat permanen di log proktor.
            </p>
        </div>
        <div class="hazard-stripe"></div>
    </div>
</div>

{{-- ===== Modal diskualifikasi (FULL SYSTEM LOCKDOWN) ===== --}}
<div class="modal-overlay modal-overlay--cinematic" id="disqualified-modal">
    <div class="cinema-box cinema-box--lockdown">
        <div class="hazard-stripe hazard-stripe--lockdown"></div>
        <div class="cinema-inner">
            <div style="text-align: center;">
                <span class="lockdown-badge">SYSTEM LOCKDOWN // TERMINATED</span>
                <div class="lockdown-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h2 class="lockdown-title">SESI UJIAN DIDISKUALIFIKASI</h2>
                <p class="lockdown-subtitle">BATAS MAKSIMAL PELANGGARAN TELAH TERLEWATI</p>
            </div>

            <div class="lockdown-terminal">
                <p>Status pengerjaan Anda telah dihentikan secara otomatis oleh sistem pengawasan keamanan SMAS Methodist Banda Aceh.</p>
                <div class="lockdown-meta">
                    <div><strong>Alasan:</strong> Pelanggaran mencapai batas toleransi ({{ $maxViolations }}/{{ $maxViolations }}).</div>
                    <div><strong>Tindakan:</strong> Lembar jawaban dibekukan &amp; diteruskan ke Proktor Pengawas.</div>
                </div>
            </div>

            <div class="redirect-countdown">
                Mengalihkan ke halaman riwayat ujian dalam <span id="redirect-timer">5</span> detik...
            </div>
        </div>
        <div class="hazard-stripe hazard-stripe--lockdown"></div>
    </div>
</div>

{{-- ===== Modal konfirmasi selesai ===== --}}
<div class="modal-overlay" id="finish-modal">
    <div class="modal">
        <div class="modal__icon">
            <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto; color: #059669;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
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
        if (q.type === 'esai') { answers[q.id] = { text: s && typeof s.answer === 'string' ? s.answer : (s && s.answer ? String(s.answer) : '') }; return; }
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
        if (q.type === 'esai') return !!(a && a.text && a.text.trim().length);
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
        if (q.type === 'esai') payload = JSON.stringify(a.text || '');
        else if (q.type === 'pg') payload = String(L2I(a));                       // indeks, mis. "3"
        else if (q.type === 'pg_kompleks') payload = JSON.stringify(a.map(L2I)); // mis. [0,2]
        else payload = JSON.stringify(a);                                    // benar_salah: [true,false,...]
        setStatus('Menyimpan...');
        try {
            const isEssay = q.type === 'esai';
            const res = await fetch(isEssay ? URL_ESSAY_TPL.replace(/\/0$/, '/' + q.id) : URL_ANSWER, {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, headers()),
                body: JSON.stringify(isEssay ? { answer: a.text || '', token: TOKEN } : { question_id: q.id, answer: payload, token: TOKEN }),
            });
            if (res.status === 403) { return autoFinish(); }
            if (!res.ok) throw new Error();
            setStatus('Tersimpan', true);
        } catch (e) {
            setStatus('Gagal menyimpan, periksa koneksi.');
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
        // Menampilkan gambar soal jika tersedia
        if (q.image) {
            const img = el('img', 'question-card__image');

            img.src = q.image;
            img.alt = 'Gambar soal';

            img.onload = () => {
                console.log('Gambar soal berhasil dimuat:', q.image);
            };

            img.onerror = () => {
                console.error('Gambar soal gagal dimuat:', q.image);
            };

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
        const wrap = el('div', 'space-y-3');
        const hint = el('p', 'text-sm text-slate-600', 'Ketik jawaban esai langsung pada kolom di bawah ini. Jawaban tersimpan otomatis.');
        const textarea = document.createElement('textarea');
        textarea.className = 'w-full min-h-48 rounded-xl border border-slate-300 p-4 text-slate-800 focus:border-sky-500 focus:ring-sky-500';
        textarea.placeholder = 'Tulis jawabanmu di sini...';
        textarea.maxLength = 20000;
        textarea.value = (answers[q.id] && answers[q.id].text) || '';
        let saveTimer;
        textarea.addEventListener('input', () => {
            answers[q.id] = { text: textarea.value };
            renderNav();
            clearTimeout(saveTimer);
            setStatus('Menunggu penyimpanan...');
            saveTimer = setTimeout(() => saveAnswer(q), 600);
        });
        wrap.append(hint, textarea);
        return wrap;
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

    // ---------- AudioFX Synthesizer (Web Audio API) ----------
    const AudioFX = {
        ctx: null,
        soundEnabled: true,
        alarmInterval: null,
        init() {
            if (!this.ctx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) this.ctx = new AudioContext();
            }
            if (this.ctx && this.ctx.state === 'suspended') {
                this.ctx.resume().catch(() => {});
            }
        },
        playAlarm() {
            if (!this.soundEnabled) return;
            this.init();
            if (!this.ctx) return;
            this.stopAlarm();

            let high = true;
            const beep = () => {
                if (!this.soundEnabled || !this.ctx) return;
                try {
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(high ? 880 : 620, this.ctx.currentTime);
                    gain.gain.setValueAtTime(0.25, this.ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.32);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start();
                    osc.stop(this.ctx.currentTime + 0.35);
                    high = !high;
                } catch(e) {}
            };
            beep();
            this.alarmInterval = setInterval(beep, 420);
        },
        stopAlarm() {
            if (this.alarmInterval) {
                clearInterval(this.alarmInterval);
                this.alarmInterval = null;
            }
        },
        playLockdown() {
            if (!this.soundEnabled) return;
            this.init();
            if (!this.ctx) return;
            this.stopAlarm();
            try {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(180, this.ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(35, this.ctx.currentTime + 2.5);
                gain.gain.setValueAtTime(0.45, this.ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 2.5);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start();
                osc.stop(this.ctx.currentTime + 2.5);
            } catch(e) {}
        },
        playAcknowledge() {
            if (!this.soundEnabled) return;
            this.init();
            if (!this.ctx) return;
            try {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, this.ctx.currentTime);
                osc.frequency.setValueAtTime(880, this.ctx.currentTime + 0.12);
                gain.gain.setValueAtTime(0.2, this.ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.45);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start();
                osc.stop(this.ctx.currentTime + 0.45);
            } catch(e) {}
        }
    };

    // ---------- Pelanggaran (HOLLYWOOD / SCI-FI CINEMATIC) ----------
    let lastViolationAt = 0;
    let resumeCountdownInterval = null;

    async function reportViolation(type, detail) {
        if (!examStarted || finishing) return;
        const now = Date.now();
        if (now - lastViolationAt < 800) return; // cegah duplikasi instan dari multiple event blur/hide
        lastViolationAt = now;

        try {
            const res = await fetch(URL_VIOLATION, {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, headers()),
                body: JSON.stringify({ type: type, detail: detail || null, token: TOKEN }),
            });
            if (!res.ok) {
                console.error('Peringatan proktor gagal dikirim, status:', res.status);
                return;
            }
            const d = await res.json();
            $('v-count').textContent = d.count;

            // Re-trigger Red Alert Vignette Flash
            const screenOverlay = $('screen-alarm-overlay');
            if (screenOverlay) {
                screenOverlay.classList.remove('is-active');
                void screenOverlay.offsetWidth; // force reflow untuk animasi ulang
                screenOverlay.classList.add('is-active');
            }

            // Animate Modal Shake & Hollywood Glitch
            const cinemaBox = document.querySelector('#warn-modal .cinema-box');
            if (cinemaBox) {
                cinemaBox.classList.remove('cinema-box--shake');
                void cinemaBox.offsetWidth; // force reflow
                cinemaBox.classList.add('cinema-box--shake');
            }

            // Update Topbar Badge
            if ($('ai-proctor-pill')) {
                $('ai-proctor-pill').className = 'ai-proctor-badge is-danger';
                $('ai-proctor-text').textContent = '⚠️ BREACH: STRIKE ' + d.count + '/' + d.max;
            }

            // Dynamic Threat Level & Header text based on Strike count
            const hudThreat = $('hud-threat-level');
            const cinemaTitle = $('cinema-title-text');
            const cinemaSub = $('cinema-subtitle-text');

            if (d.count === 1) {
                if (hudThreat) {
                    hudThreat.textContent = 'THREAT LEVEL: MODERATE (STRIKE 1/' + d.max + ')';
                    hudThreat.style.background = 'rgba(234, 179, 8, 0.2)';
                    hudThreat.style.borderColor = 'rgba(234, 179, 8, 0.6)';
                    hudThreat.style.color = '#fde047';
                }
                if (cinemaTitle) cinemaTitle.textContent = 'PELANGGARAN TERDETEKSI (STRIKE 1)';
                if (cinemaSub) cinemaSub.innerHTML = 'SECURITY INTEGRITY BREACH &bull; LOG RECORDED';
            } else if (d.count >= 2 && !d.finished) {
                if (hudThreat) {
                    hudThreat.textContent = 'THREAT LEVEL: CRITICAL (STRIKE ' + d.count + '/' + d.max + ' - FINAL WARNING)';
                    hudThreat.style.background = 'rgba(220, 38, 38, 0.35)';
                    hudThreat.style.borderColor = '#ef4444';
                    hudThreat.style.color = '#fee2e2';
                }
                if (cinemaTitle) cinemaTitle.textContent = 'PERINGATAN KRITIS TERAKHIR (STRIKE ' + d.count + ')';
                if (cinemaSub) cinemaSub.innerHTML = '⚠️ PERINGATAN TERAKHIR: 1 KALI LAGI AKAN MEMICU DISKUALIFIKASI TOTAL!';
            }

            // Update Terminal Info
            const incidentId = '#SEC-' + Math.floor(1000 + Math.random() * 9000);
            if ($('incident-code')) $('incident-code').textContent = incidentId;
            
            const nowTime = new Date();
            const timeStr = String(nowTime.getHours()).padStart(2, '0') + ':' + 
                            String(nowTime.getMinutes()).padStart(2, '0') + ':' + 
                            String(nowTime.getSeconds()).padStart(2, '0') + ' WIB';
            if ($('incident-timestamp')) $('incident-timestamp').textContent = timeStr;

            const labels = {
                tab_switch: 'PERPINDAHAN JENDELA / APLIKASI (TAB SWITCH)',
                window_blur: 'JENDELA UJIAN HILANG FOKUS (UNFOCUSED)',
                fullscreen_exit: 'KELUAR DARI LAYAR PENUH (FULLSCREEN DISENGAGED)',
                copy_paste: 'UPAYA MENYALIN / MENEMPEL KONTEN DIBLOKIR',
            };
            if ($('violation-type-label')) $('violation-type-label').textContent = labels[type] || 'PELANGGARAN KEAMANAN TERDETEKSI';

            // Strike Indicators
            if ($('strike-text-count')) $('strike-text-count').textContent = d.count + ' DARI ' + d.max + ' STRIKE';
            if ($('strike-remaining')) $('strike-remaining').textContent = Math.max(0, d.max - d.count);

            if ($('strike-bars-holder')) {
                let barsHtml = '';
                for (let i = 1; i <= d.max; i++) {
                    const isHit = i <= d.count;
                    const isFinal = i === d.max;
                    barsHtml += '<div class="strike-bar-item ' + (isHit ? 'is-hit ' : '') + (isFinal ? 'is-final' : '') + '"></div>';
                }
                $('strike-bars-holder').innerHTML = barsHtml;
            }

            // Jika batas tercapai (DISKUALIFIKASI)
            if (d.finished) {
                finishing = true;
                AudioFX.playLockdown();
                $('warn-modal').classList.remove('is-open');
                $('disqualified-modal').classList.add('is-open');

                if ($('disq-max')) $('disq-max').textContent = d.max;
                
                let cd = 5;
                const rTimer = $('redirect-timer');
                const disqInterval = setInterval(() => {
                    cd--;
                    if (rTimer) rTimer.textContent = cd;
                    if (cd <= 0) {
                        clearInterval(disqInterval);
                        window.location.href = URL_SHOW;
                    }
                }, 1000);
                return;
            }

            // Play Alarm Siren
            AudioFX.playAlarm();

            // Buka Modal Peringatan
            $('warn-modal').classList.add('is-open');

            // Countdown Cooldown Tombol "Lanjut Mengerjakan" (3 detik lock di setiap strike)
            const btnOk = $('btn-warn-ok');
            const txtOk = $('resume-btn-text');
            btnOk.disabled = true;
            if (resumeCountdownInterval) clearInterval(resumeCountdownInterval);

            let lockSeconds = 3;
            txtOk.textContent = 'Mengunci Protokol Keamanan... (' + lockSeconds + 's)';
            
            resumeCountdownInterval = setInterval(() => {
                lockSeconds--;
                if (lockSeconds > 0) {
                    txtOk.textContent = 'Mengunci Protokol Keamanan... (' + lockSeconds + 's)';
                } else {
                    clearInterval(resumeCountdownInterval);
                    resumeCountdownInterval = null;
                    btnOk.disabled = false;
                    txtOk.textContent = d.count >= (d.max - 1) 
                        ? 'SAYA PAHAM (PERINGATAN TERAKHIR), KEMBALI KE UJIAN' 
                        : 'SAYA MENGERTI, KEMBALI KE UJIAN & LAYAR PENUH';
                }
            }, 1000);

        } catch (e) {
            console.error('Proctoring error:', e);
        }
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
        AudioFX.init(); // unlock audio context
        const root = document.documentElement;
        const req = root.requestFullscreen ? root.requestFullscreen() : Promise.resolve();
        Promise.resolve(req).catch(() => {}).then(() => {
            examStarted = true;
            $('start-modal').classList.remove('is-open');
        });
    };

    $('btn-sound-toggle').onclick = () => {
        AudioFX.soundEnabled = !AudioFX.soundEnabled;
        if (!AudioFX.soundEnabled) {
            AudioFX.stopAlarm();
            $('sound-icon').textContent = '🔇';
            $('sound-text').textContent = 'Sirene Senyap';
        } else {
            $('sound-icon').textContent = '🔊';
            $('sound-text').textContent = 'Sirene Aktif';
            if ($('warn-modal').classList.contains('is-open')) {
                AudioFX.playAlarm();
            }
        }
    };

    $('btn-warn-ok').onclick = () => {
        AudioFX.stopAlarm();
        AudioFX.playAcknowledge();
        $('screen-alarm-overlay').classList.remove('is-active');
        $('warn-modal').classList.remove('is-open');

        // Kembalikan badge topbar ke state warning jika belum diskualifikasi
        if ($('ai-proctor-pill') && !finishing) {
            $('ai-proctor-pill').className = 'ai-proctor-badge is-warning';
        }

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