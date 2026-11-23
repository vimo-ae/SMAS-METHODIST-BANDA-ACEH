<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Services\Access;
use App\Services\ExamGrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    private function own(Request $request, Exam $exam): void
    {
        if (!$exam->classSubjectTeacher) {
            abort(404, 'Ujian ini tidak memiliki data pengampu kelas (cst_id kosong). Silakan hapus atau perbaiki data ujian ini.');
        }

        Access::authorizeTeacher($request->user(), $exam->classSubjectTeacher);
    }

    public function index(Request $request)
    {
        $exams = Exam::with('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass')
            ->withCount('questions', 'sessions')
            ->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $request->user()->teacher->nip))
            ->latest()
            ->paginate(6);

        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->where('teacher_id', $request->user()->teacher->nip)
            ->orderBy('class_id')
            ->get();

        return view('guru.exams.index', compact('exams', 'csts'));
    }

    public function create(Request $request)
    {
        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->where('teacher_id', $request->user()->teacher->nip)
            ->orderBy('class_id')
            ->get();

        return view('guru.exams.create', compact('csts'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'cst_id' => 'required|exists:class_subject_teacher,cst_id',
            'title' => 'required|string|max:150',
            'exam_type' => ['required', Rule::in(['uts', 'uas', 'kuis'])],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'pass_score' => 'required|numeric|min:0|max:100',
            'instructions' => 'nullable|string',
        ]);

        Access::authorizeTeacher(
            $request->user(),
            ClassSubjectTeacher::findOrFail($v['cst_id'])
        );

        $exam = Exam::create($v + [
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'is_published' => false,
        ]);

        return redirect()
            ->route('guru.exams.show', $exam)
            ->with('success', 'Ujian dibuat. Silakan isi soal beserta kunci jawaban.');
    }

    public function show(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        $exam->load(
            'classSubjectTeacher.subject',
            'classSubjectTeacher.schoolClass',
            'questions'
        );

        $this->syncQuestionWeights($exam);
        $exam->load('questions');

        return view('guru.exams.show', compact('exam'));
    }

    public function edit(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        return view('guru.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        $v = $request->validate([
            'title' => 'required|string|max:150',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'pass_score' => 'required|numeric|min:0|max:100',
            'instructions' => 'nullable|string',
        ]);

        $exam->update($v + [
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
        ]);

        return redirect()
            ->route('guru.exams.index')
            ->with('success', 'Ujian berhasil diedit.');
    }

    public function destroy(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        // Hapus file gambar semua soal milik ujian ini
        $imagePaths = DB::connection('mysql_guru')
            ->table('exam_questions')
            ->where('exam_id', $exam->exam_id)
            ->whereNotNull('image_path')
            ->pluck('image_path');

        $exam->delete();

        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('guru.exams.index')
            ->with('success', 'Ujian dihapus.');
    }

    public function togglePublish(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        if (!$exam->is_published && !$exam->isComplete()) {
            return back()->with(
                'error',
                'Ujian belum punya soal. Tambahkan soal terlebih dahulu.'
            );
        }

        $exam->update([
            'is_published' => !$exam->is_published
        ]);

        return back()->with(
            'success',
            $exam->is_published
                ? 'Ujian dipublikasikan ke siswa.'
                : 'Ujian ditarik dari siswa.'
        );
    }

    // =========================================================
    // SOAL UJIAN
    // =========================================================

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('upload')->store('exam-questions', 'public');

        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan gambar soal.');
        }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        $this->own($request, $exam);

        $data = $this->parseQuestion($request);

        if (is_string($data)) {
            return back()->withInput()->with('error', $data);
        }

        $connection = DB::connection('mysql_guru');

        $questionCount = $connection
            ->table('exam_questions')
            ->where('exam_id', $exam->exam_id)
            ->count();

        $data['score_weight'] = 100 / ($questionCount + 1);

        // Buat soal menggunakan Stored Procedure yang sudah ada
        $createdQuestion = $connection->select(
            'CALL sp_create_exam_question(?, ?, ?, ?, ?, ?, ?)',
            [
                $exam->exam_id,
                $request->user()->teacher->nip,
                $data['question_type'],
                $data['question_text'],
                $data['options'] !== null
                    ? json_encode($data['options'])
                    : null,
                $data['correct_answer'] !== null
                    ? json_encode($data['correct_answer'])
                    : null,
                $data['score_weight'],
            ]
        );

        // Simpan gambar jika guru mengunggah gambar
        if ($request->hasFile('image')) {
            $questionId = $createdQuestion[0]->exam_question_id ?? null;

            if (!$questionId) {
                return back()->with(
                    'error',
                    'Soal dibuat, tetapi ID soal tidak ditemukan untuk menyimpan gambar.'
                );
            }

            $imagePath = $request->file('image')->store(
                'exam-questions/' . $exam->exam_id,
                'public'
            );

            $connection->table('exam_questions')
                ->where('exam_question_id', $questionId)
                ->update([
                    'image_path' => $imagePath,
                    'updated_at' => now(),
                ]);
        }

        $this->syncQuestionWeights($exam);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function updateQuestion(
        Request $request,
        Exam $exam,
        ExamQuestion $question
    ) {
        $this->own($request, $exam);

        abort_unless(
            $question->exam_id == $exam->exam_id,
            404
        );

        $data = $this->parseQuestion($request);

        if (is_string($data)) {
            return back()->withInput()->with('error', $data);
        }

        $connection = DB::connection('mysql_guru');

        // Pertahankan bobot soal sebelumnya
        $data['score_weight'] = $question->score_weight;

        // Ambil lokasi gambar lama
        $oldImage = $connection->table('exam_questions')
            ->where('exam_question_id', $question->exam_question_id)
            ->value('image_path');

        $newImage = null;

        // Jika guru mengunggah gambar baru
        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store(
                'exam-questions/' . $exam->exam_id,
                'public'
            );

            $data['image_path'] = $newImage;
        }

        // Simpan perubahan soal ke database
        $connection->table('exam_questions')
            ->where('exam_question_id', $question->exam_question_id)
            ->update([
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'options' => $data['options'] !== null
                    ? json_encode($data['options'])
                    : null,
                'correct_answer' => $data['correct_answer'] !== null
                    ? json_encode($data['correct_answer'])
                    : null,
                'score_weight' => $data['score_weight'],
                'image_path' => $newImage ?? $oldImage,
                'updated_at' => now(),
            ]);

        // Hapus gambar lama setelah gambar baru tersimpan
        if (
            $newImage &&
            $oldImage &&
            $oldImage !== $newImage
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        $this->syncQuestionWeights($exam);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function updateScore(
        Request $request,
        Exam $exam,
        ExamQuestion $question
    ) {
        $this->own($request, $exam);

        abort_unless(
            $question->exam_id == $exam->exam_id,
            404
        );

        $request->validate([
            'score_weight' => 'required|numeric|min:0|max:100',
        ]);

        $question->update([
            'score_weight' => $request->score_weight
        ]);

        return back()->with(
            'success',
            'Bobot soal berhasil diperbarui.'
        );
    }

    public function destroyQuestion(
        Request $request,
        Exam $exam,
        ExamQuestion $question
    ) {
        $this->own($request, $exam);

        abort_unless(
            $question->exam_id == $exam->exam_id,
            404
        );

        // Ambil lokasi gambar sebelum soal dihapus
        $imagePath = DB::connection('mysql_guru')
            ->table('exam_questions')
            ->where('exam_question_id', $question->exam_question_id)
            ->value('image_path');

        // Hapus soal dari database
        DB::connection('mysql_guru')
            ->table('exam_questions')
            ->where('exam_question_id', $question->exam_question_id)
            ->delete();

        // Hapus file gambarnya
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $this->syncQuestionWeights($exam);

        return back()->with('success', 'Soal berhasil dihapus.');
    }

    // =========================================================
    // SINKRONISASI BOBOT SOAL
    // =========================================================

    private function syncQuestionWeights(Exam $exam): bool
    {
        $connection = DB::connection('mysql_guru');

        $questionIds = $connection->table('exam_questions')
            ->where('exam_id', $exam->exam_id)
            ->orderBy('exam_question_id')
            ->pluck('exam_question_id')
            ->all();

        $weights = ExamGrader::equalQuestionWeights(count($questionIds));
        $changed = false;

        foreach ($questionIds as $index => $questionId) {
            $weight = $weights[$index];

            $currentWeight = (float) $connection
                ->table('exam_questions')
                ->where('exam_question_id', $questionId)
                ->value('score_weight');

            if ($currentWeight !== $weight) {
                $connection->table('exam_questions')
                    ->where('exam_question_id', $questionId)
                    ->update([
                        'score_weight' => $weight
                    ]);

                $changed = true;
            }
        }

        return $changed;
    }

    // =========================================================
    // PARSE DAN VALIDASI SOAL
    // =========================================================

    private function parseQuestion(Request $request): array|string
    {
        $request->validate([
            'question_type' => [
                'required',
                Rule::in(array_keys(config('school.question_types')))
            ],
            'question_text' => 'required|string',
            'score_weight' => 'nullable|numeric|min:0|max:100',
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $type = $request->input('question_type');

        $lines = fn (?string $s) => array_values(
            array_filter(
                array_map(
                    'trim',
                    preg_split('/\R/', (string) $s)
                ),
                'strlen'
            )
        );

        $options = null;
        $key = null;

        switch ($type) {
            case 'pilihan_ganda':
                $options = $lines($request->input('options'));

                $key = $request->filled('key_single')
                    ? (int) $request->input('key_single') - 1
                    : null;
                break;

            case 'pilihan_ganda_kompleks':
                $options = $lines($request->input('options'));

                $key = array_values(array_unique(
                    array_map(
                        fn ($n) => (int) trim($n) - 1,
                        array_filter(
                            explode(',', (string) $request->input('key_multi')),
                            'strlen'
                        )
                    )
                ));
                break;

            case 'benar_salah':
                $options = [];
                $key = [];

                foreach ($lines($request->input('options')) as $line) {
                    [$text, $flag] = array_pad(
                        array_map('trim', explode('|', $line, 2)),
                        2,
                        ''
                    );

                    $options[] = $text;

                    $key[] = in_array(
                        strtoupper($flag),
                        ['B', 'BENAR', 'TRUE'],
                        true
                    );
                }
                break;

            case 'menjodohkan':
                $options = [
                    'left' => [],
                    'right' => $lines($request->input('pair_right')),
                ];

                $key = [];

                foreach ($lines($request->input('pair_left')) as $i => $line) {
                    [$text, $num] = array_pad(
                        array_map('trim', explode('|', $line, 2)),
                        2,
                        '0'
                    );

                    $options['left'][] = $text;
                    $key[(string) $i] = (int) $num - 1;
                }
                break;
        }

        if ($err = ExamGrader::validateKey($type, $options, $key)) {
            return $err;
        }

        return [
            'question_text' => $request->input('question_text'),
            'question_type' => $type,
            'options' => $options,
            'correct_answer' => $key,
            'score_weight' => $request->filled('score_weight')
                ? (float) $request->input('score_weight')
                : 100,
        ];
    }

    // =========================================================
    // HASIL UJIAN DAN KOREKSI ESAI
    // =========================================================

    public function results(
        Request $request,
        Exam $exam,
        ExamGrader $grader
    ) {
        $this->own($request, $exam);

        // Tutup sesi yang waktunya habis
        $exam->sessions()
            ->where('status', 'berlangsung')
            ->with('exam')
            ->get()
            ->filter(fn ($s) => now()->gte($s->deadline()))
            ->each(fn ($s) => $grader->gradeSession($s, 'mysql_guru'));

        $exam->load(
            'classSubjectTeacher.subject',
            'classSubjectTeacher.schoolClass.students.user',
            'questions'
        );

        $weightsChanged = $this->syncQuestionWeights($exam);
        $exam->load('questions');

        $sessions = $exam->sessions()
            ->with('student.user')
            ->withCount('violations')
            ->get()
            ->keyBy('student_id');

        $essayQuestionIds = $exam->questions
            ->where('question_type', 'esai')
            ->pluck('exam_question_id');
        $finishedSessions = $sessions->whereIn('status', ['selesai', 'keluar']);
        $gradedEssayCounts = collect();

        if ($essayQuestionIds->isNotEmpty() && $finishedSessions->isNotEmpty()) {
            $gradedEssayCounts = DB::connection('mysql_guru')
                ->table('exam_answers')
                ->where('exam_id', $exam->exam_id)
                ->whereIn('question_id', $essayQuestionIds)
                ->whereIn('students_id', $finishedSessions->pluck('student_id'))
                ->whereNotNull('graded_at')
                ->whereNotNull('score')
                ->select('students_id')
                ->selectRaw('COUNT(DISTINCT question_id) AS graded_count')
                ->groupBy('students_id')
                ->pluck('graded_count', 'students_id');
        }

        $essayQuestionCount = $essayQuestionIds->count();
        $pendingEssayCount = max(
            0,
            ($essayQuestionCount * $finishedSessions->count()) - $gradedEssayCounts->sum()
        );

        if ($weightsChanged) {
            foreach ($sessions->whereIn('status', ['selesai', 'keluar']) as $session) {
                $grader->gradeAnswers($session);

                DB::connection('mysql_guru')
                    ->table('grades')
                    ->where('exam_id', $exam->exam_id)
                    ->where('students_id', $session->student_id)
                    ->update([
                        'score' => $grader->finalScore($session),
                        'updated_at' => now(),
                    ]);
            }
        }

        $examResultsView = DB::connection('mysql_guru')
            ->table('v_exam_results')
            ->where('exam_id', $exam->exam_id)
            ->get()
            ->keyBy('exam_session_id');

        $examStats = DB::connection('mysql_guru')
            ->table('v_exam_statistics')
            ->where('exam_id', $exam->exam_id)
            ->first();

        $violationSummaries = DB::connection('mysql_guru')
            ->table('v_violation_summary')
            ->where('exam_id', $exam->exam_id)
            ->get()
            ->groupBy('exam_session_id');

        return view('guru.exams.results', compact(
            'exam',
            'sessions',
            'examResultsView',
            'examStats',
            'essayQuestionCount',
            'gradedEssayCounts',
            'pendingEssayCount',
            'violationSummaries'
        ));
    }

    public function session(Request $request, ExamSession $session)
    {
        $exam = $session->exam()
            ->with('questions', 'classSubjectTeacher.subject')
            ->first();

        Access::authorizeTeacher(
            $request->user(),
            $exam->classSubjectTeacher
        );

        $session->load('student.user', 'violations');

        $answers = $exam->answers()
            ->where('students_id', $session->student_id)
            ->get()
            ->keyBy('question_id');

        return view('guru.exams.session', compact(
            'session',
            'exam',
            'answers'
        ));
    }

    public function gradeEssay(Request $request, ExamSession $session)
    {
        $exam = $session->exam()
            ->with('classSubjectTeacher')
            ->first();

        Access::authorizeTeacher(
            $request->user(),
            $exam->classSubjectTeacher
        );

        $essayQuestions = $exam->questions()
            ->where('question_type', 'esai')
            ->get(['exam_question_id', 'score_weight'])
            ->keyBy('exam_question_id');

        $rules = [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ];
        foreach ($essayQuestions as $question) {
            $rules['scores.'.$question->exam_question_id] = [
                'nullable',
                'numeric',
                'min:0',
                'max:'.$question->score_weight,
            ];
        }

        $v = $request->validate($rules);

        $teacherNip = $request->user()->teacher->nip;
        $connection = DB::connection('mysql_guru');

        foreach ($v['scores'] as $questionId => $score) {
            if (
                $score === null ||
                !$essayQuestions->has((int) $questionId)
            ) {
                continue;
            }

            $now = now();
            $connection->table('exam_answers')->upsert(
                [[
                    'exam_id' => $exam->exam_id,
                    'students_id' => $session->student_id,
                    'question_id' => (int) $questionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]],
                ['question_id', 'students_id'],
                ['updated_at']
            );

            $connection->select(
                'CALL sp_grade_exam_essay(?, ?, ?, ?)',
                [
                    $session->exam_session_id,
                    (int) $questionId,
                    $teacherNip,
                    (float) $score,
                ]
            );
        }

        return back()->with(
            'success',
            'Nilai esai tersimpan dan nilai akhir ujian berhasil disinkronkan.'
        );
    }

    public function forceFinish(Request $request, ExamSession $session)
    {
        $exam = $session->exam()
            ->with('classSubjectTeacher')
            ->first();

        Access::authorizeTeacher(
            $request->user(),
            $exam->classSubjectTeacher
        );

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        DB::connection('mysql_guru')->select(
            'CALL sp_force_finish_session(?, ?, ?)',
            [
                $session->exam_session_id,
                $request->user()->teacher->nip,
                $request->input('reason', 'Dihentikan paksa oleh guru'),
            ]
        );

        return back()->with(
            'success',
            'Sesi ujian siswa berhasil dihentikan paksa dan nilai telah direkam.'
        );
    }
}
