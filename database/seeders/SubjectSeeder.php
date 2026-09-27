<?php
namespace Database\Seeders; use App\Models\Subject; use Illuminate\Database\Seeder;
class SubjectSeeder extends Seeder { public function run():void { foreach([['code'=>'MTK','name'=>'Matematika'],['code'=>'FIS','name'=>'Fisika'],['code'=>'BIO','name'=>'Biologi'],['code'=>'KIM','name'=>'Kimia'],['code'=>'BIN','name'=>'Bahasa Indonesia'],['code'=>'BIG','name'=>'Bahasa Inggris'],['code'=>'SEJ','name'=>'Sejarah'],['code'=>'EKO','name'=>'Ekonomi'],['code'=>'GEO','name'=>'Geografi'],['code'=>'PKN','name'=>'PPKn']] as $s){Subject::updateOrCreate(['code'=>$s['code']],$s);} } }
