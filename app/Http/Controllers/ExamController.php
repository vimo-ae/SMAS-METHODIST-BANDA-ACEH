<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Exam::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject');

        if ($user->role === 'guru') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $user->teacher->nip));
        } elseif ($user->role === 'siswa') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('class_id', $user->student->class_id));
        }

        $exams = $query->latest()->paginate(15);

        return view('exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $exam->load('questions', 'classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject');
        return view('exams.show', compact('exam'));
    }
}
