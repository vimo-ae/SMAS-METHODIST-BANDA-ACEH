<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Grade::with('student.user', 'subject');

        if ($user->role === 'siswa') {
            $query->where('students_id', $user->student->nis);
        } elseif ($user->role === 'orangtua') {
            $studentIds = $user->guardianProfile->students()->pluck('students.nis');
            $query->whereIn('students_id', $studentIds);
        } elseif ($user->role === 'guru') {
            // guru melihat nilai yang ia input untuk mapel yang diampu
            $subjectIds = $user->teacher->classSubjectTeachers()->pluck('subject_code');
            $query->whereIn('subject_code', $subjectIds);
        }

        $grades = $query->latest()->paginate(20);

        return view('grades.index', compact('grades'));
    }
}
