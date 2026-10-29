<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color:#172033;">{{ __('E-Exam') }}</h2>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --exam-green: #205C3B;
            --exam-green-dark: #17452D;
            --exam-ivory: #FAF8F4;
            --exam-sage: #E9E8D9;
            --exam-navy: #172033;
            --exam-pale-green: #DDE9DF;
            --exam-muted: #73766F;
            --exam-border: #E8E5DD;
            --exam-orange-soft: #FCE7DA;
            --exam-orange: #E8742C;
        }

        .exam-index { background: var(--exam-ivory); min-height: 100vh; padding: 48px 0 64px; }
        .exam-index .wrap { max-width: 1050px; margin: 0 auto; padding: 0 24px; }

        .exam-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--exam-sage); color: var(--exam-green);
            border-radius: 999px; padding: 8px 16px;
            font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 16px;
        }
        .exam-index h3.page-title {
            font-family: 'Playfair Display', serif;
            color: var(--exam-navy); font-size: clamp(28px, 4vw, 40px);
            line-height: 1.2; font-weight: 700; margin: 0 0 10px;
        }
        .exam-index h3.page-title span { color: var(--exam-green); }
        .exam-index .page-desc { color: var(--exam-muted); line-height: 1.8; max-width: 600px; margin-bottom: 32px; }

        .exam-item {
            background: #fff; border: 1px solid var(--exam-border); border-radius: 20px;
            padding: 26px; margin-bottom: 20px;
            box-shadow: 0 8px 28px rgba(23, 32, 51, 0.045);
            transition: transform .2s, box-shadow .2s;
        }
        .exam-item:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(23, 32, 51, 0.08); }

        .exam-item-top { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .exam-item-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px; font-weight: 700; color: var(--exam-navy); margin: 0 0 4px;
        }
        .exam-item-subject { color: var(--exam-muted); font-size: 14px; margin: 0; }

        .exam-type {
            background: var(--exam-pale-green); color: var(--exam-green);
            border-radius: 999px; padding: 6px 14px;
            font-size: 12px; font-weight: 700; letter-spacing: .5px;
        }

        .exam-meta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
        .exam-meta-box {
            background: var(--exam-ivory); border: 1px solid var(--exam-border);
            border-radius: 14px; padding: 12px 16px; flex: 1 1 240px;
        }
        .exam-meta-label { display: block; color: var(--exam-muted); font-size: 12px; margin-bottom: 4px; }
        .exam-meta-value { color: var(--exam-navy); font-weight: 600; font-size: 14px; }

        .exam-item-bottom {
            display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
            gap: 14px; margin-top: 22px;
        }
        .exam-status {
            display: inline-flex; align-items: center; gap: 8px;
            border-radius: 999px; padding: 7px 14px; font-size: 13px; font-weight: 600;
        }
        .exam-status::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: currentColor; }
        .exam-status-belum { background: var(--exam-sage); color: #62675F; }
        .exam-status-jalan { background: var(--exam-orange-soft); color: var(--exam-orange); }
        .exam-status-selesai { background: var(--exam-pale-green); color: var(--exam-green); }

        .exam-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--exam-green); color: #fff; border: 1px solid var(--exam-green);
            border-radius: 12px; padding: 12px 20px; font-weight: 600; font-size: 14px;
            text-decoration: none; transition: background .2s, transform .2s;
        }
        .exam-btn:hover { background: var(--exam-green-dark); border-color: var(--exam-green-dark); color: #fff; transform: translateY(-1px); }
        .exam-btn-outline { background: transparent; color: var(--exam-green); border-color: #B9CDBD; }
        .exam-btn-outline:hover { background: var(--exam-pale-green); color: var(--exam-green-dark); border-color: var(--exam-green); }

        .exam-empty {
            background: #fff; border: 1px dashed #CFCBBE; border-radius: 20px;
            padding: 40px 24px; text-align: center; color: var(--exam-muted);
        }
    </style>

    <div class="exam-index">
        <div class="wrap">
            <span class="exam-eyebrow">{{ __('Ujian & Kuis') }}</span>
            <h3 class="page-title">{{ __('Siap menguji') }} <span>{{ __('kemampuanmu') }}</span>?</h3>
            <p class="page-desc">{{ __('Ujian dan kuis yang tersedia untuk kelas Anda.') }}</p>

            @forelse ($exams as $exam)
                @php
                    $session = $sessions->get($exam->exam_id);
                    $subject = $exam->classSubjectTeacher->subject->subject_name
                        ?? $exam->classSubjectTeacher->subject->name
                        ?? '-';

                    if (! $session) {
                        $statusClass = 'exam-status-belum';
                        $statusLabel = __('Belum dimulai');
                        $btnLabel = __('Lihat Detail');
                        $btnClass = 'exam-btn';
                    } elseif ($session->status === 'berlangsung') {
                        $statusClass = 'exam-status-jalan';
                        $statusLabel = __('Sedang berlangsung');
                        $btnLabel = __('Lanjutkan Ujian');
                        $btnClass = 'exam-btn';
                    } else {
                        $statusClass = 'exam-status-selesai';
                        $statusLabel = __('Selesai');
                        $btnLabel = __('Lihat Detail');
                        $btnClass = 'exam-btn exam-btn-outline';
                    }
                @endphp

                <article class="exam-item">
                    <div class="exam-item-top">
                        <div>
                            <h4 class="exam-item-title">{{ $exam->title }}</h4>
                            <p class="exam-item-subject">{{ $subject }}</p>
                        </div>
                        <span class="exam-type">{{ strtoupper($exam->exam_type) }}</span>
                    </div>

                    <div class="exam-meta">
                        <div class="exam-meta-box">
                            <span class="exam-meta-label">{{ __('Waktu Mulai') }}</span>
                            <span class="exam-meta-value">{{ $exam->start_time->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="exam-meta-box">
                            <span class="exam-meta-label">{{ __('Waktu Selesai') }}</span>
                            <span class="exam-meta-value">{{ $exam->end_time->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="exam-meta-box">
                            <span class="exam-meta-label">{{ __('Jumlah Soal') }}</span>
                            <span class="exam-meta-value">{{ $exam->questions_count }} {{ __('soal') }}</span>
                        </div>
                    </div>

                    <div class="exam-item-bottom">
                        <span class="exam-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        <a href="{{ route('siswa.exams.show', $exam) }}" class="{{ $btnClass }}">
                            {{ $btnLabel }} <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="exam-empty">
                    {{ __('Belum ada ujian atau kuis yang tersedia.') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>