<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\Material;
use App\Services\Access;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->withCount('joinedStudents', 'materials', 'assignments', 'exams')
            ->where('teacher_id', $request->user()->teacher->nip)
            ->orderBy('class_id')->get();

        return view('guru.classes.index', compact('classes'));
    }

    public function show(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $cst->load('subject', 'schoolClass', 'joinedStudents.user', 'materials', 'assignments.submissions', 'exams');

        return view('guru.classes.show', compact('cst'));
    }

    public function regenerateCode(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        do {
            $code = strtoupper(Str::random(6));
        } while (ClassSubjectTeacher::where('join_code', $code)->exists());
        $cst->update(['join_code' => $code]);

        return back()->with('success', 'Kode kelas baru: '.$code);
    }

    public function removeStudent(Request $request, ClassSubjectTeacher $cst, int $nis)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $cst->joinedStudents()->detach($nis);

        return back()->with('success', 'Siswa dikeluarkan dari kelas.');
    }

    // ---------- Materi ----------
    public function storeMaterial(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,mp4,zip',
        ]);
        Material::create([
            'cst_id' => $cst->cst_id,
            'title' => $v['title'],
            'description' => $v['description'] ?? null,
            'file_path' => $request->hasFile('file') ? $request->file('file')->store('materials', 'public') : null,
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Materi berhasil diunggah.');
    }

    public function destroyMaterial(Request $request, ClassSubjectTeacher $cst, Material $material)
    {
        Access::authorizeTeacher($request->user(), $cst);
        abort_unless($material->cst_id === $cst->cst_id, 404);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();

        return back()->with('success', 'Materi dihapus.');
    }

    // ---------- Tugas ----------
    public function storeAssignment(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);
        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1|max:1000',
        ]);
        Assignment::create($v + ['cst_id' => $cst->cst_id]);

        return back()->with('success', 'Wadah pengumpulan tugas dibuat.');
    }

    public function showAssignment(Request $request, Assignment $assignment)
    {
        $cst = $assignment->classSubjectTeacher()->with('subject', 'schoolClass', 'joinedStudents.user')->first();
        Access::authorizeTeacher($request->user(), $cst);
        $subs = $assignment->submissions()->get()->keyBy('students_id');

        return view('guru.classes.assignment', compact('assignment', 'cst', 'subs'));
    }

    public function gradeSubmission(Request $request, Assignment $assignment, int $nis)
    {
        $cst = $assignment->classSubjectTeacher;
        Access::authorizeTeacher($request->user(), $cst);
        $v = $request->validate([
            'score' => 'required|numeric|min:0|max:'.$assignment->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);
        $sub = AssignmentSubmission::where('assignment_id', $assignment->assignment_id)->where('students_id', $nis)->firstOrFail();
        $sub->update($v);

        $student = $cst->joinedStudents()->where('students.nis', $nis)->first();
        if ($student) {
            app(ReportCardService::class)->syncFromActivity($student, $cst);
        }

        return back()->with('success', 'Nilai tugas tersimpan dan diakumulasi ke rapor.');
    }

    public function destroyAssignment(Request $request, Assignment $assignment)
    {
        Access::authorizeTeacher($request->user(), $assignment->classSubjectTeacher);
        $assignment->delete();

        return redirect()->route('guru.classes.show', $assignment->cst_id)->with('success', 'Tugas dihapus.');
    }
}
