<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Penyajian berkas privat (lembar esai dan tugas) dengan otorisasi. */
class FileController extends Controller
{
    /** Foto lembar jawaban esai. $id = exam_answer_id. */
    public function essay(Request $request, int $id)
    {
        $ans = ExamAnswer::with('exam.classSubjectTeacher')->findOrFail($id);
        $cst = $ans->exam->classSubjectTeacher;

        return $this->serve($request->user(), $ans->answer_file, $ans->students_id, $cst->teacher_id);
    }

    /** File tugas siswa. Dicari lewat assignment + nis, karena tabelnya memakai primary key gabungan. */
    public function submission(Request $request, Assignment $assignment, int $nis)
    {
        $submission = AssignmentSubmission::where('assignment_id', $assignment->assignment_id)
            ->where('students_id', $nis)
            ->firstOrFail();

        $cst = $assignment->classSubjectTeacher;

        return $this->serve($request->user(), $submission->file_path, $submission->students_id, $cst->teacher_id);
    }

    private function serve($user, ?string $path, $nis, $teacherNip)
    {
        $allowed = match ($user->role) {
            'admin' => true,
            'guru' => $user->teacher && (string) $user->teacher->nip === (string) $teacherNip,
            'siswa' => $user->student && (string) $user->student->nis === (string) $nis,
            default => false,
        };
        abort_unless($allowed && $path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }
}