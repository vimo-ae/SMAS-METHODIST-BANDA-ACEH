<?php

use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// ── Rute untuk SEMUA role yang sudah login ────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/materi', [MaterialController::class, 'index'])->name('materials.index');

    Route::get('/tugas', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/tugas/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

    Route::get('/ujian', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/ujian/{exam}', [ExamController::class, 'show'])->name('exams.show');

    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/absensi', [AttendanceController::class, 'store'])->middleware('role:guru')->name('attendances.store');
    Route::get('/nilai', [GradeController::class, 'index'])->name('grades.index');

    Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/pengumuman', [AnnouncementController::class, 'store'])
        ->middleware('role:superadmin,admin')
        ->name('announcements.store');

    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/{thread}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{thread}/balas', [ForumController::class, 'reply'])->name('forum.reply');
});

// ── Rute KHUSUS Admin & Super Admin (data master) ─
Route::middleware(['auth', 'role:superadmin,admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('classes', SchoolClassController::class)->parameters(['classes' => 'class']);
        Route::resource('subjects', SubjectController::class);
        Route::resource('teachers', TeacherController::class)->except(['show']);
        Route::resource('students', StudentController::class)->except(['show']);
        Route::resource('guardians', GuardianController::class)->except(['show']);
    });

require __DIR__.'/auth.php';
