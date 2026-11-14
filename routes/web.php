<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ClassEnrollmentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CstController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Guru\ClassroomController as GuruClassroom;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\ExamController as GuruExam;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Siswa\ClassroomController as SiswaClassroom;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\ExamController as SiswaExam;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/pengumuman/{announcement:slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

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
    Route::get('/guru/e-exam', [GuruExam::class, 'index'])->name('guru.exams.index');
    Route::get('/e-exam/create', [GuruExam::class, 'create'])->name('guru.exams.create');
    Route::post('/e-exam', [GuruExam::class, 'store'])->name('guru.exams.store');
    
    Route::get('/e-exam/{exam}', [GuruExam::class, 'show'])->name('guru.exams.show');
    Route::get('/e-exam/{exam}/edit', [GuruExam::class, 'edit'])->name('guru.exams.edit');
    Route::put('/e-exam/{exam}', [GuruExam::class, 'update'])->name('guru.exams.update');
    Route::delete('/e-exam/{exam}', [GuruExam::class, 'destroy'])->name('guru.exams.destroy');
    Route::patch('/e-exam/{exam}/toggle-publish', [GuruExam::class, 'togglePublish'])->name('guru.exams.toggle-publish');

    Route::post('/e-exam/{exam}/questions', [GuruExam::class, 'storeQuestion'])->name('guru.exams.questions.store');
    Route::put('/guru/e-exam/{exam}/questions/{question}', [GuruExam::class, 'updateQuestion'])->name('guru.exams.questions.update');
    Route::patch('/guru/e-exam/{exam}/questions/{question}/score', [GuruExam::class, 'updateScore'])->name('guru.exams.questions.update-score');
    Route::delete('/e-exam/{exam}/questions/{question}', [GuruExam::class, 'destroyQuestion'])->name('guru.exams.questions.destroy');

    Route::get('/e-exam/{exam}/results', [GuruExam::class, 'results'])->name('guru.exams.results');
    Route::get('/e-exam/session/{session}', [GuruExam::class, 'session'])->name('guru.exams.session');
    Route::patch('/guru/e-exam/session/{session}/grade-essay', [GuruExam::class, 'gradeEssay'])->name('guru.exams.grade-essay');
    });
    
    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])
        ->name('admin.dashboard');

    //Admin untuk monitoring
    Route::prefix('admin/monitoring')->name('admin.monitoring.')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('index');
    Route::get('/session/{id}', [MonitoringController::class, 'show'])->name('show');
    });

    //Admin untuk account management
    Route::prefix('admin/accounts')->name('admin.accounts.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/create', [AccountController::class, 'create'])->name('create');
    Route::post('/', [AccountController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AccountController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AccountController::class, 'update'])->name('update');
    Route::delete('/{id}', [AccountController::class, 'destroy'])->name('destroy');
    });

    //Admin untuk subject management
    Route::prefix('admin/subjects')->name('admin.subjects.')->group(function () {
    Route::get('/', [SubjectController::class, 'index'])->name('index');
    Route::get('/create', [SubjectController::class, 'create'])->name('create');
    Route::post('/', [SubjectController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
    Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    //Admin untuk class subject teacher management
    Route::prefix('admin/cst')->name('admin.cst.')->group(function () {
    Route::get('/', [CstController::class, 'index'])->name('index');
    Route::get('/create', [CstController::class, 'create'])->name('create');
    Route::post('/', [CstController::class, 'store'])->name('store');
    Route::get('/{cst}/edit', [CstController::class, 'edit'])->name('edit');
    Route::put('/{cst}', [CstController::class, 'update'])->name('update');
    Route::delete('/{cst}', [CstController::class, 'destroy'])->name('destroy');
    });

    //Admin untuk class enrollment management
    Route::prefix('admin/enrollments')->name('admin.enrollments.')->group(function () {
    Route::get('/', [ClassEnrollmentController::class, 'index'])->name('index');
    Route::get('/{student}/show', [ClassEnrollmentController::class, 'show'])->name('show'); // <-- Tambahan rute show
    Route::get('/create', [ClassEnrollmentController::class, 'create'])->name('create');
    Route::post('/', [ClassEnrollmentController::class, 'store'])->name('store');
    Route::get('/{enrollment}/edit', [ClassEnrollmentController::class, 'edit'])->name('edit');
    Route::put('/{enrollment}', [ClassEnrollmentController::class, 'update'])->name('update');
    Route::delete('/{enrollment}', [ClassEnrollmentController::class, 'destroy'])->name('destroy');
    });

    // Menambah enrollment langsung dari halaman siswa
    Route::post('admin/students/{student}/enrollments', [ClassEnrollmentController::class, 'storeFromStudent'])
        ->name('admin.students.enrollments.store');

    // Admin untuk student management
    Route::prefix('admin/students')->name('admin.students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::get('/create', [StudentController::class, 'create'])->name('create');
    Route::post('/', [StudentController::class, 'store'])->name('store');
    Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
    Route::put('/{student}', [StudentController::class, 'update'])->name('update');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    Route::get('/{student}', [StudentController::class, 'show'])->name('show');
    });

    // Admin untuk teacher management
    Route::prefix('admin/teachers')->name('admin.teachers.')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('index');
    Route::get('/create', [TeacherController::class, 'create'])->name('create');
    Route::post('/', [TeacherController::class, 'store'])->name('store');
    Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->name('edit');
    Route::put('/{teacher}', [TeacherController::class, 'update'])->name('update');
    Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/announcements')->name('admin.announcements.group.')->group(function () { // atau gunakan name prefix admin.announcements
    });

    // Admin untuk announcement
    Route::prefix('admin/announcements')->name('admin.announcements.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
    Route::post('/', [AnnouncementController::class, 'store'])->name('store');
    Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show');
    Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
    Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
    Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

require __DIR__.'/auth.php';