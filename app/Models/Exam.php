<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = ['cst_id', 'title', 'type', 'start_time', 'end_time', 'duration_minutes'];

    protected function casts(): array
    {
        return ['start_time' => 'datetime', 'end_time' => 'datetime'];
    }

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'cst_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
