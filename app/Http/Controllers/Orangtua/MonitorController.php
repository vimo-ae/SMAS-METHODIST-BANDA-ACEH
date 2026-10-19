<?php

namespace App\Http\Controllers\Orangtua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Guru\HomeroomController;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    private function child(Request $request, Student $student): Student
    {
        abort_unless($request->user()->guardianProfile->students()->where('students.nis', $student->nis)->exists(), 403);

        return $student->load('user', 'schoolClass.homeroomTeacher.user');
    }

    public function show(Request $request, Student $student, ReportCardService $svc)
    {
        $student = $this->child($request, $student);
        $semester = $request->input('semester', 'ganjil');
        $report = $svc->build($student, $semester, $student->schoolClass->academic_year);
        $sessions = $student->examSessions()->with('exam.classSubjectTeacher.subject')->withCount('violations')->latest()->get();
        $cstIds = $student->joinedClasses()->pluck('class_subject_teacher.cst_id');
        $assignments = Assignment::with('classSubjectTeacher.subject')->whereIn('cst_id', $cstIds)->orderByDesc('due_date')->take(15)->get();
        $subs = AssignmentSubmission::where('students_id', $student->nis)->get()->keyBy('assignment_id');

        return view('orangtua.child', compact('student', 'report', 'semester', 'sessions', 'assignments', 'subs'));
    }

    public function report(Request $request, Student $student, ReportCardService $svc)
    {
        $student = $this->child($request, $student);

        return view('report.print', app(HomeroomController::class)->reportData($student, $request->input('semester', 'ganjil'), $svc) + ['canNote' => false]);
    }
}
