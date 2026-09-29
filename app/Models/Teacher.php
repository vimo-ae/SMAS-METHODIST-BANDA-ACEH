<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'specialization',
    ];

    public function user()
    {
        return $this->hasOne(
            User::class,
            'academic_key',
            'nip'
        );
    }

    public function homeroomClasses()
    {
        return $this->hasMany(
            SchoolClass::class,
            'homeroom_teacher_id',
            'nip'
        );
    }

    public function classSubjectTeachers()
    {
        return $this->hasMany(
            ClassSubjectTeacher::class,
            'teacher_id',
            'nip'
        );
    }
}