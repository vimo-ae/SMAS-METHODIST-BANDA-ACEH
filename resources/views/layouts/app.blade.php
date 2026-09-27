<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'E-Learning') - SMA Methodist Banda Aceh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">SMAM E-Learning</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('materials.index') }}">Materi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('assignments.index') }}">Tugas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('exams.index') }}">Ujian</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('attendances.index') }}">Absensi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('grades.index') }}">Nilai</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('announcements.index') }}">Pengumuman</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('forum.index') }}">Forum</a></li>
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Data Master</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.classes.index') }}">Kelas</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.subjects.index') }}">Mapel</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.teachers.index') }}">Guru</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.students.index') }}">Siswa</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.guardians.index') }}">Orang Tua</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
                <span class="navbar-text text-white me-3">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Keluar</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<div class="container my-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>
</body>
</html>
