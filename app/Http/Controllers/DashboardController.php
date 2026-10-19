<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamViolation;
use App\Models\ForumThread;
use App\Models\Guardian;
use App\Models\ParentMessage;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $announcements = Announcement::with('creator')->latest()->take(30)->get()
            ->filter(fn ($a) => in_array($user->role, $a->target_roles ?? [], true))->take(5);

        $data = match ($user->role) {
            'admin' => [
                'stats' => [
                    'Siswa' => Student::count(), 'Guru' => Teacher::count(), 'Orang Tua' => Guardian::count(),
                    'Kelas' => SchoolClass::count(), 'Mata Pelajaran' => Subject::count(),
                    'Ujian Aktif' => Exam::where('is_published', true)->count(),
                ],
                'violations' => ExamViolation::with('session.student.user', 'session.exam')->latest()->take(8)->get(),
                'openMessages' => ParentMessage::whereNull('teacher_user_id')->whereNull('reply')->count(),
            ],
            'guru' => $this->guru($user),
            'siswa' => $this->siswa($user),
            'orangtua' => $this->orangtua($user),
        };

        return view('dashboard.'.$user->role, $data + compact('announcements'));
    }

    private function guru($user): array
    {
        $nip = $user->teacher->nip;
        $csts = ClassSubjectTeacher::where('teacher_id', $nip)->pluck('cst_id');

        return [
            'classCount' => $csts->count(),
            'pendingSubs' => AssignmentSubmission::whereNull('score')->whereNotNull('submitted_at')
                ->whereIn('assignment_id', Assignment::whereIn('cst_id', $csts)->pluck('assignment_id'))->count(),
            'essayPending' => ExamSession::where('status', 'selesai')->whereHas('exam', fn ($q) => $q->whereIn('cst_id', $csts))->count(),
            'unansweredThreads' => ForumThread::whereIn('cst_id', $csts)->doesntHave('replies')->count(),
            'unreadMessages' => ParentMessage::where('teacher_user_id', $user->user_id)->whereNull('reply')->count(),
            'upcomingExams' => Exam::with('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass')
                ->whereIn('cst_id', $csts)->where('end_time', '>=', now())->orderBy('start_time')->take(5)->get(),
            'homeroom' => SchoolClass::where('homeroom_teacher_id', $nip)->get(),
        ];
    }

    private function siswa($user): array
    {
        $student = $user->student;
        $cstIds = $student->joinedClasses()->pluck('class_subject_teacher.cst_id');

        return [
            'student' => $student->load('schoolClass'),
            'joined' => $student->joinedClasses()->with('subject')->get(),
            'upcomingExams' => Exam::with('classSubjectTeacher.subject')->whereIn('cst_id', $cstIds)
                ->where('is_published', true)->where('end_time', '>=', now())->orderBy('start_time')->take(5)->get(),
            'dueAssignments' => Assignment::with('classSubjectTeacher.subject')->whereIn('cst_id', $cstIds)
                ->where('due_date', '>=', now())
                ->whereNotIn('assignment_id', AssignmentSubmission::where('students_id', $student->nis)->pluck('assignment_id'))
                ->orderBy('due_date')->take(5)->get(),
        ];
    }

    private function orangtua($user): array
    {
        return ['children' => $user->guardianProfile->students()->with('user', 'schoolClass')->get()];
    }
}
