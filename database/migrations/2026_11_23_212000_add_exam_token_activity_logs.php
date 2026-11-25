<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('exam_token_logs')) {
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
        }

        $this->dropTriggers();

        DB::unprepared("
            CREATE TRIGGER trg_exam_token_ai AFTER INSERT ON `exam_token` FOR EACH ROW
            BEGIN
                INSERT INTO `exam_token_logs`
                    (log_action, log_by, exam_token_id, exam_id, issued_at, expires_at, is_active, created_at, updated_at)
                VALUES
                    ('INSERT', COALESCE(@app_user_id, USER()), NEW.exam_token_id, NEW.exam_id,
                     NEW.issued_at, NEW.expires_at, NEW.is_active, NEW.created_at, NEW.updated_at);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_exam_token_au AFTER UPDATE ON `exam_token` FOR EACH ROW
            BEGIN
                IF NOT (
                    OLD.exam_token_id <=> NEW.exam_token_id
                    AND OLD.exam_id <=> NEW.exam_id
                    AND OLD.issued_at <=> NEW.issued_at
                    AND OLD.expires_at <=> NEW.expires_at
                    AND OLD.is_active <=> NEW.is_active
                    AND OLD.created_at <=> NEW.created_at
                    AND OLD.updated_at <=> NEW.updated_at
                ) THEN
                    INSERT INTO `exam_token_logs`
                        (log_action, log_by, exam_token_id, exam_id, issued_at, expires_at, is_active, created_at, updated_at)
                    VALUES
                        ('UPDATE_BEFORE', COALESCE(@app_user_id, USER()), OLD.exam_token_id, OLD.exam_id,
                         OLD.issued_at, OLD.expires_at, OLD.is_active, OLD.created_at, OLD.updated_at);
                    INSERT INTO `exam_token_logs`
                        (log_action, log_by, exam_token_id, exam_id, issued_at, expires_at, is_active, created_at, updated_at)
                    VALUES
                        ('UPDATE_AFTER', COALESCE(@app_user_id, USER()), NEW.exam_token_id, NEW.exam_id,
                         NEW.issued_at, NEW.expires_at, NEW.is_active, NEW.created_at, NEW.updated_at);
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_exam_token_ad AFTER DELETE ON `exam_token` FOR EACH ROW
            BEGIN
                INSERT INTO `exam_token_logs`
                    (log_action, log_by, exam_token_id, exam_id, issued_at, expires_at, is_active, created_at, updated_at)
                VALUES
                    ('DELETE', COALESCE(@app_user_id, USER()), OLD.exam_token_id, OLD.exam_id,
                     OLD.issued_at, OLD.expires_at, OLD.is_active, OLD.created_at, OLD.updated_at);
            END
        ");

        $this->rebuildAllLogsView();
    }

    public function down(): void
    {
        $this->dropTriggers();
        Schema::dropIfExists('exam_token_logs');
        $this->rebuildAllLogsView();
    }

    private function dropTriggers(): void
    {
        foreach (['ai', 'au', 'ad'] as $suffix) {
            DB::unprepared("DROP TRIGGER IF EXISTS trg_exam_token_{$suffix}");
        }
    }

    private function rebuildAllLogsView(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_all_logs');

        $definitions = [
            'announcements' => [['announcement_id'], ['announcement_id','title','slug','link','category','content','image','target_roles','is_public','published_at','created_by','created_at','updated_at']],
            'assignments' => [['assignment_id'], ['assignment_id','cst_id','title','description','due_date','max_score','created_at','updated_at','allowed_formats']],
            'assignment_submissions' => [['assignment_id','students_id'], ['assignment_id','students_id','file_path','submitted_at','score','feedback','created_at','updated_at']],
            'classes' => [['class_id'], ['class_id','name','homeroom_teacher_id','academic_year','created_at','updated_at']],
            'class_enrollments' => [['enrollment_id'], ['enrollment_id','students_id','cst_id','academic_year','status','enrolled_at','left_at','created_at','updated_at']],
            'class_subject_teacher' => [['cst_id'], ['cst_id','class_id','subject_code','teacher_id','academic_year','semester','join_code','created_at','updated_at']],
            'cst_students' => [['cst_id','students_id'], ['cst_id','students_id','status','joined_at','left_at','created_at','updated_at']],
            'exams' => [['exam_id'], ['exam_id','cst_id','title','exam_type','start_time','end_time','duration_minutes','pass_score','shuffle_questions','shuffle_options','is_published','instructions','created_at','updated_at']],
            'exam_token' => [['exam_token_id'], ['exam_token_id','exam_id','issued_at','expires_at','is_active','created_at','updated_at']],
            'exam_answers' => [['exam_answer_id'], ['exam_answer_id','exam_id','students_id','question_id','answer','answer_file','is_correct','score','graded_by','graded_at','created_at','updated_at']],
            'exam_questions' => [['exam_question_id'], ['exam_question_id','exam_id','question_text','question_type','options','image_path','correct_answer','score_weight','created_at','updated_at']],
            'exam_sessions' => [['exam_session_id'], ['exam_session_id','exam_id','student_id','token','started_at','finished_at','left_at','status','created_at','updated_at']],
            'exam_violations' => [['exam_violation_id'], ['exam_violation_id','exam_session_id','students_id','type','detail','occurred_at']],
            'materials' => [['material_id'], ['material_id','cst_id','title','description','file_path','uploaded_at','created_at','updated_at']],
            'notifications' => [['notification_id'], ['notification_id','user_id','title','message','is_read','created_at','updated_at']],
            'schedules' => [['cst_id','day','start_time'], ['cst_id','day','start_time','end_time','created_at','updated_at']],
            'students' => [['nis'], ['nis','nisn','class_id','pob','dob','address','district','city','province','parent_name','parent_phone','entry_year','status','gender','created_at','updated_at']],
            'subjects' => [['code'], ['code','name','created_at','updated_at']],
            'teachers' => [['nip'], ['nip','specialization','education_level','address','hire_date','status','created_at','updated_at']],
            'users' => [['user_id'], ['user_id','name','email','email_verified_at','role','phone','photo','created_at','updated_at']],
        ];

        $selects = [];
        foreach ($definitions as $table => [$pk, $cols]) {
            $logTable = "{$table}_logs";
            if (! Schema::hasTable($logTable)) {
                continue;
            }

            $record = 'CONCAT_WS(\'-\', ' . implode(', ', array_map(
                fn ($column) => "CAST(`{$column}` AS CHAR)",
                $pk
            )) . ')';
            $json = 'JSON_OBJECT(' . implode(', ', array_map(
                fn ($column) => "'{$column}', `{$column}`",
                $cols
            )) . ')';

            $selects[] = "SELECT '{$table}' AS source_table, log_id, log_action, log_by, log_at, "
                . "{$record} AS record_id, {$json} AS row_data FROM `{$logTable}`";
        }

        if ($selects !== []) {
            DB::statement('CREATE VIEW v_all_logs AS ' . implode(' UNION ALL ', $selects));
        }
    }
};
