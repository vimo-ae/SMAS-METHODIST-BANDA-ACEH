<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Panel') - MALAS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS Styles -->
    <style>
        :root {
            --sidebar-bg: #1c233d; /* Warna background dark navy sesuai gambar */
            --sidebar-text: #94a3b8; /* Warna teks abu kebiruan */
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            --sidebar-active-bg: #2563eb; /* Warna biru cerah untuk menu aktif */
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
            background-color: var(--sidebar-active-bg);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
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

        /* Kategori Menu (MENU UTAMA, AKADEMIK, dll) */
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

        /* Sidebar Footer (Profil User) */
        .sidebar-footer {
            padding: 15px 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
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
        .btn-logout {
            color: var(--sidebar-text);
            background: none;
            border: none;
            font-size: 1.3rem;
            transition: 0.2s;
            padding: 0;
        }
        .btn-logout:hover {
            color: white;
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            margin-left: 270px; /* Lebar sidebar */
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
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebarMenu">
        
        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-mortarboard"></i>
            </div>
            <div class="sidebar-brand-text">
                <h5 class="fw-bold text-white mb-0">MALAS</h5>
                <small>Methodist Academic Learning</small>
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
            <li class="nav-item">
                <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Pengumuman</span>
                </a>
            </li>     
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <div class="text-white fw-bold" style="font-size: 0.85rem;">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div style="color: #94a3b8; font-size: 0.75rem;">Administrator</div>
                </div>
            </div>
            
            <!-- Tombol Logout Bawah -->
            <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-logout" title="Keluar Sistem">
                    <i class="bi bi-arrow-down-circle"></i>
                </button>
            </form>
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
            
            <div class="d-none d-md-block">
                <!-- Breadcrumb atau judul halaman bisa diletakkan di sini jika perlu -->
            </div>

            <!-- User Menu Kanan Atas -->
            <div class="dropdown">
                <button class="btn btn-light bg-white border-0 shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-person-fill small"></i>
                    </div>
                    <span class="fw-semibold small text-dark">{{ auth()->user()->name ?? 'Administrator' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2 text-muted"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2 text-muted"></i> Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
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
            <small class="text-muted">&copy; {{ date('Y') }} MALAS - Methodist Academic Learning. All rights reserved.</small>
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

</body>
</html>