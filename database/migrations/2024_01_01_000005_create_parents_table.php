<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void { Schema::create('parents',function(Blueprint $table){ $table->string('nik')->primary(); $table->string('relationship')->nullable(); $table->string('occupation')->nullable(); $table->text('address')->nullable(); $table->timestamps(); }); } public function down():void{Schema::dropIfExists('parents');} };
