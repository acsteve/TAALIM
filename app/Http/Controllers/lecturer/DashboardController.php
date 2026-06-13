<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $kpPrograms = Course::where('kp_id', $user->id)->get();

        $coordinatorSubjects = Subject::where('coordinator_id', $user->id)
            ->withCount(['courseReports', 'assessments'])
            ->get();

        // UPDATE THIS SECTION
        $smeSubjects = Subject::where('sme1_id', $user->id)
            ->orWhere('sme2_id', $user->id)
            ->with(['coordinator', 'assessments']) // Add 'assessments' here
            ->get();

        return view('lecturer.dashboard', compact(
            'kpPrograms', 
            'coordinatorSubjects', 
            'smeSubjects'
        ));
    }
}