<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\ClassSubjectTeacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassEnrollmentController extends Controller
{
    private const STATUSES = ['aktif', 'naik_kelas', 'tinggal_kelas', 'pindah', 'lulus', 'drop_out'];

    public function index(Request $request)
    {
        $enrollments = ClassEnrollment::with(
                'student.user',
                'classSubjectTeacher.subject',
                'classSubjectTeacher.schoolClass'
            )
            ->when($request->filled('academic_year'), fn ($q) => $q->where('academic_year', $request->academic_year))
            ->when($request->filled('cst_id'), fn ($q) => $q->where('cst_id', $request->cst_id))
            ->latest('enrollment_id')
            ->paginate(15)
            ->withQueryString();

        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')->orderBy('class_id')->get();

        return view('admin.enrollments.index', compact('enrollments', 'csts'));
    }

    public function create()
    {
        $students = Student::with('user')->orderBy('nis')->get();
        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')->orderBy('class_id')->get();

        return view('admin.enrollments.create', compact('students', 'csts'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'students_id' => ['required', 'exists:students,nis'],
            'cst_id' => [
                'required',
                'exists:class_subject_teacher,cst_id',
                // satu siswa hanya terdaftar sekali di satu kelas-mapel
                Rule::unique('class_enrollments')->where(
                    fn ($q) => $q->where('students_id', $request->students_id)
                ),
            ],
            'status' => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at' => 'nullable|date|after_or_equal:enrolled_at',
        ]);

        $cst = ClassSubjectTeacher::findOrFail($v['cst_id']);

        DB::transaction(function () use ($v, $cst) {
            // academic_year diambil dari cst, tidak diketik manual
            ClassEnrollment::create($v + ['academic_year' => $cst->academic_year]);
            $this->syncCurrentClass($v['students_id'], $cst, $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil ditambahkan.');
    }

    public function edit(ClassEnrollment $enrollment)
    {
        $enrollment->load('student.user');
        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')->orderBy('class_id')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'csts'));
    }

    public function update(Request $request, ClassEnrollment $enrollment)
    {
        $v = $request->validate([
            'cst_id' => [
                'required',
                'exists:class_subject_teacher,cst_id',
                Rule::unique('class_enrollments')
                    ->where(fn ($q) => $q->where('students_id', $enrollment->students_id))
                    ->ignore($enrollment->enrollment_id, 'enrollment_id'),
            ],
            'status' => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at' => 'nullable|date|after_or_equal:enrolled_at',
        ]);

        $cst = ClassSubjectTeacher::findOrFail($v['cst_id']);

        DB::transaction(function () use ($v, $cst, $enrollment) {
            $enrollment->update($v + ['academic_year' => $cst->academic_year]);
            $this->syncCurrentClass($enrollment->students_id, $cst, $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil diperbarui.');
    }

    public function destroy(ClassEnrollment $enrollment)
    {
        // students.class_id tidak diubah: hanya riwayat pendaftarannya yang dihapus
        $enrollment->delete();

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil dihapus.');
    }

    /**
     * Pendaftaran berstatus "aktif" menentukan kelas induk siswa saat ini
     * (students.class_id), diambil dari class_id milik cst.
     */
    private function syncCurrentClass(int|string $nis, ClassSubjectTeacher $cst, string $status): void
    {
        if ($status === 'aktif') {
            Student::where('nis', $nis)->update(['class_id' => $cst->class_id]);
        }
    }
}