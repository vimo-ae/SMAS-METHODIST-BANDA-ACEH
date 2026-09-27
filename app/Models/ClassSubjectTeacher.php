<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ClassSubjectTeacher extends Model { use HasFactory;
 protected $table='class_subject_teacher'; protected $fillable=['class_id','subject_code','teacher_id','academic_year','semester'];
 public function schoolClass(){return $this->belongsTo(SchoolClass::class,'class_id');}
 public function subject(){return $this->belongsTo(Subject::class,'subject_code','code');}
 public function teacher(){return $this->belongsTo(Teacher::class,'teacher_id','nip');}
 public function schedules(){return $this->hasMany(Schedule::class,'cst_id');}
 public function materials(){return $this->hasMany(Material::class,'cst_id');}
 public function assignments(){return $this->hasMany(Assignment::class,'cst_id');}
 public function exams(){return $this->hasMany(Exam::class,'cst_id');}
 public function attendances(){return $this->hasMany(Attendance::class,'cst_id');}
 public function forumThreads(){return $this->hasMany(ForumThread::class,'cst_id');}
}
