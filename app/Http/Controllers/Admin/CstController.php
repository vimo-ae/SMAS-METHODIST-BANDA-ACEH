<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Pengampu mata pelajaran: guru ↔ mapel ↔ kelas. */
class CstController extends Controller
{
    public function index(Request $request)
    {
        $items = ClassSubjectTeacher::with('subject', 'schoolClass', 'teacher.user')
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->orderBy('class_id')->paginate(20)->withQueryString();

        return view('admin.cst.index', [
            'items' => $items,
            'classes' => SchoolClass::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'subject_code' => 'required|exists:subjects,code',
            'teacher_id' => 'required|exists:teachers,nip',
            'semester' => 'required|in:ganjil,genap',
        ]);
        $class = SchoolClass::findOrFail($v['class_id']);
        $id = $v['class_id'].'-'.$v['subject_code'].'-'.($v['semester'] === 'ganjil' ? 'G' : 'E');

        if (ClassSubjectTeacher::where('cst_id', $id)->exists()) {
            return back()->with('error', 'Mata pelajaran ini sudah ditetapkan untuk kelas & semester tersebut.');
        }
        ClassSubjectTeacher::create($v + [
            'cst_id' => $id, 'academic_year' => $class->academic_year, 'join_code' => strtoupper(Str::random(6)),
        ]);

        return back()->with('success', 'Pengampu ditetapkan.');
    }

    public function destroy(ClassSubjectTeacher $cst)
    {
        $cst->delete();

        return back()->with('success', 'Pengampu dihapus.');
    }
}
