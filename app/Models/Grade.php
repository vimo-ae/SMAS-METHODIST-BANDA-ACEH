<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $primaryKey = 'grade_id';

    protected $fillable = [
        'students_id', 'cst_id', 'grade_type', 'assignment_id', 'exam_id',
        'score', 'graded_by', 'graded_at',
    ];

    protected function casts(): array
    {
        return ['score' => 'float', 'graded_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(Student::class, 'students_id', 'nis'); }
    public function classSubjectTeacher() { return $this->belongsTo(ClassSubjectTeacher::class, 'cst_id', 'cst_id'); }
    public function assignment() { return $this->belongsTo(Assignment::class, 'assignment_id', 'assignment_id'); }
    public function exam() { return $this->belongsTo(Exam::class, 'exam_id', 'exam_id'); }
    public function grader() { return $this->belongsTo(Teacher::class, 'graded_by', 'nip'); }
}