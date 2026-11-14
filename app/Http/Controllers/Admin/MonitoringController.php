<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Sesuaikan nama Model di bawah ini dengan yang Anda miliki
// use App\Models\ExamSession; 

class MonitoringController extends Controller
{
    public function index()
    {
        // CONTOH QUERY (Sesuaikan dengan relasi model Anda):
        // $activeSessions = ExamSession::with(['exam', 'cst.schoolClass', 'cst.subject'])
        //     ->orderBy('start_time', 'desc')
        //     ->get();

        // Variabel kosong sebagai placeholder agar view tidak error saat dicoba
        $activeSessions = collect(); 

        return view('admin.monitoring.index', compact('activeSessions'));
    }

    public function show($id)
    {
        // CONTOH QUERY:
        // $session = ExamSession::with(['exam', 'cst.schoolClass', 'participants.student.user'])->findOrFail($id);
        
        // Placeholder data (ganti dengan query di atas nantinya)
        $session = null; 
        $participants = collect();

        return view('admin.monitoring.show', compact('session', 'participants'));
    }
}