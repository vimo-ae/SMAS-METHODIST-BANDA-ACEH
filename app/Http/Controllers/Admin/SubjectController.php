<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subjects = Subject::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects', 'search'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
        ], [
            'code.unique' => 'Kode Mata Pelajaran ini sudah digunakan.'
        ]);

        Subject::create([
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit($code)
    {
        $subject = Subject::where('code', $code)->firstOrFail();
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, $code)
    {
        $subject = Subject::where('code', $code)->firstOrFail();

        $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->code . ',code',
            'name' => 'required|string|max:255',
        ], [
            'code.unique' => 'Kode Mata Pelajaran ini sudah digunakan.'
        ]);

        try {
            $subject->update([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangkap error foreign key agar tidak muncul layar merah
            return back()->with('error', 'Gagal memperbarui! Kode mata pelajaran ini tidak dapat diubah karena sedang digunakan dalam penugasan Guru Pengampu (Class Subject Teacher).')->withInput();
        }
    }

    public function destroy($code)
    {
        $subject = Subject::where('code', $code)->firstOrFail();
        
        try {
            $subject->delete();
            return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus! Mata pelajaran ini sedang digunakan di relasi kelas/guru.');
        }
    }
}