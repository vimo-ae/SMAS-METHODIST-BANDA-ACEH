<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $primaryKey = 'exam_session_id';   // <-- tambahkan

    protected $fillable = [
        'exam_id', 'students_id', 'face_photo', 'started_at', 'deadline_at', 'finished_at',
        'status', 'objective_score', 'essay_score', 'violation_count', 'teacher_note',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'deadline_at' => 'datetime',
            'finished_at' => 'datetime',
            'objective_score' => 'float',
            'essay_score' => 'float',
        ];
    }

    // Dipakai kalau ada route seperti /sessions/{session}
    public function getRouteKeyName(): string
    {
        return 'exam_session_id';
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'exam_session_id', 'exam_session_id');   // <-- diperjelas
    }

    public function isRunning(): bool
    {
        return $this->status === 'berlangsung' && $this->deadline_at && now()->lt($this->deadline_at);
    }
}