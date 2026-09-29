<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // Primary key users adalah user_id
    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // User -> Student
    // students.nis = users.user_id
    public function student()
    {
        return $this->hasOne(
            Student::class,
            'nis',
            'user_id'
        );
    }

    // User -> Teacher
    // teachers.nip = users.user_id
    public function teacher()
    {
        return $this->hasOne(
            Teacher::class,
            'nip',
            'user_id'
        );
    }

    // User -> Parent
    // parents.nik = users.user_id
    public function guardianProfile()
    {
        return $this->hasOne(
            Guardian::class,
            'nik',
            'user_id'
        );
    }

    public function announcements()
    {
        return $this->hasMany(
            Announcement::class,
            'created_by',
            'user_id'
        );
    }

    public function forumReplies()
    {
        return $this->hasMany(
            ForumReply::class,
            'user_id',
            'user_id'
        );
    }

    public function notifications()
    {
        return $this->hasMany(
            Notification::class,
            'user_id',
            'user_id'
        );
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array(
            $this->role,
            ['superadmin', 'admin'],
            true
        );
    }
}