<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1.5 py-0.5">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[.2em] text-[#267BB5]">
                    Methodist Learning Space
                </p>
                <h2 class="text-xl font-bold text-slate-800 leading-tight">
                    Dashboard Siswa
                </h2>
            </div>
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm">
                    <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-width="2" d="M16 3v4M8 3v4M3 10h18"/>
                    </svg>
                    <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $getSubjectTheme = function($subjectName, $subjectCode = null) {
            $name = strtolower($subjectName ?? '');
            $code = strtolower($subjectCode ?? '');

            $themes = [
                'matematika' => ['symbol' => 'MAT', 'category' => 'MATEMATIKA', 'image' => 'matematika.jpg', 'gradient' => 'linear-gradient(135deg, #0d2847 0%, #174e7d 100%)'],
                'fisika' => ['symbol' => 'FIS', 'category' => 'FISIKA', 'image' => 'fisika.jpg', 'gradient' => 'linear-gradient(135deg, #091e38 0%, #15508a 100%)'],
                'biologi' => ['symbol' => 'BIO', 'category' => 'BIOLOGI', 'image' => 'biologi.jpg', 'gradient' => 'linear-gradient(135deg, #06282d 0%, #0d5f66 100%)'],
                'kimia' => ['symbol' => 'KIM', 'category' => 'KIMIA', 'image' => 'kimia.jpg', 'gradient' => 'linear-gradient(135deg, #10263f 0%, #1a568a 100%)'],
                'indonesia' => ['symbol' => 'IDN', 'category' => 'BAHASA INDONESIA', 'image' => 'indonesia.jpg', 'gradient' => 'linear-gradient(135deg, #301712 0%, #7d3725 100%)'],
                'inggris' => ['symbol' => 'ENG', 'category' => 'BAHASA INGGRIS', 'image' => 'inggris.svg', 'gradient' => 'linear-gradient(135deg, #18223c 0%, #29457a 100%)'],
                'informatika' => ['symbol' => 'TIK', 'category' => 'INFORMATIKA', 'image' => 'informatika.jpg', 'gradient' => 'linear-gradient(135deg, #092338 0%, #104a75 100%)'],
                'sejarah' => ['symbol' => 'SEJ', 'category' => 'SEJARAH', 'image' => 'sejarah.svg', 'gradient' => 'linear-gradient(135deg, #2b1f13 0%, #694a28 100%)'],
                'geografi' => ['symbol' => 'GEO', 'category' => 'GEOGRAFI', 'image' => 'geografi.svg', 'gradient' => 'linear-gradient(135deg, #0f281e 0%, #1f6146 100%)'],
                'ekonomi' => ['symbol' => 'EKO', 'category' => 'EKONOMI', 'image' => 'ekonomi.svg', 'gradient' => 'linear-gradient(135deg, #132438 0%, #1f5080 100%)'],
                'sosiologi' => ['symbol' => 'SOS', 'category' => 'SOSIOLOGI', 'image' => 'sosiologi.svg', 'gradient' => 'linear-gradient(135deg, #281a33 0%, #5a3575 100%)'],
                'agama' => ['symbol' => 'AGM', 'category' => 'PENDIDIKAN AGAMA', 'image' => 'agama.svg', 'gradient' => 'linear-gradient(135deg, #1f2716 0%, #4a5c2d 100%)'],
                'pjok' => ['symbol' => 'PJK', 'category' => 'PJOK', 'image' => 'pjok.svg', 'gradient' => 'linear-gradient(135deg, #162a33 0%, #255d73 100%)'],
                'seni' => ['symbol' => 'SBD', 'category' => 'SENI BUDAYA', 'image' => 'seni-budaya.svg', 'gradient' => 'linear-gradient(135deg, #2d182b 0%, #6e2e68 100%)'],
                'pkn' => ['symbol' => 'PKN', 'category' => 'PPKN', 'image' => 'ppkn.svg', 'gradient' => 'linear-gradient(135deg, #30141b 0%, #732a3d 100%)'],
            ];

            $aliases = [
                'mtk' => 'matematika', 'fis' => 'fisika', 'bio' => 'biologi', 'kim' => 'kimia',
                'bin' => 'indonesia', 'bahasa indonesia' => 'indonesia', 'big' => 'inggris',
                'bahasa inggris' => 'inggris', 'sej' => 'sejarah', 'inf' => 'informatika',
                'tik' => 'informatika', 'komputer' => 'informatika', 'eko' => 'ekonomi',
                'geo' => 'geografi', 'sos' => 'sosiologi', 'agm' => 'agama',
                'pendidikan agama' => 'agama', 'penjas' => 'pjok', 'sbd' => 'seni',
                'seni budaya' => 'seni', 'ppkn' => 'pkn',
            ];

            $matchedKey = null;
            foreach ($aliases as $alias => $target) {
                if (str_contains($name, $alias) || str_contains($code, $alias)) {
                    $matchedKey = $target;
                    break;
                }
            }

            if (!$matchedKey) {
                foreach (array_keys($themes) as $k) {
                    if (str_contains($name, $k) || str_contains($code, $k)) {
                        $matchedKey = $k;
                        break;
                    }
                }
            }

            $t = $matchedKey ? $themes[$matchedKey] : [
                'symbol' => 'MLS', 'category' => 'KELAS AKADEMIK', 'image' => null,
                'gradient' => 'linear-gradient(135deg, #123B60 0%, #267BB5 100%)',
            ];

            $imgUrl = null;
            if (!empty($t['image'])) {
                $jpgName = preg_replace('/\.(svg|png)$/i', '.jpg', $t['image']);
                $svgName = preg_replace('/\.(jpg|png)$/i', '.svg', $t['image']);
                if (file_exists(public_path('images/subjects/' . $jpgName))) {
                    $imgUrl = asset('images/subjects/' . $jpgName);
                } elseif (file_exists(public_path('images/subjects/' . $svgName))) {
                    $imgUrl = asset('images/subjects/' . $svgName);
                }
            }

            if (!$imgUrl && file_exists(public_path('images/hero-school.jpg'))) {
                $imgUrl = asset('images/hero-school.jpg');
            }

            $t['imageUrl'] = $imgUrl;
            return $t;
        };
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue-primary: #2563EB;
            --blue-dark: #153E82;
            --blue-deep: #102C59;
            --blue-soft: #EFF6FF;
            --blue-light: #DBEAFE;
            --navy: #14213D;
            --muted: #64748B;
            --border: #E7EDF5;
            --background: #F5F8FD;
        }

        .student-dashboard {
            min-height: 100vh;
            padding: 48px 0 72px;
            background: var(--background);
            font-family: 'DM Sans', sans-serif;
            overflow: hidden;
        }

        .student-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* TOP INFORMATION */

        .welcome-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 0;
        }

        .welcome-breadcrumb {
            color: #94A3B8;
            font-size: 12px;
            font-weight: 600;
        }

        .welcome-breadcrumb span {
            color: #2563EB;
        }

        .welcome-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: white;
            color: #64748B;
            font-size: 11px;
            font-weight: 700;
        }

        /* HERO */

        .student-hero {
            position: relative;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            align-items: center;
            gap: 35px;
            min-height: 390px;
            padding: 48px;
            overflow: hidden;
            border-radius: 30px;
            background:
                radial-gradient(circle at 85% 15%, rgba(96,165,250,.28), transparent 30%),
                linear-gradient(125deg, #102C59 0%, #174B98 55%, #2563EB 100%);
            box-shadow: 0 22px 55px rgba(21, 62, 130, .16);
        }

        .student-hero::before {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            top: -190px;
            right: 15%;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .student-hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            bottom: -220px;
            left: 35%;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 15px;
            margin-bottom: 25px;
            border: 1px solid rgba(255,255,255,.17);
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            color: #DBEAFE;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .hero-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #60A5FA;
            box-shadow: 0 0 12px #60A5FA;
        }

        .hero-title {
            margin: 0;
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: clamp(34px, 4.5vw, 52px);
            font-weight: 700;
            line-height: 1.16;
            letter-spacing: -.8px;
        }

        .hero-title em {
            color: #93C5FD;
            font-style: normal;
            overflow-wrap: anywhere;
        }

        .hero-description {
            max-width: 490px;
            margin: 20px 0 27px;
            color: #DCEBFF;
            font-size: 13px;
            line-height: 1.9;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 13px 21px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            transition: .25s ease;
        }

        .hero-button-primary {
            background: white;
            color: #174B98;
            box-shadow: 0 8px 20px rgba(0,0,0,.12);
        }

        .hero-button-primary:hover {
            background: #DBEAFE;
            color: #153E82;
            transform: translateY(-3px);
        }

        .hero-button-secondary {
            border: 1px solid rgba(255,255,255,.25);
            background: rgba(255,255,255,.08);
            color: white;
        }

        .hero-button-secondary:hover {
            background: rgba(255,255,255,.17);
            color: white;
            transform: translateY(-3px);
        }

        /* HERO VISUAL */

        .hero-visual {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 270px;
        }

        .visual-main {
            position: relative;
            width: 250px;
            height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.23);
            border-radius: 34px;
            background: linear-gradient(145deg, rgba(255,255,255,.18), rgba(255,255,255,.06));
            box-shadow: 0 25px 50px rgba(0,0,0,.13);
            backdrop-filter: blur(12px);
            transform: rotate(-5deg);
        }

        .visual-main-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            margin-bottom: 20px;
            border-radius: 27px;
            background: white;
            color: #2563EB;
            box-shadow: 0 14px 28px rgba(0,0,0,.15);
        }

        .visual-main-icon svg {
            width: 48px;
            height: 48px;
        }

        .visual-main strong {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 21px;
            letter-spacing: .2px;
        }

        .visual-main small {
            margin-top: 6px;
            color: #DBEAFE;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .visual-decoration {
            position: absolute;
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 18px;
            background: rgba(255,255,255,.14);
            color: white;
            backdrop-filter: blur(10px);
        }

        .visual-decoration svg {
            width: 26px;
            height: 26px;
        }

        .visual-decoration.one {
            top: 7px;
            right: 18px;
            transform: rotate(12deg);
        }

        .visual-decoration.two {
            bottom: 3px;
            left: 5px;
            transform: rotate(-12deg);
        }

        /* SECTION HEADER */

        .section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin: 48px 0 20px;
        }

        .section-eyebrow {
            margin-bottom: 7px;
            color: #2563EB;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .section-title {
            margin: 0;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            font-weight: 700;
        }

        .section-description {
            margin-top: 7px;
            color: #8492A6;
            font-size: 12px;
        }

        /* QUICK ACCESS */

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .quick-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 225px;
            padding: 27px;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: white;
            box-shadow: 0 8px 28px rgba(15, 40, 80, .035);
            text-decoration: none;
            transition: transform .25s, border-color .25s, box-shadow .25s;
        }

        .quick-card:hover {
            transform: translateY(-5px);
            border-color: #BFDBFE;
            box-shadow: 0 18px 38px rgba(37,99,235,.09);
        }

        .quick-card::after {
            content: '';
            position: absolute;
            width: 155px;
            height: 155px;
            right: -70px;
            bottom: -75px;
            border-radius: 50%;
            background: #EFF6FF;
            transition: transform .3s;
        }

        .quick-card:hover::after {
            transform: scale(1.3);
        }

        .quick-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .quick-icon {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 17px;
            background: #EFF6FF;
            color: #2563EB;
        }

        .quick-icon svg {
            width: 27px;
            height: 27px;
        }

        .quick-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #F1F5F9;
            color: #64748B;
            transition: .25s;
        }

        .quick-card:hover .quick-arrow {
            background: #2563EB;
            color: white;
            transform: translateX(3px);
        }

        .quick-card h3 {
            position: relative;
            z-index: 1;
            margin: 23px 0 8px;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            font-weight: 700;
        }

        .quick-card p {
            position: relative;
            z-index: 1;
            max-width: 360px;
            margin: 0;
            color: #718096;
            font-size: 12px;
            line-height: 1.8;
        }

        .quick-link {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 20px;
            color: #2563EB;
            font-size: 11px;
            font-weight: 800;
        }

        /* BOTTOM INFORMATION */

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
            margin-top: 25px;
        }

        .info-card {
            position: relative;
            overflow: hidden;
            padding: 25px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: white;
            box-shadow: 0 8px 28px rgba(15,40,80,.035);
        }

        .info-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 19px;
        }

        .info-heading-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 13px;
            background: #EFF6FF;
            color: #2563EB;
        }

        .info-heading-icon svg {
            width: 21px;
            height: 21px;
        }

        .info-heading h3 {
            margin: 0;
            color: var(--navy);
            font-size: 13px;
            font-weight: 800;
        }

        .info-heading p {
            margin-top: 4px;
            color: #94A3B8;
            font-size: 10px;
        }

        .info-text {
            color: #64748B;
            font-size: 12px;
            line-height: 1.9;
        }

        .quote-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 190px;
            border: 0;
            background: linear-gradient(135deg, #EAF2FF, #F8FAFF);
        }

        .quote-mark {
            color: #93C5FD;
            font-family: 'Playfair Display', serif;
            font-size: 58px;
            line-height: .8;
        }

        .quote-text {
            margin: 12px 0;
            color: #1E3A8A;
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.6;
        }

        .quote-author {
            color: #64748B;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        /* FOOTER */

        .dashboard-footer {
            margin-top: 40px;
            padding-top: 22px;
            border-top: 1px solid #E2E8F0;
            text-align: center;
        }

        .dashboard-footer p {
            color: #94A3B8;
            font-size: 10px;
            letter-spacing: .3px;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            .student-hero {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 38px;
            }

            .hero-visual {
                min-height: 280px;
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .student-dashboard {
                padding: 28px 0 50px;
            }

            .student-wrap {
                padding: 0 16px;
            }

            .student-hero {
                min-height: auto;
                padding: 28px 23px;
                border-radius: 23px;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-description {
                font-size: 12px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-button {
                width: 100%;
            }

            .hero-visual {
                min-height: 245px;
            }

            .visual-main {
                width: 205px;
                height: 205px;
            }

            .visual-main-icon {
                width: 72px;
                height: 72px;
            }

            .visual-main-icon svg {
                width: 39px;
                height: 39px;
            }

            .quick-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .quick-card {
                min-height: 205px;
                padding: 23px;
            }

            .section-heading {
                margin-top: 36px;
            }

            .section-title {
                font-size: 22px;
            }

            .info-card {
                padding: 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .student-dashboard *,
            .student-dashboard *::before,
            .student-dashboard *::after {
                transition: none !important;
            }
        }
    </style>

    <div class="student-dashboard">
        <div class="student-wrap space-y-7 sm:space-y-8">

            {{-- TOP BAR --}}
            <div class="welcome-top">
                <div class="welcome-breadcrumb">
                    METHODIST LEARNING SPACE
                    <span> / STUDENT PORTAL</span>
                </div>
            </div>

            @if (session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm flex items-start gap-3">
                    <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">Perhatian</p>
                        <p class="mt-0.5 text-xs text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- HERO BANNER IDENTITAS SISWA (FOTO SISWA SMAS METHODIST DENGAN OVERLAY RESMI) --}}
            <section class="relative isolate overflow-hidden rounded-3xl bg-[#071326] p-6 text-white shadow-2xl sm:p-10">
                {{-- Foto Siswa SMAS Methodist Banda Aceh --}}
                <img src="{{ asset('images/methodist-students.jpg') }}"
                     alt="Siswa SMAS Methodist Banda Aceh"
                     class="absolute inset-0 -z-20 h-full w-full object-cover object-[center_30%]">
                
                {{-- Overlay Gradien Navy Elegan untuk Keterbacaan Kontras Maksimal --}}
                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#061122]/80 via-[#091b36]/65 to-[#0e2a54]/45"></div>
                
                {{-- Aksen Grid Geometris Halus (Gaya Portal Resmi Methodist) --}}
                <div class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgba(255,255,255,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.06)_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

                {{-- Ambient Background Glow --}}
                <div class="absolute -right-20 -top-20 -z-10 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 left-1/3 -z-10 h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>

                <div class="grid items-center gap-8 lg:grid-cols-12">
                    
                    {{-- Sapaan dan Detail Profil Siswa --}}
                    <div class="lg:col-span-8">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1 text-xs font-semibold text-white/90 backdrop-blur-md">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Siswa Aktif SMAS Methodist</span>
                        </div>

                        <h1 class="mt-4 font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                            Halo, <span class="text-sky-300">{{ $user->name }}</span>!
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-200 sm:text-base">
                            Selamat datang di portal akademik digitalmu. Pantau materi pelajaran, kerjakan tugas tepat waktu, dan ikuti ujian dengan jujur dan penuh semangat.
                        </p>

                        {{-- Metadata Siswa (NIS, Kelas, Wali Kelas) --}}
                        <div class="mt-6 flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs">
                            <div class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3.5 py-2 backdrop-blur-md">
                                <span class="text-slate-400">NIS:</span>
                                <span class="font-mono font-bold text-white">{{ $student->nis ?? '-' }}</span>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3.5 py-2 backdrop-blur-md">
                                <span class="text-slate-400">NISN:</span>
                                <span class="font-mono font-bold text-white">{{ $student->nisn ?? '-' }}</span>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-xl border border-sky-400/40 bg-sky-500/20 px-3.5 py-2 backdrop-blur-md">
                                <span class="text-sky-200">Kelas:</span>
                                <span class="font-bold text-white">{{ $student->schoolClass->name ?? 'Belum Ditentukan' }}</span>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3.5 py-2 backdrop-blur-md">
                                <span class="text-slate-400">Wali Kelas:</span>
                                <span class="font-semibold text-white">
                                    {{ $student->schoolClass?->homeroomTeacher?->user?->name ?? 'Belum Ditugaskan' }}
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Aksi Cepat --}}
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <a href="{{ route('siswa.classes.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-xs sm:text-sm font-bold text-[#101C35] shadow-lg shadow-black/20 transition hover:bg-slate-100 hover:shadow-xl hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                                </svg>
                                <span>Buka E-Class Saya</span>
                            </a>

                            <a href="{{ route('siswa.exams.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-xs sm:text-sm font-semibold text-white backdrop-blur-md transition hover:bg-white/20 hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-sky-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Jadwal Ujian (E-Exam)</span>
                            </a>

                            <a href="{{ route('siswa.roster') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-xs sm:text-sm font-semibold text-white backdrop-blur-md transition hover:bg-white/20 hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                                <span>Roster Pelajaran</span>
                            </a>
                        </div>
                    </div>

                    {{-- Kartu Ringkasan Samping (Visual Card) --}}
                    <div class="lg:col-span-4">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-xl">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-sky-400 text-2xl font-bold text-white shadow-lg shadow-sky-500/30">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-base font-bold text-white">{{ $user->name }}</h3>
                                    <p class="truncate text-xs text-sky-200">{{ $user->email }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-full bg-emerald-400/20 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-300">
                                        {{ $student->gender === 'L' ? 'Laki-Laki' : ($student->gender === 'P' ? 'Perempuan' : 'Siswa') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-white/10 pt-4 space-y-2.5 text-xs text-slate-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Tahun Ajaran:</span>
                                    <span class="font-semibold">{{ $student->schoolClass->academic_year ?? '2024/2025' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Status Akun:</span>
                                    <span class="font-semibold text-emerald-300 capitalize">{{ $student->status ?? 'Aktif' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Tahun Masuk:</span>
                                    <span class="font-semibold">{{ $student->entry_year ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- 4 KARTU STATISTIK CEPAT (QUICK STATS GRID) --}}
            <section class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:gap-6">
                
                {{-- Stat 1: Total Kelas --}}
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif text-2xl font-bold text-slate-800">{{ $totalJoined }}</p>
                        <p class="text-xs font-medium text-slate-500">Kelas Diikuti</p>
                    </div>
                </div>

                {{-- Stat 2: Tugas Menunggu --}}
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif text-2xl font-bold text-slate-800">{{ $pendingAssignments->count() }}</p>
                        <p class="text-xs font-medium text-slate-500">Tugas Pending</p>
                    </div>
                </div>

                {{-- Stat 3: Ujian Tersedia --}}
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif text-2xl font-bold text-slate-800">{{ $upcomingExams->count() }}</p>
                        <p class="text-xs font-medium text-slate-500">Ujian CBT</p>
                    </div>
                </div>

                {{-- Stat 4: Tugas Terkumpul --}}
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-serif text-2xl font-bold text-slate-800">{{ $submittedCount }}</p>
                        <p class="text-xs font-medium text-slate-500">Tugas Diserahkan</p>
                    </div>
                </div>

            </section>

            {{-- KONTEN UTAMA: 2 KOLOM (KOLOM KIRI 8 COLS, KOLOM KANAN 4 COLS) --}}
            <div class="grid gap-8 lg:grid-cols-12">
                
                {{-- KOLOM KIRI (8 COLS): KELAS SAYA dan TUGAS PENDING --}}
                <div class="space-y-8 lg:col-span-8">
                    
                    {{-- Section: Mata Pelajaran Aktif (E-Class) --}}
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-serif text-xl font-bold text-slate-800">
                                    Kelas Mata Pelajaran
                                </h3>
                                <p class="text-xs text-slate-500">
                                    Rombongan belajar yang sedang kamu ikuti di semester ini.
                                </p>
                            </div>
                            <a href="{{ route('siswa.classes.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                <span>Lihat Semua</span>
                                <span>&rarr;</span>
                            </a>
                        </div>

                        @if ($joinedClasses->isNotEmpty())
                            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                @foreach ($joinedClasses as $cst)
                                    @php
                                        $subName = $cst->subject->name ?? $cst->subject_code ?? 'Mata Pelajaran';
                                        $subTheme = $getSubjectTheme($subName, $cst->subject_code);
                                        $clsName = $cst->schoolClass->name ?? '';
                                    @endphp
                                    <article class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:border-[#BBD7E9] hover:shadow-lg">
                                        {{-- Mini Header Cover --}}
                                        <div class="relative min-h-[90px] p-4 text-white overflow-hidden flex flex-col justify-between" style="{{ empty($subTheme['imageUrl']) ? 'background: ' . $subTheme['gradient'] : 'background-color: #123B60;' }}">
                                            @if(!empty($subTheme['imageUrl']))
                                                <img src="{{ $subTheme['imageUrl'] }}" alt="{{ $subName }}" class="absolute inset-0 h-full w-full object-cover transition transform group-hover:scale-105 duration-300">
                                                <div class="absolute inset-0 bg-gradient-to-r from-[#091a30]/90 to-[#123B60]/70 pointer-events-none"></div>
                                            @endif
                                            <div class="relative z-10 flex items-center justify-between gap-2">
                                                <span class="rounded bg-white/20 backdrop-blur-md px-2 py-0.5 text-[10px] font-extrabold text-white border border-white/20">
                                                    {{ $subTheme['symbol'] }}
                                                </span>
                                                <span class="text-[10px] font-bold text-cyan-200">
                                                    {{ $clsName }}
                                                </span>
                                            </div>
                                            <h4 class="relative z-10 mt-2 font-serif text-base font-bold text-white truncate">
                                                {{ $subName }}
                                            </h4>
                                        </div>

                                        {{-- Body --}}
                                        <div class="p-4 flex flex-col flex-grow justify-between">
                                            <p class="text-xs text-slate-500 flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="truncate">{{ $cst->teacher?->user?->name ?? 'Guru Pengampu' }}</span>
                                            </p>

                                            <div class="mt-4 border-t border-slate-100 pt-3">
                                                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                                    <span>{{ $cst->materials_count }} Materi</span>
                                                    <span>&bull;</span>
                                                    <span>{{ $cst->assignments_count }} Tugas</span>
                                                    <span>&bull;</span>
                                                    <span>{{ $cst->exams_count }} Ujian</span>
                                                </div>

                                                <a href="{{ route('siswa.classes.show', $cst->cst_id) }}"
                                                   class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-slate-50 border border-slate-200 py-2 text-xs font-bold text-[#123B60] transition hover:bg-[#123B60] hover:text-white hover:border-[#123B60]">
                                                    <span>Masuk Kelas</span>
                                                    <span>&rarr;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State: Belum Ada Kelas --}}
                            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-8 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-[#123B60]">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25"/>
                                    </svg>
                                </div>
                                <h4 class="mt-3 font-serif text-base font-bold text-slate-800">Belum Ada Kelas yang Diikuti</h4>
                                <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-slate-500">
                                    Dapatkan kode gabung kelas dari guru mata pelajaranmu, lalu masukkan pada formulir di sebelah kanan untuk mulai belajar.
                                </p>
                            </div>
                        @endif
                    </section>

                    {{-- Section: Tugas Mendekati Tenggat Waktu --}}
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-serif text-xl font-bold text-slate-800">
                                    Tugas Perlu Dikerjakan
                                </h3>
                                <p class="text-xs text-slate-500">
                                    Daftar penugasan aktif yang belum kamu kumpulkan.
                                </p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                {{ $pendingAssignments->count() }} Menunggu
                            </span>
                        </div>

                        @if ($pendingAssignments->isNotEmpty())
                            <div class="mt-4 divide-y divide-slate-100">
                                @foreach ($pendingAssignments as $assignment)
                                    @php
                                        $isUrgent = $assignment->due_date && $assignment->due_date->isFuture() && $assignment->due_date->diffInHours(now()) <= 24;
                                        $isOverdue = $assignment->due_date && $assignment->due_date->isPast();
                                    @endphp
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700">
                                                    {{ $assignment->classSubjectTeacher?->subject?->name ?? 'Mata Pelajaran' }}
                                                </span>
                                                @if ($isOverdue)
                                                    <span class="rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">Lewat Tenggat</span>
                                                @elseif ($isUrgent)
                                                    <span class="rounded bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 animate-pulse">Segera Berakhir</span>
                                                @endif
                                            </div>
                                            <h4 class="font-medium text-sm text-slate-800">
                                                {{ $assignment->title }}
                                            </h4>
                                            <p class="text-xs text-slate-400">
                                                Batas pengumpulan: <span class="font-semibold text-slate-600">{{ $assignment->due_date ? $assignment->due_date->translatedFormat('d M Y, H:i') : 'Tanpa batas' }}</span>
                                            </p>
                                        </div>

                                        <a href="{{ route('siswa.classes.show', $assignment->cst_id) }}"
                                           class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white transition">
                                            <span>Kumpulkan Tugas</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Semua Tugas Selesai --}}
                            <div class="mt-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 p-6 text-center">
                                <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="mt-2 font-serif text-sm font-bold text-emerald-900">Hebat! Semua Tugas Sudah Kamu Kumpulkan</h4>
                                <p class="mt-1 text-xs text-emerald-700">Tidak ada penugasan tertunda saat ini. Manfaatkan waktu untuk mengulang materi di E-Class.</p>
                            </div>
                        @endif
                    </section>

                </div>

                {{-- KOLOM KANAN (4 COLS): GABUNG KELAS, JADWAL UJIAN, WARTA --}}
                <div class="space-y-6 lg:col-span-4">

                    {{-- Widget 0: Roster Pelajaran Hari Ini --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h3 class="font-serif text-sm font-bold text-slate-800">Roster Hari Ini</h3>
                            </div>
                            <a href="{{ route('siswa.roster') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                                Lengkap &rarr;
                            </a>
                        </div>

                        <div class="mt-2.5 flex items-center justify-between text-xs">
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 font-bold text-[#123B60] text-[11px]">
                                Kelas {{ $className ?? 'X-A' }}
                            </span>
                            <span class="text-slate-400 font-semibold text-[11px]">{{ $todaySchedule['day_name'] ?? 'Hari Ini' }}</span>
                        </div>

                        @if(!empty($todayGroupedSchedule ?? $todaySchedule['items'] ?? []))
                            @php
                                $displayItems = !empty($todayGroupedSchedule) ? $todayGroupedSchedule : ($todaySchedule['items'] ?? []);
                            @endphp
                            <div class="mt-2.5 space-y-1.5">
                                @foreach(array_slice($displayItems, 0, 3) as $item)
                                    @if(!empty($item['is_break']) || !empty($item['is_special']))
                                        <div class="rounded-xl border border-dashed border-amber-200 bg-amber-50/70 px-3 py-1.5 text-center">
                                            <p class="text-[10px] font-bold text-amber-800">{{ $item['time'] }} &bull; {{ $item['activity'] }}</p>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 border border-slate-100 hover:bg-slate-100/80 transition">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="rounded bg-[#123B60] text-white px-1.5 py-0.5 text-[9px] font-mono font-bold shrink-0">
                                                    {{ $item['les_label'] ?? ('#' . ($item['les'] ?? '')) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $item['subject_desc'] ?? $item['subject'] }}</p>
                                                    <p class="text-[10px] text-slate-400 truncate">{{ $item['teacher_name'] ?? $item['teacher'] }}</p>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-mono font-semibold text-slate-400 shrink-0 ml-2">{{ $item['time'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="mt-3 text-center text-xs text-slate-400 py-2">Tidak ada jadwal pelajaran hari ini.</p>
                        @endif

                        <a href="{{ route('siswa.roster') }}" class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 py-2 text-xs font-bold text-slate-700 hover:bg-[#123B60] hover:text-white transition">
                            <span>Buka Roster Lengkap Kelas {{ $className ?? 'X-A' }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    {{-- Widget 1: Gabung Kelas Baru --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#123B60]">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800">Gabung Kelas Baru</h3>
                                <p class="text-[10px] text-slate-400">Punya kode dari guru mapel?</p>
                            </div>
                        </div>

                        <form action="{{ route('siswa.classes.join') }}" method="POST" class="mt-3 space-y-2.5">
                            @csrf
                            <div>
                                <input
                                    id="join_code"
                                    name="join_code"
                                    type="text"
                                    maxlength="8"
                                    placeholder="CONTOH: 10AMAT1"
                                    required
                                    style="text-transform: uppercase;"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-center font-mono text-xs tracking-wider uppercase text-slate-800 placeholder-slate-400 focus:border-[#267BB5] focus:outline-none focus:ring-1 focus:ring-blue-100"
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-[#123B60] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#267BB5] active:scale-[0.99]"
                            >
                                Gabung ke Rombel
                            </button>
                        </form>
                    </div>

                    {{-- Widget 2: Ujian Daring (E-Exam Terdekat) --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <h3 class="font-serif text-sm font-bold text-slate-800">Jadwal Ujian (CBT)</h3>
                            <a href="{{ route('siswa.exams.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                                Semua ({{ $upcomingExams->count() }}) &rarr;
                            </a>
                        </div>

                        @if ($upcomingExams->isNotEmpty())
                            <div class="mt-3 space-y-2.5">
                                @foreach ($upcomingExams->take(2) as $exam)
                                    @php
                                        $isOpen = $exam->isOpen();
                                    @endphp
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-3 transition hover:bg-white hover:border-slate-200 hover:shadow-xs">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">
                                                {{ $exam->classSubjectTeacher?->subject?->name ?? 'Ujian' }}
                                            </span>
                                            @if ($isOpen)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-700 shrink-0">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Sedang Berlangsung
                                                </span>
                                            @else
                                                <span class="rounded bg-slate-200 px-2 py-0.5 text-[9px] font-semibold text-slate-600 shrink-0">
                                                    Terjadwal
                                                </span>
                                            @endif
                                        </div>

                                        <h4 class="mt-1.5 text-xs font-bold text-slate-800 truncate" title="{{ $exam->title }}">{{ $exam->title }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            Durasi: {{ $exam->duration_minutes }}m &bull; Mulai: {{ $exam->start_time->translatedFormat('d M, H:i') }}
                                        </p>

                                        <a href="{{ route('siswa.exams.show', $exam->exam_id) }}"
                                           class="mt-2.5 inline-flex w-full items-center justify-center gap-1 rounded-lg {{ $isOpen ? 'bg-[#123B60] text-white hover:bg-[#267BB5]' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }} py-1.5 text-[11px] font-bold transition">
                                            <span>{{ $isOpen ? 'Mulai Kerjakan Ujian' : 'Lihat Informasi' }}</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                @endforeach

                                @if($upcomingExams->count() > 2)
                                    <a href="{{ route('siswa.exams.index') }}" class="block text-center text-xs font-bold text-blue-600 hover:text-blue-800 pt-1">
                                        +{{ $upcomingExams->count() - 2 }} Ujian Lainnya (Lihat Semua) &rarr;
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 p-5 text-center text-xs text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Belum ada ujian yang dijadwalkan dalam waktu dekat.
                            </div>
                        @endif
                    </div>

                    {{-- Widget 3: Warta dan Pengumuman Sekolah --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-serif text-base font-bold text-slate-800">Warta Sekolah</h3>
                            <a href="{{ route('announcements.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                                Arsip &rarr;
                            </a>
                        </div>

                        @if ($announcements->isNotEmpty())
                            <div class="mt-4 space-y-3">
                                @foreach ($announcements as $info)
                                    <article class="border-b border-slate-100 pb-3 last:border-b-0 last:pb-0">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                                            <span class="font-semibold text-blue-600 capitalize">{{ $info->category }}</span>
                                            <time>{{ optional($info->published_at)->translatedFormat('d M Y') }}</time>
                                        </div>
                                        <h4 class="mt-1 text-xs font-bold text-slate-800 hover:text-blue-600">
                                            <a href="{{ route('announcements.show', $info->slug) }}">
                                                {{ $info->title }}
                                            </a>
                                        </h4>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-4 text-center text-xs text-slate-400">
                                Belum ada warta pengumuman terbaru.
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
