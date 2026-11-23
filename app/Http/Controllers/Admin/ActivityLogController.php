<?php
// Simpan di: app/Http/Controllers/Admin/ActivityLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ActivityLogController extends Controller
{
    /**
     * tabel => [kelompok, label, kolom ringkasan yang ditampilkan di daftar log]
     * Sekaligus menjadi whitelist nama tabel.
     */
    public const TABLES = [
        // Pengguna
        'users'    => ['Pengguna', 'Akun Pengguna', ['user_id', 'name', 'email', 'role']],
        'students' => ['Pengguna', 'Siswa', ['nis', 'nisn', 'class_id', 'status']],
        'teachers' => ['Pengguna', 'Guru', ['nip', 'specialization', 'status']],
        // Akademik
        'classes'               => ['Akademik', 'Kelas', ['class_id', 'name', 'homeroom_teacher_id', 'academic_year']],
        'subjects'              => ['Akademik', 'Mata Pelajaran', ['code', 'name']],
        'class_subject_teacher' => ['Akademik', 'Kelas - Mapel - Guru', ['cst_id', 'class_id', 'subject_code', 'teacher_id']],
        'class_enrollments'     => ['Akademik', 'Pendaftaran Kelas', ['enrollment_id', 'students_id', 'cst_id', 'status']],
        'cst_students'          => ['Akademik', 'Siswa per Mapel', ['cst_id', 'students_id', 'status']],
        'schedules'             => ['Akademik', 'Jadwal', ['cst_id', 'day', 'start_time', 'end_time']],
        // Pembelajaran
        'materials'              => ['Pembelajaran', 'Materi', ['material_id', 'cst_id', 'title']],
        'assignments'            => ['Pembelajaran', 'Tugas', ['assignment_id', 'cst_id', 'title', 'due_date']],
        'assignment_submissions' => ['Pembelajaran', 'Pengumpulan Tugas', ['assignment_id', 'students_id', 'score', 'submitted_at']],
        'grades'                 => ['Pembelajaran', 'Nilai', ['grade_id', 'students_id', 'cst_id', 'grade_type', 'old_score', 'new_score']],
        // Ujian
        'exams'           => ['Ujian', 'Ujian', ['exam_id', 'cst_id', 'title', 'exam_type']],
        'exam_token'      => ['Ujian', 'Token Ujian', ['exam_token_id', 'exam_id', 'issued_at', 'expires_at', 'is_active']],
        'exam_questions'  => ['Ujian', 'Soal Ujian', ['exam_question_id', 'exam_id', 'question_type', 'score_weight']],
        'exam_sessions'   => ['Ujian', 'Sesi Ujian', ['exam_session_id', 'exam_id', 'student_id', 'status']],
        'exam_answers'    => ['Ujian', 'Jawaban Ujian', ['exam_answer_id', 'exam_id', 'students_id', 'score']],
        'exam_violations' => ['Ujian', 'Pelanggaran Ujian', ['exam_violation_id', 'exam_session_id', 'students_id', 'type']],
        // Informasi
        'announcements' => ['Informasi', 'Pengumuman', ['announcement_id', 'title', 'category', 'is_public']],
        'notifications' => ['Informasi', 'Notifikasi', ['notification_id', 'user_id', 'title', 'is_read']],
    ];

    private const ACTIONS = ['INSERT', 'UPDATE_BEFORE', 'UPDATE_AFTER', 'DELETE'];
    private const META    = ['log_id', 'log_action', 'log_by', 'log_at', 'action', 'db_user', 'changed_at'];

    private function conn()
    {
        return DB::connection('mysql_admin');
    }

    private function schemaConn()
    {
        return Schema::connection('mysql_admin');
    }

    private function resolveLogTable(string $table): string
    {
        return $table === 'grades' ? 'grade_logs' : "{$table}_logs";
    }

    private function actionCol(string $logTable): string
    {
        return $logTable === 'grade_logs' ? 'action' : 'log_action';
    }

    private function timeCol(string $logTable): string
    {
        return $logTable === 'grade_logs' ? 'changed_at' : 'log_at';
    }

    private function actorCol(string $logTable): string
    {
        return $logTable === 'grade_logs' ? 'db_user' : 'log_by';
    }

    private function normalizeAction(?string $val): string
    {
        return strtoupper((string) $val);
    }

    /** Halaman 1: kotak-kotak semua tabel (memanfaatkan VIEW v_all_logs + grade_logs). */
    public function index()
    {
        $summary = Cache::remember('activity_log_summary', 60, function () {
            $out = [];
            foreach (array_keys(self::TABLES) as $t) {
                $out[$t] = ['total' => 0, 'ins' => 0, 'upd' => 0, 'del' => 0, 'last_at' => null];
            }

            $conn   = $this->conn();
            $schema = $this->schemaConn();

            // 1. Ambil rekap ke-19 tabel sekaligus dari VIEW v_all_logs
            if ($schema->hasView('v_all_logs') || $schema->hasTable('v_all_logs')) {
                $rows = $conn->table('v_all_logs')
                    ->selectRaw(
                        "source_table,
                         COUNT(*) AS total,
                         COALESCE(SUM(log_action = 'INSERT'), 0) AS ins,
                         COALESCE(SUM(log_action = 'UPDATE_AFTER'), 0) AS upd,
                         COALESCE(SUM(log_action = 'DELETE'), 0) AS del,
                         MAX(log_at) AS last_at"
                    )
                    ->groupBy('source_table')
                    ->get();

                foreach ($rows as $r) {
                    if (isset($out[$r->source_table])) {
                        $out[$r->source_table] = [
                            'total'   => (int) $r->total,
                            'ins'     => (int) $r->ins,
                            'upd'     => (int) $r->upd,
                            'del'     => (int) $r->del,
                            'last_at' => $r->last_at,
                        ];
                    }
                }
            } else {
                foreach (array_keys(self::TABLES) as $t) {
                    if ($t === 'grades') {
                        continue;
                    }
                    $logTable = "{$t}_logs";
                    if (! $schema->hasTable($logTable)) {
                        continue;
                    }
                    $row = $conn->table($logTable)->selectRaw(
                        "COUNT(*) AS total,
                         COALESCE(SUM(log_action = 'INSERT'), 0) AS ins,
                         COALESCE(SUM(log_action = 'UPDATE_AFTER'), 0) AS upd,
                         COALESCE(SUM(log_action = 'DELETE'), 0) AS del,
                         MAX(log_at) AS last_at"
                    )->first();
                    $out[$t] = (array) $row;
                }
            }

            // 2. Ambil rekap tabel khusus grade_logs
            if ($schema->hasTable('grade_logs')) {
                $gRow = $conn->table('grade_logs')->selectRaw(
                    "COUNT(*) AS total,
                     COALESCE(SUM(LOWER(action) = 'insert'), 0) AS ins,
                     COALESCE(SUM(LOWER(action) = 'update'), 0) AS upd,
                     COALESCE(SUM(LOWER(action) = 'delete'), 0) AS del,
                     MAX(changed_at) AS last_at"
                )->first();

                $out['grades'] = [
                    'total'   => (int) ($gRow->total ?? 0),
                    'ins'     => (int) ($gRow->ins ?? 0),
                    'upd'     => (int) ($gRow->upd ?? 0),
                    'del'     => (int) ($gRow->del ?? 0),
                    'last_at' => $gRow->last_at ?? null,
                ];
            }

            return $out;
        });

        $groups = [];
        foreach (self::TABLES as $t => [$group, $label]) {
            $groups[$group][$t] = ['label' => $label] + $summary[$t];
        }

        return view('admin.logs.index', [
            'groups'     => $groups,
            'grandTotal' => collect($summary)->sum('total'),
        ]);
    }

    /** Halaman 2: isi log dari satu tabel. */
    public function table(Request $request, string $table)
    {
        abort_unless(isset(self::TABLES[$table]), 404);

        [$group, $label, $summaryCols] = self::TABLES[$table];
        $logTable = $this->resolveLogTable($table);
        abort_unless($this->schemaConn()->hasTable($logTable), 404);

        $aCol = $this->actionCol($logTable);
        $tCol = $this->timeCol($logTable);
        $uCol = $this->actorCol($logTable);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', Rule::in(self::ACTIONS)],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = $this->conn()->table($logTable);

        if (!empty($filters['action'])) {
            if ($logTable === 'grade_logs') {
                $mapped = match ($filters['action']) {
                    'INSERT'                        => 'insert',
                    'UPDATE_BEFORE', 'UPDATE_AFTER' => 'update',
                    'DELETE'                        => 'delete',
                };
                $query->whereRaw("LOWER(`{$aCol}`) = ?", [$mapped]);
            } else {
                $query->where($aCol, $filters['action']);
            }
        }

        if (!empty($filters['from'])) {
            $query->where($tCol, '>=', $filters['from'] . ' 00:00:00');
        }
        if (!empty($filters['to'])) {
            $query->where($tCol, '<=', $filters['to'] . ' 23:59:59');
        }
        if (!empty($filters['search'])) {
            $term    = $filters['search'];
            $columns = array_diff($this->schemaConn()->getColumnListing($logTable), ['log_id', $aCol, $tCol]);
            $ids     = $this->conn()->table('users')->where('name', 'like', "%{$term}%")->pluck('user_id')->all();

            $query->where(function ($w) use ($columns, $term, $ids, $uCol) {
                foreach ($columns as $col) {
                    $w->orWhereRaw("CAST(`{$col}` AS CHAR) LIKE ?", ["%{$term}%"]);
                }
                if ($ids) {
                    $w->orWhereIn($uCol, $ids);
                }
            });
        }

        $logs = $query->orderByDesc($tCol)->orderByDesc('log_id')
                      ->paginate(25)->withQueryString();

        $logs->getCollection()->transform(function ($item) use ($aCol, $tCol, $uCol) {
            $item->log_action = $item->{$aCol} ?? null;
            $item->log_at     = $item->{$tCol} ?? null;
            $item->log_by     = $item->{$uCol} ?? null;
            return $item;
        });

        $users = $this->conn()->table('users')
            ->whereIn('user_id', $logs->pluck('log_by')->filter()->unique()->all())
            ->pluck('name', 'user_id');

        if ($logTable === 'grade_logs') {
            $stats = $this->conn()->table($logTable)->selectRaw(
                "COUNT(*) AS total,
                 COALESCE(SUM(LOWER(`{$aCol}`) = 'insert'), 0) AS ins,
                 COALESCE(SUM(LOWER(`{$aCol}`) = 'update'), 0) AS upd,
                 COALESCE(SUM(LOWER(`{$aCol}`) = 'delete'), 0) AS del"
            )->first();
        } else {
            $stats = $this->conn()->table($logTable)->selectRaw(
                "COUNT(*) AS total,
                 COALESCE(SUM(`{$aCol}` = 'INSERT'), 0) AS ins,
                 COALESCE(SUM(`{$aCol}` = 'UPDATE_AFTER'), 0) AS upd,
                 COALESCE(SUM(`{$aCol}` = 'DELETE'), 0) AS del"
            )->first();
        }

        return view('admin.logs.table', [
            'table'   => $table,
            'label'   => $label,
            'group'   => $group,
            'cols'    => $summaryCols,
            'logs'    => $logs,
            'users'   => $users,
            'stats'   => $stats,
            'actions' => self::ACTIONS,
        ]);
    }

    /** Halaman 3: detail satu log (data lama vs baru). */
    public function show(string $table, int $id)
    {
        abort_unless(isset(self::TABLES[$table]), 404);

        $logTable = $this->resolveLogTable($table);
        abort_unless($this->schemaConn()->hasTable($logTable), 404);

        $aCol = $this->actionCol($logTable);
        $tCol = $this->timeCol($logTable);
        $uCol = $this->actorCol($logTable);

        $row = $this->conn()->table($logTable)->where('log_id', $id)->first();
        abort_if(!$row, 404);

        $row->log_action = $row->{$aCol} ?? null;
        $row->log_at     = $row->{$tCol} ?? null;
        $row->log_by     = $row->{$uCol} ?? null;

        $actionUpper = $this->normalizeAction($row->log_action);
        $old = $new = null;

        if ($actionUpper === 'INSERT') {
            $new = $row;
        } elseif ($actionUpper === 'DELETE') {
            $old = $row;
        } elseif ($actionUpper === 'UPDATE_BEFORE') {
            $old = $row;
            $new = $this->counterpart($logTable, $row, 'UPDATE_AFTER', '>');
        } elseif ($actionUpper === 'UPDATE_AFTER') {
            $new = $row;
            $old = $this->counterpart($logTable, $row, 'UPDATE_BEFORE', '<');
        } elseif ($actionUpper === 'UPDATE') {
            $new = $row;
        }

        $base    = (array) ($new ?? $old);
        $columns = array_values(array_diff(array_keys($base), self::META));

        $rows = [];
        foreach ($columns as $col) {
            $o = $old ? $old->{$col} : null;
            $n = $new ? $new->{$col} : null;
            $rows[] = ['column' => $col, 'old' => $o, 'new' => $n, 'changed' => $old && $new && $o != $n];
        }

        $by = $row->log_by
            ? $this->conn()->table('users')->where('user_id', $row->log_by)->value('name')
            : null;

        return view('admin.logs.show', [
            'table'  => $table,
            'label'  => self::TABLES[$table][1],
            'row'    => $row,
            'rows'   => $rows,
            'hasOld' => (bool) $old,
            'hasNew' => (bool) $new,
            'by'     => $by,
        ]);
    }

    /** Helper untuk Dashboard: ambil gabungan log terbaru dari VIEW v_all_logs + grade_logs. */
    public static function recentAcrossTables(int $limit = 7)
    {
        $conn    = DB::connection('mysql_admin');
        $schema  = Schema::connection('mysql_admin');
        $allLogs = collect();

        // 1. Ambil dari VIEW v_all_logs (sudah menggabungkan 19 tabel log)
        if ($schema->hasView('v_all_logs') || $schema->hasTable('v_all_logs')) {
            $viewItems = $conn->table('v_all_logs')
                ->where('log_action', '!=', 'UPDATE_BEFORE')
                ->orderByDesc('log_at')
                ->orderByDesc('log_id')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    $table       = $row->source_table;
                    $meta        = self::TABLES[$table] ?? ['Sistem', ucfirst($table), []];
                    $summaryCols = $meta[2];
                    $data        = json_decode((string) ($row->row_data ?? '{}'), true) ?: [];

                    $highlights = [];
                    foreach (array_slice($summaryCols, 0, 2) as $col) {
                        if (isset($data[$col]) && $data[$col] !== null) {
                            $highlights[] = "{$col}: {$data[$col]}";
                        }
                    }
                    if (empty($highlights) && !empty($row->record_id)) {
                        $highlights[] = "ID: {$row->record_id}";
                    }

                    return (object) [
                        'log_id'      => $row->log_id,
                        'table'       => $table,
                        'table_label' => $meta[1],
                        'action'      => strtoupper((string) $row->log_action),
                        'actor'       => $row->log_by,
                        'logged_at'   => $row->log_at,
                        'summary'     => implode(' · ', $highlights),
                    ];
                });

            $allLogs = $allLogs->merge($viewItems);
        }

        // 2. Gabungkan dengan grade_logs
        if ($schema->hasTable('grade_logs')) {
            $gradeItems = $conn->table('grade_logs')
                ->orderByDesc('changed_at')
                ->orderByDesc('log_id')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    $scoreInfo = $row->new_score !== null ? "Skor: {$row->new_score}" : 'Dihapus';
                    return (object) [
                        'log_id'      => $row->log_id,
                        'table'       => 'grades',
                        'table_label' => 'Nilai',
                        'action'      => strtoupper((string) $row->action),
                        'actor'       => $row->db_user,
                        'logged_at'   => $row->changed_at,
                        'summary'     => "NIS: {$row->students_id} · {$row->grade_type} ({$scoreInfo})",
                    ];
                });

            $allLogs = $allLogs->merge($gradeItems);
        }

        $recent = $allLogs
            ->sortByDesc(fn ($item) => $item->logged_at ?? '')
            ->take($limit)
            ->values();

        $actorNames = $conn->table('users')
            ->whereIn('user_id', $recent->pluck('actor')->filter()->unique()->all())
            ->pluck('name', 'user_id');

        return $recent->transform(function ($item) use ($actorNames) {
            $item->actor_name = ($item->actor && isset($actorNames[$item->actor]))
                ? $actorNames[$item->actor]
                : ($item->actor ?? 'Sistem');
            return $item;
        });
    }

    private function counterpart(string $logTable, object $row, string $action, string $op): ?object
    {
        $aCol = $this->actionCol($logTable);
        $uCol = $this->actorCol($logTable);

        return $this->conn()->table($logTable)
            ->where($aCol, $action)
            ->where($uCol, $row->log_by)
            ->where('log_id', $op, $row->log_id)
            ->orderBy('log_id', $op === '>' ? 'asc' : 'desc')
            ->first();
    }
}