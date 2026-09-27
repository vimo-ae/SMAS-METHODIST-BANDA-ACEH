<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $announcements = Announcement::with('creator')
            ->latest()
            ->get()
            ->filter(fn ($a) => in_array($user->role, $a->target_roles ?? []))
            ->take(20);

        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:superadmin,admin,guru,siswa,orangtua',
        ]);

        Announcement::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }
}
