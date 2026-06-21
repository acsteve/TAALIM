<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Assessment;
use App\Models\AnswerSample;
use App\Models\AcademicSession;
use App\Models\CourseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubjectCoordinatorController extends Controller
{
    /**
     * The Management Hub: View own uploaded assessments and their status
     */
    public function index()
    {
        $userId = Auth::id();
        
        $assessments = Assessment::with('subject')
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->groupBy('type');

        // Fetch fallback context variables so the updated sidebar layout can resolve parameters safely
        $firstAssignedSubject = Subject::where('coordinator_id', $userId)->first();
        $activeSession = AcademicSession::where('is_active', true)->first();
        
        $subject = $firstAssignedSubject ?? null;
        $session = $activeSession ? $activeSession->name : 'N/A';

        return view('subjcoordinator.assessment-list', compact('assessments', 'subject', 'session'));
    }

    /**
     * Show the form for creating a new assessment upload
     */
    public function showUploadForm()
    {
        // Only show subjects assigned to this specific coordinator
        $subjects = Subject::where('coordinator_id', Auth::id())->get(); 
        $activeSession = AcademicSession::where('is_active', true)->first();
        
        $subject = $subjects->first() ?? null;
        $session = $activeSession ? $activeSession->name : 'N/A';

        return view('subjcoordinator.assessment-upload', compact('subjects', 'activeSession', 'subject', 'session'));
    }

    /**
     * Store a newly created assessment
     */
    public function store(Request $request)
        {
            $request->validate([
                'subject_id'      => 'required|exists:subjects,id',
                'assessment_type' => 'required|string',
                'title'           => 'required|string|max:255',
                'question_file'   => 'required|mimes:pdf|max:10240',
                'schema_file'     => 'required|mimes:pdf|max:10240',
            ]);

            $activeSession = AcademicSession::where('is_active', true)->first();

            $assessment = new Assessment();
            $assessment->user_id    = Auth::id();
            $assessment->subject_id = $request->subject_id;
            $assessment->title      = $request->title;
            $assessment->type       = $request->assessment_type;
            $assessment->session    = $activeSession ? $activeSession->name : 'N/A';
            
            // Handling file storage
            if ($request->hasFile('question_file')) {
                $qFile = $request->file('question_file');
                $assessment->question_file = $qFile->store('assessments', 'public');
                // ADD THIS LINE
                $assessment->question_filename = $qFile->getClientOriginalName();
            }
            
            if ($request->hasFile('schema_file')) {
                $sFile = $request->file('schema_file');
                $assessment->schema_file = $sFile->store('inventory', 'public');
                // ADD THIS LINE
                $assessment->schema_filename = $sFile->getClientOriginalName();
            }

            // Initialize statuses
            $assessment->sme1_status = 'pending';
            $assessment->sme2_status = 'pending';
            $assessment->kp_status   = 'pending';
            $assessment->status      = 'pending';
            
            $assessment->save();

            return redirect()->route('subjcoordinator.index')->with('success', 'Assessment uploaded successfully.');
        }

    /**
     * Handle the Re-upload/Correction logic from the Alpine.js Modal
     */
    public function reupload(Request $request, $id)
    {
        $assessment = Assessment::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title'           => 'required|string|max:255',
            'assessment_file' => 'nullable|mimes:pdf,doc,docx,zip|max:10240', 
            'inventory_file'  => 'nullable|mimes:pdf,doc,docx,zip|max:10240',  
        ]);

        $assessment->title = $request->title;

        // Update Question Paper
        if ($request->hasFile('assessment_file')) {
            if ($assessment->question_file) {
                Storage::disk('public')->delete($assessment->question_file);
            }
            $assessment->question_file = $request->file('assessment_file')->store('assessments', 'public');
        }

        // Update Marking Scheme
        if ($request->hasFile('inventory_file')) {
            if ($assessment->schema_file) {
                Storage::disk('public')->delete($assessment->schema_file);
            }
            $assessment->schema_file = $request->file('inventory_file')->store('inventory', 'public');
        }

        // RESET WORKFLOW: Important for re-verification
        $assessment->sme1_status = 'pending';
        $assessment->sme2_status = 'pending';
        $assessment->kp_status   = 'pending';
        $assessment->status      = 'pending';
        
        // Clear previous rejection comments
        $assessment->sme1_comments = null;
        $assessment->sme2_comments = null;
        $assessment->kp_comments   = null;

        $assessment->save();

        return redirect()->back()->with('success', 'Assessment updated and resubmitted for review.');
    }

    /**
     * View the full folder
     */
    public function viewFolder($id)
    {
        $assessment = Assessment::where('user_id', Auth::id())->findOrFail($id);
        
        // Context parameters for sidebar matching
        $subject = $assessment->subject;
        $session = $assessment->session;
        $activeSidebar = 'subjcoordinator';

        return view('subjcoordinator.folder-view', compact('assessment', 'subject', 'session'));
    }

    /**
     * Remove the assessment and its physical files
     */
    public function destroy($id)
    {
        $assessment = Assessment::where('user_id', Auth::id())->findOrFail($id);

        if ($assessment->question_file) {
            Storage::disk('public')->delete($assessment->question_file);
        }
        if ($assessment->schema_file) {
            Storage::disk('public')->delete($assessment->schema_file);
        }

        $assessment->delete();

        return redirect()->back()->with('success', 'Assessment removed from system.');
    }

    # =========================================================================
    # WORKFLOW: STUDENT ANSWER SAMPLES PROTOCOL
    # =========================================================================

    /**
     * Render the Student Answer Samples upload portal dashboard
     */
    public function manageSamples()
    {
        $userId = Auth::id();

        // 1. Get folders fully cleared by both SMEs, verified by KP, and eager-load existing sample counters
        $completedAssessments = Assessment::where('user_id', $userId)
            ->where('sme1_status', 'approved')
            ->where('sme2_status', 'approved')
            ->where('status', 'completed')
            ->withCount('answerSamples') 
            ->with('subject')
            ->get();

        // 2. Track recently processed item groups displaying downstream sample counters
        $recentUploads = Assessment::where('user_id', $userId)
            ->where('status', 'completed')
            ->with(['subject', 'answerSamples'])
            ->latest()
            ->get();

        // Safe layout defaults
        $firstAssignedSubject = Subject::where('coordinator_id', $userId)->first();
        $activeSession = AcademicSession::where('is_active', true)->first();
        $subject = $firstAssignedSubject ?? null;
        $session = $activeSession ? $activeSession->name : 'N/A';

        return view('subjcoordinator.answersample-upload', compact('completedAssessments', 'recentUploads', 'subject', 'session'));
    }

public function viewFileInline($id, $type)
{
    // Handle the Sample type separately
    if ($type === 'sample') {
        $item = \App\Models\AnswerSample::findOrFail($id);
        $path = $item->file_path;
        $filename = $item->filename; // Ensure you have this column!
    } else {
        // Handle Assessment types
        $item = \App\Models\Assessment::findOrFail($id);
        $path = ($type === 'question') ? $item->question_file : $item->schema_file;
        $filename = ($type === 'question') ? $item->question_filename : $item->schema_filename;
    }

    if (!Storage::disk('public')->exists($path)) {
        
        abort(404);
    }

    return response()->file(storage_path('app/public/' . $path), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
}

    /**
     * Atomically process and store all 9 nested student answer samples
     */
    public function storeSamples(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'samples'       => 'required|array|size:3', // Must contain Best, Medium, Weak arrays
            'samples.*'     => 'required|array|size:3', // Each must contain exactly 3 files
            'samples.*.*'   => 'required|mimes:pdf|max:10240', // File restrictions: 10MB PDF maximum
        ]);

        // Secure verification that this assessment belongs to the current Coordinator
        $assessment = Assessment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($request->assessment_id);

        // 🛑 BACKEND IMMUTABILITY GUARD: Prevent double upload exploitation
        if ($assessment->answerSamples()->count() >= 9) {
            return redirect()->back()->with('error', 'Integrity Lock: This course folder has already archived its target 9 student scripts.');
        }

        // Process loops over categorical keys ('Best', 'Medium', 'Weak')
        foreach ($request->file('samples') as $category => $files) {
            
            // Convert to lowercase ('best', 'medium', 'weak') to stay uniform with Blade count conditions
            $normalizedCategory = strtolower($category);

            foreach ($files as $file) {
                if ($file->isValid()) {
                    
                    // Creates clean workspace directory: storage/app/public/samples/{assessment_id}/{category}/
                    $storagePath = $file->store("samples/{$assessment->id}/{$normalizedCategory}", 'public');

                    // Persistence Entry
                    AnswerSample::create([
                        'assessment_id' => $assessment->id,
                        'category'      => $normalizedCategory,
                        'file_path'     => $storagePath,
                        'filename'      => $file->getClientOriginalName()
                        
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'All 9 sample student scripts successfully archived into this course folder!');
    }

    # =========================================================================
    # 🚀 NEW WORKFLOW: DECOUPLED SEMESTER COURSE REPORTS MODULE
    # =========================================================================

    /**
     * Display compliance reports bound directly to a Subject and Semester Session
     */
    public function courseReportsIndex($subject_id, $session)
    {
        // Enforce access control verification mapping 
        $subject = Subject::where('coordinator_id', Auth::id())->findOrFail($subject_id);
        
        // Fetch existing course reports uploaded for this semester instance
        $reports = CourseReport::where('subject_id', $subject->id)
            ->where('session', $session)
            ->get()
            ->groupBy('type');

        return view('subjcoordinator.reports-checklist', compact('subject', 'session', 'reports'));
    }
}