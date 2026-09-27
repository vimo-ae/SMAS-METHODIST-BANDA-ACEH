<?php
namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\ClassSubjectTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AttendanceController extends Controller {
 public function index(Request $request){
  $user=$request->user();
  $query=Attendance::with('student.user','classSubjectTeacher.schoolClass','classSubjectTeacher.subject','classSubjectTeacher.teacher.user');
  $csts=collect();
  if($user->role==='guru'){
   $query->whereHas('classSubjectTeacher',fn($q)=>$q->where('teacher_id',$user->teacher->nip));
   $csts=ClassSubjectTeacher::with('schoolClass.students.user','subject')->where('teacher_id',$user->teacher->nip)->get();
  } elseif($user->role==='siswa') $query->where('students_id',$user->student->nis);
  elseif($user->role==='orangtua') $query->whereIn('students_id',$user->guardianProfile->students()->pluck('students.nis'));
  $attendances=$query->latest('date')->paginate(20)->withQueryString();
  return view('attendances.index',compact('attendances','csts'));
 }
 public function store(Request $request){
  $user=$request->user(); abort_unless($user->role==='guru' && $user->teacher,403);
  $v=$request->validate(['cst_id'=>'required|exists:class_subject_teacher,id','date'=>'required|date','attendance'=>'required|array|min:1','attendance.*'=>'required|in:hadir,izin,sakit,alpha']);
  $cst=ClassSubjectTeacher::with('schoolClass')->whereKey($v['cst_id'])->where('teacher_id',$user->teacher->nip)->firstOrFail();
  $nisList=$cst->schoolClass->students()->pluck('nis')->all();
  foreach($v['attendance'] as $nis=>$status){ if(!in_array((string)$nis,$nisList,true)) abort(403,'Siswa bukan anggota kelas yang diajar guru ini.'); Attendance::updateOrCreate(['cst_id'=>$cst->id,'students_id'=>$nis,'date'=>$v['date']],['status'=>$status]); }
  return back()->with('success','Absensi berhasil disimpan.');
 }
}
