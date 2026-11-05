<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
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

        $related = Announcement::published()
            ->where('announcement_id', '!=', $announcement->announcement_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('announcements.show', compact('announcement', 'related'));
    }
}