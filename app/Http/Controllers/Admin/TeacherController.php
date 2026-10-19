<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        return view('admin.teachers.index', [
            'teachers' => Teacher::with('user')->paginate(15)
        ]);
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'nip' => 'required|string|max:30|unique:teachers,nip',
            'specialization' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($v) {

            User::create([
                'user_id' => $v['nip'],
                'name' => $v['name'],
                'email' => $v['email'],
                'phone' => $v['phone'] ?? null,
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ]);
            Teacher::create([
                'nip' => $v['nip'],
                'specialization' => $v['specialization'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                'Guru berhasil ditambahkan. Password default: password123'
            );
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $v = $request->validate([
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                'unique:users,email,' . $teacher->user?->user_id . ',user_id',
            ],

            'phone' => 'nullable|string|max:20',


            'specialization' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($v, $teacher) {

            $teacher->update([
                'specialization' => $v['specialization'] ?? null,
            ]);

            $teacher->user()->update([
                'name' => $v['name'],
                'email' => $v['email'],
                'phone' => $v['phone'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user()?->delete();
        $teacher->delete();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}