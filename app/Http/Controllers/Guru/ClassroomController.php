<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubjectTeacher;
use App\Models\Material;
use App\Services\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    /**
     * Format file yang diperbolehkan untuk pengumpulan tugas.
     */
    private const FORMATS = [
        'pdf',
        'doc',
        'docx',
        'ppt',
        'pptx',
        'xls',
        'xlsx',
        'jpg',
        'png',
        'zip',
        'txt',
    ];

    /**
     * Format file yang diperbolehkan untuk materi.
     */
    private const MATERIAL_MIMES =
        'pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,mp4,zip';

    /**
     * Membersihkan HTML dari editor WYSIWYG.
     * Membutuhkan package mews/purifier.
     */
    private function cleanHtml(?string $html): ?string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return null;
        }

        $html = clean($html);

        return trim($html) !== '' ? trim($html) : null;
    }

    /**
     * Memastikan guru memiliki akses terhadap kelas.
     */
    private function authorizeAssignmentTeacher(
        Request $request,
        Assignment $assignment
    ): ClassSubjectTeacher {
        $cst = $assignment->classSubjectTeacher;

        abort_unless($cst, 404);

        Access::authorizeTeacher($request->user(), $cst);

        return $cst;
    }

    // =====================================================
    // DAFTAR KELAS GURU
    // =====================================================

    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403, 'Akun ini bukan akun guru.');

        $classes = ClassSubjectTeacher::with([
                'subject',
                'schoolClass',
            ])
            ->withCount([
                'joinedStudents',
                'materials',
                'assignments',
                'exams',
            ])
            ->where('teacher_id', $teacher->nip)
            ->orderBy('class_id')
            ->get();

        return view('guru.classes.index', compact('classes'));
    }

    // =====================================================
    // DETAIL KELAS
    // =====================================================

    public function show(Request $request, ClassSubjectTeacher $cst)
    {
        Access::authorizeTeacher($request->user(), $cst);

        $cst->load([
            'subject',
            'schoolClass',
            'joinedStudents.user',
            'materials',
            'assignments.submissions',
            'exams.questions',
        ]);

        $class = $cst;

        $students = $cst->joinedStudents;

        $exams = $cst->exams()
            ->withCount('questions')
            ->get();

        // Data jadwal dan nilai dibatasi untuk kelas yang sedang dibuka.
        $connection = DB::connection('mysql_guru');

        $schedule = $connection->table('v_teaching_schedule')
            ->where('cst_id', $cst->cst_id)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('start_time')
            ->get();

        $gradebook = $connection->table('v_gradebook')
            ->where('cst_id', $cst->cst_id)
            ->orderBy('student_name')
            ->orderByDesc('graded_at')
            ->get();

        return view(
            'guru.classes.show',
            compact('cst', 'class', 'students', 'exams', 'schedule', 'gradebook')
        );
    }

    // =====================================================
    // KODE GABUNG KELAS
    // =====================================================

    public function regenerateCode(
        Request $request,
        ClassSubjectTeacher $cst
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        do {
            $code = strtoupper(Str::random(6));
        } while (
            ClassSubjectTeacher::where('join_code', $code)->exists()
        );

        $cst->update([
            'join_code' => $code,
        ]);

        return back()->with(
            'success',
            'Kode kelas baru: ' . $code
        );
    }

    // =====================================================
    // MENGELUARKAN SISWA
    // =====================================================

    public function removeStudent(
        Request $request,
        ClassSubjectTeacher $cst,
        int $nis
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        $cst->joinedStudents()->detach($nis);

        return back()->with(
            'success',
            'Siswa berhasil dikeluarkan dari kelas.'
        );
    }

    // =====================================================
    // MATERI PEMBELAJARAN
    // =====================================================

    /**
     * Halaman form tambah materi.
     */
    public function createMaterial(
        Request $request,
        ClassSubjectTeacher $cst
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        $cst->load(['subject', 'schoolClass']);

        return view('guru.classes.materials.create', compact('cst'));
    }

    /**
     * Menambahkan materi.
     */
    public function storeMaterial(
        Request $request,
        ClassSubjectTeacher $cst
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480|mimes:' .
                self::MATERIAL_MIMES,
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store('materials', 'public');
        }

        try {
            Material::create([
                'cst_id' => $cst->cst_id,
                'title' => $v['title'],
                'description' => $this->cleanHtml(
                    $v['description'] ?? null
                ),
                'file_path' => $filePath,
                'uploaded_at' => now(),
            ]);
        } catch (\Throwable $e) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $e;
        }

        return redirect()->route('guru.classes.show', $cst)->with(
            'success',
            'Materi berhasil ditambahkan.'
        );
    }

    /**
     * Memperbarui materi.
     */
    public function updateMaterial(
        Request $request,
        ClassSubjectTeacher $cst,
        Material $material
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        abort_unless(
            (int) $material->cst_id === (int) $cst->cst_id,
            404
        );

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480|mimes:' .
                self::MATERIAL_MIMES,
        ]);

        $data = [
            'title' => $v['title'],
            'description' => $this->cleanHtml(
                $v['description'] ?? null
            ),
        ];

        $newFilePath = null;

        if ($request->hasFile('file')) {
            $newFilePath = $request->file('file')
                ->store('materials', 'public');

            $data['file_path'] = $newFilePath;
        }

        try {
            $oldFilePath = $material->file_path;

            $material->update($data);

            if ($newFilePath && $oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }
        } catch (\Throwable $e) {
            if ($newFilePath) {
                Storage::disk('public')->delete($newFilePath);
            }

            throw $e;
        }

        return back()->with(
            'success',
            'Materi berhasil diperbarui.'
        );
    }

    /**
     * Menghapus materi.
     */
    public function destroyMaterial(
        Request $request,
        ClassSubjectTeacher $cst,
        Material $material
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        abort_unless(
            (int) $material->cst_id === (int) $cst->cst_id,
            404
        );

        $filePath = $material->file_path;

        $material->delete();

        if ($filePath) {
            Storage::disk('public')->delete($filePath);
        }

        return back()->with(
            'success',
            'Materi berhasil dihapus.'
        );
    }

    // =====================================================
    // TUGAS
    // =====================================================

    /**
     * Halaman form buat tugas baru.
     */
    public function createAssignment(
        Request $request,
        ClassSubjectTeacher $cst
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        $cst->load(['subject', 'schoolClass']);

        $formatOptions = [
            'pdf' => 'PDF',
            'doc' => 'DOC',
            'docx' => 'DOCX',
            'ppt' => 'PPT',
            'pptx' => 'PPTX',
            'xls' => 'XLS',
            'xlsx' => 'XLSX',
            'jpg' => 'JPG / JPEG',
            'png' => 'PNG',
            'zip' => 'ZIP',
            'txt' => 'TXT',
        ];

        return view('guru.classes.assignments.create', compact('cst', 'formatOptions'));
    }

    /**
     * Membuat tugas baru.
     */
    public function storeAssignment(
        Request $request,
        ClassSubjectTeacher $cst
    ) {
        Access::authorizeTeacher($request->user(), $cst);

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer|min:1|max:1000',
            'allowed_formats' => 'required|array|min:1',
            'allowed_formats.*' => [
                'required',
                'string',
                Rule::in(self::FORMATS),
            ],
        ]);

        $v['description'] = $this->cleanHtml(
            $v['description'] ?? null
        );

        $v['max_score'] = $v['max_score'] ?? 100;

        $v['cst_id'] = $cst->cst_id;

        Assignment::create($v);

        return redirect()->route('guru.classes.show', $cst)->with(
            'success',
            'Tugas berhasil dibuat.'
        );
    }

    /**
     * Memperbarui tugas.
     */
    public function updateAssignment(
        Request $request,
        Assignment $assignment
    ) {
        $this->authorizeAssignmentTeacher($request, $assignment);

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer|min:1|max:1000',
            'allowed_formats' => 'required|array|min:1',
            'allowed_formats.*' => [
                'required',
                'string',
                Rule::in(self::FORMATS),
            ],
        ]);

        $v['description'] = $this->cleanHtml(
            $v['description'] ?? null
        );

        $v['max_score'] = $v['max_score']
            ?? $assignment->max_score;

        $assignment->update($v);

        return back()->with(
            'success',
            'Tugas berhasil diperbarui.'
        );
    }

    /**
     * Menampilkan detail tugas dan pengumpulan siswa.
     */
    public function showAssignment(
        Request $request,
        Assignment $assignment
    ) {
        $cst = $this->authorizeAssignmentTeacher(
            $request,
            $assignment
        );

        $cst->load([
            'subject',
            'schoolClass',
            'joinedStudents.user',
        ]);

        $subs = $assignment->submissions()
            ->get()
            ->keyBy('students_id');

        return view(
            'guru.classes.assignment',
            compact('assignment', 'cst', 'subs')
        );
    }

    /**
     * Mengunduh file tugas siswa.
     */
    public function downloadSubmission(
        Request $request,
        Assignment $assignment,
        int $nis
    ) {
        $this->authorizeAssignmentTeacher(
            $request,
            $assignment
        );

        $submission = AssignmentSubmission::where(
                'assignment_id',
                $assignment->assignment_id
            )
            ->where('students_id', $nis)
            ->firstOrFail();

        abort_unless(
            $submission->file_path &&
            Storage::disk('local')->exists(
                $submission->file_path
            ),
            404
        );

        return Storage::disk('local')->download(
            $submission->file_path
        );
    }

    /**
     * Membuka / pratinjau berkas jawaban siswa.
     */
    public function viewSubmission(
        Request $request,
        Assignment $assignment,
        int $nis
    ) {
        $this->authorizeAssignmentTeacher(
            $request,
            $assignment
        );

        $submission = AssignmentSubmission::where(
                'assignment_id',
                $assignment->assignment_id
            )
            ->where('students_id', $nis)
            ->firstOrFail();

        abort_unless(
            $submission->file_path &&
            Storage::disk('local')->exists(
                $submission->file_path
            ),
            404
        );

        return response()->file(
            Storage::disk('local')->path($submission->file_path)
        );
    }

    /**
     * Memberikan nilai dan feedback tugas.
     */
    public function gradeSubmission(
        Request $request,
        Assignment $assignment,
        int $nis
    ) {
        $cst = $this->authorizeAssignmentTeacher(
            $request,
            $assignment
        );

        $v = $request->validate([
            'score' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $assignment->max_score,
            ],
            'feedback' => 'nullable|string|max:1000',
        ]);

        $query = AssignmentSubmission::where(
                'assignment_id',
                $assignment->assignment_id
            )
            ->where('students_id', $nis);

        abort_unless($query->exists(), 404);

        DB::connection('mysql_guru')->select(
            'CALL sp_grade_assignment(?, ?, ?, ?, ?)',
            [
                $assignment->assignment_id,
                $nis,
                $request->user()->teacher->nip,
                $v['score'],
                $v['feedback'] ?? null,
            ]
        );

        return back()->with(
            'success',
            'Nilai dan feedback berhasil disimpan.'
        );
    }

    /**
     * Menghapus tugas.
     */
    public function destroyAssignment(
        Request $request,
        Assignment $assignment
    ) {
        $this->authorizeAssignmentTeacher(
            $request,
            $assignment
        );

        $cstId = $assignment->cst_id;

        $assignment->delete();

        return redirect()
            ->route('guru.classes.show', $cstId)
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
