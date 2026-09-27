<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Assignment::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject');

        if ($user->role === 'guru') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $user->teacher->nip));
        } elseif ($user->role === 'siswa') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('class_id', $user->student->class_id));
        }

        $assignments = $query->latest()->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment, Request $request)
    {
        $user = $request->user();
        $assignment->load('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject', 'submissions.student.user');

        $mySubmission = null;
        if ($user->role === 'siswa') {
            $mySubmission = $assignment->submissions()->where('students_id', $user->student->nis)->first();
        }

        return view('assignments.show', compact('assignment', 'mySubmission'));
    }
}
