<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\ExamViolation;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'Total Siswa' => Student::count(),
            'Total Guru' => Teacher::count(),
            'Total Kelas' => SchoolClass::count(),
            'Mata Pelajaran' => Subject::count(),
            'Total Ujian' => Exam::count(),
            'Pengumuman' => Announcement::count(),
        ];

        $sessionsRunning = ExamSession::where('status', 'berlangsung')
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->count();

        $pendingEssays = ExamAnswer::whereNull('score')
            ->whereHas('question', function ($query) {
                $query->where('question_type', 'esai');
            })
            ->count();

        $studentsNoClass = Student::whereNull('class_id')->count();

        $recentAnnouncements = Announcement::latest()
            ->take(5)
            ->get();

        $violations = ExamViolation::with([
            'student.user',
            'session.exam'
        ])
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get();

        $classes = SchoolClass::withCount('students')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'sessionsRunning',
            'pendingEssays',
            'studentsNoClass',
            'recentAnnouncements',
            'violations',
            'classes'
        ));
    }
}