<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ForumThread::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject', 'creator');

        if ($user->role === 'guru') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $user->teacher->nip));
        } elseif ($user->role === 'siswa') {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('class_id', $user->student->class_id));
        }

        $threads = $query->latest()->paginate(15);

        return view('forum.index', compact('threads'));
    }

    public function show(ForumThread $thread)
    {
        $thread->load('replies.user', 'classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject');
        return view('forum.show', compact('thread'));
    }

    public function reply(Request $request, ForumThread $thread)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $thread->replies()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
