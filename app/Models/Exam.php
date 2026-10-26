<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_id';   // <-- tambahkan

    protected $fillable = [
    'cst_id', 'title', 'exam_type', 'start_time', 'end_time', 'duration_minutes',
    'pass_score', 'shuffle_questions', 'shuffle_options',
    'is_published', 'instructions',
];

protected function casts(): array
{
    return [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'pass_score' => 'float',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'is_published' => 'boolean',
    ];
}

    public function getRouteKeyName(): string
    {
        return 'exam_id';
    }

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'cst_id', 'cst_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id', 'exam_id')->orderBy('exam_question_id');   // <-- ganti dari 'id'
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'exam_id', 'exam_id');
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class, 'exam_id', 'exam_id');
    }

    public function isOpen(): bool
    {
        return $this->is_published && now()->between($this->start_time, $this->end_time);
    }

    /** Total bobot soal objektif (esai tidak berbobot). */
    public function totalWeight(): float
    {
        return (float) $this->questions->where('question_type', '!=', 'esai')->sum('score_weight');
    }

    public function isComplete(): bool
    {
        return $this->questions()->exists();
    }
}