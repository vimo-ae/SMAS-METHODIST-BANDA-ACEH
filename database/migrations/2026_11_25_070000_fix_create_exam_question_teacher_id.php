<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_create_exam_question`;');
        DB::unprepared("
            CREATE PROCEDURE `sp_create_exam_question`(
                IN p_exam_id BIGINT UNSIGNED,
                IN p_teacher_id VARCHAR(50),
                IN p_question_type VARCHAR(30),
                IN p_question_text TEXT,
                IN p_options JSON,
                IN p_correct_answer TEXT,
                IN p_score_weight DECIMAL(6,2)
            )
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_new_id BIGINT UNSIGNED;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id INTO v_owner
                FROM exams e
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE e.exam_id = p_exam_id;

                IF v_owner IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ujian tidak ditemukan.';
                ELSEIF v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: bukan pemilik ujian ini.';
                END IF;

                START TRANSACTION;
                    INSERT INTO exam_questions (
                        exam_id, question_type, question_text, options, correct_answer, score_weight, created_at, updated_at
                    ) VALUES (
                        p_exam_id, p_question_type, p_question_text, p_options, p_correct_answer, COALESCE(p_score_weight, 1.00), NOW(), NOW()
                    );
                    SET v_new_id = LAST_INSERT_ID();
                COMMIT;

                SELECT * FROM exam_questions WHERE exam_question_id = v_new_id;
            END
        ");

        $db = config('database.connections.mysql.database', 'methodist_aceh');
        $guruUser = config('database.connections.mysql_guru.username', 'methodist_guru');

        foreach (['localhost', '127.0.0.1', '%'] as $host) {
            if ($guruUser) {
                try {
                    DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_create_exam_question` TO '{$guruUser}'@'{$host}'");
                } catch (\Throwable $e) {
                }
            }
        }

        try {
            DB::unprepared('FLUSH PRIVILEGES;');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_create_exam_question`;');
        DB::unprepared("
            CREATE PROCEDURE `sp_create_exam_question`(
                IN p_exam_id BIGINT UNSIGNED,
                IN p_teacher_id VARCHAR(18),
                IN p_question_type VARCHAR(30),
                IN p_question_text TEXT,
                IN p_options JSON,
                IN p_correct_answer TEXT,
                IN p_score_weight DECIMAL(6,2)
            )
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(18);
                DECLARE v_new_id BIGINT UNSIGNED;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id INTO v_owner
                FROM exams e
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE e.exam_id = p_exam_id;

                IF v_owner IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ujian tidak ditemukan.';
                ELSEIF v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: bukan pemilik ujian ini.';
                END IF;

                START TRANSACTION;
                    INSERT INTO exam_questions (
                        exam_id, question_type, question_text, options, correct_answer, score_weight, created_at, updated_at
                    ) VALUES (
                        p_exam_id, p_question_type, p_question_text, p_options, p_correct_answer, COALESCE(p_score_weight, 1.00), NOW(), NOW()
                    );
                    SET v_new_id = LAST_INSERT_ID();
                COMMIT;

                SELECT * FROM exam_questions WHERE exam_question_id = v_new_id;
            END
        ");
    }
};
