<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\CourseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Essential for fluent query execution

class CourseReportController extends Controller
{
    /**
     * Show the Single-Page Checklist Portal for a specific Subject and Session
     */
    public function index($subjectId, $session)
    {
        // FIXED: Eager load both sections and reports to make progress bars calculate smoothly
        $subject = Subject::with(['sections', 'courseReports' => function($query) use ($session) {
                $query->where('session', $session);
            }])
            ->where('coordinator_id', Auth::id())
            ->findOrFail($subjectId);

        // FIXED: Swapped 'section_number' out for the proper database relationship pointer 'section_id'
        $uploadedDocs = $subject->courseReports->keyBy(function($item) {
            return $item->type . ($item->section_id ? '_' . $item->section_id : '');
        });

        $standardTypes = [
            'teaching_assignment'       => 'Teaching Assignment',
            'teaching_plan'             => 'Teaching Plan',
            'student_registration_list' => 'IMS Student Registration List',
            'course_timetable'          => 'IMS Course Timetable',
            'student_grades'            => 'IMS Student Grades',
            'overall_marks'             => 'IMS Overall Marks',
            'grade_summary'             => 'IMS Grade Summary',
            'copo_analysis'             => 'IMS CO Analysis, PO Analysis, COPO',
            'coordinator_analysis'      => 'IMS Analysis by Coordinator',
            'student_attendance'        => 'Student Attendance',
            'coordination_report'       => 'Course Coordination Report',
        ];

        return view('subjcoordinator.reports-checklist', compact(
            'subject', 
            'session',
            'uploadedDocs', 
            'standardTypes'
        ));
    }

    /**
     * Process, validate, and store a newly uploaded or updated document asset
     */
    public function upload(Request $request, $subjectId, $session)
    {
        // FIXED: Renamed input validation schema target to check against existing foreign key constraints
        $request->validate([
            'type'       => 'required|string',
            'section_id' => 'nullable|exists:sections,id', // Double checks that the section genuinely exists
            'report_file' => 'required|mimes:pdf|max:10240',
        ]);

        $subject = Subject::where('coordinator_id', Auth::id())->findOrFail($subjectId);

        // FIXED: Updated queries to point directly to 'section_id'
        $existing = CourseReport::where('subject_id', $subject->id)
            ->where('session', $session)
            ->where('type', $request->type)
            ->where('section_id', $request->section_id)
            ->first();

        if ($existing) {
            if (Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $existing->delete();
        }

        $folderPath = "course_folders/subject_{$subject->id}/{$session}/reports";
        $path = $request->file('report_file')->store($folderPath, 'public');

        // FIXED: Saved record using section_id
        CourseReport::create([
            'user_id'    => Auth::id(), 
            'subject_id' => $subject->id,
            'session'    => $session,
            'type'       => $request->type,
            'section_id' => $request->section_id,
            'file_path'  => $path,
        ]);

        return redirect()->back()->with('success', 'Document asset successfully archived.');
    }

    /**
     * Cleanly purge an explicit report file row
     */
    public function destroy($id)
    {
        $report = CourseReport::whereHas('subject', function($query) {
            $query->where('coordinator_id', Auth::id());
        })->findOrFail($id);

        if (Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();
        return redirect()->back()->with('success', 'Document asset removed from system storage.');
    }

    /*
    |--------------------------------------------------------------------------
    | KETUA PROGRAM (KP) METHODS
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Display a master list of all program subjects for the KP with Search filters
     */
public function kpIndex(Request $request)
{
    $sessions = DB::table('academic_sessions')->get();
    $defaultSessionId = $sessions->where('is_active', true)->first()->id ?? ($sessions->first()->id ?? 1);
    $selectedSession = $request->get('session', $defaultSessionId);
    $search = $request->get('search');

    // 1. Find the Course assigned to the logged-in user (KP)
    $kpCourse = \App\Models\Course::where('kp_id', Auth::id())->first();

    // If the user isn't assigned to any course, return an empty collection or handle accordingly
    if (!$kpCourse) {
        return view('kp.view-report-overview', [
            'subjects' => collect(), // Empty list
            'selectedSession' => $selectedSession,
            'sessions' => $sessions,
            'search' => $search
        ]);
    }

    // 2. Filter subjects that belong to that specific course
    $subjectsQuery = Subject::with(['sections', 'courseReports' => function($query) use ($selectedSession) {
            $query->where('session', $selectedSession);
        }])
        ->where('course_id', $kpCourse->id); // Use the retrieved course ID

    // Apply search filter
    if (!empty($search)) {
        $subjectsQuery->where(function($q) use ($search) {
            $q->where('subject_code', 'LIKE', "%{$search}%")
              ->orWhere('subject_name', 'LIKE', "%{$search}%");
        });
    }

    $subjects = $subjectsQuery->get();
    $baseStandardCount = 11;

    return view('kp.view-report-overview', compact(
        'subjects', 
        'selectedSession', 
        'baseStandardCount', 
        'search',
        'sessions'
    ));
}

    /**
     * Read-only layout view of a specific subject dossier for the KP
     */
    public function kpShow($subjectId, $session)
    {
        // FIXED: Eager load real database sections for audit template processing loops
        $subject = Subject::with(['sections', 'courseReports' => function($query) use ($session) {
                $query->where('session', $session);
            }])->findOrFail($subjectId);

        // FIXED: Swapped out legacy 'section_number' string parsing key points
        $uploadedDocs = $subject->courseReports->keyBy(function($item) {
            return $item->type . ($item->section_id ? '_' . $item->section_id : '');
        });

        $standardTypes = [
            'teaching_assignment'       => 'Teaching Assignment',
            'teaching_plan'             => 'Teaching Plan',
            'student_registration_list' => 'IMS Student Registration List',
            'course_timetable'          => 'IMS Course Timetable',
            'student_grades'            => 'IMS Student Grades',
            'overall_marks'             => 'IMS Overall Marks',
            'grade_summary'             => 'IMS Grade Summary',
            'copo_analysis'             => 'IMS CO Analysis, PO Analysis, COPO',
            'coordinator_analysis'      => 'IMS Analysis by Coordinator',
            'student_attendance'        => 'Student Attendance',
            'coordination_report'       => 'Course Coordination Report',
        ];

        $sessionRecord = DB::table('academic_sessions')->where('id', $session)->first();
        
        if ($sessionRecord && isset($sessionRecord->name)) {
            $sessionName = $sessionRecord->name;
        } else {
            $sessionName = 'Semester Track #' . $session; 
        }

        return view('kp.view-report', compact(
            'subject', 
            'session', 
            'sessionName', 
            'uploadedDocs', 
            'standardTypes'
        ));
    }
}