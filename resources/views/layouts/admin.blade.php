
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | MALAS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --navy: #172554;
            --blue: #2563eb;
            --bg: #f5f7fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            font-family: 'Segoe UI', sans-serif;
            color: #1e293b;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 255px;
            background: var(--navy);
            color: white;
            padding: 24px 17px;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: .3s;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 5px 10px 25px;
            border-bottom: 1px solid #ffffff20;
        }

        .brand-icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            background: var(--blue);
            border-radius: 12px;
            font-size: 22px;
        }

        .brand h5 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .brand small {
            color: #bfdbfe;
            font-size: 10px;
        }

        .menu-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            padding: 25px 12px 10px;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            margin-bottom: 5px;
            font-size: 14px;
            text-decoration: none;
            transition: .2s;
        }

        .sidebar .nav-link i { font-size: 18px; }

        .sidebar .nav-link:hover {
            color: white;
            background: #ffffff15;
        }

        .sidebar .nav-link.active {
            color: white;
            background: var(--blue);
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 18px;
            border-top: 1px solid #ffffff20;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            margin-bottom: 12px;
        }

        .avatar {
            width: 39px;
            height: 39px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 700;
        }

        .admin-profile small { color: #94a3b8; }

        .logout-btn {
            width: 100%;
            padding: 11px;
            color: #fecaca;
            background: transparent;
            border: 1px solid #ffffff30;
            border-radius: 9px;
            text-align: left;
            transition: .2s;
        }

        .logout-btn:hover {
            background: #ef444425;
            border-color: #ef4444;
            color: white;
        }

        /* Main */
        .main-wrapper {
            margin-left: 255px;
            min-height: 100vh;
        }

        .topbar {
            height: 75px;
            padding: 0 30px;
            background: white;
            border-bottom: 1px solid #e8edf4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-title {
            margin: 0;
            font-weight: 700;
            font-size: 15px;
        }

        .topbar small { color: #94a3b8; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-area { padding: 30px; }

        .content-area .card {
            border: 1px solid #e9edf4;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0f172a08;
        }

        .content-area .card-header {
            background: white;
            padding: 15px 18px;
            font-weight: 600;
            border-bottom: 1px solid #edf0f5;
            border-radius: 12px 12px 0 0;
        }

        .content-area .card-body { padding: 18px; }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 25px;
            color: var(--navy);
        }

        .sidebar-overlay { display: none; }

        @media(max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }

            .sidebar-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: #0007;
                z-index: 1040;
            }

            .main-wrapper { margin-left: 0; }
            .menu-toggle { display: block; }

            .topbar { padding: 0 15px; height: 65px; }
            .content-area { padding: 18px 14px; }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">

        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <h5>MALAS</h5>
                <small>Methodist Academic Learning</small>
            </div>
        </div>

        <div class="menu-label">MENU UTAMA</div>

        <nav>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

            <a href="{{ route('announcements.index') }}"
               class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i>
                Pengumuman
            </a>
        </nav>

        <div class="menu-label">AKUN</div>

        <div class="sidebar-footer">

            <div class="admin-profile">
                <div class="avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold small">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>
                    <small>Administrator</small>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}"
                  onsubmit="return confirm('Apakah kamu yakin ingin logout?')">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-left me-2"></i>
                    Logout
                </button>
            </form>

        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- MAIN CONTENT --}}
    <div class="main-wrapper">

        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle" id="menuToggle">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <p class="topbar-title">MALAS Administration</p>
                    <small>Sistem Informasi Akademik</small>
                </div>
            </div>

            <div class="topbar-user">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold small">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>
                    <small>Administrator</small>
                </div>

                <div class="avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>

    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('menuToggle');
        const overlay = document.getElementById('sidebarOverlay');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    </script>

    @stack('scripts')
</body>
</html>
