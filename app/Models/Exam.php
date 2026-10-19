<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_id';   // <-- tambahkan

    protected $fillable = [
        'cst_id', 'title', 'type', 'start_time', 'end_time', 'duration_minutes',
        'total_questions', 'is_published', 'require_face_photo', 'instructions',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_published' => 'boolean',
            'require_face_photo' => 'boolean',
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
        return $this->questions()->count() === (int) $this->total_questions;
    }
}