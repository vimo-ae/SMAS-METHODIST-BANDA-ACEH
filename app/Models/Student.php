<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    // Primary key students = nis
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'nisn',
        'class_id',
        'gender',
        'birth_date',
        'pob',
        'dob',
        'address',
        'district',
        'city',
        'province',
        'parent_name',
        'parent_phone',
        'entry_year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'dob' => 'date',
            'entry_year' => 'integer',
        ];
    }

    // students.nis → users.user_id
    public function user()
    {
        return $this->hasOne(
            User::class,
            'user_id',
            'nis'
        );
    }

    // students.class_id → classes.class_id
    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'class_id',
            'class_id'
        );
    }

    public function submissions()
    {
        return $this->hasMany(
            AssignmentSubmission::class,
            'students_id',
            'nis'
        );
    }

    public function examAnswers()
    {
        return $this->hasMany(
            ExamAnswer::class,
            'students_id',
            'nis'
        );
    }

    public function grades()
    {
        return $this->hasMany(
            Grade::class,
            'students_id',
            'nis'
        );
    }

    // Kelas mata pelajaran yang diikuti (join kelas guru mapel)
    public function joinedClasses()
    {
        return $this->belongsToMany(
            ClassSubjectTeacher::class,
            'cst_students',
            'students_id',
            'cst_id',
            'nis',
            'cst_id'
        )->withPivot('joined_at');
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'students_id', 'nis');
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? (string) $this->nis;
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'students_id', 'nis');
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'students_id', 'nis');
    }
}