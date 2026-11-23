
<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[.2em] text-blue-600">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    E-Class Guru
                </h2>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Ruang Kerja Guru
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $subjectThemes = [
            'matematika' => [
                'symbol' => 'MAT',
                'category' => 'MATEMATIKA',
                'description' => 'Eksplorasi angka, pola, dan logika berpikir',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/matematika.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'bahasa indonesia' => [
                'symbol' => 'IDN',
                'category' => 'BAHASA dan SASTRA INDONESIA',
                'description' => 'Mengembangkan literasi dan kekuatan berbahasa',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/indonesia.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'bahasa inggris' => [
                'symbol' => 'ENG',
                'category' => 'BAHASA INGGRIS',
                'description' => 'Berkomunikasi dan terhubung secara global',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'biologi' => [
                'symbol' => 'BIO',
                'category' => 'BIOLOGI',
                'description' => 'Menjelajahi dunia kehidupan dan ekosistem',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/biologi.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'fisika' => [
                'symbol' => 'FIS',
                'category' => 'FISIKA',
                'description' => 'Memahami hukum alam dan prinsip semesta',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/fisika.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'kimia' => [
                'symbol' => 'KIM',
                'category' => 'KIMIA',
                'description' => 'Mempelajari materi, zat, dan reaksi kimia',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/kimia.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'sejarah' => [
                'symbol' => 'SEJ',
                'category' => 'SEJARAH',
                'description' => 'Mengambil hikmah dan perjalanan masa lalu',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'informatika' => [
                'symbol' => 'TIK',
                'category' => 'INFORMATIKA',
                'description' => 'Membangun logika digital dan komputasi',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'image' => asset('images/subjects/informatika.jpg'),
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'agama' => [
                'symbol' => 'AGM',
                'category' => 'PENDIDIKAN AGAMA',
                'description' => 'Membina karakter, iman, dan pekerti mulia',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'ekonomi' => [
                'symbol' => 'EKO',
                'category' => 'EKONOMI',
                'description' => 'Memahami pasar, bisnis, dan keuangan',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'geografi' => [
                'symbol' => 'GEO',
                'category' => 'GEOGRAFI',
                'description' => 'Mempelajari bumi, ruang, dan interaksi lingkungan',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
            'sosiologi' => [
                'symbol' => 'SOS',
                'category' => 'SOSIOLOGI',
                'description' => 'Memahami struktur sosial dan dinamika masyarakat',
                'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
                'light' => '#E8F1F8',
                'accent' => '#267BB5',
            ],
        ];

        $defaultTheme = [
            'symbol' => 'MLS',
            'category' => 'KELAS AKADEMIK',
            'description' => 'Ruang belajar dan berkembang bersama',
            'background' => 'linear-gradient(135deg,#123B60,#267BB5)',
            'light' => '#E8F1F8',
            'accent' => '#267BB5',
        ];

        $totalClasses = $classes->count();
        $totalStudents = $classes->sum('joined_students_count');
        $totalMaterials = $classes->sum('materials_count');
        $totalAssignments = $classes->sum('assignments_count');
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap');

        .eclass-page {
            --navy: #123B60;
            --blue: #267BB5;
            --ivory: #FAF9F6;
            --border: #E6ECF1;

            min-height: 100vh;
            padding: 34px 0 65px;
            background: var(--ivory);
            color: #263747;
            font-family: 'DM Sans', sans-serif;
        }

        .eclass-page * {
            box-sizing: border-box;
        }

        .eclass-wrap {
            width: min(1440px, 100%);
            margin: auto;
            padding: 0 28px;
        }

        .eclass-serif {
            font-family: 'Playfair Display', serif;
        }

        /* HERO */

        .eclass-hero {
            position: relative;
            isolation: isolate;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            align-items: center;
            gap: 30px;
            min-height: 350px;
            overflow: hidden;
            padding: 43px 48px;
            border: 1px solid #DFE9F0;
            border-radius: 30px;
            background: linear-gradient(115deg,#FAFAF8,#E7F0F7);
            box-shadow: 0 15px 45px rgba(18,59,96,.05);
        }

        .eclass-hero::before {
            content: "";
            position: absolute;
            z-index: -1;
            width: 350px;
            height: 350px;
            top: -180px;
            right: -80px;
            border: 1px solid rgba(38,123,181,.18);
            border-radius: 50%;
            box-shadow:
                0 0 0 30px rgba(38,123,181,.035),
                0 0 0 65px rgba(38,123,181,.025);
        }

        .eclass-eyebrow {
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

        .eclass-hero h1 {
            margin-top: 20px;
            color: var(--navy);
            font-family: 'Playfair Display',serif;
            font-size: clamp(38px,5vw,59px);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -1.8px;
        }

        .eclass-hero h1 span {
            color: var(--blue);
        }

        .eclass-hero p.description {
            max-width: 550px;
            margin-top: 17px;
            color: #66798A;
            font-size: 13px;
            line-height: 1.9;
        }

        .eclass-hero-visual {
            position: relative;
            display: flex;
            min-height: 250px;
            align-items: center;
            justify-content: center;
        }

        .eclass-hero-art {
            position: relative;
            display: flex;
            width: 100%;
            min-height: 240px;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            padding: 30px;
            border-radius: 25px;
            background: #10243E;
            box-shadow: 0 20px 40px rgba(18,59,96,.20);
            color: white;
        }

        .eclass-hero-art-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 25%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eclass-hero-art:hover .eclass-hero-art-img {
            transform: scale(1.06);
        }

        .eclass-hero-art-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(10, 26, 47, 0.88) 0%, rgba(18, 59, 96, 0.75) 60%, rgba(20, 45, 75, 0.60) 100%);
        }

        .eclass-hero-art-content {
            position: relative;
            z-index: 2;
        }

        .eclass-hero-art .art-symbol {
            font-family: 'Playfair Display',serif;
            font-size: clamp(36px, 4.2vw, 52px);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        /* SUMMARY */

        .eclass-summary {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 16px;
            margin-top: 28px;
        }

        .eclass-summary-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 21px;
            border: 1px solid var(--border);
            border-radius: 19px;
            background: #fff;
            box-shadow: 0 7px 22px rgba(18,59,96,.035);
            transition: .25s ease;
        }

        .eclass-summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(18,59,96,.07);
        }

        .summary-icon {
            display: grid;
            width: 47px;
            height: 47px;
            flex-shrink: 0;
            place-items: center;
            border-radius: 15px;
            background: #E8F1F8;
            color: #267BB5;
            font-size: 21px;
        }

        .summary-number {
            color: #123B60;
            font-family: 'Playfair Display',serif;
            font-size: 27px;
            font-weight: 700;
        }

        .summary-label {
            margin-top: 2px;
            color: #8593A0;
            font-size: 10px;
            font-weight: 600;
        }

        /* SECTION */

        .eclass-section {
            margin-top: 54px;
        }

        .eclass-section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 22px;
        }

        .eclass-section-label {
            color: #267BB5;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .eclass-section-title {
            margin-top: 6px;
            color: #123B60;
            font-family: 'Playfair Display',serif;
            font-size: 28px;
            font-weight: 700;
        }

        .eclass-section-description {
            margin-top: 5px;
            color: #81909E;
            font-size: 12px;
        }

        .eclass-count {
            flex-shrink: 0;
            padding: 9px 15px;
            border: 1px solid #D8E6EF;
            border-radius: 50px;
            background: #EAF2F8;
            color: #185B87;
            font-size: 11px;
            font-weight: 800;
        }

        /* CLASS GRID */

        .eclass-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 21px;
        }

        .eclass-card {
            display: block;
            overflow: hidden;
            border: 1px solid #E5EBF0;
            border-radius: 22px;
            background: #fff;
            color: inherit;
            text-decoration: none;
            box-shadow: 0 7px 25px rgba(18,59,96,.035);
            transition: .3s ease;
        }

        .eclass-card:hover {
            transform: translateY(-6px);
            border-color: #BBD7E9;
            box-shadow: 0 18px 38px rgba(18,59,96,.10);
        }

        .eclass-card-cover {
            position: relative;
            display: flex;
            min-height: 175px;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            padding: 24px;
            color: white;
            background-color: #123B60;
        }

        .eclass-card-cover:not(.has-image)::before {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            top: -110px;
            right: -40px;
            border: 1px solid rgba(255,255,255,.24);
            border-radius: 50%;
            box-shadow:
                0 0 0 25px rgba(255,255,255,.06),
                0 0 0 55px rgba(255,255,255,.035);
        }

        .eclass-card-cover:not(.has-image)::after {
            content: "";
            position: absolute;
            width: 110px;
            height: 110px;
            bottom: -75px;
            left: 30%;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 50%;
        }

        .eclass-cover-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .eclass-card:hover .eclass-cover-image {
            transform: scale(1.08);
        }

        .eclass-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(8, 22, 40, 0.88) 0%, rgba(18, 59, 96, 0.72) 55%, rgba(18, 59, 96, 0.38) 100%);
            pointer-events: none;
        }

        .eclass-cover-content {
            position: relative;
            z-index: 1;
            max-width: 75%;
        }

        .eclass-cover-category {
            color: rgba(255,255,255,.78);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .15em;
        }

        .eclass-cover-name {
            margin-top: 10px;
            font-family: 'Playfair Display',serif;
            font-size: 23px;
            font-weight: 700;
            line-height: 1.25;
        }

        .eclass-cover-description {
            margin-top: 8px;
            color: rgba(255,255,255,.83);
            font-size: 10px;
        }

        .eclass-cover-symbol {
            position: relative;
            z-index: 1;
            display: grid;
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            place-items: center;
            border: 1px solid rgba(255,255,255,.36);
            border-radius: 18px;
            background: rgba(255,255,255,.17);
            color: white;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .08em;
            backdrop-filter: blur(8px);
        }

        .eclass-card-body {
            padding: 22px;
        }

        .eclass-class-label {
            color: #99A4AE;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .13em;
        }

        .eclass-class-name {
            margin-top: 6px;
            color: #183B59;
            font-family: 'Playfair Display',serif;
            font-size: 20px;
            font-weight: 700;
        }

        .eclass-class-meta {
            margin-top: 5px;
            color: #8794A0;
            font-size: 11px;
        }

        .eclass-card-stats {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 9px;
            margin-top: 21px;
        }

        .eclass-card-stat {
            padding: 13px 7px;
            border-radius: 13px;
            background: #F5F8FA;
            text-align: center;
        }

        .eclass-card-stat strong {
            display: block;
            color: #123B60;
            font-family: 'Playfair Display',serif;
            font-size: 23px;
            font-weight: 700;
        }

        .eclass-card-stat span {
            display: block;
            margin-top: 3px;
            color: #84919E;
            font-size: 9px;
            font-weight: 600;
        }

        .eclass-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 19px;
            padding-top: 15px;
            border-top: 1px solid #EDF1F4;
        }

        .eclass-year {
            color: #8794A0;
            font-size: 10px;
        }

        .eclass-open {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #176A9E;
            font-size: 11px;
            font-weight: 800;
            transition: .2s ease;
        }

        .eclass-card:hover .eclass-open {
            gap: 11px;
            color: #123B60;
        }

        /* EMPTY STATE */

        .eclass-empty {
            padding: 65px 20px;
            border: 1px dashed #C9DCE9;
            border-radius: 24px;
            background: white;
            text-align: center;
        }

        .eclass-empty-icon {
            display: grid;
            width: 75px;
            height: 75px;
            margin: auto;
            place-items: center;
            border-radius: 24px;
            background: #E8F1F8;
            color: #267BB5;
            font-size: 33px;
        }

        .eclass-empty h3 {
            margin-top: 20px;
            color: #123B60;
            font-family: 'Playfair Display',serif;
            font-size: 26px;
            font-weight: 700;
        }

        .eclass-empty p {
            max-width: 390px;
            margin: 10px auto 0;
            color: #82909D;
            font-size: 12px;
            line-height: 1.8;
        }

        .eclass-footer {
            margin-top: 60px;
            padding-top: 22px;
            border-top: 1px solid #E5EAEF;
            color: #9AA5AE;
            font-size: 10px;
            text-align: center;
            letter-spacing: .07em;
        }

        /* RESPONSIVE */

        @media(max-width: 1050px) {
            .eclass-grid {
                grid-template-columns: repeat(2,minmax(0,1fr));
            }

            .eclass-hero {
                padding: 35px;
            }
        }

        @media(max-width: 760px) {
            .eclass-hero {
                grid-template-columns: 1fr;
            }

            .eclass-hero-visual {
                min-height: 210px;
            }

            .eclass-hero-art {
                min-height: 210px;
            }

            .eclass-summary {
                grid-template-columns: repeat(3,minmax(0,1fr));
                gap: 10px;
            }

            .eclass-summary-card {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }
        }

        @media(max-width: 560px) {
            .eclass-page {
                padding: 22px 0 45px;
            }

            .eclass-wrap {
                padding: 0 15px;
            }

            .eclass-hero {
                padding: 26px 21px;
                border-radius: 23px;
            }

            .eclass-hero h1 {
                font-size: 39px;
            }

            .eclass-hero p.description {
                font-size: 12px;
            }

            .eclass-summary {
                grid-template-columns: 1fr;
            }

            .eclass-summary-card {
                flex-direction: row;
                align-items: center;
                padding: 14px 17px;
            }

            .eclass-grid {
                grid-template-columns: 1fr;
            }

            .eclass-section {
                margin-top: 42px;
            }

            .eclass-section-title {
                font-size: 24px;
            }

            .eclass-section-heading {
                align-items: flex-start;
            }

            .eclass-card-cover {
                min-height: 155px;
            }
        }

        @media(prefers-reduced-motion: reduce) {
            .eclass-card,
            .eclass-summary-card {
                transition: none;
            }
        }
    </style>

    <main class="eclass-page">
        <div class="eclass-wrap">

            {{-- HERO SECTION --}}

            <section class="eclass-hero">

                <div>
                    <span class="eclass-eyebrow">
                        <svg class="h-3 w-3 text-[#185B87]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>METHODIST LEARNING SPACE</span>
                    </span>

                    <h1>
                        Setiap Kelas,<br>
                        <span>Peluang Baru Menanti.</span>
                    </h1>

                    <p class="description">
                        Selamat datang di ruang pembelajaran digital Anda.
                        Kelola materi, tugas, siswa, dan aktivitas akademik
                        dengan pengalaman mengajar yang lebih terorganisasi.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">

                        <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white/80 px-4 py-2 text-xs font-bold text-blue-900">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ $totalClasses }} Kelas Aktif
                        </span>

                        <span class="text-xs text-slate-500">
                            Mendidik dengan tujuan dan ketulusan.
                        </span>

                    </div>
                </div>

                <div class="eclass-hero-visual">

                    <div class="eclass-hero-art group">

                        <img src="{{ asset('images/methodist-teachers-activity.jpg') }}"
                             alt="Aktivitas Guru SMAS Methodist Banda Aceh"
                             class="eclass-hero-art-img">
                        <div class="eclass-hero-art-overlay"></div>

                        <div class="eclass-hero-art-content">
                            <p class="text-[10px] font-bold tracking-[.2em] text-sky-200 uppercase drop-shadow-sm">
                                METHODIST LEARNING SPACE
                            </p>

                            <div class="art-symbol drop-shadow-md">E-Class.</div>

                            <h2 class="eclass-serif mt-3 text-2xl font-bold drop-shadow-md">
                                Belajar. Tumbuh. Berprestasi.
                            </h2>

                            <p class="mt-2 text-xs text-slate-100 font-medium drop-shadow">
                                Mewujudkan prestasi melalui pendidikan bermutu.
                            </p>
                        </div>

                    </div>

                </div>

            </section>

            {{-- SUMMARY CARDS --}}

            <section class="eclass-summary">

                <article class="eclass-summary-card">
                    <div class="summary-icon">
                        <svg class="h-6 w-6 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number">{{ $totalClasses }}</p>
                        <p class="summary-label">Total Kelas</p>
                    </div>
                </article>

                <article class="eclass-summary-card">
                    <div class="summary-icon">
                        <svg class="h-6 w-6 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-5.25 6.557q.75.31 1.5.586"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number">{{ $totalStudents }}</p>
                        <p class="summary-label">Siswa Terdaftar</p>
                    </div>
                </article>

                <article class="eclass-summary-card">
                    <div class="summary-icon">
                        <svg class="h-6 w-6 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                    </div>
                    <div>
                        <p class="summary-number">{{ $totalMaterials }}</p>
                        <p class="summary-label">Materi Pembelajaran</p>
                    </div>
                </article>

            </section>

            {{-- CLASS LIST --}}

            <section class="eclass-section">

                <div class="eclass-section-heading">

                    <div>
                        <p class="eclass-section-label">
                            Ruang Pembelajaran Anda
                        </p>

                        <h2 class="eclass-section-title">
                            Daftar Kelas yang Anda Ampu
                        </h2>

                        <p class="eclass-section-description">
                            Pilih kelas untuk mengelola materi, tugas, jadwal mengajar, dan direktori siswa.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                        @if ($classes->isNotEmpty())
                            <div class="relative w-full sm:w-64">
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input
                                    id="guruClassSearch"
                                    type="search"
                                    placeholder="Cari kelas atau mapel..."
                                    class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-10 pr-4 text-xs font-medium text-slate-700 shadow-sm transition focus:border-[#267BB5] focus:ring-[#267BB5]">
                            </div>
                        @endif

                        <span class="eclass-count shrink-0">
                            {{ $totalClasses }} Kelas
                        </span>
                    </div>

                </div>

                @if ($classes->isEmpty())

                    <div class="eclass-empty">

                        <div class="eclass-empty-icon">
                            <svg class="h-8 w-8 text-[#267BB5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                            </svg>
                        </div>

                        <h3>
                            Belum Ada Kelas yang Diampu
                        </h3>

                        <p>
                            Saat ini belum ada kelas yang ditugaskan kepada Anda.
                            Kelas yang nantinya Anda ampu akan muncul di halaman ini.
                        </p>

                    </div>

                @else

                    <div class="eclass-grid">

                        @foreach ($classes as $class)

                            @php
                                $subjectName = $class->subject->name
                                    ?? $class->subject_code
                                    ?? 'Mata Pelajaran';

                                $subjectLower = strtolower($subjectName);

                                $theme = $defaultTheme;

                                foreach ($subjectThemes as $key => $value) {
                                    if (str_contains($subjectLower, $key)) {
                                        $theme = $value;
                                        break;
                                    }
                                }

                                if (empty($theme['image'])) {
                                    $map = [
                                        'matematika' => 'matematika.jpg', 'biologi' => 'biologi.jpg', 'kimia' => 'kimia.jpg',
                                        'fisika' => 'fisika.jpg', 'indonesia' => 'indonesia.jpg', 'bahasa indonesia' => 'indonesia.jpg',
                                        'informatika' => 'informatika.jpg', 'inggris' => 'inggris.svg', 'sejarah' => 'sejarah.svg',
                                        'geografi' => 'geografi.svg', 'ekonomi' => 'ekonomi.svg', 'sosiologi' => 'sosiologi.svg',
                                        'agama' => 'agama.svg', 'pjok' => 'pjok.svg', 'seni' => 'seni-budaya.svg', 'ppkn' => 'ppkn.svg',
                                    ];
                                    foreach ($map as $k => $f) {
                                        if (str_contains($subjectLower, $k)) {
                                            $jpg = preg_replace('/\.(svg|png)$/i', '.jpg', $f);
                                            $svg = preg_replace('/\.(jpg|png)$/i', '.svg', $f);
                                            if (file_exists(public_path('images/subjects/' . $jpg))) {
                                                $theme['image'] = asset('images/subjects/' . $jpg);
                                                break;
                                            } elseif (file_exists(public_path('images/subjects/' . $svg))) {
                                                $theme['image'] = asset('images/subjects/' . $svg);
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp

                            <a href="{{ route('guru.classes.show', $class->cst_id) }}"
                               data-guru-class-card
                               data-search="{{ strtolower($subjectName . ' ' . ($class->schoolClass->name ?? '') . ' ' . ($class->subject_code ?? '')) }}"
                               class="eclass-card"
                               title="Klik untuk membuka kelas {{ $class->schoolClass->name ?? '' }} {{ $subjectName }}">

                                {{-- SUBJECT COVER --}}

                                <div class="eclass-card-cover {{ !empty($theme['image']) ? 'has-image' : '' }}"
                                     style="{{ empty($theme['image']) ? 'background: ' . $theme['background'] : '' }}">

                                    @if(!empty($theme['image']))
                                        <img src="{{ $theme['image'] }}"
                                             alt="{{ $subjectName }}"
                                             class="eclass-cover-image">
                                        <div class="eclass-cover-overlay"></div>
                                    @endif

                                    <div class="eclass-cover-content">

                                        <p class="eclass-cover-category">
                                            {{ $theme['category'] }}
                                        </p>

                                        <h3 class="eclass-cover-name">
                                            {{ $subjectName }}
                                        </h3>

                                        <p class="eclass-cover-description">
                                            {{ $theme['description'] }}
                                        </p>

                                    </div>

                                    <div class="eclass-cover-symbol">
                                        {{ $theme['symbol'] }}
                                    </div>

                                </div>

                                {{-- CARD CONTENT --}}

                                <div class="eclass-card-body">

                                    <p class="eclass-class-label">
                                        KELAS
                                    </p>

                                    <h3 class="eclass-class-name">
                                        {{ $class->schoolClass->name ?? 'Kelas '.$class->class_id }}
                                    </h3>

                                    <p class="eclass-class-meta">
                                        {{ $class->academic_year ?? 'Tahun akademik belum tersedia' }}
                                        ·
                                        Semester {{ ucfirst($class->semester ?? '-') }}
                                    </p>

                                    {{-- STATISTICS --}}

                                    <div class="eclass-card-stats">

                                        <div class="eclass-card-stat">
                                            <strong>
                                                {{ $class->joined_students_count ?? 0 }}
                                            </strong>
                                            <span>Siswa</span>
                                        </div>

                                        <div class="eclass-card-stat">
                                            <strong>
                                                {{ $class->materials_count ?? 0 }}
                                            </strong>
                                            <span>Materi</span>
                                        </div>

                                        <div class="eclass-card-stat">
                                            <strong>
                                                {{ $class->assignments_count ?? 0 }}
                                            </strong>
                                            <span>Tugas</span>
                                        </div>

                                    </div>

                                    {{-- FOOTER --}}

                                    <div class="eclass-card-footer">

                                        <span class="eclass-year">
                                            Kelas Pembelajaran
                                        </span>

                                        <span class="eclass-open">
                                            Buka Kelas <span>→</span>
                                        </span>

                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>

                    {{-- SEARCH EMPTY RESULT --}}
                    <div id="noGuruClassResult" class="hidden rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                        <p class="font-bold text-slate-700">Kelas atau mata pelajaran tidak ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                    </div>

                    {{-- NAVIGASI SLIDE / PAGINASI KARTU KELAS GURU (MAKSIMAL 6 PER SLIDE) --}}
                    <div id="guruClassPagination" class="hidden"></div>

                @endif

            </section>

        </div>
    </main>

    {{-- GURU CLASS SLIDE dan SEARCH SCRIPT --}}
    @if($classes->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('guruClassSearch');
                const cards = Array.from(document.querySelectorAll('[data-guru-class-card]'));
                const empty = document.getElementById('noGuruClassResult');
                const pagination = document.getElementById('guruClassPagination');
                const perPage = 6;
                let currentPage = 1;
                let filteredCards = [...cards];

                function render() {
                    const total = filteredCards.length;
                    const totalPages = Math.ceil(total / perPage) || 1;

                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    // Sembunyikan semua kartu terlebih dahulu
                    cards.forEach(c => c.style.display = 'none');

                    // Tampilkan hanya kartu pada slide saat ini
                    filteredCards.forEach((c, idx) => {
                        if (idx >= start && idx < end) {
                            c.style.display = '';
                        }
                    });

                    // Pesan jika pencarian kosong
                    if (empty) {
                        empty.classList.toggle('hidden', total > 0);
                    }

                    // Tampilkan pagination jika lebih dari 6 kartu
                    renderPagination(total, totalPages);
                }

                function renderPagination(total, totalPages) {
                    if (!pagination) return;

                    if (totalPages <= 1) {
                        pagination.innerHTML = '';
                        pagination.classList.add('hidden');
                        return;
                    }

                    pagination.classList.remove('hidden');

                    const startItem = Math.min((currentPage - 1) * perPage + 1, total);
                    const endItem = Math.min(currentPage * perPage, total);

                    let pillsHtml = '';
                    for (let p = 1; p <= totalPages; p++) {
                        const isActive = p === currentPage;
                        pillsHtml += `
                            <button type="button"
                                    data-page="${p}"
                                    title="Buka Slide ${p}"
                                    class="h-9 w-9 rounded-xl text-xs font-black transition flex items-center justify-center ${isActive ? 'bg-[#123B60] text-white shadow-md shadow-blue-900/20 ring-2 ring-[#123B60]/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-[#123B60] shadow-sm'}">
                                ${p}
                            </button>
                        `;
                    }

                    pagination.innerHTML = `
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-200/90">
                            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>Menampilkan <strong class="text-slate-800 font-bold">${startItem}–${endItem}</strong> dari <strong class="text-slate-800 font-bold">${total}</strong> kelas diampu</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">Slide ${currentPage} / ${totalPages}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        data-page="${currentPage - 1}"
                                        ${currentPage === 1 ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border text-xs font-bold transition ${currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm active:scale-95'}">
                                    <span>&larr;</span>
                                    <span>Sebelumnya</span>
                                </button>

                                <div class="flex items-center gap-1.5">
                                    ${pillsHtml}
                                </div>

                                <button type="button"
                                        data-page="${currentPage + 1}"
                                        ${currentPage === totalPages ? 'disabled' : ''}
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border text-xs font-bold transition ${currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm active:scale-95'}">
                                    <span>Berikutnya</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                        </div>
                    `;

                    pagination.querySelectorAll('button[data-page]').forEach(btn => {
                        btn.addEventListener('click', function () {
                            if (this.disabled) return;
                            currentPage = parseInt(this.getAttribute('data-page'));
                            render();

                            const grid = document.querySelector('.eclass-grid');
                            if (grid) {
                                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    });
                }

                if (search) {
                    search.addEventListener('input', function () {
                        const keyword = this.value.trim().toLowerCase();
                        if (!keyword) {
                            filteredCards = [...cards];
                        } else {
                            filteredCards = cards.filter(card => {
                                const searchableText = card.dataset.search || '';
                                return searchableText.includes(keyword);
                            });
                        }
                        currentPage = 1;
                        render();
                    });
                }

                render();
            });
        </script>
    @endif

</x-app-layout>
