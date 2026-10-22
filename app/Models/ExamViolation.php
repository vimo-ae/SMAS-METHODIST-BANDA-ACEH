<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    protected $primaryKey = 'exam_violation_id';

    public $timestamps = false;

    protected $fillable = ['exam_session_id', 'students_id', 'type', 'detail', 'occurred_at'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id', 'exam_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }
}