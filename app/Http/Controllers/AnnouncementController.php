<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $announcements = Announcement::with('creator')->latest()->get()
            ->filter(fn ($a) => $user->isAdmin() || in_array($user->role, $a->target_roles ?? [], true))->take(30);

        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:admin,guru,siswa,orangtua',
        ]);
        Announcement::create($v + ['created_by' => $request->user()->user_id]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
