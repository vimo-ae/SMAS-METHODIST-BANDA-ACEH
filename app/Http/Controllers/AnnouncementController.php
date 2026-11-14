<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /** Semua pengguna melihat pengumuman yang ditujukan ke perannya. Admin melihat semuanya. */
    public function index(Request $request)
    {
<<<<<<< HEAD
        $category = $request->query('kategori');

        $announcements = Announcement::published()
            ->when(
                array_key_exists($category, Announcement::CATEGORIES),
                fn ($q) => $q->where('category', $category)
            )
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('announcements.index', compact('announcements', 'category'));
    }

    public function show(Announcement $announcement)
    {
        abort_unless(
            $announcement->is_public
                && $announcement->published_at
                && $announcement->published_at->lte(now()),
            404
        );
=======
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
>>>>>>> 014121bbfae10bf11aad37e9d5abba9c3797084e

        $related = Announcement::published()
            ->where('announcement_id', '!=', $announcement->announcement_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('announcements.show', compact('announcement', 'related'));
    }
<<<<<<< HEAD
=======

    public function destroy(Request $request, Announcement $announcement)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
>>>>>>> 014121bbfae10bf11aad37e9d5abba9c3797084e
}