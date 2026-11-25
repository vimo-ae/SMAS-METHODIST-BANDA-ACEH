<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::on('mysql_admin')->with(['user', 'schoolClass']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('class_id')) {
            if ($request->class_id === 'unassigned') {
                $query->whereNull('class_id');
            } else {
                $query->where('class_id', $request->class_id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('nis')->paginate(15);
        $classes  = SchoolClass::on('mysql_admin')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::on('mysql_admin')->orderBy('name')->get();

        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:mysql_admin.users,email',
            'phone'        => 'nullable|string|max:20',
            'nis'          => 'required|string|max:10|unique:mysql_admin.students,nis|unique:mysql_admin.users,user_id',
            'nisn'         => 'nullable|digits:10|unique:mysql_admin.students,nisn',
            'class_id'     => ['nullable', 'exists:mysql_admin.classes,class_id'],
            'gender'       => 'required|in:L,P',
            'pob'          => 'nullable|string|max:50',
            'dob'          => 'nullable|date',
            'address'      => 'nullable|string|max:255',
            'district'     => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'parent_name'  => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'entry_year'   => 'nullable|digits:4',
            'status'       => ['nullable', Rule::in(['aktif', 'lulus', 'pindah', 'drop_out'])],
        ]);

        DB::connection('mysql_admin')->transaction(function () use ($v) {
            User::on('mysql_admin')->create([
                'user_id'  => $v['nis'],
                'name'     => $v['name'],
                'email'    => $v['email'],
                'phone'    => $v['phone'] ?? null,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ]);

            Student::on('mysql_admin')->create([
                'nis'          => $v['nis'],
                'nisn'         => $v['nisn'] ?? null,
                'class_id'     => $v['class_id'] ?? null,
                'gender'       => $v['gender'],
                'pob'          => $v['pob'] ?? null,
                'dob'          => $v['dob'] ?? null,
                'address'      => $v['address'] ?? null,
                'district'     => $v['district'] ?? null,
                'city'         => $v['city'] ?? null,
                'province'     => $v['province'] ?? null,
                'parent_name'  => $v['parent_name'] ?? null,
                'parent_phone' => $v['parent_phone'] ?? null,
                'entry_year'   => $v['entry_year'] ?? null,
                'status'       => $v['status'] ?? 'aktif',
            ]);
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan. Password default: password123');
    }

    public function edit(string $nis)
    {
        $student = Student::on('mysql_admin')->with('user')->findOrFail($nis);
        $classes = SchoolClass::on('mysql_admin')->orderBy('name')->get();

        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Menampilkan detail informasi siswa beserta integrasi Database View & Stored Function:
     * - v_class_roster (Daftar rombel mapel yang diikuti)
     * - v_student_grade_summary (Ringkasan nilai per mapel)
     * - fn_student_average (Rata-rata nilai per mapel)
     */
    public function show(string $nis)
    {
        $student = Student::on('mysql_admin')
            ->with(['user', 'schoolClass'])
            ->where('nis', $nis)
            ->firstOrFail();

        // Roster kelas mata pelajaran dari Database View: v_class_roster
        $rosters = DB::connection('mysql_admin')
            ->table('v_class_roster')
            ->where('nis', $nis)
            ->get();

        // Rekap nilai per mata pelajaran dari Database View: v_student_grade_summary
        $gradeSummaries = DB::connection('mysql_admin')
            ->table('v_student_grade_summary')
            ->where('student_id', $nis)
            ->get();

        // Rata-rata nilai per kelas mapel via Stored Function: fn_student_average
        $averages = [];
        foreach ($rosters as $r) {
            $val = DB::connection('mysql_admin')
                ->selectOne('SELECT fn_student_average(?, ?) AS avg_score', [$nis, $r->cst_id]);
            $averages[$r->cst_id] = $val?->avg_score;
        }

        return view('admin.students.show', compact('student', 'rosters', 'gradeSummaries', 'averages'));
    }

    public function update(Request $request, string $nis)
    {
        $student = Student::on('mysql_admin')->with('user')->findOrFail($nis);

        $v = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => [
                'required',
                'email',
                Rule::unique('mysql_admin.users', 'email')->ignore($student->nis, 'user_id'),
            ],
            'phone'        => 'nullable|string|max:20',
            'nisn'         => [
                'nullable',
                'digits:10',
                Rule::unique('mysql_admin.students', 'nisn')->ignore($student->nis, 'nis'),
            ],
            'class_id'     => ['nullable', 'exists:mysql_admin.classes,class_id'],
            'gender'       => 'required|in:L,P',
            'pob'          => 'nullable|string|max:50',
            'dob'          => 'nullable|date',
            'address'      => 'nullable|string|max:255',
            'district'     => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'parent_name'  => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'entry_year'   => 'nullable|digits:4',
            'status'       => ['nullable', Rule::in(['aktif', 'lulus', 'pindah', 'drop_out'])],
        ]);

        DB::connection('mysql_admin')->transaction(function () use ($v, $student) {
            $student->update([
                'nisn'         => $v['nisn'] ?? null,
                'class_id'     => $v['class_id'] ?? null,
                'gender'       => $v['gender'],
                'pob'          => $v['pob'] ?? null,
                'dob'          => $v['dob'] ?? null,
                'address'      => $v['address'] ?? null,
                'district'     => $v['district'] ?? null,
                'city'         => $v['city'] ?? null,
                'province'     => $v['province'] ?? null,
                'parent_name'  => $v['parent_name'] ?? null,
                'parent_phone' => $v['parent_phone'] ?? null,
                'entry_year'   => $v['entry_year'] ?? null,
                'status'       => $v['status'] ?? $student->status,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name'  => $v['name'],
                    'email' => $v['email'],
                    'phone' => $v['phone'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus siswa beserta akun pengguna-nya.
     *
     * Ditolak jika:
     *  1. Status siswa masih "aktif" (ubah dulu ke lulus / pindah / drop_out lewat Edit), atau
     *  2. Siswa masih terhubung dengan data lain (enrollment, nilai, ujian, dst.)
     *     sehingga database menolak penghapusan (foreign key).
     */
    public function destroy(string $nis)
    {
        try {
            $hasil = DB::connection('mysql_admin')->transaction(function () use ($nis) {
                // Kunci baris dulu, lalu cek di dalam transaksi
                $student = Student::on('mysql_admin')
                    ->with('user')
                    ->where('nis', $nis)
                    ->lockForUpdate()
                    ->first();

                if (! $student) {
                    return ['status' => 'hilang'];
                }

                $nama = $student->user->name ?? $student->nis;

                // Siswa yang masih aktif tidak boleh dihapus
                if (strtolower(trim((string) $student->status)) === 'aktif') {
                    return ['status' => 'aktif', 'nama' => $nama];
                }

                $student->delete();
                $student->user?->delete();

                return ['status' => 'ok', 'nama' => $nama];
            });
        } catch (QueryException $e) {
            // SQLSTATE 23000 = pelanggaran constraint (siswa masih dipakai tabel lain)
            if ((string) $e->getCode() === '23000') {
                return redirect()
                    ->route('admin.students.index')
                    ->with('error', 'Siswa tidak bisa dihapus karena masih terhubung dengan data lain '
                        . '(misalnya enrollment kelas, nilai, atau hasil ujian). '
                        . 'Jika sudah tidak bersekolah, ubah statusnya menjadi lulus/pindah/drop out lewat Edit.');
            }

            Log::error('Gagal menghapus siswa', ['nis' => $nis, 'error' => $e->getMessage()]);

            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Terjadi kesalahan saat menghapus siswa. Silakan coba lagi.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus siswa', ['nis' => $nis, 'error' => $e->getMessage()]);

            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Terjadi kesalahan saat menghapus siswa. Silakan coba lagi.');
        }

        if ($hasil['status'] === 'hilang') {
            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Data siswa tidak ditemukan atau sudah dihapus.');
        }

        if ($hasil['status'] === 'aktif') {
            return redirect()
                ->route('admin.students.index')
                ->with('error', "Siswa {$hasil['nama']} masih berstatus aktif sehingga tidak bisa dihapus. "
                    . 'Ubah statusnya menjadi lulus/pindah/drop out lewat Edit terlebih dahulu.');
        }

        return redirect()
            ->route('admin.students.index')
            ->with('success', "Siswa {$hasil['nama']} berhasil dihapus.");
    }
}