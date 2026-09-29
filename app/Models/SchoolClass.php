<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Nama kelas PHP tidak boleh "Class" (reserved word), tabelnya tetap "classes".
class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $primaryKey = 'class_id';

    public $incrementing = false;
    
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'homeroom_teacher_id',
        'academic_year',
    ];

    public function homeroomTeacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'homeroom_teacher_id',
            'nip'
        );
    }

    public function students()
    {
        return $this->hasMany(
            Student::class,
            'class_id',
            'class_id'
        );
    }

    public function classSubjectTeachers()
    {
        return $this->hasMany(
            ClassSubjectTeacher::class,
            'class_id',
            'class_id'
        );
    }
}