<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSubjectTeacher extends Model
{
    use HasFactory;

    protected $table = 'class_subject_teacher';

    protected $primaryKey = 'cst_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'cst_id',
        'class_id',
        'subject_code',
        'teacher_id',
        'academic_year',
        'semester',
        'join_code',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'class_id',
            'class_id'
        );
    }

    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_code',
            'code'
        );
    }

    public function teacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'teacher_id',
            'nip'
        );
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'cst_id', 'cst_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'cst_id', 'cst_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'cst_id', 'cst_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'cst_id', 'cst_id');
    }

    // Siswa yang sudah bergabung lewat kode kelas
    public function joinedStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'cst_students',
            'cst_id',
            'students_id',
            'cst_id',
            'nis'
        )->withPivot('joined_at');
    }

    public function label(): string
    {
        return ($this->subject->name ?? $this->subject_code).' — '.($this->schoolClass->name ?? '-');
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'cst_id', 'cst_id');
    }
}
