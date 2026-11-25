<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    E-Exam dan Evaluasi Siswa
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Asesmen dan Ujian Digital Guru
                </span>
            </div>
        </div>
    </x-slot>

    @php
        /*
        |--------------------------------------------------------------------------
        | TEMA VISUAL MATA PELAJARAN (SERASAM DENGAN E-CLASS GURU)
        |--------------------------------------------------------------------------
        */
        $subjectThemes = [
            'matematika' => [
                'symbol' => 'MAT',
                'category' => 'MATEMATIKA',
                'description' => 'Eksplorasi angka, pola, dan logika berpikir',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/matematika.jpg'),
            ],
            'bahasa indonesia' => [
                'symbol' => 'IDN',
                'category' => 'BAHASA dan SASTRA INDONESIA',
                'description' => 'Mengembangkan literasi dan kekuatan berbahasa',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/indonesia.jpg'),
            ],
            'bahasa inggris' => [
                'symbol' => 'ENG',
                'category' => 'BAHASA INGGRIS',
                'description' => 'Berkomunikasi dan terhubung secara global',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
            'biologi' => [
                'symbol' => 'BIO',
                'category' => 'BIOLOGI',
                'description' => 'Menjelajahi dunia kehidupan dan ekosistem',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/biologi.jpg'),
            ],
            'fisika' => [
                'symbol' => 'FIS',
                'category' => 'FISIKA',
                'description' => 'Memahami hukum alam dan prinsip semesta',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/fisika.jpg'),
            ],
            'kimia' => [
                'symbol' => 'KIM',
                'category' => 'KIMIA',
                'description' => 'Mempelajari materi, zat, dan reaksi kimia',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/kimia.jpg'),
            ],
            'sejarah' => [
                'symbol' => 'SEJ',
                'category' => 'SEJARAH',
                'description' => 'Mengambil hikmah dan perjalanan masa lalu',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
            'informatika' => [
                'symbol' => 'TIK',
                'category' => 'INFORMATIKA',
                'description' => 'Membangun logika digital dan komputasi',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/informatika.jpg'),
            ],
            'agama' => [
                'symbol' => 'AGM',
                'category' => 'PENDIDIKAN AGAMA',
                'description' => 'Membina karakter, iman, dan pekerti mulia',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
            'ekonomi' => [
                'symbol' => 'EKO',
                'category' => 'EKONOMI',
                'description' => 'Memahami pasar, bisnis, dan keuangan',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
            'geografi' => [
                'symbol' => 'GEO',
                'category' => 'GEOGRAFI',
                'description' => 'Mempelajari bumi, ruang, dan lingkungan',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
            'sosiologi' => [
                'symbol' => 'SOS',
                'category' => 'SOSIOLOGI',
                'description' => 'Memahami struktur dan dinamika masyarakat',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            ],
        ];

        $defaultTheme = [
            'symbol' => 'EXAM',
            'category' => 'ASESMEN DIGITAL',
            'description' => 'Evaluasi kompetensi dan pemahaman materi',
            'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
        ];

        $totalExams = $exams->total();
        $publishedCount = $exams->where('is_published', true)->count();
        $draftCount = $exams->where('is_published', false)->count();
        $totalSessions = $exams->sum('sessions_count');
        $totalQuestions = $exams->sum('questions_count');
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&display=swap');

        .eexam-page {
            --navy: #123B60;
            --blue: #267BB5;
            --ivory: #FAF9F6;
            --border: #E6ECF1;

            min-height: 100vh;
            padding: 30px 0 65px;
            background: var(--ivory);
            color: #263747;
            font-family: 'DM Sans', sans-serif;
        }

        .eexam-wrap {
            width: min(1440px, 100%);
            margin: auto;
            padding: 0 24px;
        }

        @media (min-width: 640px) {
            .eexam-wrap {
                padding: 0 32px;
            }
        }

        .eexam-serif {
            font-family: 'Playfair Display', serif;
        }

        /* HERO SECTION */
        .eexam-hero {
            position: relative;
            isolation: isolate;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            align-items: center;
            gap: 30px;
            min-height: 330px;
            overflow: hidden;
            padding: 40px 48px;
            border: 1px solid #DFE9F0;
            border-radius: 30px;
            background: linear-gradient(115deg, #FAFAF8, #E7F0F7);
            box-shadow: 0 15px 45px rgba(18,59,96,.05);
        }

        .eexam-hero::before {
            content: "";
            position: absolute;
            z-index: -1;
            width: 380px;
            height: 380px;
            top: -190px;
            right: -80px;
            border: 1px solid rgba(38,123,181,.18);
            border-radius: 50%;
            box-shadow:
                0 0 0 30px rgba(38,123,181,.035),
                0 0 0 65px rgba(38,123,181,.025);
        }

        .eexam-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border: 1px solid #D2E3EF;
            border-radius: 50px;
            background: #E8F1F8;
            color: #185B87;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .15em;
        }

        .eexam-hero h1 {
            margin-top: 18px;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            font-size: clamp(34px, 4.5vw, 54px);
            font-weight: 700;
            line-height: 1.12;
            letter-spacing: -1.5px;
        }

        .eexam-hero h1 span {
            color: var(--blue);
        }

        .eexam-hero p.description {
            max-width: 540px;
            margin-top: 15px;
            color: #66798A;
            font-size: 13px;
            line-height: 1.85;
        }

        .eexam-hero-visual {
            position: relative;
            display: flex;
            min-height: 240px;
            align-items: center;
            justify-content: center;
        }

        .eexam-hero-art {
            position: relative;
            display: flex;
            width: 100%;
            min-height: 240px;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            padding: 32px;
            border-radius: 25px;
            background: #10243E;
            box-shadow: 0 20px 40px rgba(18,59,96,.20);
            color: white;
        }

        .eexam-hero-art-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 25%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eexam-hero-art:hover .eexam-hero-art-img {
            transform: scale(1.06);
        }

        .eexam-hero-art-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(10, 26, 47, 0.88) 0%, rgba(18, 59, 96, 0.75) 60%, rgba(20, 45, 75, 0.60) 100%);
        }

        .eexam-hero-art-content {
            position: relative;
            z-index: 2;
        }

        .eexam-hero-art .art-symbol {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 4.2vw, 52px);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        /* SUMMARY CARDS */
        .eexam-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 26px;
        }

        .eexam-summary-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 7px 22px rgba(18,59,96,.035);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .eexam-summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(18,59,96,.07);
        }

        .summary-icon {
            display: grid;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            place-items: center;
            border-radius: 16px;
            background: #E8F1F8;
            color: #267BB5;
            font-size: 20px;
        }

        .summary-number {
            color: #123B60;
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            font-weight: 700;
            line-height: 1.1;
        }

        .summary-label {
            margin-top: 3px;
            color: #8593A0;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* GRID UJIAN */
        .eexam-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .eexam-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #E5EBF0;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 7px 25px rgba(18,59,96,.035);
            transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease;
            cursor: pointer;
        }

        .eexam-card:hover {
            transform: translateY(-6px);
            border-color: #BBD7E9;
            box-shadow: 0 18px 38px rgba(18,59,96,.10);
        }

        .eexam-card:hover .eexam-card-stat {
            background: #EEF4F9;
            border-color: #DDE7F0;
        }

        .eexam-card-cover {
            position: relative;
            min-height: 160px;
            padding: 20px;
            color: white;
            background-color: #123B60;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            cursor: pointer;
        }

        .eexam-cover-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eexam-card:hover .eexam-cover-image {
            transform: scale(1.08);
        }

        .eexam-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(8, 22, 40, 0.90) 0%, rgba(18, 59, 96, 0.74) 60%, rgba(18, 59, 96, 0.40) 100%);
            pointer-events: none;
        }

        .eexam-cover-symbol {
            position: relative;
            z-index: 1;
            display: grid;
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            place-items: center;
            border: 1px solid rgba(255,255,255,.32);
            border-radius: 16px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .08em;
            backdrop-filter: blur(8px);
        }

        .eexam-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: space-between;
        }

        .eexam-card-stat {
            padding: 11px 6px;
            border-radius: 12px;
            background: #F5F8FA;
            text-align: center;
            border: 1px solid #EDF2F6;
        }

        .eexam-card-stat strong {
            display: block;
            color: #123B60;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
        }

        .eexam-card-stat span {
            display: block;
            margin-top: 2px;
            color: #84919E;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        @media (max-width: 1024px) {
            .eexam-hero {
                grid-template-columns: 1fr;
                padding: 32px;
            }
            .eexam-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .eexam-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .eexam-summary {
                grid-template-columns: 1fr;
            }
            .eexam-grid {
                grid-template-columns: 1fr;
            }
            .eexam-hero h1 {
                font-size: 32px;
            }
        }
    </style>

    <main class="eexam-page"
          x-data="{
              search: '',
              classFilter: 'all',
              statusFilter: 'all',
              typeFilter: 'all',
              items: [
                  @foreach($exams as $ex)
                  {
                      id: {{ $ex->exam_id }},
                      title: '{{ addslashes($ex->title) }}',
                      subject: '{{ addslashes($ex->classSubjectTeacher?->subject?->name ?? '') }}',
                      className: '{{ addslashes($ex->classSubjectTeacher?->schoolClass?->name ?? '') }}',
                      classId: '{{ $ex->classSubjectTeacher?->class_id ?? 0 }}',
                      status: '{{ $ex->is_published ? 'published' : 'draft' }}',
                      type: '{{ $ex->exam_type }}'
                  },
                  @endforeach
              ],
              get filteredCount() {
                  const q = this.search.toLowerCase().trim();
                  return this.items.filter(item => {
                      const matchClass = (this.classFilter === 'all' || this.classFilter == item.classId);
                      const matchStatus = (this.statusFilter === 'all' || this.statusFilter === item.status);
                      const matchType = (this.typeFilter === 'all' || this.typeFilter === item.type);
                      const matchSearch = (!q || (item.title + ' ' + item.subject + ' ' + item.className).toLowerCase().includes(q));
                      return matchClass && matchStatus && matchType && matchSearch;
                  }).length;
              },
              matches(title, subject, className, classId, status, type) {
                  const matchClass = (this.classFilter === 'all' || this.classFilter == classId);
                  const matchStatus = (this.statusFilter === 'all' || this.statusFilter === status);
                  const matchType = (this.typeFilter === 'all' || this.typeFilter === type);
                  const q = this.search.toLowerCase().trim();
                  const matchSearch = (!q || (title + ' ' + subject + ' ' + className).toLowerCase().includes(q));
                  return matchClass && matchStatus && matchType && matchSearch;
              }
          }">

        <div class="eexam-wrap space-y-8">

            {{-- BREADCRUMB dan NAVIGASI CEPAT (DI BAWAH HEADER) --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <a href="{{ route('guru.dashboard') }}" class="transition hover:text-blue-600">
                        Dashboard
                    </a>
                    <span>/</span>
                    <span class="font-bold text-blue-700">E-Exam dan Evaluasi Siswa</span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('guru.dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 active:scale-95">
                        <span>←</span>
                        <span>Kembali ke Dashboard</span>
                    </a>
                    <a href="{{ route('guru.classes.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 shadow-sm transition hover:bg-blue-100 active:scale-95">
                        <span>Daftar E-Class</span>
                        <span>↗</span>
                    </a>
                    <a href="{{ route('guru.exams.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5] active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Tambah Ujian Baru</span>
                    </a>
                </div>
            </div>

            {{-- NOTIFIKASI FLASH --}}
            @if (session('success'))
                <div x-data="{ show: true }" 
                     x-init="setTimeout(() => show = false, 4000)" 
                     x-show="show" 
                     x-transition.duration.500ms
                     class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                <div x-data="{ show: true }" 
                     x-init="setTimeout(() => show = false, 5000)" 
                     x-show="show" 
                     x-transition.duration.500ms
                     class="flex items-center gap-3 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-sm">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- HERO SECTION (E-EXAM GURU METHODIST) --}}
            <section class="eexam-hero">
                <div>
                    <span class="eexam-eyebrow">
                        <svg class="h-3 w-3 text-[#185B87]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>METHODIST LEARNING SPACE · E-EXAM</span>
                    </span>

                    <h1>
                        Ukur Pemahaman,<br>
                        <span>Wujudkan Prestasi Nyata.</span>
                    </h1>

                    <p class="description">
                        Kelola asesmen digital, ulangan harian, UTS, dan UAS secara interaktif. 
                        Rancang bank soal objektif dan esai terstruktur, evaluasi capaian siswa, serta dapatkan analisis hasil ujian secara instan dan akurat.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white/85 px-4 py-2 text-xs font-bold text-blue-900 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $totalExams }} Ujian Terdaftar
                        </span>

                        <a href="{{ route('guru.exams.create') }}"
                           class="inline-flex items-center gap-1.5 rounded-full bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5]">
                            <span>+ Buat Ujian Baru</span>
                        </a>

                        <span class="text-xs text-slate-500 hidden sm:inline">
                            Standar penilaian terpadu SMAS Methodist Banda Aceh.
                        </span>
                    </div>
                </div>

                <div class="eexam-hero-visual">
                    <div class="eexam-hero-art group">
                        <img src="{{ asset('images/methodist-teachers-activity.jpg') }}"
                             alt="Aktivitas Guru SMAS Methodist Banda Aceh"
                             class="eexam-hero-art-img">
                        <div class="eexam-hero-art-overlay"></div>

                        <div class="eexam-hero-art-content">
                            <p class="text-[10px] font-bold tracking-[.2em] text-sky-200 uppercase drop-shadow-sm">
                                ASESMEN DIGITAL GURU
                            </p>

                            <div class="art-symbol drop-shadow-md">E-Exam.</div>

                            <h2 class="eexam-serif mt-3 text-2xl font-bold drop-shadow-md">
                                Ujian dan Evaluasi Bermakna.
                            </h2>

                            <p class="mt-2 text-xs text-slate-100 font-medium drop-shadow">
                                Membangun integritas dan pemikiran analitis siswa Methodist.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SUMMARY STATS --}}
            <section class="eexam-summary">
                <article class="eexam-summary-card">
                    <div class="summary-icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number">{{ $totalExams }}</p>
                        <p class="summary-label">Total Ujian</p>
                    </div>
                </article>

                <article class="eexam-summary-card">
                    <div class="summary-icon" style="background: #ECFDF5; color: #059669;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number" style="color: #065F46;">{{ $publishedCount }}</p>
                        <p class="summary-label">Ujian Terbit (Aktif)</p>
                    </div>
                </article>

                <article class="eexam-summary-card">
                    <div class="summary-icon" style="background: #FFFBEB; color: #D97706;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number" style="color: #92400E;">{{ $draftCount }}</p>
                        <p class="summary-label">Draft Tersimpan</p>
                    </div>
                </article>

                <article class="eexam-summary-card">
                    <div class="summary-icon" style="background: #EFF6FF; color: #2563EB;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number" style="color: #1E40AF;">{{ $totalSessions }}</p>
                        <p class="summary-label">Sesi Mengerjakan</p>
                    </div>
                </article>
            </section>

            {{-- FILTER dan SEARCH TOOLBAR --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    
                    {{-- SEARCH INPUT --}}
                    <div class="relative flex-1 min-w-[240px]">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model="search"
                               placeholder="Cari judul ujian, nama mata pelajaran, atau kelas..."
                               class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 pl-10 pr-9 py-2.5 text-xs sm:text-sm font-medium text-slate-700 placeholder-slate-400 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <button type="button"
                                x-show="search.length > 0"
                                @click="search = ''"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- FILTERS GROUP --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        
                        {{-- FILTER KELAS --}}
                        <div class="relative">
                            <select x-model="classFilter"
                                    class="appearance-none rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 pr-8 text-xs font-bold text-slate-700 transition hover:bg-slate-100 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                                <option value="all">Semua Rombel Kelas</option>
                                @foreach($csts->unique('class_id') as $cst)
                                    <option value="{{ $cst->class_id }}">{{ $cst->schoolClass->name ?? ('Kelas ' . $cst->class_id) }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        {{-- FILTER STATUS --}}
                        <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs font-bold text-slate-600">
                            <button type="button"
                                    @click="statusFilter = 'all'"
                                    :class="statusFilter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-3 py-1.5 transition">
                                Semua Status
                            </button>
                            <button type="button"
                                    @click="statusFilter = 'published'"
                                    :class="statusFilter === 'published' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-3 py-1.5 transition">
                                Terbit
                            </button>
                            <button type="button"
                                    @click="statusFilter = 'draft'"
                                    :class="statusFilter === 'draft' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-3 py-1.5 transition">
                                Draft
                            </button>
                        </div>

                        {{-- FILTER TIPE --}}
                        <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs font-bold text-slate-600">
                            <button type="button"
                                    @click="typeFilter = 'all'"
                                    :class="typeFilter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-3 py-1.5 transition">
                                Semua Tipe
                            </button>
                            <button type="button"
                                    @click="typeFilter = 'kuis'"
                                    :class="typeFilter === 'kuis' ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-2.5 py-1.5 transition">
                                Kuis
                            </button>
                            <button type="button"
                                    @click="typeFilter = 'uts'"
                                    :class="typeFilter === 'uts' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-2.5 py-1.5 transition">
                                UTS
                            </button>
                            <button type="button"
                                    @click="typeFilter = 'uas'"
                                    :class="typeFilter === 'uas' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                    class="rounded-lg px-2.5 py-1.5 transition">
                                UAS
                            </button>
                        </div>

                    </div>

                </div>
            </section>

            {{-- SECTION DAFTAR UJIAN --}}
            <section class="space-y-6">

                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-[#267BB5]">
                            Bank Asesmen dan Evaluasi Aktif
                        </p>
                        <h2 class="eexam-serif text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                            Daftar Ujian yang Anda Kelola
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Pilih ujian untuk menyusun bank soal, memantau sesi pengerjaan, atau mengevaluasi hasil jawaban siswa.
                        </p>
                    </div>

                    <span class="hidden sm:inline-flex shrink-0 items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3.5 py-1 text-xs font-bold text-blue-800">
                        {{ $totalExams }} Total Ujian
                    </span>
                </div>

                @if ($exams->isEmpty())
                    {{-- EMPTY STATE KETIKA BELUM ADA DATA SAMA SEKALI --}}
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#123B60]">
                            <svg class="h-8 w-8 text-[#123B60]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="eexam-serif text-xl font-bold text-slate-900">Belum Ada Ujian atau Kuis</h3>
                        <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                            Anda belum membuat evaluasi pembelajaran untuk kelas yang diampu. Mulai rancang ujian pertama Anda sekarang.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('guru.exams.create') }}"
                               class="inline-flex items-center gap-2 rounded-full bg-[#123B60] px-6 py-3 text-xs font-bold text-white shadow-md transition hover:bg-[#267BB5]">
                                <span>+ Buat Ujian Pertama</span>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- GRID CARD UJIAN (DESAIN SELARAS DENGAN E-CLASS) --}}
                    <div class="eexam-grid">
                        @foreach ($exams as $exam)
                            @php
                                $subjectName = $exam->classSubjectTeacher?->subject?->name ?? 'Mata Pelajaran';
                                $className = $exam->classSubjectTeacher?->schoolClass?->name ?? 'Semua Kelas';
                                $classId = $exam->classSubjectTeacher?->class_id ?? 0;
                                $subjectLower = strtolower($subjectName);

                                $theme = $defaultTheme;
                                foreach ($subjectThemes as $key => $value) {
                                    if (str_contains($subjectLower, $key)) {
                                        $theme = $value;
                                        break;
                                    }
                                }

                                if (empty($theme['image'])) {
                                    $slugMap = [
                                        'matematika' => 'matematika.jpg', 'biologi' => 'biologi.jpg', 'kimia' => 'kimia.jpg', 'informatika' => 'informatika.jpg',
                                        'fisika' => 'fisika.jpg', 'bahasa indonesia' => 'indonesia.jpg', 'indonesia' => 'indonesia.jpg', 'bahasa inggris' => 'inggris.svg',
                                        'sejarah' => 'sejarah.svg', 'geografi' => 'geografi.svg', 'ekonomi' => 'ekonomi.svg', 'sosiologi' => 'sosiologi.svg',
                                        'agama' => 'agama.svg', 'pjok' => 'pjok.svg', 'seni' => 'seni-budaya.svg', 'ppkn' => 'ppkn.svg',
                                    ];
                                    foreach ($slugMap as $k => $f) {
                                        if (str_contains($subjectLower, $k)) {
                                            $jpgFile = preg_replace('/\.(svg|png)$/i', '.jpg', $f);
                                            $svgFile = preg_replace('/\.(jpg|png)$/i', '.svg', $f);
                                            if (file_exists(public_path('images/subjects/' . $jpgFile))) {
                                                $theme['image'] = asset('images/subjects/' . $jpgFile);
                                                break;
                                            } elseif (file_exists(public_path('images/subjects/' . $svgFile))) {
                                                $theme['image'] = asset('images/subjects/' . $svgFile);
                                                break;
                                            }
                                        }
                                    }
                                }

                                $typeLabels = [
                                    'kuis' => ['label' => 'Kuis', 'bg' => 'bg-sky-500', 'light' => 'bg-sky-50 text-sky-700 border-sky-200'],
                                    'uts' => ['label' => 'UTS', 'bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                    'uas' => ['label' => 'UAS', 'bg' => 'bg-purple-600', 'light' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                ];
                                $typeInfo = $typeLabels[$exam->exam_type] ?? ['label' => strtoupper($exam->exam_type), 'bg' => 'bg-blue-600', 'light' => 'bg-blue-50 text-blue-700 border-blue-200'];
                            @endphp

                            <article class="eexam-card group cursor-pointer"
                                     x-show="matches(
                                         '{{ addslashes($exam->title) }}',
                                         '{{ addslashes($subjectName) }}',
                                         '{{ addslashes($className) }}',
                                         '{{ $classId }}',
                                         '{{ $exam->is_published ? 'published' : 'draft' }}',
                                         '{{ $exam->exam_type }}'
                                     )"
                                     x-transition
                                     @click="if (! $event.target.closest('a, button, form, input')) { window.location.href = '{{ route('guru.exams.show', $exam) }}' }">

                                {{-- CARD COVER DENGAN SUBJECT IMAGE / THEME --}}
                                <a href="{{ route('guru.exams.show', $exam) }}"
                                   class="eexam-card-cover block group/cover {{ !empty($theme['image']) ? 'has-image' : '' }}"
                                   style="{{ empty($theme['image']) ? 'background: ' . $theme['background'] : '' }}"
                                   title="Buka dan Kelola Ujian: {{ $exam->title }}">

                                    @if(!empty($theme['image']))
                                        <img src="{{ $theme['image'] }}"
                                             alt="{{ $subjectName }}"
                                             class="eexam-cover-image">
                                        <div class="eexam-cover-overlay"></div>
                                    @endif

                                    {{-- HEADER BAR COVER: TIPE dan STATUS --}}
                                    <div class="relative z-10 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-white shadow-sm {{ $typeInfo['bg'] }}">
                                                {{ $typeInfo['label'] }}
                                            </span>
                                            <span class="inline-flex items-center rounded-lg bg-white/20 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold text-white border border-white/30">
                                                {{ $className }}
                                            </span>
                                        </div>

                                        {{-- STATUS PUBLISH --}}
                                        @if($exam->is_published)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/90 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold text-white shadow-sm border border-emerald-400/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                                <span>Terbit</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/90 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold text-white shadow-sm border border-amber-400/40">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-200"></span>
                                                <span>Draft</span>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- FOOTER BAR COVER: MAPEL dan SYMBOL --}}
                                    <div class="relative z-10 flex items-end justify-between gap-3 mt-4">
                                        <div>
                                            <p class="text-[9px] font-extrabold uppercase tracking-[.15em] text-white/80">
                                                {{ $theme['category'] }}
                                            </p>
                                            <h3 class="eexam-serif text-xl font-bold text-white leading-tight mt-0.5 drop-shadow">
                                                {{ $subjectName }}
                                            </h3>
                                        </div>

                                        <div class="eexam-cover-symbol drop-shadow-md">
                                            {{ $theme['symbol'] }}
                                        </div>
                                    </div>

                                </a>

                                {{-- CARD BODY --}}
                                <div class="eexam-card-body space-y-4">
                                    
                                    {{-- JUDUL UJIAN dan JADWAL --}}
                                    <div>
                                        <h4 class="text-base font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-[#267BB5] transition">
                                            <a href="{{ route('guru.exams.show', $exam) }}">
                                                {{ $exam->title }}
                                            </a>
                                        </h4>

                                        <div class="mt-2.5 space-y-1 text-xs text-slate-500">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>Mulai: <strong class="text-slate-700 font-semibold">{{ $exam->start_time->translatedFormat('d M Y, H:i') }}</strong></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Durasi: <strong class="text-slate-700">{{ $exam->duration_minutes }} Menit</strong> &bull; KKM: <strong class="text-slate-700">{{ (int)$exam->pass_score }}</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 3-COLUMN STATS STRIP (SAMA PERSIS DENGAN KARTU E-CLASS) --}}
                                    <div class="grid grid-cols-3 gap-2 pt-1">
                                        <div class="eexam-card-stat">
                                            <strong>{{ $exam->questions_count }}</strong>
                                            <span>Butir Soal</span>
                                        </div>
                                        <div class="eexam-card-stat">
                                            <strong>{{ $exam->sessions_count }}</strong>
                                            <span>Peserta</span>
                                        </div>
                                        <div class="eexam-card-stat">
                                            <strong>{{ $exam->duration_minutes }}m</strong>
                                            <span>Waktu</span>
                                        </div>
                                    </div>

                                    {{-- ACTION BUTTONS FOOTER --}}
                                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                                        <div class="flex items-center gap-2">
                                            {{-- TOMBOL KELOLA SOAL (PRIMARY) --}}
                                            <a href="{{ route('guru.exams.show', $exam) }}"
                                               class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#123B60] px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5] active:scale-95">
                                                <span>Kelola Soal</span>
                                                <span>→</span>
                                            </a>

                                            {{-- TOMBOL HASIL SISWA --}}
                                            <a href="{{ route('guru.exams.results', $exam) }}"
                                               class="inline-flex items-center justify-center gap-1 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 active:scale-95"
                                               title="Periksa hasil jawaban siswa">
                                                <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Hasil ({{ $exam->sessions_count }})</span>
                                            </a>
                                        </div>

                                        {{-- SECONDARY ACTION TOOLBAR: EDIT, PUBLISH, HAPUS --}}
                                        <div class="flex items-center justify-between text-xs pt-1">
                                            <div class="flex items-center gap-1.5">
                                                {{-- EDIT BUTTON --}}
                                                <a href="{{ route('guru.exams.edit', $exam) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition font-semibold">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- TOGGLE PUBLISH FORM --}}
                                                <form action="{{ route('guru.exams.toggle-publish', $exam) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($exam->is_published)
                                                        <button type="submit"
                                                                onclick="return confirm('Arsipkan ujian ini kembali ke status Draft?');"
                                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-slate-500 hover:bg-amber-50 hover:text-amber-700 transition font-medium"
                                                                title="Jadikan Draft">
                                                            <span>Arsipkan</span>
                                                        </button>
                                                    @else
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-emerald-700 hover:bg-emerald-50 transition font-bold"
                                                                title="Terbitkan ujian agar dapat diakses siswa">
                                                            <span>Terbitkan</span>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>

                                            {{-- HAPUS BUTTON --}}
                                            <form action="{{ route('guru.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                        title="Hapus Ujian">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>

                            </article>
                        @endforeach
                    </div>

                    {{-- FILTER EMPTY STATE --}}
                    <div x-cloak
                         x-show="filteredCount === 0"
                         class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h4 class="eexam-serif text-lg font-bold text-slate-800">Tidak Menemukan Ujian yang Cocok</h4>
                        <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau bersihkan filter yang aktif.</p>
                        <button type="button"
                                @click="search = ''; classFilter = 'all'; statusFilter = 'all'; typeFilter = 'all'"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                            Reset Semua Filter
                        </button>
                    </div>

                    {{-- PAGINATION --}}
                    @if($exams->hasPages())
                        <div class="mt-8 pt-4 border-t border-slate-200">
                            {{ $exams->links() }}
                        </div>
                    @endif
                @endif

            </section>

        </div>
    </main>
</x-app-layout>
