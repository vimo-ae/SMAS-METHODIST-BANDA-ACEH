<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil sesuai role pengguna.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $connectionName = $this->connectionForRole($user->role);

        if ($user->role === 'siswa') {
            $student = Student::on($connectionName)
                ->with('schoolClass')
                ->where('nis', $user->user_id)
                ->first();

            return view('profile.student', compact('user', 'student'));
        }

        if ($user->role === 'guru') {
            $teacher = Teacher::on($connectionName)
                ->where('nip', $user->user_id)
                ->first();

            return view('profile.teacher', compact('user', 'teacher'));
        }

        // Halaman profil lama tetap dipakai untuk Admin.
        return view('profile.edit', [
            'user' => $user,
            'profile' => null,
            'isStudent' => false,
        ]);
    }

    /**
     * Simpan data profil sesuai hak akses masing-masing role.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $connectionName = $this->connectionForRole($user->role);

        $user->setConnection($connectionName);

        if ($user->role === 'siswa') {

            $student = Student::on($connectionName)
                ->where('nis', $user->user_id)
                ->firstOrFail();

            DB::connection($connectionName)->transaction(function () use ($user, $student, $data) {

                $this->updateAccountContact($user, $data);

                foreach ([
                    'address',
                    'district',
                    'city',
                    'province',
                    'parent_name',
                    'parent_phone',
                ] as $field) {
                    if (array_key_exists($field, $data)) {
                        $student->{$field} = $data[$field];
                    }
                }

                $student->save();
            });

        } elseif ($user->role === 'guru') {

            $teacher = Teacher::on($connectionName)
                ->where('nip', $user->user_id)
                ->firstOrFail();

            DB::connection($connectionName)->transaction(function () use ($user, $teacher, $data) {

                $this->updateAccountContact($user, $data);

                if (array_key_exists('address', $data)) {
                    $teacher->address = $data['address'];
                }

                $teacher->save();
            });

        } else {

            // Perilaku profil Admin tetap dipertahankan.
            $user->name = $data['name'];
            $user->email = $data['email'];

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Perbarui email dan nomor HP akun.
     */
    private function updateAccountContact($user, array $data): void
    {
        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        }

        if (array_key_exists('phone', $data)) {
            $user->phone = $data['phone'];
        }

        $user->save();
    }

    /**
     * Koneksi database berdasarkan role.
     */
    private function connectionForRole(string $role): string
    {
        return match ($role) {
            'admin' => 'mysql_admin',
            'guru' => 'mysql_guru',
            'siswa' => 'mysql_siswa',
            default => config('database.default'),
        };
    }

    /**
     * Siswa dan guru tidak boleh menghapus akun sendiri.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(
            in_array($user->role, ['siswa', 'guru'], true),
            403,
            'Akun siswa dan guru hanya dapat dikelola administrator sekolah.'
        );

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user->setConnection($this->connectionForRole($user->role));

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}