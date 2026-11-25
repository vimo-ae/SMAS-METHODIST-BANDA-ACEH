<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Exam;
use App\Services\RosterService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student ? $user->student->load('schoolClass.homeroomTeacher.user') : null;

        $joinedClasses = collect();
        $pendingAssignments = collect();
        $upcomingExams = collect();
        $submittedCount = 0;
        $totalJoined = 0;

        if ($student) {
            // Rombel kelas/mata pelajaran yang diikuti siswa
            $joinedClasses = $student->joinedClasses()
                ->with(['subject', 'teacher.user', 'schoolClass'])
                ->withCount([
                    'materials',
                    'assignments',
                    'exams' => function ($q) {
                        $q->where('is_published', true);
                    }
                ])
                ->get();

            $totalJoined = $joinedClasses->count();
            $cstIds = $joinedClasses->pluck('cst_id');

            // ID tugas yang sudah diserahkan siswa
            $submittedAssignmentIds = $student->submissions()->pluck('assignment_id')->toArray();
            $submittedCount = count($submittedAssignmentIds);

            if ($cstIds->isNotEmpty()) {
                // Tugas aktif yang belum dikumpulkan
                $pendingAssignments = Assignment::whereIn('cst_id', $cstIds)
                    ->whereNotIn('assignment_id', $submittedAssignmentIds)
                    ->where('due_date', '>=', now()->subDays(1))
                    ->with(['classSubjectTeacher.subject', 'classSubjectTeacher.teacher.user'])
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
                    ->get();

                // Ujian yang aktif atau akan datang
                $upcomingExams = Exam::whereIn('cst_id', $cstIds)
                    ->where('is_published', true)
                    ->where('end_time', '>=', now())
                    ->with(['classSubjectTeacher.subject'])
                    ->orderBy('start_time', 'asc')
                    ->limit(4)
                    ->get();
            }
        }

        // Pengumuman sekolah terbaru
        $announcements = Announcement::query()
            ->where(function ($q) {
                $q->where('is_public', true)
                  ->orWhere('target_roles', 'like', '%siswa%');
            })
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Jadwal pelajaran hari ini berdasarkan rombel siswa
        $className = $student?->schoolClass?->name ?? 'X-A';
        $todaySchedule = RosterService::getTodayScheduleForClass($className);
        $todayGroupedSchedule = RosterService::groupConsecutiveSlots($todaySchedule['items'] ?? []);

        return view('siswa.dashboard', compact(
            'user',
            'student',
            'joinedClasses',
            'totalJoined',
            'submittedCount',
            'pendingAssignments',
            'upcomingExams',
            'announcements',
            'todaySchedule',
            'todayGroupedSchedule'
        ));
    }
}
