<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('siswa.dashboard');
    }
}
