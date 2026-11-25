<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ExamToken extends Model
{
    protected $table = 'exam_token';
    protected $primaryKey = 'exam_token_id';

    protected $fillable = [
        'exam_id',
        'token_hash',
        'token_cipher',
        'issued_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function plainToken(): string
    {
        return (string) Crypt::decryptString($this->token_cipher);
    }
}
