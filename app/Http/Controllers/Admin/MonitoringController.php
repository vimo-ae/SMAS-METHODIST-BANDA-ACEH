<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Services\ExamGrader;
use App\Services\ExamTokenService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MonitoringController extends Controller
{
    private function db()
    {
        return DB::connection('mysql_admin');
    }

    public function index()
    {
        $conn = $this->db();

        $activeSessions = $conn->table('v_exam_statistics as vs')
            ->join('exams as e', 'e.exam_id', '=', 'vs.exam_id')
            ->select(
                'vs.exam_id as id',
                'vs.exam_type as name',
                'vs.exam_title',
                'vs.class_name',
                'vs.subject_name',
                'vs.total_sessions as participants_count',
                'vs.finished_sessions',
                'e.start_time',
                'e.end_time',
                'e.is_published'
            )
            ->selectRaw("
                CASE
                    WHEN fn_is_exam_open(vs.exam_id) = 1
                     AND NOT (
                         vs.total_sessions > 0
                         AND vs.finished_sessions >= (
                             SELECT COUNT(*) FROM cst_students cs
                             WHERE cs.cst_id = e.cst_id AND cs.status = 'aktif'
                         )
                     )
                    THEN 1 ELSE 0
                END AS is_open
            ")
            ->orderByDesc('is_open')
            ->orderByDesc('e.start_time')
            ->get()
            ->map(function ($row) {
                $row->exam = (object) ['title' => $row->exam_title];
                $row->cst = (object) [
                    'schoolClass' => (object) ['name' => $row->class_name],
                    'subject'     => (object) ['name' => $row->subject_name],
                ];

                return $row;
            });

        $classes = $conn->table('classes as c')
            ->select('c.*')
            ->selectRaw("(SELECT COUNT(*) FROM students s
                          WHERE s.class_id = c.class_id AND s.status = 'aktif') AS students_count")
            ->when(
                Schema::connection('mysql_admin')->hasColumn('class_subject_teacher', 'class_id'),
                fn ($q) => $q->selectRaw("(SELECT COUNT(*) FROM exams e
                                           JOIN class_subject_teacher cst ON cst.cst_id = e.cst_id
                                           WHERE cst.class_id = c.class_id) AS exams_count")
            )
            ->orderBy('c.class_id')
            ->get();

        return view('admin.monitoring.index', compact('activeSessions', 'classes'));
    }

    public function classExams($classId)
    {
        $conn = $this->db();

        $class = $conn->table('classes')->where('class_id', $classId)->first();
        abort_if(! $class, 404);

        $exams = $conn->table('v_exam_statistics as vs')
            ->join('exams as e', 'e.exam_id', '=', 'vs.exam_id')
            ->join('class_subject_teacher as cst', 'cst.cst_id', '=', 'e.cst_id')
            ->where('cst.class_id', $classId)
            ->select(
                'vs.exam_id as id',
                'vs.exam_type',
                'vs.exam_title',
                'vs.subject_name',
                'vs.total_sessions',
                'vs.finished_sessions',
                'e.start_time',
                'e.end_time',
                'e.is_published'
            )
            ->selectRaw("
                CASE
                    WHEN fn_is_exam_open(vs.exam_id) = 1
                     AND NOT (
                         vs.total_sessions > 0
                         AND vs.finished_sessions >= (
                             SELECT COUNT(*) FROM cst_students cs
                             WHERE cs.cst_id = e.cst_id AND cs.status = 'aktif'
                         )
                     )
                    THEN 1 ELSE 0
                END AS is_open
            ")
            ->orderByDesc('is_open')
            ->orderByDesc('e.start_time')
            ->get()
            ->map(function ($row) use ($class) {
                $row->exam_id = $row->id;
                $row->name = $row->exam_type;
                $row->participants_count = $row->total_sessions;
                $row->class_name = $class->name ?? $class->class_name ?? null;
                $row->exam = (object) ['title' => $row->exam_title];
                $row->cst = (object) [
                    'schoolClass' => (object) ['name' => $row->class_name],
                    'subject'     => (object) ['name' => $row->subject_name],
                ];

                return $row;
            });

        $schoolClass = $class;
        $schoolClass->name = $class->name ?? $class->class_name ?? ('Kelas ' . $classId);

        return view('admin.monitoring.class_exams', [
            'schoolClass' => $schoolClass,
            'class'       => $schoolClass,
            'exams'       => $exams,
            'sessions'    => $exams,
        ]);
    }

    public function activeToken($id, ExamTokenService $examTokens)
    {
        $exam = Exam::on('mysql_admin')->findOrFail($id);

        if (! $exam->isOpen()) {
            return response()->json([
                'token' => null,
                'expires_at' => null,
            ]);
        }

        $token = $examTokens->activeToken($exam);

        return response()->json([
            'token' => $token->plainToken(),
            'expires_at' => $token->expires_at->toIso8601String(),
            'expires_at_display' => $token->expires_at->format('H:i:s'),
        ]);
    }

    public function show($id, ExamGrader $grader, ExamTokenService $examTokens)
    {
        $conn = $this->db();

        // 1. Tutup otomatis sesi melewati batas waktu via SP sp_find_expired_sessions()
        $expiredIds = collect($conn->select('CALL sp_find_expired_sessions()'))
            ->where('exam_id', $id)
            ->pluck('exam_session_id');

        if ($expiredIds->isNotEmpty()) {
            ExamSession::on('mysql_admin')
                ->with('exam')
                ->whereIn('exam_session_id', $expiredIds)
                ->get()
                ->each(function ($s) use ($grader, $conn) {
                    $grader->gradeSession($s);
                    $conn->statement('CALL sp_record_exam_grade(?)', [$s->exam_session_id]);
                });
        }

        // 2. Detail ujian dari View v_exam_statistics + fn_is_exam_open()
        $rawSession = $conn->table('v_exam_statistics as vs')
            ->join('exams as e', 'e.exam_id', '=', 'vs.exam_id')
            ->join('class_subject_teacher as cst', 'cst.cst_id', '=', 'e.cst_id')
            ->select(
                'vs.*',
                'e.start_time',
                'e.end_time',
                'e.duration_minutes',
                'e.pass_score',
                'cst.join_code as token',
                'cst.class_id'
            )
            ->selectRaw("
                CASE
                    WHEN fn_is_exam_open(vs.exam_id) = 1
                     AND NOT (
                         vs.total_sessions > 0
                         AND vs.finished_sessions >= (
                             SELECT COUNT(*) FROM cst_students cs
                             WHERE cs.cst_id = e.cst_id AND cs.status = 'aktif'
                         )
                     )
                    THEN 1 ELSE 0
                END AS is_open
            ")
            ->where('vs.exam_id', $id)
            ->first();

        abort_if(! $rawSession, 404);

        $session = $rawSession;
        $session->exam = (object) ['title' => $rawSession->exam_title];
        $session->cst = (object) [
            'schoolClass' => (object) ['name' => $rawSession->class_name],
            'subject'     => (object) ['name' => $rawSession->subject_name],
        ];

        $session->exam_token = null;
        $session->exam_token_expires_at = null;
        $exam = Exam::on('mysql_admin')->find($id);
        if ($exam && $exam->isOpen()) {
            try {
                $activeToken = $examTokens->activeToken($exam);
                $session->exam_token = $activeToken->plainToken();
                $session->exam_token_expires_at = $activeToken->expires_at;
            } catch (\RuntimeException) {
                // Tidak menampilkan token jika ujian tidak aktif.
            }
        }

        // 3. Hasil per sesi dari View v_exam_results (memakai fn_exam_objective_score & fn_violation_count)
        $resultsByNis = $conn->table('v_exam_results')
            ->where('exam_id', $id)
            ->get()
            ->keyBy('nis');

        // 4. Rekap pelanggaran dari View v_violation_summary per NIS
        $violationMap = $conn->table('v_violation_summary')
            ->where('exam_id', $id)
            ->select('nis', DB::raw('SUM(total) as total_violations'))
            ->groupBy('nis')
            ->pluck('total_violations', 'nis');

        // Cadangan pemetaan Nama -> NIS dari siswa terdaftar pada kelas ujian ini
        $nisByName = $conn->table('exams as e')
            ->join('cst_students as cs', function ($j) {
                $j->on('cs.cst_id', '=', 'e.cst_id')->where('cs.status', 'aktif');
            })
            ->join('users as u', 'u.user_id', '=', 'cs.students_id')
            ->where('e.exam_id', $id)
            ->pluck('cs.students_id', 'u.name');

        // 5. Daftar peserta dari SP sp_exam_participants(?)
        $participants = collect($conn->select('CALL sp_exam_participants(?)', [$id]))
            ->map(function ($row) use ($resultsByNis, $violationMap, $nisByName, $session, $conn) {
                $name = $row->student_name ?? $row->name ?? '-';
                $nis  = $row->nis ?? $row->students_id ?? $row->student_id ?? ($nisByName[$name] ?? null);

                $row->nis          = $nis;
                $row->student_name = $name;
                $res = $nis ? ($resultsByNis[$nis] ?? null) : null;

                // Sinkronkan ke grades via SP sp_record_exam_grade bila sesi selesai tapi belum masuk grades
                if (($row->score ?? null) === null && ($row->session_status ?? $row->status ?? null) === 'selesai' && !empty($row->exam_session_id)) {
                    $conn->statement('CALL sp_record_exam_grade(?)', [$row->exam_session_id]);
                    $row->score = $nis
                        ? $conn->table('grades')
                            ->where('exam_id', $session->exam_id)
                            ->where('students_id', $nis)
                            ->value('score')
                        : null;
                }

                // Fallback nilai: g.score dari SP -> objective_score dari v_exam_results (fn_exam_objective_score)
                $score = $row->score ?? $row->objective_score ?? null;
                if ($score === null && $res && in_array($res->status, ['selesai', 'berlangsung'])) {
                    $score = $res->objective_score;
                }

                $row->score = $score;
                $row->passed = $score === null
                    ? null
                    : ((float) $score >= (float) ($session->pass_score ?? 0));

                $row->session_status = $row->session_status ?? $row->status ?? ($res->status ?? 'belum_mulai');

                $row->start_time = !empty($row->started_at)
                    ? Carbon::parse($row->started_at)->format('H:i:s')
                    : '-';

                $row->finish_time = !empty($row->finished_at)
                    ? Carbon::parse($row->finished_at)->format('H:i:s')
                    : '-';

                $row->violation_count = max(
                    (int) ($row->violation_count ?? 0),
                    (int) ($nis ? ($violationMap[$nis] ?? 0) : 0)
                );

                $row->student = (object) [
                    'nisn' => $nis ?? '-',
                    'user' => (object) ['name' => $name],
                ];

                return $row;
            });

        // 6. Rincian pelanggaran dari View v_violation_summary
        $violations = $conn->table('v_violation_summary')
            ->where('exam_id', $id)
            ->orderByDesc('last_occurred_at')
            ->get()
            ->map(function ($v) {
                $v->type = $v->violation_type;
                $v->occurred_at = $v->last_occurred_at;
                $v->description = 'Terdeteksi meninggalkan halaman ujian (pindah tab browser).';

                return $v;
            });

        return view('admin.monitoring.show', compact('session', 'participants', 'violations'));
    }
}