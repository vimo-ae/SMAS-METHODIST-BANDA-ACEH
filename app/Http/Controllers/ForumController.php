<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\ForumThread;
use App\Services\Access;
use Illuminate\Http\Request;

/** Forum tanya jawab per kelas-mapel (siswa bertanya, guru menjawab). */
class ForumController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ForumThread::with('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass', 'creator')->withCount('replies');

        if ($user->isGuru()) {
            $query->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $user->teacher->nip));
            $csts = ClassSubjectTeacher::with('subject', 'schoolClass')->where('teacher_id', $user->teacher->nip)->get();
        } elseif ($user->isSiswa()) {
            $ids = $user->student->joinedClasses()->pluck('class_subject_teacher.cst_id');
            $query->whereIn('cst_id', $ids);
            $csts = $user->student->joinedClasses()->with('subject', 'schoolClass')->get();
        } else {
            $csts = collect();
        }

        return view('forum.index', ['threads' => $query->latest()->paginate(15), 'csts' => $csts]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'cst_id' => 'required|exists:class_subject_teacher,cst_id',
            'title' => 'required|string|max:150',
            'body' => 'required|string',
        ]);
        Access::authorizeCst($request->user(), ClassSubjectTeacher::findOrFail($v['cst_id']));
        abort_unless(in_array($request->user()->role, ['siswa', 'guru'], true), 403);
        $thread = ForumThread::create($v + ['created_by' => $request->user()->user_id]);

        return redirect()->route('forum.show', $thread)->with('success', 'Pertanyaan terkirim ke guru.');
    }

    public function show(Request $request, ForumThread $thread)
    {
        Access::authorizeCst($request->user(), $thread->classSubjectTeacher);
        $thread->load('replies.user', 'creator', 'classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass');

        return view('forum.show', compact('thread'));
    }

    public function reply(Request $request, ForumThread $thread)
    {
        Access::authorizeCst($request->user(), $thread->classSubjectTeacher);
        abort_unless(in_array($request->user()->role, ['siswa', 'guru'], true), 403);
        $v = $request->validate(['content' => 'required|string|max:5000']);
        $thread->replies()->create(['user_id' => $request->user()->user_id, 'content' => $v['content']]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
