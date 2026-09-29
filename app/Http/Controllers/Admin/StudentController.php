<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user', 'schoolClass')
            ->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100',

            'email' => 'required|email|unique:users,email',

            'nis' => 'required|string|max:30|unique:students,nis',

            'class_id' => [
                'nullable',
                'exists:classes,class_id'
            ],

            'gender' => 'required|in:L,P',

            'birth_date' => 'nullable|date',

            'address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($v) {

            User::create([
                'name' => $v['name'],
                'email' => $v['email'],
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'academic_key' => $v['nis'],
            ]);

            Student::create([
                'nis' => $v['nis'],
                'class_id' => $v['class_id'] ?? null,
                'gender' => $v['gender'],
                'birth_date' => $v['birth_date'] ?? null,
                'address' => $v['address'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Siswa berhasil ditambahkan. Password default: password123'
            );
    }

    public function edit(Student $student)
    {
        $student->load('user');

        $classes = SchoolClass::orderBy('name')->get();

        return view(
            'admin.students.edit',
            compact('student', 'classes')
        );
    }

    public function update(Request $request, Student $student)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                'unique:users,email,' . $student->user?->id,
            ],

            'nis' => [
                'required',
                'string',
                'max:30',
                'unique:students,nis,' . $student->nis . ',nis',
            ],

            'class_id' => [
                'nullable',
                'exists:classes,class_id'
            ],

            'gender' => 'required|in:L,P',

            'birth_date' => 'nullable|date',

            'address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($v, $student) {

            $student->update([
                'nis' => $v['nis'],
                'class_id' => $v['class_id'] ?? null,
                'gender' => $v['gender'],
                'birth_date' => $v['birth_date'] ?? null,
                'address' => $v['address'] ?? null,
            ]);

            $student->user()->update([
                'name' => $v['name'],
                'email' => $v['email'],
                'academic_key' => $v['nis'],
            ]);
        });

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Data siswa berhasil diperbarui.'
            );
    }

    public function destroy(Student $student)
    {
        $student->user()?->delete();

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Siswa berhasil dihapus.'
            );
    }
}