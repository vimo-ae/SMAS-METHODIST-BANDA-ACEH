<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class CekNilaiController extends Controller
{
    /** Cari anak berdasarkan nama. */
    public function index(Request $request)
    {
        $students = collect();

        if ($request->filled('name')) {
            $request->validate(['name' => 'string|min:3|max:100']);

            $students = Student::with('user', 'schoolClass')
                ->where('status', 'aktif')
                ->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->name.'%'))
                ->limit(20)->get();
        }

        return view('cek-nilai.index', compact('students'));
    }

    public function form(Student $student)
    {
        $student->load('user', 'schoolClass');

        return view('cek-nilai.verify', compact('student'));
    }

    /** Verifikasi tanggal lahir, lalu tampilkan nilai. */
    public function show(Request $request, Student $student)
    {
        $request->validate(['dob' => 'required|date']);

        if (config('school.guest_check.require_dob', true)
            && ! $student->dob?->isSameDay($request->dob)) {
            return back()->withErrors(['dob' => 'Tanggal lahir tidak cocok.']);
        }

        $student->load('user', 'schoolClass');

        $grades = Grade::where('students_id', $student->nis)
            ->with('classSubjectTeacher.subject', 'assignment', 'exam')
            ->get()
            ->groupBy('cst_id');

        return view('cek-nilai.show', compact('student', 'grades'));
    }
}