<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignments';

    protected $primaryKey = 'assignment_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'assignment_id',
        'cst_id',
        'title',
        'description',
        'due_date',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function classSubjectTeacher()
    {
        return $this->belongsTo(
            ClassSubjectTeacher::class,
            'cst_id',
            'cst_id'
        );
    }
}