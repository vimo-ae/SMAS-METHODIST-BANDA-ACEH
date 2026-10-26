<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    protected $primaryKey = 'enrollment_id';

    protected $fillable = ['students_id', 'cst_id', 'academic_year', 'status', 'enrolled_at', 'left_at'];

    protected function casts(): array
    {
        return ['enrolled_at' => 'date', 'left_at' => 'date'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'cst_id', 'cst_id');
    }
}