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

    /**
     * Menampilkan daftar siswa secara unik (1 baris per siswa yang memiliki enrollment).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $students = Student::on('mysql_admin')
            ->with(['user', 'schoolClass', 'classEnrollments'])
            ->has('classEnrollments')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nis', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('nis')
            ->paginate(15)
            ->withQueryString();

        // Daftar kelas-mapel untuk modal "Masukkan Satu Kelas"
        $csts = ClassSubjectTeacher::on('mysql_admin')
            ->with(['subject', 'schoolClass', 'teacher.user'])
            ->orderBy('class_id')
            ->get();

        // Siswa per kelas induk & siswa yang sudah terdaftar per kelas-mapel
        $studentsByClass = Student::on('mysql_admin')
            ->with('user')
            ->whereNotNull('class_id')
            ->orderBy('nis')
            ->get()
            ->groupBy('class_id');

        $enrolledByCst = ClassEnrollment::on('mysql_admin')
            ->select('cst_id', 'students_id')
            ->get()
            ->groupBy('cst_id')
            ->map(fn ($rows) => $rows->pluck('students_id')->values());

        return view('admin.enrollments.index', compact(
            'students',
            'search',
            'csts',
            'studentsByClass',
            'enrolledByCst'
        ));
    }

    /**
     * Menampilkan rincian seluruh kelas mapel yang diikuti oleh satu siswa tertentu.
     */
    public function show(string $nis)
    {
        $student = Student::on('mysql_admin')
            ->with([
                'user',
                'schoolClass',
                'classEnrollments.classSubjectTeacher.subject',
                'classEnrollments.classSubjectTeacher.schoolClass',
                'classEnrollments.classSubjectTeacher.teacher.user',
            ])
            ->where('nis', $nis)
            ->firstOrFail();

        $csts = ClassSubjectTeacher::on('mysql_admin')
            ->with(['subject', 'schoolClass', 'teacher.user'])
            ->orderBy('class_id')
            ->get();

        // enrollment_id yang sudah punya aktivitas => tombol hapus dikunci di view
        $terkunci = $student->classEnrollments
            ->filter(fn ($enr) => ! empty($this->blockers($student->nis, $enr->cst_id)))
            ->pluck('enrollment_id')
            ->flip();

        // Ringkasan nilai per kelas mapel dari Database View: v_student_grade_summary
        $gradeSummaries = DB::connection('mysql_admin')
            ->table('v_student_grade_summary')
            ->where('student_id', $nis)
            ->get();

        return view('admin.enrollments.show', compact('student', 'csts', 'terkunci', 'gradeSummaries'));
    }

    public function create()
    {
        $students = Student::on('mysql_admin')->with(['user', 'schoolClass'])->orderBy('nis')->get();
        $csts = ClassSubjectTeacher::on('mysql_admin')->with(['subject', 'schoolClass', 'teacher.user'])->orderBy('class_id')->get();

        return view('admin.enrollments.create', compact('students', 'csts'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'students_id' => ['required', 'exists:mysql_admin.students,nis'],
            'cst_id'      => [
                'required',
                'exists:mysql_admin.class_subject_teacher,cst_id',
                Rule::unique('mysql_admin.class_enrollments', 'cst_id')->where(
                    fn ($q) => $q->where('students_id', $request->students_id)
                ),
            ],
            'status'      => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at'     => 'nullable|date|after_or_equal:enrolled_at',
        ], [
            'cst_id.unique' => 'Siswa tersebut sudah terdaftar pada kelas mata pelajaran ini.',
        ]);

        $cst = ClassSubjectTeacher::on('mysql_admin')->findOrFail($v['cst_id']);

        DB::connection('mysql_admin')->transaction(function () use ($v, $cst) {
            ClassEnrollment::on('mysql_admin')->create($v + [
                'academic_year' => $cst->academic_year,
            ]);
            $this->syncCurrentClass($v['students_id'], $cst, $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.show', $v['students_id'])
            ->with('success', 'Siswa berhasil dimasukkan ke kelas mapel.');
    }

    /**
     * Memasukkan siswa terpilih dari satu kelas induk ke satu kelas-mapel (cst).
     */
    public function storeBulk(Request $request)
    {
        $v = $request->validate([
            'cst_id'        => ['required', 'exists:mysql_admin.class_subject_teacher,cst_id'],
            'students_id'   => ['required', 'array', 'min:1'],
            'students_id.*' => ['exists:mysql_admin.students,nis'],
        ], [
            'students_id.required' => 'Pilih minimal satu siswa.',
            'students_id.min'      => 'Pilih minimal satu siswa.',
        ]);

        $cst = ClassSubjectTeacher::on('mysql_admin')->findOrFail($v['cst_id']);

        $nisList = Student::on('mysql_admin')
            ->where('class_id', $cst->class_id)
            ->whereIn('nis', $v['students_id'])
            ->pluck('nis');

        $alreadyEnrolled = ClassEnrollment::on('mysql_admin')
            ->where('cst_id', $cst->cst_id)
            ->whereIn('students_id', $nisList)
            ->pluck('students_id');

        $toEnroll = $nisList->diff($alreadyEnrolled);

        if ($toEnroll->isEmpty()) {
            return back()->withInput()->withErrors([
                'students_id' => 'Semua siswa yang dipilih sudah terdaftar di kelas mapel ini.',
            ]);
        }

        DB::connection('mysql_admin')->transaction(function () use ($toEnroll, $cst) {
            foreach ($toEnroll as $nis) {
                ClassEnrollment::on('mysql_admin')->create([
                    'students_id'   => $nis,
                    'cst_id'        => $cst->cst_id,
                    'status'        => 'aktif',
                    'enrolled_at'   => now()->toDateString(),
                    'academic_year' => $cst->academic_year,
                ]);
            }
        });

        $message = "{$toEnroll->count()} siswa berhasil dimasukkan ke kelas mapel.";

        if ($alreadyEnrolled->isNotEmpty()) {
            $message .= " {$alreadyEnrolled->count()} siswa dilewati karena sudah terdaftar.";
        }

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', $message);
    }

    public function edit(int $id)
    {
        $enrollment = ClassEnrollment::on('mysql_admin')->with('student.user')->findOrFail($id);
        $csts = ClassSubjectTeacher::on('mysql_admin')->with(['subject', 'schoolClass', 'teacher.user'])->orderBy('class_id')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'csts'));
    }

    public function update(Request $request, int $id)
    {
        $enrollment = ClassEnrollment::on('mysql_admin')->findOrFail($id);

        $v = $request->validate([
            'cst_id'      => [
                'required',
                'exists:mysql_admin.class_subject_teacher,cst_id',
                Rule::unique('mysql_admin.class_enrollments', 'cst_id')
                    ->where(fn ($q) => $q->where('students_id', $enrollment->students_id))
                    ->ignore($enrollment->enrollment_id, 'enrollment_id'),
            ],
            'status'      => ['required', Rule::in(self::STATUSES)],
            'enrolled_at' => 'nullable|date',
            'left_at'     => 'nullable|date|after_or_equal:enrolled_at',
        ], [
            'cst_id.unique' => 'Siswa tersebut sudah terdaftar pada kelas mata pelajaran ini.',
        ]);

        $cst = ClassSubjectTeacher::on('mysql_admin')->findOrFail($v['cst_id']);

        DB::connection('mysql_admin')->transaction(function () use ($v, $cst, $enrollment) {
            $enrollment->update($v + ['academic_year' => $cst->academic_year]);
            $this->syncCurrentClass($enrollment->students_id, $cst, $v['status']);
        });

        return redirect()
            ->route('admin.enrollments.show', $enrollment->students_id)
            ->with('success', 'Data enrollment kelas siswa berhasil diperbarui.');
    }

    /**
     * Mengeluarkan siswa dari kelas mapel.
     * Ditolak jika siswa sudah punya aktivitas di kelas mapel itu (nilai, tugas, ujian),
     * agar riwayat akademiknya tidak hilang. Gunakan Edit -> status "pindah" / "drop_out".
     */
    public function destroy(int $id)
    {
        $conn = DB::connection('mysql_admin');

        try {
            $hasil = $conn->transaction(function () use ($id) {
                // Kunci baris dulu, lalu cek di dalam transaksi
                $enrollment = ClassEnrollment::on('mysql_admin')
                    ->where('enrollment_id', $id)
                    ->lockForUpdate()
                    ->first();

                if (! $enrollment) {
                    return ['status' => 'hilang'];
                }

                $terpakai = $this->blockers($enrollment->students_id, $enrollment->cst_id);

                if (! empty($terpakai)) {
                    return [
                        'status' => 'ditolak',
                        'nis'    => $enrollment->students_id,
                        'detail' => collect($terpakai)
                            ->map(fn ($jumlah, $label) => "{$jumlah} {$label}")
                            ->implode(', '),
                    ];
                }

                $enrollment->delete();

                return ['status' => 'ok', 'nis' => $enrollment->students_id];
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengeluarkan siswa: ' . $e->getMessage());
        }

        if ($hasil['status'] === 'hilang') {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Data enrollment tidak ditemukan atau sudah dihapus.');
        }

        if ($hasil['status'] === 'ditolak') {
            return redirect()
                ->route('admin.enrollments.show', $hasil['nis'])
                ->with('error', "Siswa tidak bisa dikeluarkan karena sudah memiliki {$hasil['detail']} di kelas mapel ini. "
                    . 'Ubah statusnya menjadi "pindah" atau "drop out" lewat Edit agar riwayat tetap tersimpan.');
        }

        return redirect()
            ->route('admin.enrollments.show', $hasil['nis'])
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas mapel.');
    }

    /**
     * Aktivitas siswa di satu kelas mapel (cst) yang menghalangi penghapusan enrollment.
     * Mengembalikan [label => jumlah] hanya untuk yang > 0.
     */
    private function blockers(string|int $nis, string|int $cstId): array
    {
        $conn = DB::connection('mysql_admin');

        $counts = [
            'nilai' => $conn->table('grades')
                ->where('students_id', $nis)
                ->where('cst_id', $cstId)
                ->count(),

            'pengumpulan tugas' => $conn->table('assignment_submissions as s')
                ->join('assignments as a', 'a.assignment_id', '=', 's.assignment_id')
                ->where('s.students_id', $nis)
                ->where('a.cst_id', $cstId)
                ->count(),

            'sesi ujian' => $conn->table('exam_sessions as es')
                ->join('exams as e', 'e.exam_id', '=', 'es.exam_id')
                ->where('es.student_id', $nis)
                ->where('e.cst_id', $cstId)
                ->count(),

            'jawaban ujian' => $conn->table('exam_answers as ea')
                ->join('exams as e', 'e.exam_id', '=', 'ea.exam_id')
                ->where('ea.students_id', $nis)
                ->where('e.cst_id', $cstId)
                ->count(),
        ];

        return array_filter($counts);
    }

    private function syncCurrentClass(int|string $nis, ClassSubjectTeacher $cst, string $status): void
    {
        if ($status === 'aktif') {
            Student::on('mysql_admin')
                ->where('nis', $nis)
                ->update(['class_id' => $cst->class_id]);
        }
    }
}