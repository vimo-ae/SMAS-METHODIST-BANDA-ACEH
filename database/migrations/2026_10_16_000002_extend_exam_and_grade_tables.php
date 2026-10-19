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

        // Ujian: tipe fleksibel + pengaturan guru
        Schema::table('exams', function (Blueprint $table) {
            $table->string('type', 10)->change(); // kuis | uts | uas
            $table->unsignedSmallInteger('total_questions')->default(35)->after('duration_minutes');
            $table->boolean('is_published')->default(false)->after('total_questions');
            $table->boolean('require_face_photo')->default(true)->after('is_published');
            $table->text('instructions')->nullable()->after('require_face_photo');
        });

        // Soal: 5 tipe, kunci jawaban JSON, bobot desimal
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->string('question_type', 30)->change();
            $table->text('correct_answer')->nullable()->change();
            $table->decimal('score_weight', 6, 2)->default(0)->change();
            $table->string('image_path')->nullable()->after('options');
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
        Schema::table('exam_questions', fn (Blueprint $t) => $t->dropColumn('image_path'));
        Schema::table('exams', fn (Blueprint $t) => $t->dropColumn(['total_questions', 'is_published', 'require_face_photo', 'instructions']));
        Schema::table('class_subject_teacher', function (Blueprint $t) {
            $t->dropUnique(['join_code']);
            $t->dropColumn('join_code');
        });
    }
};
