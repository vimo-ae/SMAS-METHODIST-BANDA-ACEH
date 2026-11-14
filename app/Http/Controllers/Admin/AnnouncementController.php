<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $announcements = Announcement::with('creator') // Diperbaiki dari 'author' ke 'creator'
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest('announcement_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements', 'search'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_roles' => 'required|array', // Sesuai kolom model (array)
            'target_roles.*' => Rule::in(['all', 'teacher', 'student']),
        ]);

        Announcement::create([
            'title' => $v['title'],
            'content' => $v['content'],
            'target_roles' => $v['target_roles'],
            'created_by' => Auth::id(), // Sesuai kolom created_by di model
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('creator'); // Diperbaiki dari 'author' ke 'creator'
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $v = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_roles' => 'required|array',
            'target_roles.*' => Rule::in(['all', 'teacher', 'student']),
        ]);

        $announcement->update([
            'title' => $v['title'],
            'content' => $v['content'],
            'target_roles' => $v['target_roles'],
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}