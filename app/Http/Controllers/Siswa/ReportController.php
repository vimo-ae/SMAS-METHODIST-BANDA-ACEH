<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Guru\HomeroomController;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function show(Request $request, ReportCardService $svc)
    {
        $student = $request->user()->student;
        abort_unless($student->class_id, 404, 'Anda belum ditempatkan di kelas.');
        $data = app(HomeroomController::class)->reportData($student, $request->input('semester', 'ganjil'), $svc);

        return view('report.print', $data + ['canNote' => false]);
    }
}
