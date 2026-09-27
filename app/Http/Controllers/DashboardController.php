<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $announcements = Announcement::latest()->take(5)->get()->filter(function ($a) use ($user) {
            return in_array($user->role, $a->target_roles ?? []);
        });

        return view('dashboard', compact('user', 'announcements'));
    }
}
