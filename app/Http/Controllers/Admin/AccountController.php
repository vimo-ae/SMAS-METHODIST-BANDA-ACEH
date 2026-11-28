<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::on('mysql_admin')
            ->select('users.*')
            ->selectRaw("
                CASE
                    WHEN users.role = 'siswa' THEN (
                        SELECT AVG(fn_student_average(cs.students_id, cs.cst_id))
                        FROM cst_students cs
                        WHERE cs.students_id = users.user_id
                    )
                    ELSE NULL
                END AS student_avg_score
            ")
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('user_id', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.accounts.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|string|max:30|unique:mysql_admin.users,user_id',
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:mysql_admin.users,email',
            'role'     => 'required|in:admin,guru,siswa,orangtua',
            'password' => 'required|min:6',
        ], [
            'user_id.unique' => 'User ID ini sudah terdaftar.',
            'email.unique'   => 'Email ini sudah digunakan.',
        ]);

        if ($request->role === 'siswa' && strlen($request->user_id) > 10) {
            return back()->withErrors(['user_id' => 'User ID (NIS) untuk role Siswa maksimal 10 karakter.'])->withInput();
        }

        try {
            DB::connection('mysql_admin')->beginTransaction();

            User::on('mysql_admin')->create([
                'user_id'  => $request->user_id,
                'name'     => $request->name,
                'email'    => $request->email,
                'role'     => $request->role,
                'password' => Hash::make($request->password),
            ]);

            if ($request->role === 'guru') {
                DB::connection('mysql_admin')->table('teachers')->insert([
                    'nip'        => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($request->role === 'siswa') {
                DB::connection('mysql_admin')->table('students')->insert([
                    'nis' => $request->user_id,
                ]);
            }

            DB::connection('mysql_admin')->commit();

            return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::connection('mysql_admin')->rollBack();

            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::on('mysql_admin')->where('user_id', $id)->firstOrFail();

        $gradeSummary = $user->role === 'siswa'
            ? DB::connection('mysql_admin')->table('cst_students as cs')
                ->join('class_subject_teacher as cst', 'cst.cst_id', '=', 'cs.cst_id')
                ->join('subjects as s', 's.code', '=', 'cst.subject_code')
                ->join('classes as c', 'c.class_id', '=', 'cst.class_id')
                ->where('cs.students_id', $user->user_id)
                ->select('s.name as subject_name', 'c.name as class_name')
                ->selectRaw('fn_student_average(cs.students_id, cs.cst_id) AS final_score')
                ->get()
            : collect();

        return view('admin.accounts.edit', compact('user', 'gradeSummary'));
    }

    public function editPassword(string $id)
    {
        $user = User::on('mysql_admin')
            ->whereIn('role', ['guru', 'siswa'])
            ->where('user_id', $id)
            ->firstOrFail();

        return view('admin.accounts.password', compact('user'));
    }

    public function resetPassword(Request $request, string $id)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:128', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.max'       => 'Password baru maksimal 128 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = User::on('mysql_admin')
            ->whereIn('role', ['guru', 'siswa'])
            ->where('user_id', $id)
            ->firstOrFail();

        $user->password = Hash::make($validated['password']);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return redirect()
            ->route('admin.accounts.password.edit', $user->user_id)
            ->with('success', 'Password akun berhasil direset. Sampaikan password baru kepada pengguna melalui jalur yang aman.');
    }

    public function update(Request $request, $id)
    {
        $user = User::on('mysql_admin')->where('user_id', $id)->firstOrFail();

        $request->validate([
            'user_id'  => ['required', 'string', 'max:30', Rule::unique('mysql_admin.users', 'user_id')->ignore($user->user_id, 'user_id')],
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:255', Rule::unique('mysql_admin.users', 'email')->ignore($user->user_id, 'user_id')],
            'role'     => 'required|in:admin,guru,siswa,orangtua',
            'password' => 'nullable|min:6',
        ], [
            'user_id.unique' => 'User ID ini sudah terdaftar.',
            'email.unique'   => 'Email ini sudah digunakan.',
        ]);

        if ($request->role === 'siswa' && strlen($request->user_id) > 10) {
            return back()->withErrors(['user_id' => 'User ID (NIS) untuk role Siswa maksimal 10 karakter.'])->withInput();
        }

        try {
            DB::connection('mysql_admin')->beginTransaction();

            $oldRole = $user->role;
            $oldId   = $user->user_id;

            $user->user_id = $request->user_id;
            $user->name    = $request->name;
            $user->email   = $request->email;
            $user->role    = $request->role;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($request->role === 'guru') {
                DB::connection('mysql_admin')->table('teachers')->updateOrInsert(
                    ['nip' => $user->user_id],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            } elseif ($request->role === 'siswa') {
                DB::connection('mysql_admin')->table('students')->updateOrInsert(
                    ['nis' => $user->user_id],
                    ['nis' => $user->user_id]
                );
            }

            if ($oldRole !== $request->role) {
                if ($oldRole === 'guru') {
                    DB::connection('mysql_admin')->table('teachers')->where('nip', $oldId)->delete();
                } elseif ($oldRole === 'siswa') {
                    DB::connection('mysql_admin')->table('students')->where('nis', $oldId)->delete();
                }
            }

            DB::connection('mysql_admin')->commit();

            return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::connection('mysql_admin')->rollBack();

            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $userId)
    {
        $conn = DB::connection('mysql_admin');
        $user = $conn->table('users')->where('user_id', $userId)->first();

        if (! $user) {
            return back()->with('error', 'Akun pengguna tidak ditemukan.');
        }

        if ($user->role === 'siswa') {
            $student = $conn->table('students')
                ->where('user_id', $userId)
                ->orWhere('nis', $userId)
                ->first();

            $status = strtolower((string) ($student->status ?? ''));
            if (in_array($status, ['aktif', 'active', '1'])) {
                return back()->with('error', "Akun siswa {$user->name} masih berstatus aktif sehingga tidak dapat dihapus.");
            }
        }

        $conn->table('users')->where('user_id', $userId)->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun {$user->name} berhasil dihapus.");
    }
}