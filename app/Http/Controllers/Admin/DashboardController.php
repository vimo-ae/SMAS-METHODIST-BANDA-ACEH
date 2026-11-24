<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamViolation;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $conn = DB::connection('mysql_admin');
        $dbStats = $conn->select('CALL sp_dashboard_stats()')[0];

        $stats = [
            'Total Siswa'    => $dbStats->total_students ?? 0,
            'Total Guru'     => $dbStats->total_teachers ?? 0,
            'Total Kelas'    => $dbStats->total_classes ?? 0,
            'Mata Pelajaran' => $dbStats->total_subjects ?? 0,
            'Total Ujian'    => $dbStats->published_exams ?? $dbStats->total_exams ?? 0,
            'Pengumuman'     => $dbStats->total_announcements ?? 0,
        ];

        $activeSessions = \App\Models\ExamSession::on('mysql_admin')
            ->with([
                'exam.classSubjectTeacher.schoolClass',
                'exam.classSubjectTeacher.subject'
            ])
            ->where('status', 'berlangsung')
            ->latest('started_at')
            ->take(5)
            ->get();

        $examMonitors = $conn->table('exams as e')
            ->join('class_subject_teacher as cst', 'cst.cst_id', '=', 'e.cst_id')
            ->join('classes as c', 'c.class_id', '=', 'cst.class_id')
            ->leftJoin('subjects as s', 's.code', '=', 'cst.subject_code')
            ->leftJoin('exam_sessions as es', 'es.exam_id', '=', 'e.exam_id')
            ->select(
                'e.exam_id',
                'e.title',
                'e.exam_type',
                'e.start_time',
                'e.end_time',
                'e.is_published',
                'c.name as class_name',
                's.name as subject_name'
            )
            ->selectRaw('COUNT(DISTINCT es.exam_session_id) AS participants_count')
            ->selectRaw("SUM(CASE WHEN es.status = 'berlangsung' THEN 1 ELSE 0 END) AS running_count")
            ->selectRaw("SUM(CASE WHEN es.status IN ('selesai', 'keluar') THEN 1 ELSE 0 END) AS finished_count")
            ->where('e.is_published', true)
            ->groupBy(
                'e.exam_id',
                'e.title',
                'e.exam_type',
                'e.start_time',
                'e.end_time',
                'e.is_published',
                'c.name',
                's.name'
            )
            ->orderByDesc('e.start_time')
            ->orderByDesc('e.exam_id')
            ->take(5)
            ->get();

        $sessionsRunning = $dbStats->active_sessions ?? $dbStats->sessions_running ?? 0;

        $pendingEssays = $conn->table('v_essay_pending')->count();

        $studentsNoClass = $conn->table('students')->whereNull('class_id')->count();

        $recentAnnouncements = Announcement::on('mysql_admin')
            ->latest()
            ->take(5)
            ->get();

        $violations = ExamViolation::on('mysql_admin')
            ->with([
                'student.user',
                'session.exam'
            ])
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get();

        $classColumns = Schema::connection('mysql_admin')->getColumnListing('classes');
        $teacherCol = in_array('homeroom_teacher_id', $classColumns)
            ? 'k.homeroom_teacher_id'
            : (in_array('teacher_id', $classColumns) ? 'k.teacher_id' : null);

        $classQuery = $conn->table('classes as k')
            ->leftJoin('students as s', 's.class_id', '=', 'k.class_id');

        if ($teacherCol) {
            $classes = $classQuery
                ->leftJoin('users as tu', 'tu.user_id', '=', $teacherCol)
                ->select(
                    'k.class_id',
                    'k.name as class_name',
                    'k.academic_year',
                    DB::raw("{$teacherCol} as homeroom_teacher_nip"),
                    'tu.name as homeroom_teacher_name',
                    DB::raw('COUNT(DISTINCT s.nis) as total_students')
                )
                ->groupBy('k.class_id', 'k.name', 'k.academic_year', $teacherCol, 'tu.name')
                ->orderBy('k.name')
                ->take(5)
                ->get();
        } else {
            $classes = $classQuery
                ->select(
                    'k.class_id',
                    'k.name as class_name',
                    'k.academic_year',
                    DB::raw('NULL as homeroom_teacher_nip'),
                    DB::raw('NULL as homeroom_teacher_name'),
                    DB::raw('COUNT(DISTINCT s.nis) as total_students')
                )
                ->groupBy('k.class_id', 'k.name', 'k.academic_year')
                ->orderBy('k.name')
                ->take(5)
                ->get();
        }

        // Ambil 5 log nilai terbaru hasil trigger trg_grades_ai/au/ad
        $recentGradeLogs = $conn->table('grade_logs as gl')
            ->leftJoin('users as u', 'u.user_id', '=', 'gl.students_id')
            ->select('gl.*', 'u.name as student_name')
            ->orderByDesc('gl.changed_at')
            ->orderByDesc('gl.log_id')
            ->take(5)
            ->get();

        // Optimalisasi query: Ambil 5 riwayat aktivitas lintas tabel dari view log
        $recentActivityLogs = ActivityLogController::recentAcrossTables(5);

        return view('admin.dashboard', compact(
            'stats',
            'sessionsRunning',
            'activeSessions',
            'examMonitors',
            'pendingEssays',
            'studentsNoClass',
            'recentAnnouncements',
            'violations',
            'classes',
            'recentGradeLogs',
            'recentActivityLogs'
        ));
    }
}