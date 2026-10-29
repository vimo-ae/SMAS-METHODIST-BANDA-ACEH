<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
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
    
    // Route Siswa
    Route::get('/siswa/dashboard', [SiswaDashboard::class, 'index'])
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

    // Route Guru
    Route::get('/guru/dashboard', [GuruDashboard::class, 'index'])->name('guru.dashboard');
    Route::get('/guru/e-class', [GuruClassroom::class, 'index'])->name('guru.classes.index');
    Route::get('/guru/e-exam', [GuruExam::class, 'index'])->name('guru.exams.index');

    // Route Admin
    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
