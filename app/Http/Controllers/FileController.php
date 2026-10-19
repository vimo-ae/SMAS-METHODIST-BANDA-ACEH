<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Penyajian berkas privat (foto wajah, lembar esai, tugas) dengan otorisasi. */
class FileController extends Controller
{
    public function faceOrEssay(Request $request, string $kind, int $id)
    {
        $user = $request->user();
        if ($kind === 'face') {
            $session = ExamSession::with('exam.classSubjectTeacher')->findOrFail($id);
            $path = $session->face_photo;
            $nis = $session->students_id;
            $cst = $session->exam->classSubjectTeacher;
        } else {
            $ans = ExamAnswer::with('exam.classSubjectTeacher')->findOrFail($id);
            $path = $ans->answer_file;
            $nis = $ans->students_id;
            $cst = $ans->exam->classSubjectTeacher;
        }

        return $this->serve($user, $path, $nis, $cst->teacher_id);
    }

    public function submission(Request $request, AssignmentSubmission $submission)
    {
        $cst = $submission->assignment->classSubjectTeacher;

        return $this->serve($request->user(), $submission->file_path, $submission->students_id, $cst->teacher_id);
    }

    private function serve($user, ?string $path, $nis, $teacherNip)
    {
        $allowed = match ($user->role) {
            'admin' => true,
            'guru' => $user->teacher && (string) $user->teacher->nip === (string) $teacherNip,
            'siswa' => $user->student && (string) $user->student->nis === (string) $nis,
            'orangtua' => $user->guardianProfile && $user->guardianProfile->students()->where('students.nis', $nis)->exists(),
            default => false,
        };
        abort_unless($allowed && $path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
