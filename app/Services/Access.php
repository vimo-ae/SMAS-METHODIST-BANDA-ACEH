<?php

namespace App\Services;

use App\Models\ClassSubjectTeacher;
use App\Models\User;

/** Aturan akses ke kelas-mapel (cst) untuk setiap role. */
class Access
{
    public static function cst(User $user, ClassSubjectTeacher $cst): bool
    {
        return match ($user->role) {
            'admin' => true,
            'guru' => $user->teacher && (string) $cst->teacher_id === (string) $user->teacher->nip,
            'siswa' => $user->student && $cst->joinedStudents()->where('students.nis', $user->student->nis)->exists(),
            default => false,
        };
    }

    public static function authorizeCst(User $user, ClassSubjectTeacher $cst): void
    {
        abort_unless(self::cst($user, $cst), 403, 'Anda tidak memiliki akses ke kelas ini.');
    }

    /** Hanya guru pengampu yang boleh mengelola. */
    public static function authorizeTeacher(User $user, ClassSubjectTeacher $cst): void
    {
        abort_unless($user->role === 'guru' && $user->teacher && (string) $cst->teacher_id === (string) $user->teacher->nip, 403, 'Hanya guru pengampu yang dapat mengakses.');
    }
}
