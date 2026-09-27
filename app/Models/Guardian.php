<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Guardian extends Model { use HasFactory;
 protected $table='parents'; protected $primaryKey='nik'; public $incrementing=false; protected $keyType='string'; protected $fillable=['nik','relationship','occupation','address'];
 public function user(){return $this->belongsTo(User::class,'nik','academic_key');}
 public function students(){return $this->belongsToMany(Student::class,'parent_student','parents_id','students_id','nik','nis');}
}
