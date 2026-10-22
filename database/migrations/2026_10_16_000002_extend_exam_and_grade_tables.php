<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perluasan tabel yang sudah ada (tanpa mengubah kunci utama / relasi lama).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Mapel di kelas: kode untuk "join kelas" oleh siswa
        Schema::table('class_subject_teacher', function (Blueprint $table) {
            $table->string('join_code', 8)->nullable()->unique()->after('semester');
        });

        // Jawaban: skor desimal + foto lembar esai
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->decimal('score', 6, 2)->nullable()->change();
            $table->string('answer_file')->nullable()->after('answer');
            $table->boolean('is_correct')->nullable()->after('answer_file');
        });

        // Rapor: tipe nilai fleksibel (tugas, kuis, uts, uas, rapor)
        Schema::table('grades', function (Blueprint $table) {
            $table->string('grade_type', 10)->change();
        });

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->text('body')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('forum_threads', fn (Blueprint $t) => $t->dropColumn('body'));
        Schema::table('exam_answers', fn (Blueprint $t) => $t->dropColumn(['answer_file', 'is_correct']));
        Schema::table('class_subject_teacher', function (Blueprint $t) {
            $t->dropUnique(['join_code']);
            $t->dropColumn('join_code');
        });
    }
};