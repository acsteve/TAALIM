<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Subject;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\File;


class AssessmentController extends Controller
{
    private function getSessionPath()
    {
        $activeSession = AcademicSession::where('is_active', true)->first();
        $sessionName = $activeSession ? $activeSession->name : 'Unknown-Session';
        return str_replace(['/', ' '], '-', $sessionName);
    }

    // =========================================================================
    // SME ROLE METHODS
    // =========================================================================

    public function index()
    {
        $userId = Auth::id();
        $activeSession = AcademicSession::where('is_active', true)->first();
        $activeSessionName = $activeSession ? $activeSession->name : null;

        $allSmeAssessments = Assessment::with(['subject', 'coordinator'])
                            ->where(function($query) use ($userId) {
                                $query->where('sme1_id', $userId)->orWhere('sme2_id', $userId);
                            })
                            ->where('session', $activeSessionName)
                            ->latest()
                            ->get();

        $pendingAssessments = $allSmeAssessments->filter(fn($item) => ($item->sme1_id == $userId && $item->sme1_status == 'pending') || ($item->sme2_id == $userId && $item->sme2_status == 'pending'));
        $reviewedAssessments = $allSmeAssessments->filter(fn($item) => ($item->sme1_id == $userId && $item->sme1_status != 'pending') || ($item->sme2_id == $userId && $item->sme2_status != 'pending'));

        return view('SME.assessment-verification', compact('pendingAssessments', 'reviewedAssessments', 'activeSessionName', 'pendingAssessments'));
    }

    public function review($id)
    {
        $userId = Auth::id();
        $assessment = Assessment::with(['subject', 'coordinator'])->findOrFail($id);
        if ($assessment->sme1_id != $userId && $assessment->sme2_id != $userId) abort(403);
        
        return view('SME.review-workspace', compact('assessment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::id();
        $assessment = Assessment::findOrFail($id);
        $request->validate(['status' => 'required|in:approved,rejected', 'sme_comments' => 'required_if:status,rejected|nullable|string']);

        $updateData = ($assessment->sme1_id == $userId) 
            ? ['sme1_status' => $request->status, 'sme1_comments' => $request->sme_comments, 'sme1_verified_at' => now()]
            : ['sme2_status' => $request->status, 'sme2_comments' => $request->sme_comments, 'sme2_verified_at' => now()];

        if ($request->status == 'approved' && (($assessment->sme1_id == $userId ? $assessment->sme2_status : $assessment->sme1_status) == 'approved')) {
            $updateData['status'] = 'approved';
        } elseif ($request->status == 'rejected') {
            $updateData['status'] = 'rejected';
        }

        $assessment->update($updateData);
        return redirect()->route('sme.verification')->with('success', 'Review submitted.');
    }

    // =========================================================================
    // COORDINATOR ROLE METHODS
    // =========================================================================

    // public function coordinatorIndex()
    // {
    //     $userId = Auth::id();
    //     $activeSession = AcademicSession::where('is_active', true)->first()?->name;
    //     $mySubjects = Subject::where('coordinator_id', $userId)->get();
    //     $myUploads = Assessment::where('user_id', $userId)->where('session', $activeSession)->latest()->get()->groupBy('type');

    //     return view('coordinator.my-uploads', compact('mySubjects', 'myUploads', 'activeSession'));
    // }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required', 'subject_id' => 'required', 'question_file' => 'required|mimes:pdf', 'schema_file' => 'required|mimes:pdf']);
        $subject = Subject::findOrFail($request->subject_id);
        
        Assessment::create([
            'user_id' => Auth::id(), 'subject_id' => $request->subject_id, 'title' => $request->title, 'type' => $request->assessment_type,
            'question_file' => $request->file('question_file')->store("assessments/{$this->getSessionPath()}/questions", 'public'),
            'schema_file' => $request->file('schema_file')->store("assessments/{$this->getSessionPath()}/schemas", 'public'),
            'session' => AcademicSession::where('is_active', true)->first()?->name, 'status' => 'pending',
            'sme1_id' => $subject->sme1_id, 'sme2_id' => $subject->sme2_id,
        ]);

        return redirect()->back()->with('success', 'Assessment uploaded successfully!');
    }

    // =========================================================================
    // KP ROLE METHODS (Archive Drill-down)
    // =========================================================================

    public function downloadZip($id)
    {
        $assessment = Assessment::with('answerSamples')->findOrFail($id);
        $zipFileName = 'assessment_' . $assessment->id . '_' . time() . '.zip';
        $fullZipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) mkdir(storage_path('app/temp'), 0755, true);
        
        $zip = new ZipArchive();
        if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $publicPath = storage_path('app/public/');
            if (Storage::disk('public')->exists($assessment->question_file)) $zip->addFile(storage_path("app/public/{$assessment->question_file}"), 'Question_Paper.pdf');
            if (Storage::disk('public')->exists($assessment->schema_file)) $zip->addFile(storage_path("app/public/{$assessment->schema_file}"), 'Marking_Scheme.pdf');
            foreach ($assessment->answerSamples as $sample) {
                if (Storage::disk('public')->exists($sample->file_path)) {
                    // We add $sample->id to the filename to ensure it is unique
                    $internalPath = "Samples/" . ucfirst($sample->category) . "/{$sample->id}_{$sample->filename}";
                    
                    $zip->addFile(storage_path("app/public/{$sample->file_path}"), $internalPath);
                }
            }
            $zip->close();
        }
        return response()->download($fullZipPath, 'Assessment_' . $assessment->title . '.zip')
                 ->deleteFileAfterSend(true);
    }

public function kpSubjectList(Request $request)
{
    $user = Auth::user();
    $active = AcademicSession::where('is_active', true)->first();
    $selectedSessionId = $request->query('session', $active?->id);
    $selectedSession = AcademicSession::find($selectedSessionId);
    $selectedSessionName = $selectedSession ? $selectedSession->name : '';

    $sessions = AcademicSession::all();
    $search = $request->query('search');

    // Filter subjects by the logged-in KP's assigned course
    $subjects = Subject::whereHas('course', function($query) use ($user) {
            $query->where('kp_id', $user->id);
        })
        ->when($search, function($query) use ($search) {
            $query->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('subject_code', 'like', "%{$search}%");
        })->get();

    foreach ($subjects as $subject) {
        $count = Assessment::where('subject_id', $subject->id)
            ->where('status', 'completed')
            ->where('session', 'LIKE', $selectedSessionName)
            ->count();
        $subject->setAttribute('assessments_count', $count);
    }

    return view('kp.subject-list', compact('subjects', 'sessions', 'selectedSessionId', 'search'));
}

public function kpSubjectAssessments($subjectId, Request $request)
{
    $user = Auth::user();

    // Ensure the subject belongs to the KP's course
    $subject = Subject::whereHas('course', function($query) use ($user) {
            $query->where('kp_id', $user->id);
        })->findOrFail($subjectId);
    
    $sessionId = $request->query('session', AcademicSession::where('is_active', true)->first()?->id);
    $sessionName = AcademicSession::find($sessionId)?->name;

    $assessments = Assessment::where('subject_id', $subjectId)
        ->where('session', $sessionName)
        ->where('status', 'completed')
        ->get();

    return view('kp.subject-assessment', compact('subject', 'assessments', 'sessionName'));
}

    public function viewFileInline($id, $type)
    {
        // 1. Handle Sample type vs Assessment type
        if ($type === 'sample') {
            $item = \App\Models\AnswerSample::findOrFail($id);
            $path = $item->file_path;
            $filename = $item->filename ?? 'student_sample.pdf';
        } else {
            $item = \App\Models\Assessment::findOrFail($id);
            $path = ($type === 'question') ? $item->question_file : $item->schema_file;
            $filename = ($type === 'question') ? $item->question_filename : $item->schema_filename;
        }

        // 2. Validate existence
        if (!$path || !Storage::disk('public')->exists($path)) {
            
            abort(404, "The requested file could not be found.");
        }

        // 3. Return the file
        return response()->file(storage_path('app/public/' . $path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

public function viewFile($id)
{
    $user = Auth::user();

    // Use a strict lookup: Find the file ONLY if it belongs to an assessment under this KP
    $assessment = Assessment::whereHas('subject.course', function($query) use ($user) {
            $query->where('kp_id', $user->id);
        })->findOrFail($id);
    
    $path = storage_path('app/public/' . $assessment->question_file);

    if (!File::exists($path)) {
        $path = storage_path('app/' . $assessment->question_file);
        if (!File::exists($path)) abort(404);
    }

    return response()->file($path);
}

public function download($id)
{
    $user = Auth::user();

    $assessment = Assessment::whereHas('subject.course', function($query) use ($user) {
            $query->where('kp_id', $user->id);
        })->findOrFail($id);
    
    return response()->download(storage_path('app/public/' . $assessment->question_file));
}

public function kpShowFolder($id)
{
    $user = Auth::user();

    $assessment = Assessment::with([
            'subject.coordinator', 
            'kp', 
            'sme1', 
            'sme2', 
            'answerSamples'
        ])
        ->whereHas('subject.course', function($query) use ($user) {
            $query->where('kp_id', $user->id);
        })
        ->findOrFail($id);

    return view('kp.assessment-folder', compact('assessment'));
}
}