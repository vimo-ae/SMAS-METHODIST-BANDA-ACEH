<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportNote extends Model
{
    protected $fillable = ['students_id', 'semester', 'academic_year', 'attitude', 'note'];
}
