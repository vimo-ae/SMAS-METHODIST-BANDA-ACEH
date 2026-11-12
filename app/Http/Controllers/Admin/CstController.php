<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CstController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $csts = ClassSubjectTeacher::with(['schoolClass', 'subject', 'teacher.user'])
            ->when($search, function ($query, $search) {
                $query->whereHas('subject', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('schoolClass', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('teacher.user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhere('join_code', 'like', "%{$search}%");
            })
            ->latest('cst_id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.cst.index', compact('csts', 'search'));
    }

    public function create()
    {
        $rooms = SchoolClass::orderBy('name')->get(); 
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();

        return view('admin.cst.create', compact('rooms', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'subject_code' => 'required|exists:subjects,code',
            'teacher_id' => 'required|exists:teachers,nip',
            'academic_year' => 'required|string|max:9',
        ]);

        $exists = ClassSubjectTeacher::where('class_id', $v['class_id'])
            ->where('subject_code', $v['subject_code'])
            ->where('academic_year', $v['academic_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kelas mata pelajaran ini sudah ada di ruang dan tahun ajaran tersebut.');
        }

        $v['cst_id'] = 'CST-' . strtoupper(Str::random(6));
        $v['join_code'] = strtoupper(Str::random(6));

        ClassSubjectTeacher::create($v);

        return redirect()->route('admin.cst.index')
            ->with('success', 'Kelas mapel berhasil dibuat! Kode Gabung Siswa: ' . $v['join_code']);
    }

    public function edit(ClassSubjectTeacher $cst)
    {
        $rooms = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();

        return view('admin.cst.edit', compact('cst', 'rooms', 'subjects', 'teachers'));
    }

    public function update(Request $request, ClassSubjectTeacher $cst)
    {
        $v = $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'subject_code' => 'required|exists:subjects,code',
            'teacher_id' => 'required|exists:teachers,nip',
            'academic_year' => 'required|string|max:9',
        ]);

        $exists = ClassSubjectTeacher::where('class_id', $v['class_id'])
            ->where('subject_code', $v['subject_code'])
            ->where('academic_year', $v['academic_year'])
            ->where('cst_id', '!=', $cst->cst_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Perubahan dibatalkan karena akan menduplikat kelas yang sudah ada.');
        }

        $cst->update($v);

        return redirect()->route('admin.cst.index')
            ->with('success', 'Data kelas mapel berhasil diperbarui.');
    }

    public function destroy(ClassSubjectTeacher $cst)
    {
        try {
            $cst->delete();
            return redirect()->route('admin.cst.index')->with('success', 'Kelas mapel berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus! Kelas ini sudah memiliki siswa terdaftar (Enrollment).');
        }
    }
}