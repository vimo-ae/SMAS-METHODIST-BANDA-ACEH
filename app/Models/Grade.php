<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $fillable = [
        'students_id', 'subject_code', 'semester', 'academic_year', 'grade_type', 'score',
    ];

    protected function casts(): array
    {
        return ['score' => 'float'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }
}
