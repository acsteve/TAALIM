<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Assessment;
use App\Models\CourseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpController extends Controller
{
    public function index()
    {
        // 1. Get the authenticated KP user
        $user = Auth::user();


        $assessments = Assessment::with(['subject', 'coordinator'])
            ->whereHas('subject.course', function($query) use ($user) {
                $query->where('kp_id', $user->id);
            })
            ->orderBy('kp_verified_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Pending KP Approval
        $pendingKpAssessments = $assessments->filter(function ($item) {
            return $item->sme1_status === 'approved' 
                && $item->sme2_status === 'approved' 
                && $item->status !== 'completed'
                && $item->status !== 'rejected';
        });

        // 4. In Progress
        $inProgressAssessments = $assessments->filter(function ($item) {
            return ($item->sme1_status === 'pending' || $item->sme2_status === 'pending')
                && $item->status !== 'completed';
        });

        // 5. Archived
        $archivedAssessments = $assessments->where('status', 'completed');

        // Badges Counting
        $pendingKpCount = $pendingKpAssessments->count();
        $pendingSmeCount = $inProgressAssessments->count();

        return view('kp.assessment-verification', compact(
            'pendingKpAssessments', 
            'inProgressAssessments', 
            'archivedAssessments', 
            'pendingKpCount', 
            'pendingSmeCount'
        ));
    }

    public function review($id)
    {
        $assessment = Assessment::with(['subject', 'coordinator'])->findOrFail($id);
        
        // Gatekeeping logic: Both evaluations must be positive for the KP workspace to unlock
        if ($assessment->sme1_status !== 'approved' || $assessment->sme2_status !== 'approved') {
            return redirect()->route('kp.verification')->with('warning', 'This folder is still undergoing SME review.');
        }

        return view('kp.review-assessment', compact('assessment'));
    }

    public function finalize(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'kp_remarks' => 'nullable|string|max:1000', 
        ]);

        if ($request->action === 'approve') {
            $assessment->update([
                'status' => 'completed',
                'kp_status' => 'approved',
                'kp_comments' => $request->kp_remarks,
                'kp_id' => Auth::id(),
                'kp_verified_at' => now(),
            ]);

            return redirect()->route('kp.verification')->with('success', 'Course Folder finalized and archived.');
        } else {
            // Roll back to structural resetting so coordinator can refactor files
            $assessment->update([
                'status' => 'rejected',
                'sme1_status' => 'pending', 
                'sme2_status' => 'pending', 
                'kp_status' => 'rejected',
                'kp_comments' => $request->kp_remarks,
            ]);

            return redirect()->route('kp.verification')->with('warning', 'Folder returned for corrections.');
        }
    }

    // =========================================================================
    // 📊 NEW METHOD: READ-ONLY MIDTERM REPORTS VIEWER FOR MEETING AUDITS
    // =========================================================================
    /**
     * Interactive Interface to view Midterm Reports by Subject
     */
    public function viewReports(Request $request)
    {
        // Fetch subjects linked to this specific KP's program tracking scope
        $subjects = Subject::where('program_head_id', Auth::id())->get();

        $selectedSubjectId = $request->get('subject_id');
        $midtermRecords = [];

        // Fetch all Midterm files across sections if a subject filter is active
        if ($selectedSubjectId) {
            $midtermRecords = CourseReport::whereHas('assessment', function($query) use ($selectedSubjectId) {
                    $query->where('subject_id', $selectedSubjectId);
                })
                ->whereIn('type', ['midterm_boe', 'midterm_overall'])
                ->orderBy('section_number')
                ->orderBy('type')
                ->get()
                ->groupBy('section_number'); // Groups by section for clean grid display
        }

        return view('kp.midterm-meeting-audit', compact('subjects', 'midtermRecords', 'selectedSubjectId'));
    }
}