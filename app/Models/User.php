<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    public const ROLES = ['admin', 'guru', 'siswa'];

    protected $table = 'users';

    // user_id diisi manual sesuai NIP / NIS / NIK
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 'name', 'email', 'password', 'role', 'phone', 'photo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'nis', 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'nip', 'user_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function roleLabel(): string
    {
        return ['admin' => 'Administrator', 'guru' => 'Guru', 'siswa' => 'Siswa', 'orangtua' => 'Orang Tua'][$this->role] ?? $this->role;
    }
}
