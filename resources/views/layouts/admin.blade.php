<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Panel') - SMAS Methodist</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS Styles -->
    <style>
        :root {
            --sidebar-bg: #1c233d;
            --sidebar-text: #94a3b8;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            --sidebar-active-bg: #2563eb;
            --sidebar-active-text: #ffffff;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR STYLING */
        .sidebar {
            width: 270px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
        }

        /* Sidebar Brand / Logo */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 18px;
        }
        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background-color: transparent;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .sidebar-brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .sidebar-brand-text h5 {
            font-size: 1.05rem;
            letter-spacing: 0.5px;
        }
        .sidebar-brand-text small {
            font-size: 0.7rem;
            color: var(--sidebar-text);
        }

        /* Sidebar Navigasi */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0 20px 0;
        }

        /* Custom Scrollbar untuk Sidebar */
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        /* Kategori Menu */
        .menu-category {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 18px 8px 18px;
        }

        /* Link Menu */
        .sidebar a.nav-link {
            color: var(--sidebar-text);
            text-decoration: none;
            padding: 10px 15px;
            margin: 4px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .sidebar a.nav-link i {
            font-size: 1.1rem;
        }
        .sidebar a.nav-link:hover {
            color: var(--sidebar-active-text);
            background-color: var(--sidebar-hover-bg);
        }
        .sidebar a.nav-link.active {
            color: var(--sidebar-active-text);
            background-color: var(--sidebar-active-bg);
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        /* Sidebar Footer (Info Akun Saja) */
        .sidebar-footer {
            padding: 15px 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            background-color: #dbeafe;
            color: var(--sidebar-active-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar / Navbar Atas */
        .topbar {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 25px;
            justify-content: space-between;
        }

        .profile-dropdown-btn {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50rem;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }
        .profile-dropdown-btn:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .main-content {
            padding: 25px;
            flex: 1;
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>

    <!-- Tambahan CSS per Halaman -->
    @stack('styles')
    @include('partials.favicon')
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebarMenu">

        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('images/methodist-logo.png') }}" alt="Logo SMAS Methodist Banda Aceh">
            </div>
            <div class="sidebar-brand-text">
                <h5 class="fw-bold text-white mb-0">SMAS Methodist</h5>
                <small>Methodist Academic System</small>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-nav">

            <div class="menu-category">Menu Utama</div>
            <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="{{ Route::has('admin.monitoring.index') ? route('admin.monitoring.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                <i class="bi bi-display"></i> Monitoring Ujian
            </a>
            <a href="{{ Route::has('admin.accounts.index') ? route('admin.accounts.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Manajemen Akun
            </a>

            <div class="menu-category">Akademik</div>
            <a href="{{ Route::has('admin.classes.index') ? route('admin.classes.index') : url('/admin/classes') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i> Data Kelas
            </a>
            <a href="{{ Route::has('admin.subjects.index') ? route('admin.subjects.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i> Mata Pelajaran
            </a>
            <a href="{{ Route::has('admin.cst.index') ? route('admin.cst.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.cst.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Manajemen Kelas 
            </a>
            <a href="{{ Route::has('admin.enrollments.index') ? route('admin.enrollments.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Enrollment Siswa
            </a>

            <div class="menu-category">Data Sekolah</div>
            <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i> Data Siswa
            </a>
            <a href="{{ Route::has('admin.teachers.index') ? route('admin.teachers.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                <i class="bi bi-person"></i> Data Guru
            </a>

            <div class="menu-category">Informasi</div>
            <a href="{{ Route::has('admin.announcements.index') ? route('admin.announcements.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i>
                <span>Pengumuman</span>
            </a>
            <a href="{{ Route::has('admin.activity-logs.index') ? route('admin.activity-logs.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>

            <div class="menu-category">Pengaturan</div>
            <a href="{{ Route::has('admin.profile.index') ? route('admin.profile.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Profil Saya</span>
            </a>
            <a href="{{ Route::has('admin.recovery.index') ? route('admin.recovery.index') : '#' }}" class="nav-link {{ request()->routeIs('admin.recovery.*') ? 'active' : '' }}">
                <i class="bi bi-database-check"></i>
                <span>Pemulihan dan Cadangan</span>
            </a>
        </div>

    </aside>

    <!-- WRAPPER KONTEN UTAMA -->
    <div class="main-wrapper">

        <!-- TOPBAR -->
        <header class="topbar">
            <!-- Tombol Toggle Sidebar untuk Mobile -->
            <button class="btn btn-light d-md-none" type="button" id="sidebarToggleBtn">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="d-none d-md-block"></div>

            <!-- Dropdown Profil Kanan Atas (Klik untuk memunculkan menu Profil dan Logout) -->
            <div class="dropdown ms-auto">
                <button class="profile-dropdown-btn dropdown-toggle shadow-sm" type="button" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-white border rounded-circle d-flex align-items-center justify-content-center p-0.5" style="width: 28px; height: 28px;">
                        <img src="{{ auth()->user()?->avatar_url ?? asset('images/methodist-logo.png') }}" 
                             alt="{{ auth()->user()->name ?? 'Admin' }}" 
                             class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <span class="fw-semibold small text-dark">{{ auth()->user()->name ?? 'Administrator Sekolah' }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="adminProfileDropdown" style="min-width: 230px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <img src="{{ asset('images/methodist-logo.png') }}" alt="Logo SMAS Methodist" style="width: 20px; height: 20px; object-fit: contain;">
                            <div class="fw-bold text-dark small">{{ auth()->user()->name ?? 'Administrator Sekolah' }}</div>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">Admin Akademik &middot; SMAS Methodist</div>
                    </li>
                    <li class="p-1">
                        <a href="{{ route('admin.profile.index') }}" class="dropdown-item fw-medium rounded-2 d-flex align-items-center gap-2 py-2 {{ request()->routeIs('admin.profile.*') ? 'active bg-primary text-white' : 'text-dark' }}">
                            <i class="bi bi-person-gear {{ request()->routeIs('admin.profile.*') ? 'text-white' : 'text-primary' }}"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li class="p-1">
                        <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-medium rounded-2 d-flex align-items-center gap-2 py-2">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <!-- KONTEN DINAMIS DARI DASHBOARD (yield) -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-top text-center py-3 mt-auto">
            <small class="text-muted">&copy; {{ date('Y') }} Methodist Academic Learning. All rights reserved.</small>
        </footer>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Toggle Sidebar untuk Mobile -->
    <script>
        document.getElementById('sidebarToggleBtn').addEventListener('click', function() {
            document.getElementById('sidebarMenu').classList.toggle('show');
        });
    </script>

    <!-- Tambahan JS per Halaman -->
    @stack('scripts')

    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>