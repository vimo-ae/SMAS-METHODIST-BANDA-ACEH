<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::on('mysql_admin')->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teachers = $query->orderBy('nip')->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:mysql_admin.users,email',
            'phone'           => 'nullable|string|max:20',
            'nip'             => 'required|string|max:30|unique:mysql_admin.teachers,nip|unique:mysql_admin.users,user_id',
            'specialization'  => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:255',
            'hire_date'       => 'nullable|date',
            'status'          => ['nullable', Rule::in(['aktif', 'cuti', 'pensiun', 'resign'])],
        ]);

        DB::connection('mysql_admin')->transaction(function () use ($v) {
            User::on('mysql_admin')->create([
                'user_id'  => $v['nip'],
                'name'     => $v['name'],
                'email'    => $v['email'],
                'phone'    => $v['phone'] ?? null,
                'password' => Hash::make('password123'),
                'role'     => 'guru',
            ]);

            Teacher::on('mysql_admin')->create([
                'nip'             => $v['nip'],
                'specialization'  => $v['specialization'] ?? null,
                'education_level' => $v['education_level'] ?? null,
                'address'         => $v['address'] ?? null,
                'hire_date'       => $v['hire_date'] ?? null,
                'status'          => $v['status'] ?? 'aktif',
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan. Password default: password123');
    }

    public function edit(string $nip)
    {
        $teacher = Teacher::on('mysql_admin')->with('user')->findOrFail($nip);

        $teachingSchedules = DB::connection('mysql_admin')
            ->table('v_teaching_schedule')
            ->where('teacher_id', $nip)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('admin.teachers.edit', compact('teacher', 'teachingSchedules'));
    }

    public function update(Request $request, string $nip)
    {
        $teacher = Teacher::on('mysql_admin')->with('user')->findOrFail($nip);

        $v = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => [
                'required',
                'email',
                Rule::unique('mysql_admin.users', 'email')->ignore($teacher->nip, 'user_id'),
            ],
            'phone'           => 'nullable|string|max:20',
            'specialization'  => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:255',
            'hire_date'       => 'nullable|date',
            'status'          => ['nullable', Rule::in(['aktif', 'cuti', 'pensiun', 'resign'])],
        ]);

        DB::connection('mysql_admin')->transaction(function () use ($v, $teacher) {
            $teacher->update([
                'specialization'  => $v['specialization'] ?? null,
                'education_level' => $v['education_level'] ?? null,
                'address'         => $v['address'] ?? null,
                'hire_date'       => $v['hire_date'] ?? null,
                'status'          => $v['status'] ?? $teacher->status,
            ]);

            if ($teacher->user) {
                $teacher->user->update([
                    'name'  => $v['name'],
                    'email' => $v['email'],
                    'phone' => $v['phone'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus guru beserta akun pengguna-nya.
     *
     * Ditolak jika:
     *  1. Status guru masih "aktif" (ubah dulu ke cuti / pensiun / resign lewat Edit), atau
     *  2. Guru masih terhubung dengan data lain (kelas mapel, wali kelas,
     *     nilai, jawaban ujian, pengumuman).
     */
    public function destroy(string $nip)
    {
        try {
            $hasil = DB::connection('mysql_admin')->transaction(function () use ($nip) {
                // Kunci baris dulu, lalu cek di dalam transaksi
                $teacher = Teacher::on('mysql_admin')
                    ->with('user')
                    ->where('nip', $nip)
                    ->lockForUpdate()
                    ->first();

                if (! $teacher) {
                    return ['status' => 'hilang'];
                }

                $nama = $teacher->user->name ?? $teacher->nip;

                // Guru yang masih aktif tidak boleh dihapus
                if (strtolower(trim((string) $teacher->status)) === 'aktif') {
                    return ['status' => 'aktif', 'nama' => $nama];
                }

                $terpakai = $this->blockers($teacher->nip);

                if (! empty($terpakai)) {
                    return [
                        'status' => 'ditolak',
                        'nama'   => $nama,
                        'detail' => collect($terpakai)
                            ->map(fn ($jumlah, $label) => "{$jumlah} {$label}")
                            ->implode(', '),
                    ];
                }

                $teacher->delete();
                $teacher->user?->delete();

                return ['status' => 'ok', 'nama' => $nama];
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }

        if ($hasil['status'] === 'hilang') {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Data guru tidak ditemukan atau sudah dihapus.');
        }

        if ($hasil['status'] === 'aktif') {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', "Guru {$hasil['nama']} masih berstatus aktif sehingga tidak bisa dihapus. "
                    . 'Ubah statusnya menjadi cuti/pensiun/resign lewat Edit terlebih dahulu.');
        }

        if ($hasil['status'] === 'ditolak') {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', "Guru {$hasil['nama']} tidak bisa dihapus karena masih terhubung dengan {$hasil['detail']}. "
                    . 'Jika sudah tidak bertugas, ubah statusnya menjadi cuti/pensiun/resign lewat Edit.');
        }

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', "Guru {$hasil['nama']} berhasil dihapus.");
    }

    /**
     * Data yang menghalangi penghapusan guru. Mengembalikan [label => jumlah] untuk yang > 0.
     * (hapus baris yang tidak ingin ikut memblokir)
     */
    private function blockers(string $nip): array
    {
        $conn = DB::connection('mysql_admin');

        return array_filter([
            'kelas mapel yang diampu'     => $conn->table('class_subject_teacher')->where('teacher_id', $nip)->count(),
            'kelas sebagai wali kelas'    => $conn->table('classes')->where('homeroom_teacher_id', $nip)->count(),
            'nilai yang diinput'          => $conn->table('grades')->where('graded_by', $nip)->count(),
            'jawaban ujian yang dinilai'  => $conn->table('exam_answers')->where('graded_by', $nip)->count(),
            'pengumuman yang dibuat'      => $conn->table('announcements')->where('created_by', $nip)->count(),
        ]);
    }
}