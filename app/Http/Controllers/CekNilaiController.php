<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CekNilaiController extends Controller
{
    public function examForm()
    {
        return view('cek-nilai-ujian.index');
    }

    public function examResults(Request $request)
    {
        $validated = $request->validate([
            'nis' => ['required', 'digits_between:1,20'],
            'dob' => ['required', 'date_format:Y-m-d'],
        ]);

        $connection = DB::connection('mysql_siswa');
        $student = $connection->table('students as s')
            ->leftJoin('users as u', 'u.user_id', '=', 's.nis')
            ->leftJoin('classes as c', 'c.class_id', '=', 's.class_id')
            ->where('s.nis', $validated['nis'])
            ->whereDate('s.dob', $validated['dob'])
            ->where('s.status', 'aktif')
            ->select('s.nis', 'u.name as student_name', 'c.name as class_name')
            ->first();

        if (!$student) {
            return back()->withErrors([
                'identity' => 'NIS atau tanggal lahir tidak cocok.',
            ]);
        }

        $exams = $connection->table('v_public_exam_results')
            ->where('student_id', $student->nis)
            ->orderByDesc('finished_at')
            ->orderByDesc('exam_session_id')
            ->get();

        $examMaxScores = $connection->table('exam_questions')
            ->whereIn('exam_id', $exams->pluck('exam_id'))
            ->select('exam_id')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN question_type <> 'esai' THEN score_weight ELSE 0 END), 0)
                    AS objective_max_score,
                COALESCE(SUM(CASE WHEN question_type = 'esai' THEN score_weight ELSE 0 END), 0)
                    AS essay_max_score
            ")
            ->groupBy('exam_id')
            ->get()
            ->keyBy('exam_id');

        foreach ($exams as $exam) {
            $maxScores = $examMaxScores->get($exam->exam_id);
            $exam->objective_max_score = (float) ($maxScores->objective_max_score ?? 0);
            $exam->essay_max_score = (float) ($maxScores->essay_max_score ?? 0);
        }

        return response()
            ->view('cek-nilai-ujian.show', compact('student', 'exams'))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /** Cari anak berdasarkan nama. */
    public function index(Request $request)
    {
        $students = collect();

        if ($request->filled('name')) {
            $request->validate(['name' => 'string|min:3|max:100']);

            $students = Student::with('user', 'schoolClass')
                ->where('status', 'aktif')
                ->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->name.'%'))
                ->limit(20)->get();
        }

        return view('cek-nilai.index', compact('students'));
    }

    public function form(Student $student)
    {
        $student->load('user', 'schoolClass');

        return view('cek-nilai.verify', compact('student'));
    }

    /** Verifikasi tanggal lahir, lalu tampilkan nilai. */
    public function show(Request $request, Student $student)
    {
        $request->validate(['dob' => 'required|date']);

        if (config('school.guest_check.require_dob', true)
            && ! $student->dob?->isSameDay($request->dob)) {
            return back()->withErrors(['dob' => 'Tanggal lahir tidak cocok.']);
        }

        $student->load('user', 'schoolClass');

        $grades = collect(DB::connection('mysql_siswa')->select(
            'CALL sp_public_check_grades(?, ?)',
            [$student->nis, $request->dob]
        ))->groupBy('subject_name');

        return view('cek-nilai.show', compact('student', 'grades'));
    }
}