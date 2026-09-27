<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Attendance extends Model { use HasFactory; protected $fillable=['cst_id','students_id','date','status','note']; protected function casts():array{return ['date'=>'date'];} public function classSubjectTeacher(){return $this->belongsTo(ClassSubjectTeacher::class,'cst_id');} public function student(){return $this->belongsTo(Student::class,'students_id','nis');} }
