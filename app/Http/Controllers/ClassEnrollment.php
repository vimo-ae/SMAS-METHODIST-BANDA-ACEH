<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassEnrollmentController extends Controller
{
    private const STATUSES = ['aktif', 'naik_kelas', 'tinggal_kelas', 'pindah', 'lulus', 'drop_out'];

    public function index(Request $request)
    {
        $enrollments = ClassEnrollment::with('student.user', 'schoolClass')
            ->when($request->filled('academic_year'), fn ($q) => $q->where('academic_year', $request->academic_year))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->latest('enrollment_id')
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.enrollments.index', compact('enrollments', 'classes'));
    }

    public function create()
    {
        $students = Student::with('user')->orderBy('nis')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.enrollments.create', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'students_id' => ['required', 'exists:students,nis'],
            'class_id' => ['required', 'exists:classes,class_id'],
            'academic_year' => [
                'required', 'string', 'max:9',
                // satu siswa hanya boleh punya satu kelas per tahun ajaran
                Rule::unique('class_enrollments')->where(
                    fn ($q) => $q->where('students_id', $request->students_id)
                ),
            ],
            'status' => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at' => 'nullable|date|after_or_equal:enrolled_at',
        ]);

        DB::transaction(function () use ($v) {
            ClassEnrollment::create($v);
            $this->syncCurrentClass($v['students_id'], $v['class_id'], $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil ditambahkan.');
    }

    public function edit(ClassEnrollment $enrollment)
    {
        $enrollment->load('student.user');
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'classes'));
    }

    public function update(Request $request, ClassEnrollment $enrollment)
    {
        $v = $request->validate([
            'class_id' => ['required', 'exists:classes,class_id'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at' => 'nullable|date|after_or_equal:enrolled_at',
        ]);

        DB::transaction(function () use ($v, $enrollment) {
            $enrollment->update($v);
            $this->syncCurrentClass($enrollment->students_id, $v['class_id'], $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil diperbarui.');
    }

    public function destroy(ClassEnrollment $enrollment)
    {
        // students.class_id tidak diubah: hanya riwayatnya yang dihapus
        $enrollment->delete();

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data kelas siswa berhasil dihapus.');
    }

    /**
     * Enrollment aktif menentukan "kelas saat ini" di students.class_id.
     * Status lain (naik_kelas, lulus, dll.) tidak mengubahnya.
     */
    private function syncCurrentClass(int|string $nis, int|string $classId, string $status): void
    {
        if ($status === 'aktif') {
            Student::where('nis', $nis)->update(['class_id' => $classId]);
        }
    }
}