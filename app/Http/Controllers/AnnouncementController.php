<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /** Semua pengguna melihat pengumuman yang ditujukan ke perannya. Admin melihat semuanya. */
    public function index(Request $request)
    {
        $user = $request->user();

        $announcements = Announcement::with('creator')->latest()->get()
            ->filter(fn ($a) => $user->isAdmin() || in_array($user->role, $a->target_roles ?? [], true))
            ->take(30);

        return view('admin.announcements.index', compact('announcements'));
    }

    /** Hanya admin (dibatasi di route dan dicek lagi di sini). */
    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string|max:5000',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:admin,guru,siswa',
        ], [
            'target_roles.required' => 'Pilih minimal satu penerima pengumuman.',
        ]);

        Announcement::create($v + ['created_by' => $request->user()->user_id]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}