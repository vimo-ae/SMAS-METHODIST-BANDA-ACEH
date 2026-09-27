<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Student extends Model { use HasFactory;
 protected $primaryKey='nis'; public $incrementing=false; protected $keyType='string';
 protected $fillable=['nis','class_id','gender','birth_date','address'];
 protected function casts():array{return ['birth_date'=>'date'];}
 public function user(){return $this->belongsTo(User::class,'nis','academic_key');}
 public function schoolClass(){return $this->belongsTo(SchoolClass::class,'class_id');}
 public function guardians(){return $this->belongsToMany(Guardian::class,'parent_student','students_id','parents_id','nis','nik');}
 public function submissions(){return $this->hasMany(AssignmentSubmission::class,'students_id','nis');}
 public function examAnswers(){return $this->hasMany(ExamAnswer::class,'students_id','nis');}
 public function attendances(){return $this->hasMany(Attendance::class,'students_id','nis');}
 public function grades(){return $this->hasMany(Grade::class,'students_id','nis');}
}
