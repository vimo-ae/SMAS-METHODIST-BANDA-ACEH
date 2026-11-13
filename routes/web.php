<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Guru\ClassroomController as GuruClassroom;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\ExamController as GuruExam;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Siswa\ClassroomController as SiswaClassroom;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\ExamController as SiswaExam;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
    })->middleware(['role.redirect'])->name('dashboard');
   
    // Route Siswa
    Route::get('/siswa', [SiswaDashboard::class, 'index'])
        ->name('siswa.dashboard');

    Route::get('/siswa/e-class', [SiswaClassroom::class, 'index'])
        ->name('siswa.classes.index');

    Route::get('/siswa/e-exam', [SiswaExam::class, 'index'])
        ->name('siswa.exams.index');

    // Halaman informasi dan tata tertib ujian
    Route::get('/siswa/e-exam/{exam}', [SiswaExam::class, 'show'])
        ->name('siswa.exams.show');

    // Memulai ujian
    Route::post('/siswa/e-exam/{exam}/start', [SiswaExam::class, 'start'])
        ->name('siswa.exams.start');

    // Halaman pengerjaan ujian
    Route::get('/siswa/e-exam/{exam}/take', [SiswaExam::class, 'take'])
        ->name('siswa.exams.take');

    // Aksi saat mengerjakan ujian
    Route::post('/siswa/e-exam/{exam}/answer', [SiswaExam::class, 'answer'])
        ->name('siswa.exams.answer');

    Route::post('/siswa/e-exam/{exam}/essay/{question}', [SiswaExam::class, 'essay'])
        ->name('siswa.exams.essay');

    Route::post('/siswa/e-exam/{exam}/violation', [SiswaExam::class, 'violation'])
        ->name('siswa.exams.violation');

    Route::post('/siswa/e-exam/{exam}/leave', [SiswaExam::class, 'leave'])
        ->name('siswa.exams.leave');

    Route::post('/siswa/e-exam/{exam}/finish', [SiswaExam::class, 'finish'])
        ->name('siswa.exams.finish');

    Route::get('/guru', [GuruDashboard::class, 'index'])->name('guru.dashboard');
    Route::get('/guru/e-class', [GuruClassroom::class, 'index'])->name('guru.classes.index');
    Route::get('/guru/e-class/{cst}', [GuruClassroom::class, 'show'])->name('guru.classes.show');
    Route::get('/guru/e-exam', [GuruExam::class, 'index'])->name('guru.exams.index');

        Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])
            ->name('admin.dashboard');
    });
    
    // Route Admin khusus Announcement 
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengumuman', [AnnouncementController::class, 'index'])
            ->name('announcements.index');
    
        Route::get('/pengumuman/create', [AnnouncementController::class, 'create'])
            ->name('announcements.create');
    
        Route::post('/pengumuman', [AnnouncementController::class, 'store'])
            ->name('announcements.store');
    
        Route::get('/pengumuman/{announcement}', [AnnouncementController::class, 'show'])
            ->name('announcements.show');
    
        Route::get('/pengumuman/{announcement}/edit', [AnnouncementController::class, 'edit'])
            ->name('announcements.edit');
    
        Route::put('/pengumuman/{announcement}', [AnnouncementController::class, 'update'])
            ->name('announcements.update');
    
        Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])
            ->name('announcements.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

require __DIR__.'/auth.php';