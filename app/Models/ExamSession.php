<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    protected $primaryKey = 'exam_session_id';

    protected $fillable = [
        'exam_id', 'students_id', 'token', 'face_photo',
        'started_at', 'finished_at', 'left_at', 'status',
    ];

    protected static function booted(): void
    {
        // token dibuat otomatis saat sesi dibuat
        static::creating(function (self $session) {
            $session->token ??= Str::random(40);
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'left_at' => 'datetime',
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
        return $this->hasMany(ExamViolation::class, 'exam_session_id', 'exam_session_id');
    }

    // Batas waktu memakai end_time ujian, bukan deadline_at
    public function isRunning(): bool
    {
        return $this->status === 'berlangsung'
            && $this->exam
            && now()->lt($this->exam->end_time);
    }
}