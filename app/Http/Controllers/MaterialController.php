<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Material::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject', 'classSubjectTeacher.teacher.user');

        if ($user->role === 'guru') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $user->teacher->nip));
        } elseif ($user->role === 'siswa') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('class_id', $user->student->class_id));
        }

        $materials = $query->latest()->paginate(15);

        return view('materials.index', compact('materials'));
    }
}
