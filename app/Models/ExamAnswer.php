<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
    'exam_id', 'students_id', 'question_id', 'answer', 'answer_file',
    'is_correct', 'score', 'graded_by', 'graded_at',
];

protected function casts(): array
{
    return [
        'answer' => 'array',
        'is_correct' => 'boolean',
        'score' => 'float',
        'graded_at' => 'datetime',
    ];
}

public function grader()
{
    return $this->belongsTo(Teacher::class, 'graded_by', 'nip');
}

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id','exam_question_id');
    }
}
