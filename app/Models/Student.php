<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'nis';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'class_id',
        'gender',
        'birth_date',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    // students.nis → users.academic_key
    public function user()
    {
        return $this->hasOne(
            User::class,
            'academic_key',
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

    public function guardians()
    {
        return $this->belongsToMany(
            Guardian::class,
            'parent_student',
            'students_id',
            'parents_id',
            'nis',
            'nik'
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

    public function attendances()
    {
        return $this->hasMany(
            Attendance::class,
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
}