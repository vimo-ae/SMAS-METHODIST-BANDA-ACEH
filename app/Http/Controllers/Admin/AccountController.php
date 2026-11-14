<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('user_id', 'like', "%{$search}%");
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
        // Validasi disesuaikan dengan skema database
        $request->validate([
            'user_id'  => 'required|string|max:18|unique:users,user_id', // max varchar(18)
            'name'     => 'required|string|max:100', // max varchar(100)
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => 'required|in:admin,guru,siswa,orangtua', // enum role
            'password' => 'required|min:6',
        ]);

        // Cek khusus untuk siswa karena field nis di tabel students adalah varchar(10)
        if ($request->role === 'siswa' && strlen($request->user_id) > 10) {
            return back()->withErrors(['user_id' => 'User ID (NIS) untuk role Siswa maksimal 10 karakter.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Buat data User
            User::create([
                'user_id'  => $request->user_id,
                'name'     => $request->name,
                'email'    => $request->email,
                'role'     => $request->role,
                'password' => Hash::make($request->password),
            ]);

            // 2. Inisialisasi data relasi (Guru/Siswa) agar tidak error saat ditarik relasinya
            if ($request->role === 'guru') {
                DB::table('teachers')->insert([
                    'nip' => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($request->role === 'siswa') {
                DB::table('students')->insert([
                    'nis' => $request->user_id,
                    // Karena nisn, class_id dll mungkin butuh nilai default/nullable,
                    // pastikan field tersebut di database diperbolehkan NULL (nullable)
                ]);
            }

            DB::commit();
            return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();
        return view('admin.accounts.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        $request->validate([
            'user_id'  => 'required|string|max:18|unique:users,user_id,' . $user->user_id . ',user_id',
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'role'     => 'required|in:admin,guru,siswa,orangtua',
            'password' => 'nullable|min:6',
        ]);

        if ($request->role === 'siswa' && strlen($request->user_id) > 10) {
            return back()->withErrors(['user_id' => 'User ID (NIS) untuk role Siswa maksimal 10 karakter.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Update data user
            $user->user_id = $request->user_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();
            return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id) // Tambahkan 'string' di sini
    {
        $user = User::where('user_id', $id)->firstOrFail();
        
        // Gunakan Auth::id() langsung (VS Code sangat mengenali Facade ini)
        if ($user->user_id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            DB::beginTransaction();

            if ($user->role === 'guru') {
                DB::table('teachers')->where('nip', $user->user_id)->delete();
            } elseif ($user->role === 'siswa') {
                DB::table('students')->where('nis', $user->user_id)->delete();
            }

            $user->delete();

            DB::commit();
            return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}