<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Announcement extends Model
{
    protected $table = 'announcements';

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'content',
        'target_roles',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
