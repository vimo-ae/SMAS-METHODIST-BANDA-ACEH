<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ExamViolation;
use App\Services\ExamGrader;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /** Pastikan siswa berhak mengikuti ujian ini (kelas sudah di-join & ujian terbit). */
    private function authorizeExam(Request $request, Exam $exam)
    {
        $student = $request->user()->student;
        abort_unless($exam->is_published, 404);
        abort_unless(
            $student->joinedClasses()->where('class_subject_teacher.cst_id', $exam->cst_id)->exists(),
            403, 'Bergabunglah ke kelas mata pelajaran ini terlebih dahulu.'
        );

        return $student;
    }

    private function closeIfExpired(?ExamSession $session): ?ExamSession
    {
        if ($session && $session->status === 'berlangsung' && now()->gte($session->deadline_at)) {
            $session = app(ExamGrader::class)->gradeSession($session);
            $this->sync($session);
        }

        return $session;
    }

    private function sync(ExamSession $session): void
    {
        $exam = $session->exam()->with('classSubjectTeacher')->first();
        app(ReportCardService::class)->syncFromActivity($session->student, $exam->classSubjectTeacher);
    }

    public function index(Request $request)
    {
        $student = $request->user()->student;
        $cstIds = $student->joinedClasses()->pluck('class_subject_teacher.cst_id');
        $exams = Exam::with('classSubjectTeacher.subject')->withCount('questions')
            ->whereIn('cst_id', $cstIds)->where('is_published', true)->orderBy('start_time')->get();
        $sessions = $student->examSessions()->get()->keyBy('exam_id');
        $sessions->each(fn ($s) => $this->closeIfExpired($s));
        $sessions = $student->examSessions()->get()->keyBy('exam_id');

        return view('siswa.exams.index', compact('exams', 'sessions'));
    }

    public function show(Request $request, Exam $exam)
    {
        $student = $this->authorizeExam($request, $exam);
        $exam->load('classSubjectTeacher.subject');
        $session = $this->closeIfExpired(ExamSession::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->first());
        $exam->loadCount('questions');

        return view('siswa.exams.show', compact('exam', 'session'));
    }

    /** Absensi ujian: unggah foto wajah, lalu mulai sesi. */
    public function start(Request $request, Exam $exam)
    {
        $student = $this->authorizeExam($request, $exam);
        abort_unless($exam->isOpen(), 403, 'Ujian belum dibuka atau sudah ditutup.');

        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Soal belum tersedia.');
        }
        $session = ExamSession::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->first();
        if ($session) {
            return redirect()->route('siswa.exams.take', $exam);
        }

        $path = null;
        if ($exam->require_face_photo) {
            $request->validate(['face_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096']);
            $path = $request->file('face_photo')->store("exam-faces/{$exam->id}", 'local');
        }

        $deadline = now()->addMinutes($exam->duration_minutes);
        if ($deadline->gt($exam->end_time)) {
            $deadline = $exam->end_time->copy();
        }
        ExamSession::create([
            'exam_id' => $exam->id, 'students_id' => $student->nis, 'face_photo' => $path,
            'started_at' => now(), 'deadline_at' => $deadline, 'status' => 'berlangsung',
        ]);

        return redirect()->route('siswa.exams.take', $exam);
    }

    public function take(Request $request, Exam $exam)
    {
        $student = $this->authorizeExam($request, $exam);
        $session = ExamSession::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->first();
        abort_unless($session, 403, 'Lakukan absensi foto wajah terlebih dahulu.');
        $session = $this->closeIfExpired($session);
        if ($session->status !== 'berlangsung') {
            return redirect()->route('siswa.exams.show', $exam)->with('error', 'Ujian sudah berakhir.');
        }

        // Kunci jawaban TIDAK dikirim ke browser
        $questions = $exam->questions()->get()->map(fn ($q) => [
            'id' => $q->id, 'type' => $q->question_type, 'text' => $q->question_text,
            'options' => $q->options, 'weight' => $q->score_weight,
            'image' => $q->image_path ? asset('storage/'.$q->image_path) : null,
        ])->values();
        $saved = ExamAnswer::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->get()
            ->mapWithKeys(fn ($a) => [$a->question_id => ['answer' => $a->answer, 'file' => (bool) $a->answer_file]]);

        return view('siswa.exams.take', [
            'exam' => $exam->load('classSubjectTeacher.subject'), 'session' => $session,
            'questions' => $questions, 'saved' => $saved,
            'remaining' => max(0, now()->diffInSeconds($session->deadline_at, false)),
            'maxViolations' => config('school.exam.max_violations'),
        ]);
    }

    private function runningSession(Request $request, Exam $exam): ExamSession
    {
        $student = $this->authorizeExam($request, $exam);
        $session = ExamSession::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->firstOrFail();
        abort_unless($session->isRunning(), 403, 'Waktu ujian telah habis.');

        return $session;
    }

    public function answer(Request $request, Exam $exam)
    {
        $session = $this->runningSession($request, $exam);
        $v = $request->validate(['question_id' => 'required|integer', 'answer' => 'nullable']);
        $q = $exam->questions()->findOrFail($v['question_id']);
        abort_if($q->isEssay(), 422);

        ExamAnswer::updateOrCreate(
            ['exam_id' => $exam->id, 'students_id' => $session->students_id, 'question_id' => $q->id],
            ['answer' => $v['answer'] ?? null]
        );

        return response()->json(['ok' => true]);
    }

    /** Esai: unggah foto lembar jawaban. */
    public function essay(Request $request, Exam $exam, int $question)
    {
        $session = $this->runningSession($request, $exam);
        $q = $exam->questions()->where('question_type', 'esai')->findOrFail($question);
        $request->validate(['photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:6144']);
        $path = $request->file('photo')->store("exam-essays/{$exam->id}/{$session->students_id}", 'local');

        ExamAnswer::updateOrCreate(
            ['exam_id' => $exam->id, 'students_id' => $session->students_id, 'question_id' => $q->id],
            ['answer_file' => $path]
        );

        return response()->json(['ok' => true]);
    }

    public function violation(Request $request, Exam $exam)
    {
        $session = $this->runningSession($request, $exam);
        $v = $request->validate(['type' => 'required|in:tab_switch,window_blur,fullscreen_exit,copy_paste', 'detail' => 'nullable|string|max:200']);
        ExamViolation::create(['exam_session_id' => $session->id, 'type' => $v['type'], 'detail' => $v['detail'] ?? null, 'occurred_at' => now()]);
        $session->increment('violation_count');

        $max = config('school.exam.max_violations');
        $finished = false;
        if ($session->violation_count >= $max) {
            $this->sync($session = app(ExamGrader::class)->gradeSession($session));
            $finished = true;
        }

        return response()->json(['count' => $session->violation_count, 'max' => $max, 'finished' => $finished]);
    }

    public function finish(Request $request, Exam $exam)
    {
        $student = $this->authorizeExam($request, $exam);
        $session = ExamSession::where(['exam_id' => $exam->id, 'students_id' => $student->nis])->firstOrFail();
        if ($session->status === 'berlangsung') {
            $session->update(['finished_at' => now()]);
            $this->sync(app(ExamGrader::class)->gradeSession($session));
        }

        return redirect()->route('siswa.exams.show', $exam)->with('success', 'Ujian diselesaikan. Jawaban Anda telah dikoreksi otomatis.');
    }
}
