<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable; use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class User extends Authenticatable { use HasFactory, Notifiable; protected $fillable=['name','email','password','role','academic_key','phone','avatar']; protected $hidden=['password','remember_token']; protected function casts():array{return ['email_verified_at'=>'datetime'];}
 public function student(){return $this->hasOne(Student::class,'nis','academic_key');} public function teacher(){return $this->hasOne(Teacher::class,'nip','academic_key');} public function guardianProfile(){return $this->hasOne(Guardian::class,'nik','academic_key');}
 public function announcements(){return $this->hasMany(Announcement::class,'created_by');} public function forumReplies(){return $this->hasMany(ForumReply::class); } public function notifications(){return $this->hasMany(Notification::class);}
 public function isSuperAdmin():bool{return $this->role==='superadmin';} public function isAdmin():bool{return in_array($this->role,['superadmin','admin'],true);} }
