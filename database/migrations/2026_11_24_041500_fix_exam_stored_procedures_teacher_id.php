<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Perbaiki sp_record_exam_grade (widen v_teacher_id dan v_student_id)
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_record_exam_grade`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_record_exam_grade`(IN p_session_id BIGINT UNSIGNED)
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_student_id VARCHAR(30);
                DECLARE v_exam_id BIGINT UNSIGNED;
                DECLARE v_cst_id VARCHAR(30);
                DECLARE v_exam_type VARCHAR(30);
                DECLARE v_teacher_id VARCHAR(50);
                DECLARE v_final DECIMAL(6,2);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT s.student_id, s.exam_id, e.cst_id, e.exam_type, cst.teacher_id
                INTO v_student_id, v_exam_id, v_cst_id, v_exam_type, v_teacher_id
                FROM exam_sessions s
                JOIN exams e ON s.exam_id = e.exam_id
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE s.exam_session_id = p_session_id;

                SET v_final = fn_exam_final_score(p_session_id);

                START TRANSACTION;
                    UPDATE exam_sessions
                    SET status = 'selesai',
                        finished_at = COALESCE(finished_at, NOW()),
                        updated_at = NOW()
                    WHERE exam_session_id = p_session_id;

                    INSERT INTO grades (students_id, cst_id, grade_type, exam_id, score, graded_by, graded_at, created_at, updated_at)
                    VALUES (v_student_id, v_cst_id, v_exam_type, v_exam_id, v_final, v_teacher_id, NOW(), NOW(), NOW())
                    ON DUPLICATE KEY UPDATE score = v_final, graded_at = NOW(), updated_at = NOW();
                COMMIT;

                SELECT v_student_id AS student_id, v_exam_id AS exam_id, v_final AS final_score;
            END;
        ");

        // 2. Perbaiki trg_exam_violations_ai
        DB::unprepared("DROP TRIGGER IF EXISTS `trg_exam_violations_ai`;");
        DB::unprepared("
            CREATE TRIGGER `trg_exam_violations_ai` AFTER INSERT ON `exam_violations` FOR EACH ROW
            BEGIN
                DECLARE v_total INT DEFAULT 0;
                DECLARE v_exam_id BIGINT UNSIGNED;
                DECLARE v_student_id VARCHAR(30);
                DECLARE v_teacher_id VARCHAR(50);
                DECLARE v_student_name VARCHAR(100);
                DECLARE v_exam_title VARCHAR(255);

                SELECT COUNT(*) INTO v_total FROM exam_violations WHERE exam_session_id = NEW.exam_session_id;

                SELECT es.exam_id, es.student_id, cst.teacher_id, u.name, e.title
                INTO v_exam_id, v_student_id, v_teacher_id, v_student_name, v_exam_title
                FROM exam_sessions es
                JOIN exams e ON e.exam_id = es.exam_id
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                JOIN users u ON u.user_id = es.student_id
                WHERE es.exam_session_id = NEW.exam_session_id;

                IF v_total >= 3 THEN
                    UPDATE exam_sessions
                    SET status = 'keluar', left_at = NOW(), finished_at = NOW(), updated_at = NOW()
                    WHERE exam_session_id = NEW.exam_session_id AND status = 'berlangsung';

                    IF v_teacher_id IS NOT NULL THEN
                        INSERT INTO notifications (user_id, title, message, is_read, created_at, updated_at)
                        VALUES (
                            v_teacher_id,
                            'Pelanggaran Ujian (Auto-Stop)',
                            CONCAT(v_student_name, ' (', v_student_id, ') mencapai ', v_total, ' pelanggaran pada ujian \"', v_exam_title, '\" dan otomatis dikeluarkan.'),
                            0,
                            NOW(),
                            NOW()
                        );
                    END IF;
                END IF;
            END;
        ");

        // 3. Perbaiki sp_reset_exam_session
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_reset_exam_session`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_reset_exam_session`(IN p_session_id BIGINT UNSIGNED, IN p_teacher_id VARCHAR(50))
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_exam_id BIGINT UNSIGNED;
                DECLARE v_student_id VARCHAR(30);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id, s.exam_id, s.student_id
                INTO v_owner, v_exam_id, v_student_id
                FROM exam_sessions s
                JOIN exams e ON s.exam_id = e.exam_id
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE s.exam_session_id = p_session_id;

                IF v_owner IS NULL OR v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: sesi ujian bukan milik guru ini';
                END IF;

                START TRANSACTION;
                    DELETE FROM exam_violations WHERE exam_session_id = p_session_id;
                    DELETE FROM exam_answers WHERE exam_id = v_exam_id AND students_id = v_student_id;
                    DELETE FROM grades WHERE exam_id = v_exam_id AND students_id = v_student_id;
                    UPDATE exam_sessions
                    SET status = 'berlangsung', started_at = NOW(), finished_at = NULL, left_at = NULL, updated_at = NOW()
                    WHERE exam_session_id = p_session_id;
                COMMIT;
            END;
        ");

        // 4. Perbaiki sp_create_exam & sp_publish_exam & sp_grade_exam_essay & sp_guru_exam_analytics
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_create_exam`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_create_exam`(
                IN p_teacher_id VARCHAR(50),
                IN p_cst_id VARCHAR(20),
                IN p_title VARCHAR(255),
                IN p_description TEXT,
                IN p_exam_type VARCHAR(20),
                IN p_start_time DATETIME,
                IN p_end_time DATETIME,
                IN p_duration_minutes INT,
                IN p_is_published TINYINT(1)
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

                SELECT teacher_id INTO v_owner FROM class_subject_teacher WHERE cst_id = p_cst_id;
                IF v_owner IS NULL OR v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: kelas bukan milik guru ini';
                END IF;

                START TRANSACTION;
                    INSERT INTO exams (cst_id, title, description, exam_type, start_time, end_time, duration_minutes, is_published, created_at, updated_at)
                    VALUES (p_cst_id, p_title, p_description, p_exam_type, p_start_time, p_end_time, p_duration_minutes, p_is_published, NOW(), NOW());
                    SET v_new_id = LAST_INSERT_ID();
                COMMIT;

                SELECT * FROM exams WHERE exam_id = v_new_id;
            END;
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_publish_exam`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_publish_exam`(IN p_exam_id BIGINT UNSIGNED, IN p_teacher_id VARCHAR(50), IN p_publish TINYINT(1))
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_cst_id VARCHAR(20);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id, e.cst_id INTO v_owner, v_cst_id
                FROM exams e
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE e.exam_id = p_exam_id;

                IF v_owner IS NULL OR v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: ujian bukan milik guru ini';
                END IF;

                START TRANSACTION;
                    UPDATE exams SET is_published = p_publish, updated_at = NOW() WHERE exam_id = p_exam_id;
                COMMIT;
            END;
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_grade_exam_essay`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_grade_exam_essay`(
                IN p_session_id BIGINT UNSIGNED,
                IN p_question_id BIGINT UNSIGNED,
                IN p_teacher_id VARCHAR(50),
                IN p_score DECIMAL(5,2)
            )
            MODIFIES SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                DECLARE v_exam_id BIGINT UNSIGNED;
                DECLARE v_student_id VARCHAR(30);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                SELECT cst.teacher_id, s.exam_id, s.student_id
                INTO v_owner, v_exam_id, v_student_id
                FROM exam_sessions s
                JOIN exams e ON s.exam_id = e.exam_id
                JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                WHERE s.exam_session_id = p_session_id;

                IF v_owner IS NULL OR v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: ujian bukan milik guru ini';
                END IF;

                START TRANSACTION;
                    UPDATE exam_answers
                    SET score = p_score, graded_by = p_teacher_id, graded_at = NOW(), updated_at = NOW()
                    WHERE exam_id = v_exam_id AND students_id = v_student_id AND question_id = p_question_id;

                    CALL sp_record_exam_grade(p_session_id);
                COMMIT;
            END;
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_guru_exam_analytics`;");
        DB::unprepared("
            CREATE PROCEDURE `sp_guru_exam_analytics`(IN p_cst_id VARCHAR(20), IN p_teacher_id VARCHAR(50))
            READS SQL DATA
            BEGIN
                DECLARE v_owner VARCHAR(50);
                SELECT teacher_id INTO v_owner FROM class_subject_teacher WHERE cst_id = p_cst_id;
                IF v_owner IS NULL OR v_owner <> p_teacher_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akses ditolak: kelas bukan milik guru ini';
                END IF;

                SELECT
                    e.exam_id,
                    e.title,
                    e.exam_type,
                    e.is_published,
                    COUNT(s.exam_session_id) AS total_participants,
                    SUM(CASE WHEN s.status = 'selesai' THEN 1 ELSE 0 END) AS finished_participants,
                    AVG(g.score) AS average_score,
                    MIN(g.score) AS min_score,
                    MAX(g.score) AS max_score
                FROM exams e
                LEFT JOIN exam_sessions s ON s.exam_id = e.exam_id
                LEFT JOIN grades g ON g.exam_id = e.exam_id AND g.students_id = s.student_id
                WHERE e.cst_id = p_cst_id
                GROUP BY e.exam_id, e.title, e.exam_type, e.is_published
                ORDER BY e.created_at DESC;
            END;
        ");

        // 7. Kembalikan hak akses EXECUTE untuk user database (siswa, guru, admin)
        $hosts = ['localhost', '127.0.0.1', '%'];
        $db = config('database.connections.mysql.database', 'methodist_aceh');
        $siswaUser = config('database.connections.mysql_siswa.username', 'methodist_siswa');
        $guruUser = config('database.connections.mysql_guru.username', 'methodist_guru');
        $adminUser = config('database.connections.mysql_admin.username', 'methodist_admin');

        foreach ($hosts as $host) {
            foreach ([$siswaUser, $guruUser, $adminUser] as $u) {
                if ($u) {
                    try {
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_record_exam_grade` TO '{$u}'@'{$host}'");
                    } catch (\Throwable $e) {}
                }
            }
            foreach ([$guruUser, $adminUser] as $u) {
                if ($u) {
                    try {
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_create_exam` TO '{$u}'@'{$host}'");
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_publish_exam` TO '{$u}'@'{$host}'");
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_grade_exam_essay` TO '{$u}'@'{$host}'");
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_guru_exam_analytics` TO '{$u}'@'{$host}'");
                        DB::unprepared("GRANT EXECUTE ON PROCEDURE `{$db}`.`sp_reset_exam_session` TO '{$u}'@'{$host}'");
                    } catch (\Throwable $e) {}
                }
            }
        }
        try { DB::unprepared("FLUSH PRIVILEGES;"); } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // No-op
    }
};
