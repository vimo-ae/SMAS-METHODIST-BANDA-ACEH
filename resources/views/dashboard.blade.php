<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color:#172033;">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-green: #4338CA;
            --c-green-dark: #2E2A7F;
            --c-ivory: #F8F9FC;
            --c-sage: #E0E7FF;
            --c-navy: #0F172A;
            --c-pale: #EEF2FF;
            --c-muted: #475569;s
            --c-border: #E5E7EB;
            --c-orange-soft: #E0E7FF;
            --c-orange: #6366F1;
        }

        .d-page { background: var(--c-ivory); min-height: 100vh; padding: 56px 0 72px; overflow: hidden; }
        .d-wrap { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        .d-hero { display: grid; grid-template-columns: 1fr; gap: 48px; align-items: center; }
        @media (min-width: 900px) { .d-hero { grid-template-columns: 1.1fr 0.9fr; } }

        .d-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--c-sage); color: var(--c-green);
            border-radius: 999px; padding: 9px 18px;
            font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 22px;
        }
        .d-title {
            font-family: 'Playfair Display', serif;
            color: var(--c-navy); font-weight: 700;
            font-size: clamp(34px, 5vw, 56px); line-height: 1.1;
            letter-spacing: -1px; margin: 0 0 18px;
        }
        .d-title em { font-style: normal; color: var(--c-green); }
        .d-desc { color: var(--c-muted); line-height: 1.8; max-width: 520px; margin: 0 0 30px; }

        .d-actions { display: flex; flex-wrap: wrap; gap: 14px; }
        .d-btn {
            display: inline-flex; align-items: center; gap: 10px;
            border-radius: 999px; padding: 14px 26px;
            font-weight: 600; font-size: 15px; text-decoration: none;
            transition: transform .2s, background .2s, box-shadow .2s;
        }
        .d-btn-primary { background: var(--c-green); color: #fff; box-shadow: 0 12px 24px rgba(32, 92, 59, .25); }
        .d-btn-primary:hover { background: var(--c-green-dark); color: #fff; transform: translateY(-2px); }
        .d-btn-soft { background: var(--c-sage); color: var(--c-navy); }
        .d-btn-soft:hover { background: #DEDCC8; color: var(--c-navy); transform: translateY(-2px); }

        /* Panel kanan */
        .d-visual { position: relative; min-height: 360px; }
        .d-panel {
            position: absolute; inset: 0 24px 24px 0;
            background: linear-gradient(145deg, var(--c-green) 0%, var(--c-green-dark) 100%);
            border-radius: 32px;
            box-shadow: 0 24px 50px rgba(23, 32, 51, .15);
        }
        .d-panel::after {
            content: ''; position: absolute; right: -40px; bottom: -40px;
            width: 220px; height: 220px; border-radius: 50%;
            background: rgba(255, 255, 255, .07);
        }
        .d-float {
            position: absolute; display: flex; align-items: center; gap: 14px;
            background: #fff; border-radius: 18px; padding: 16px 22px;
            box-shadow: 0 14px 34px rgba(23, 32, 51, .12);
            text-decoration: none; transition: transform .2s;
        }
        .d-float:hover { transform: translateY(-3px); }
        .d-float-1 { top: 36px; right: 0; }
        .d-float-2 { bottom: 0; left: 0; }
        .d-ico {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .d-ico svg { width: 22px; height: 22px; }
        .d-ico-orange { background: var(--c-orange-soft); color: var(--c-orange); }
        .d-ico-green { background: var(--c-pale); color: var(--c-green); }
        .d-float small {
            display: block; color: var(--c-muted); font-size: 11px;
            font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
        }
        .d-float strong { font-family: 'Playfair Display', serif; color: var(--c-navy); font-size: 17px; }

        /* Kartu pintasan */
        .d-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 64px; }
        @media (min-width: 768px) { .d-grid { grid-template-columns: 1fr 1fr; } }
        .d-card {
            display: block; text-decoration: none; background: #fff;
            border: 1px solid var(--c-border); border-radius: 22px; padding: 28px;
            box-shadow: 0 8px 28px rgba(23, 32, 51, .045);
            transition: transform .2s, border-color .2s, box-shadow .2s;
        }
        .d-card:hover { transform: translateY(-3px); border-color: #B9CDBD; box-shadow: 0 14px 34px rgba(23, 32, 51, .09); }
        .d-card h3 { font-family: 'Playfair Display', serif; color: var(--c-navy); font-size: 22px; margin: 16px 0 8px; }
        .d-card p { color: var(--c-muted); line-height: 1.7; margin: 0 0 18px; }
        .d-card .more { color: var(--c-green); font-weight: 600; font-size: 14px; }
    </style>

    <div class="d-page">
        <div class="d-wrap">

            <section class="d-hero">
                <div>
                    <span class="d-badge">{{ __('Portal Siswa') }}</span>
                    <h1 class="d-title">
                        {{ __('Halo,') }} <em>{{ Auth::user()->name }}</em>.<br>
                        {{ __('Siap belajar hari ini?') }}
                    </h1>
                    <p class="d-desc">
                        {{ __('Akses kelas, materi pembelajaran, serta ujian dan kuis kamu dari satu tempat.') }}
                    </p>
                    <div class="d-actions">
                        <a href="{{ route('siswa.classes.index') }}" class="d-btn d-btn-primary">
                            {{ __('Buka E-Class') }} <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ route('siswa.exams.index') }}" class="d-btn d-btn-soft">
                            {{ __('Lihat Ujian') }}
                        </a>
                    </div>
                </div>

                <div class="d-visual" aria-hidden="true">
                    <div class="d-panel"></div>

                    <a href="{{ route('siswa.exams.index') }}" class="d-float d-float-1">
                        <span class="d-ico d-ico-orange">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <span><small>{{ __('Ujian & Kuis') }}</small><strong>{{ __('E-Exam') }}</strong></span>
                    </a>

                    <a href="{{ route('siswa.classes.index') }}" class="d-float d-float-2">
                        <span class="d-ico d-ico-green">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                            </svg>
                        </span>
                        <span><small>{{ __('Kelas & Materi') }}</small><strong>{{ __('E-Class') }}</strong></span>
                    </a>
                </div>
            </section>

            <div class="d-grid">
                <a href="{{ route('siswa.classes.index') }}" class="d-card">
                    <span class="d-ico d-ico-green">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                        </svg>
                    </span>
                    <h3>{{ __('E-Class') }}</h3>
                    <p>{{ __('Buka kelas dan materi pembelajaran.') }}</p>
                    <span class="more">{{ __('Buka E-Class') }} &rarr;</span>
                </a>

                <a href="{{ route('siswa.exams.index') }}" class="d-card">
                    <span class="d-ico d-ico-orange">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <h3>{{ __('E-Exam') }}</h3>
                    <p>{{ __('Lihat ujian dan kuis yang tersedia.') }}</p>
                    <span class="more">{{ __('Buka E-Exam') }} &rarr;</span>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>