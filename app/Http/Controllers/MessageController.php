<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\ParentMessage;
use App\Models\User;
use Illuminate\Http\Request;

/** Orang tua: tanya guru / pengaduan.  Guru & admin: balas. */
class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = ParentMessage::with('parent', 'teacher', 'student.user')->latest();

        if ($user->isOrangtua()) {
            $q->where('parent_user_id', $user->user_id);
            $children = $user->guardianProfile->students()->with('user', 'schoolClass')->get();
            $teachers = User::where('role', 'guru')
                ->whereIn('user_id', ClassSubjectTeacher::whereIn('class_id', $children->pluck('class_id'))->pluck('teacher_id'))
                ->orderBy('name')->get();
        } elseif ($user->isGuru()) {
            $q->where('teacher_user_id', $user->user_id);
        } else { // admin: pengaduan ke sekolah + semua
            $q->where(fn ($w) => $w->whereNull('teacher_user_id')->orWhere('type', 'pengaduan'));
        }

        return view('messages.index', [
            'messages' => $q->paginate(15),
            'children' => $children ?? collect(),
            'teachers' => $teachers ?? collect(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isOrangtua(), 403);
        $v = $request->validate([
            'type' => 'required|in:pertanyaan,pengaduan',
            'teacher_user_id' => 'nullable|exists:users,user_id',
            'students_id' => 'required|exists:students,nis',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
        ]);
        abort_unless($request->user()->guardianProfile->students()->where('students.nis', $v['students_id'])->exists(), 403);
        if ($v['type'] === 'pertanyaan' && empty($v['teacher_user_id'])) {
            return back()->withInput()->with('error', 'Pilih guru tujuan untuk pertanyaan.');
        }
        if ($v['type'] === 'pengaduan') {
            $v['teacher_user_id'] = null; // pengaduan ditangani admin sekolah
        }
        ParentMessage::create($v + ['parent_user_id' => $request->user()->user_id]);

        return back()->with('success', 'Pesan terkirim.');
    }

    public function reply(Request $request, ParentMessage $message)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || ($user->isGuru() && (int) $message->teacher_user_id === (int) $user->user_id), 403);
        $v = $request->validate(['reply' => 'required|string|max:5000']);
        $message->update($v + ['replied_by' => $user->user_id, 'replied_at' => now()]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
