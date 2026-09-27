<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Subject extends Model { use HasFactory;
 protected $primaryKey='code'; public $incrementing=false; protected $keyType='string'; protected $fillable=['code','name'];
 public function classSubjectTeachers(){return $this->hasMany(ClassSubjectTeacher::class,'subject_code','code');}
 public function grades(){return $this->hasMany(Grade::class,'subject_code','code');}
}
