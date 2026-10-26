<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

            'nisn' => 'nullable|digits:10|unique:students,nisn',

            'class_id' => [
                'nullable',
                'exists:classes,class_id'
            ],

            'gender' => 'required|in:L,P',

            'pob' => 'nullable|string|max:100',

            'dob' => 'nullable|date',

            'address' => 'nullable|string',

            'district' => 'nullable|string|max:100',

            'city' => 'nullable|string|max:100',

            'province' => 'nullable|string|max:100',

            'parent_name' => 'nullable|string|max:100',

            'parent_phone' => 'nullable|string|max:20',

            'entry_year' => 'nullable|digits:4',

            'status' => ['nullable', Rule::in(['aktif', 'lulus', 'pindah', 'drop_out'])],
        ]);

        DB::transaction(function () use ($v) {

            User::create([
                'user_id' => $v['nis'],
                'name' => $v['name'],
                'email' => $v['email'],
                'password' => Hash::make('password123'),
                'role' => 'siswa',
            ]);

            Student::create([
                'nis' => $v['nis'],
                'nisn' => $v['nisn'] ?? null,
                'class_id' => $v['class_id'] ?? null,
                'gender' => $v['gender'],
                'pob' => $v['pob'] ?? null,
                'dob' => $v['dob'] ?? null,
                'address' => $v['address'] ?? null,
                'district' => $v['district'] ?? null,
                'city' => $v['city'] ?? null,
                'province' => $v['province'] ?? null,
                'parent_name' => $v['parent_name'] ?? null,
                'parent_phone' => $v['parent_phone'] ?? null,
                'entry_year' => $v['entry_year'] ?? null,
                'status' => $v['status'] ?? 'aktif',
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
                'unique:users,email,' . $student->user?->user_id . ',user_id',
            ],

            'nisn' => [
                'nullable',
                'digits:10',
                'unique:students,nisn,' . $student->nis . ',nis',
            ],

            'class_id' => [
                'nullable',
                'exists:classes,class_id'
            ],

            'gender' => 'required|in:L,P',

            'pob' => 'nullable|string|max:100',

            'dob' => 'nullable|date',

            'address' => 'nullable|string',

            'district' => 'nullable|string|max:100',

            'city' => 'nullable|string|max:100',

            'province' => 'nullable|string|max:100',

            'parent_name' => 'nullable|string|max:100',

            'parent_phone' => 'nullable|string|max:20',

            'entry_year' => 'nullable|digits:4',

            'status' => ['nullable', Rule::in(['aktif', 'lulus', 'pindah', 'drop_out'])],
        ]);

        DB::transaction(function () use ($v, $student) {

            $student->update([
                'nisn' => $v['nisn'] ?? null,
                'class_id' => $v['class_id'] ?? null,
                'gender' => $v['gender'],
                'pob' => $v['pob'] ?? null,
                'dob' => $v['dob'] ?? null,
                'address' => $v['address'] ?? null,
                'district' => $v['district'] ?? null,
                'city' => $v['city'] ?? null,
                'province' => $v['province'] ?? null,
                'parent_name' => $v['parent_name'] ?? null,
                'parent_phone' => $v['parent_phone'] ?? null,
                'entry_year' => $v['entry_year'] ?? null,
                'status' => $v['status'] ?? $student->status,
            ]);

            $student->user()->update([
                'name' => $v['name'],
                'email' => $v['email'],
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