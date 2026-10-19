<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentMessage extends Model
{
    protected $fillable = [
        'parent_user_id', 'teacher_user_id', 'students_id', 'type', 'subject',
        'message', 'reply', 'replied_by', 'replied_at',
    ];

    protected function casts(): array
    {
        return ['replied_at' => 'datetime'];
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id', 'user_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_user_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id', 'nis');
    }
}
