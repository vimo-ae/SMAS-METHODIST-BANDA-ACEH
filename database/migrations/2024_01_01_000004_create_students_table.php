<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void { Schema::create('students',function(Blueprint $table){ $table->string('nis')->primary(); $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete(); $table->enum('gender',['L','P']); $table->date('birth_date')->nullable(); $table->text('address')->nullable(); $table->timestamps(); }); } public function down():void{Schema::dropIfExists('students');} };
