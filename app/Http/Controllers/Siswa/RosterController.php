<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\RosterService;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = Student::with('schoolClass')->where('nis', $user->user_id)->first();

        // Tentukan kelas siswa saat ini (default ke X-A jika belum ditentukan)
        $rawClassName = $student?->schoolClass?->name ?? 'X-A';
        
        // Pilihan kelas dari query atau default kelas siswa
        $selectedClass = $request->query('class', $rawClassName);

        $myClassSchedule = RosterService::getScheduleForClass($selectedClass);
        $todaySchedule = RosterService::getTodayScheduleForClass($selectedClass);
        $allRosterData = RosterService::getRosterData();
        $classes = RosterService::CLASSES;
        $teachers = RosterService::TEACHERS;
        
        $subjectStyles = [];
        foreach (RosterService::SUBJECT_STYLES as $sCode => $sStyle) {
            $subjectStyles[$sCode] = array_merge([
                'border' => $sStyle['bg'] ?? '#CBD5E1',
            ], $sStyle);
            if (empty($subjectStyles[$sCode]['border'])) {
                $subjectStyles[$sCode]['border'] = $sStyle['bg'] ?? '#CBD5E1';
            }
        }

        $metadata = [
            'academic_year' => RosterService::ACADEMIC_YEAR,
            'school_name' => RosterService::SCHOOL_NAME,
            'principal_name' => RosterService::PRINCIPAL_NAME,
            'curriculum_head' => RosterService::CURRICULUM_HEAD,
            'decree_date' => RosterService::DECREE_DATE,
        ];

        return view('siswa.roster.index', compact(
            'user',
            'student',
            'rawClassName',
            'selectedClass',
            'myClassSchedule',
            'todaySchedule',
            'allRosterData',
            'classes',
            'teachers',
            'subjectStyles',
            'metadata'
        ));
    }
}
