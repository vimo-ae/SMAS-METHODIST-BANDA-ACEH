<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_token_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_token_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('announcements_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('announcement_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('category', 30)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->json('target_roles')->nullable();
            $table->boolean('is_public')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('created_by', 18)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('assignments_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->string('cst_id', 20)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->unsignedSmallInteger('max_score')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->json('allowed_formats')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('assignment_submissions_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->string('students_id', 10)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('classes_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('name', 50)->nullable();
            $table->string('homeroom_teacher_id', 18)->nullable();
            $table->char('academic_year', 9)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('class_enrollments_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->string('students_id', 10)->nullable();
            $table->string('cst_id', 20)->nullable();
            $table->char('academic_year', 9)->nullable();
            $table->enum('status', ['aktif','naik_kelas','tinggal_kelas','pindah','lulus','drop_out'])->nullable();
            $table->date('enrolled_at')->nullable();
            $table->date('left_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('class_subject_teacher_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('cst_id', 20)->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('subject_code', 20)->nullable();
            $table->string('teacher_id', 18)->nullable();
            $table->char('academic_year', 9)->nullable();
            $table->enum('semester', ['ganjil','genap'])->nullable();
            $table->string('join_code', 10)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('cst_students_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('cst_id', 20)->nullable();
            $table->string('students_id', 10)->nullable();
            $table->enum('status', ['aktif','keluar','selesai'])->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('exams_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->string('cst_id', 20)->nullable();
            $table->string('title', 255)->nullable();
            $table->enum('exam_type', ['uts','uas','kuis'])->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->decimal('pass_score', 5, 2)->nullable();
            $table->boolean('shuffle_questions')->nullable();
            $table->boolean('shuffle_options')->nullable();
            $table->boolean('is_published')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('exam_answers_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_answer_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->string('students_id', 10)->nullable();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->text('answer')->nullable();
            $table->string('answer_file', 255)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->string('graded_by', 18)->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('exam_questions_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_question_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->text('question_text')->nullable();
            $table->enum('question_type', ['pilihan_ganda','pilihan_ganda_kompleks','benar_salah','menjodohkan','esai'])->nullable();
            $table->json('options')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->text('correct_answer')->nullable();
            $table->decimal('score_weight', 6, 2)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('exam_sessions_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_session_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->string('student_id', 10)->nullable();
            $table->char('token', 40)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->enum('status', ['berlangsung','selesai','keluar'])->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('exam_violations_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('exam_violation_id')->nullable();
            $table->unsignedBigInteger('exam_session_id')->nullable();
            $table->string('students_id', 10)->nullable();
            $table->enum('type', ['tab_switch','window_blur','fullscreen_exit','copy_paste'])->nullable();
            $table->string('detail', 255)->nullable();
            $table->timestamp('occurred_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('materials_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->string('cst_id', 20)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('notifications_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->unsignedBigInteger('notification_id')->nullable();
            $table->string('user_id', 18)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('schedules_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('cst_id', 20)->nullable();
            $table->enum('day', ['senin','selasa','rabu','kamis','jumat','sabtu'])->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('students_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('nis', 10)->nullable();
            $table->char('nisn', 10)->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('pob', 50)->nullable();
            $table->date('dob')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('parent_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->year('entry_year')->nullable();
            $table->enum('status', ['aktif','lulus','pindah','drop_out'])->nullable();
            $table->enum('gender', ['L','P'])->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('subjects_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('code', 20)->nullable();
            $table->string('name', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('teachers_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('nip', 18)->nullable();
            $table->string('specialization', 255)->nullable();
            $table->string('education_level', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('status', ['aktif','cuti','pensiun','resign'])->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });

        Schema::create('users_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->enum('log_action', ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE']);
            $table->string('log_by', 100)->nullable();
            $table->timestamp('log_at', 3)->useCurrent();
            $table->string('user_id', 18)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['admin','guru','siswa','orangtua'])->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('log_at');
            $table->index('log_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_token_logs');
        Schema::dropIfExists('announcements_logs');
        Schema::dropIfExists('assignments_logs');
        Schema::dropIfExists('assignment_submissions_logs');
        Schema::dropIfExists('classes_logs');
        Schema::dropIfExists('class_enrollments_logs');
        Schema::dropIfExists('class_subject_teacher_logs');
        Schema::dropIfExists('cst_students_logs');
        Schema::dropIfExists('exams_logs');
        Schema::dropIfExists('exam_answers_logs');
        Schema::dropIfExists('exam_questions_logs');
        Schema::dropIfExists('exam_sessions_logs');
        Schema::dropIfExists('exam_violations_logs');
        Schema::dropIfExists('materials_logs');
        Schema::dropIfExists('notifications_logs');
        Schema::dropIfExists('schedules_logs');
        Schema::dropIfExists('students_logs');
        Schema::dropIfExists('subjects_logs');
        Schema::dropIfExists('teachers_logs');
        Schema::dropIfExists('users_logs');
    }
};