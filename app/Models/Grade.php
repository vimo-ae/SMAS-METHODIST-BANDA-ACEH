<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'students_id',
        'subject_code',
        'semester',
        'academic_year',
        'grade_type',
        'score',
    ];

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'students_id',
            'nis'
        );
    }

    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_code',
            'code'
        );
    }
}