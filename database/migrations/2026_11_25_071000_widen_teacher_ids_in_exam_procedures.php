<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_force_finish_session`;');
        DB::unprepared("
            CREATE PROCEDURE `sp_force_finish_session`(
                IN p_session_id BIGINT UNSIGNED,
                IN p_teacher_id VARCHAR(50),
                IN p_reason VARCHAR(255)
            )
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_exam_id BIGINT UNSIGNED;
                DECLARE v_student_id VARCHAR(10);
                DECLARE v_cst_id VARCHAR(20);
                DECLARE v_type VARCHAR(20);
                DECLARE v_final DECIMAL(6,2);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT es.exam_id, es.student_id, e.cst_id, e.exam_type, cst.teacher_id
                INTO v_exam_id, v_student_id, v_cst_id, v_type, v_owner
                FROM exam_sessions es
                JOIN exams e ON e.exam_id = es.exam_id
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE es.exam_session_id = p_session_id;

                IF v_owner IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Sesi ujian tidak ditemukan.';
                ELSEIF v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: bukan pengampu kelas ini.';
                END IF;

                START TRANSACTION;
                    UPDATE exam_sessions
                    SET status = 'keluar', left_at = NOW(), finished_at = NOW(), updated_at = NOW()
                    WHERE exam_session_id = p_session_id;

                    INSERT INTO exam_violations (exam_session_id, students_id, type, detail, occurred_at)
                    VALUES (p_session_id, v_student_id, 'window_blur', COALESCE(p_reason, 'Dihentikan paksa oleh guru'), NOW());

                    SET v_final = fn_exam_final_score(p_session_id);

                    INSERT INTO grades (students_id, cst_id, grade_type, exam_id, score, graded_by, graded_at, created_at, updated_at)
                    VALUES (v_student_id, v_cst_id, v_type, v_exam_id, v_final, p_teacher_id, NOW(), NOW(), NOW())
                    ON DUPLICATE KEY UPDATE score = v_final, graded_by = p_teacher_id, graded_at = NOW(), updated_at = NOW();
                COMMIT;

                SELECT p_session_id AS exam_session_id, v_final AS final_score;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_grade_assignment`;');
        DB::unprepared("
            CREATE PROCEDURE `sp_grade_assignment`(
                IN p_assignment_id BIGINT UNSIGNED,
                IN p_student_id VARCHAR(10),
                IN p_teacher_id VARCHAR(50),
                IN p_score DECIMAL(5,2),
                IN p_feedback TEXT
            )
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_cst_id VARCHAR(20);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id, a.cst_id
                INTO v_owner, v_cst_id
                FROM assignments a
                JOIN class_subject_teacher cst ON cst.cst_id = a.cst_id
                WHERE a.assignment_id = p_assignment_id;

                IF v_owner IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tugas tidak ditemukan.';
                ELSEIF v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: bukan pengampu tugas ini.';
                END IF;

                START TRANSACTION;
                    UPDATE assignment_submissions
                    SET score = p_score, feedback = p_feedback, updated_at = NOW()
                    WHERE assignment_id = p_assignment_id AND students_id = p_student_id;

                    INSERT INTO grades (students_id, cst_id, grade_type, assignment_id, score, graded_by, graded_at, created_at, updated_at)
                    VALUES (p_student_id, v_cst_id, 'tugas', p_assignment_id, p_score, p_teacher_id, NOW(), NOW(), NOW())
                    ON DUPLICATE KEY UPDATE score = p_score, graded_by = p_teacher_id, graded_at = NOW(), updated_at = NOW();
                COMMIT;

                SELECT * FROM assignment_submissions
                WHERE assignment_id = p_assignment_id AND students_id = p_student_id;
            END
        ");

        $db = config('database.connections.mysql.database', 'methodist_aceh');
        $guruUser = config('database.connections.mysql_guru.username', 'methodist_guru');

        foreach (['localhost', '127.0.0.1', '%'] as $host) {
            if ($guruUser) {
                try {
                    DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_force_finish_session` TO '{$guruUser}'@'{$host}'");
                    DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_grade_assignment` TO '{$guruUser}'@'{$host}'");
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
    }
};
