<?php
namespace Database\Seeders; use App\Models\User; use Illuminate\Database\Seeder; use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder { public function run():void { foreach([['superadmin@methodistbandaaceh.sch.id','Super Admin','superadmin',null],['admin@methodistbandaaceh.sch.id','Admin Sekolah','admin',null]] as $a){User::firstOrCreate(['email'=>$a[0]],['name'=>$a[1],'password'=>Hash::make('password123'),'role'=>$a[2],'academic_key'=>$a[3]]);} } }
