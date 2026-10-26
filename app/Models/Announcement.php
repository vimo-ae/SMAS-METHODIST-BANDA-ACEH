<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'target_roles', 'created_by'];

    protected function casts(): array
    {
        return ['target_roles' => 'array'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
