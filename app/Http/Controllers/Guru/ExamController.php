<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Services\Access;
use App\Services\ExamGrader;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    private function own(Request $request, Exam $exam): void
    {
        Access::authorizeTeacher($request->user(), $exam->classSubjectTeacher);
    }

    public function index(Request $request)
    {
        $exams = Exam::with('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass')
            ->withCount('questions', 'sessions')
            ->whereHas('classSubjectTeacher', fn ($q) => $q->where('teacher_id', $request->user()->teacher->nip))
            ->latest()->paginate(15);
        $csts = ClassSubjectTeacher::with('subject', 'schoolClass')
            ->where('teacher_id', $request->user()->teacher->nip)->orderBy('class_id')->get();

        return view('guru.exams.index', compact('exams', 'csts'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'cst_id' => 'required|exists:class_subject_teacher,cst_id',
            'title' => 'required|string|max:150',
            'type' => ['required', Rule::in(['kuis', 'uts', 'uas'])],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'total_questions' => 'required|integer|min:1|max:100',
            'instructions' => 'nullable|string',
        ]);
        Access::authorizeTeacher($request->user(), ClassSubjectTeacher::findOrFail($v['cst_id']));
        $exam = Exam::create($v + ['require_face_photo' => true, 'is_published' => false]);

        return redirect()->route('guru.exams.show', $exam)->with('success', 'Ujian dibuat. Silakan isi soal beserta kunci jawaban.');
    }

    public function show(Request $request, Exam $exam)
    {
        $this->own($request, $exam);
        $exam->load('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass', 'questions');

        return view('guru.exams.show', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->own($request, $exam);
        $v = $request->validate([
            'title' => 'required|string|max:150',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'total_questions' => 'required|integer|min:1|max:100',
            'instructions' => 'nullable|string',
        ]);
        $exam->update($v + ['require_face_photo' => $request->boolean('require_face_photo')]);

        return back()->with('success', 'Pengaturan ujian diperbarui.');
    }

    public function destroy(Request $request, Exam $exam)
    {
        $this->own($request, $exam);
        $exam->delete();

        return redirect()->route('guru.exams.index')->with('success', 'Ujian dihapus.');
    }

    public function togglePublish(Request $request, Exam $exam)
    {
        $this->own($request, $exam);
        if (! $exam->is_published && ! $exam->isComplete()) {
            return back()->with('error', "Jumlah soal harus tepat {$exam->total_questions} (saat ini {$exam->questions()->count()}).");
        }
        $exam->update(['is_published' => ! $exam->is_published]);

        return back()->with('success', $exam->is_published ? 'Ujian dipublikasikan ke siswa.' : 'Ujian ditarik dari siswa.');
    }

    // ---------- Soal ----------
    public function storeQuestion(Request $request, Exam $exam)
    {
        $this->own($request, $exam);
        $data = $this->parseQuestion($request);
        if (is_string($data)) {
            return back()->withInput()->with('error', $data);
        }
        if ($exam->questions()->count() >= $exam->total_questions) {
            return back()->withInput()->with('error', "Batas soal ({$exam->total_questions}) sudah tercapai.");
        }
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('questions', 'public');
        }
        $exam->questions()->create($data);

        return back()->with('success', 'Soal ditambahkan.');
    }

    public function destroyQuestion(Request $request, Exam $exam, ExamQuestion $question)
    {
        $this->own($request, $exam);
        abort_unless($question->exam_id === $exam->id, 404);
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }

    /** Ubah input form guru menjadi struktur options/kunci. Mengembalikan array atau string error. */
    private function parseQuestion(Request $request): array|string
    {
        $request->validate([
            'question_type' => ['required', Rule::in(array_keys(config('school.question_types')))],
            'question_text' => 'required|string',
            'score_weight' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
        $type = $request->input('question_type');
        $lines = fn (?string $s) => array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $s)), 'strlen'));

        $options = null;
        $key = null;
        switch ($type) {
            case 'pilihan_ganda':
                $options = $lines($request->input('options'));
                $key = $request->filled('key_single') ? (int) $request->input('key_single') - 1 : null; // input 1-based
                break;
            case 'pilihan_ganda_kompleks':
                $options = $lines($request->input('options'));
                $key = array_values(array_unique(array_map(fn ($n) => (int) trim($n) - 1, array_filter(explode(',', (string) $request->input('key_multi')), 'strlen'))));
                break;
            case 'benar_salah': // satu baris per pernyataan: "teks pernyataan | B" atau "| S"
                $options = [];
                $key = [];
                foreach ($lines($request->input('options')) as $line) {
                    [$text, $flag] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                    $options[] = $text;
                    $key[] = in_array(strtoupper($flag), ['B', 'BENAR', 'TRUE'], true);
                }
                break;
            case 'menjodohkan': // premis: "teks premis | nomor jawaban yang benar"
                $options = ['left' => [], 'right' => $lines($request->input('pair_right'))];
                $key = [];
                foreach ($lines($request->input('pair_left')) as $i => $line) {
                    [$text, $num] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '0');
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
            // Esai tidak berbobot
            'score_weight' => $type === 'esai' ? 0 : (float) $request->input('score_weight', 1),
        ];
    }

    // ---------- Hasil & koreksi esai ----------
    public function results(Request $request, Exam $exam, ExamGrader $grader)
    {
        $this->own($request, $exam);
        // Tutup sesi yang waktunya habis
        $exam->sessions()->where('status', 'berlangsung')->where('deadline_at', '<', now())->get()
            ->each(fn ($s) => $grader->gradeSession($s));

        $exam->load('classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass.students.user', 'questions');
        $sessions = $exam->sessions()->with('student.user')->withCount('violations')->get()->keyBy('students_id');

        return view('guru.exams.results', compact('exam', 'sessions'));
    }

    public function session(Request $request, ExamSession $session)
    {
        $exam = $session->exam()->with('questions', 'classSubjectTeacher.subject')->first();
        Access::authorizeTeacher($request->user(), $exam->classSubjectTeacher);
        $session->load('student.user', 'violations');
        $answers = $exam->answers()->where('students_id', $session->students_id)->get()->keyBy('question_id');

        return view('guru.exams.session', compact('session', 'exam', 'answers'));
    }

    public function gradeEssay(Request $request, ExamSession $session, ReportCardService $svc)
    {
        $exam = $session->exam()->with('classSubjectTeacher')->first();
        Access::authorizeTeacher($request->user(), $exam->classSubjectTeacher);
        $v = $request->validate(['essay_score' => 'required|numeric|min:0|max:100', 'teacher_note' => 'nullable|string|max:1000']);
        $session->update($v + ['status' => 'dinilai']);
        $svc->syncFromActivity($session->student, $exam->classSubjectTeacher);

        return back()->with('success', 'Nilai esai tersimpan (tidak memengaruhi bobot nilai objektif).');
    }
}
