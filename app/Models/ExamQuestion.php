<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Format options / correct_answer (JSON) menurut tipe:
 *  pilihan_ganda          options: ["A teks","B teks",...]            correct: 1 indeks (mis. 2)
 *  pilihan_ganda_kompleks options: [...]                              correct: [0,2]
 *  benar_salah            options: ["pernyataan 1","pernyataan 2"]    correct: [true,false]
 *  menjodohkan            options: {"left":[...],"right":[...]}       correct: {"0":1,"1":0}
 *  esai                   -                                           correct: null (tanpa bobot)
 */
class ExamQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_question_id';

    protected $fillable = [
        'exam_id', 'question_text', 'question_type', 'options', 'image_path',
        'correct_answer', 'score_weight',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'score_weight' => 'float',
        ];
    }

    // Dipakai kalau ada route seperti /questions/{question}
    public function getRouteKeyName(): string
    {
        return 'exam_question_id';
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id', 'exam_question_id');
    }

    public function isEssay(): bool
    {
        return $this->question_type === 'esai';
    }
}
