<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmeController extends Controller
{
    /**
     * Display the SME Dashboard with pending and reviewed assessments.
     */
    public function index()
    {
        $userId = Auth::id();
        $activeSession = AcademicSession::where('is_active', true)->first();
        $activeSessionName = $activeSession ? $activeSession->name : null;

        // Fetch assessments where the user is SME1 or SME2
        // We check BOTH the assessment table and the parent subject table for the assignment
        $query = Assessment::with(['subject', 'coordinator'])
            ->where(function($q) use ($userId) {
                $q->where('sme1_id', $userId)
                  ->orWhere('sme2_id', $userId)
                  ->orWhereHas('subject', function($subQ) use ($userId) {
                      $subQ->where('sme1_id', $userId)
                           ->orWhere('sme2_id', $userId);
                  });
            });

        // Filter by active academic session if available
        if ($activeSessionName) {
            $query->where('session', $activeSessionName);
        }

        $allSmeAssessments = $query->latest()->get();

        // Separate into Pending and Reviewed using collection filters
        $pendingAssessments = $allSmeAssessments->filter(function($item) use ($userId) {
            // Determine if the user is SME1 or SME2 for this specific item
            // Logic: User matches the ID AND the status is still pending
            $isSme1 = ($item->sme1_id == $userId || optional($item->subject)->sme1_id == $userId) 
                      && $item->sme1_status == 'pending';
            $isSme2 = ($item->sme2_id == $userId || optional($item->subject)->sme2_id == $userId) 
                      && $item->sme2_status == 'pending';
            
            return $isSme1 || $isSme2;
        });

        $reviewedAssessments = $allSmeAssessments->filter(function($item) use ($userId) {
            $isSme1 = ($item->sme1_id == $userId || optional($item->subject)->sme1_id == $userId) 
                      && $item->sme1_status != 'pending';
            $isSme2 = ($item->sme2_id == $userId || optional($item->subject)->sme2_id == $userId) 
                      && $item->sme2_status != 'pending';
            
            return $isSme1 || $isSme2;
        });

        $pendingCount = $pendingAssessments->count();

        return view('sme.assessment-verification', compact(
            'pendingAssessments', 
            'reviewedAssessments', 
            'activeSessionName', 
            'pendingCount'
        ));
    }

    /**
     * Show the review workspace for a specific assessment.
     */
    public function review($id)
    {
        $assessment = Assessment::with(['subject', 'coordinator'])->findOrFail($id);
        return view('sme.review-workspace', compact('assessment'));
    }

    /**
     * Process the SME approval or rejection.
     */
    public function updateStatus(Request $request, $id)
    {
        $assessment = Assessment::with('subject')->findOrFail($id);
        $userId = Auth::id();

        // 1. Determine the prefix (sme1 or sme2)
        // We check the assessment table first, then fallback to the subject table assignment
        $prefix = null;
        if ($assessment->sme1_id == $userId || optional($assessment->subject)->sme1_id == $userId) {
            $prefix = 'sme1';
        } elseif ($assessment->sme2_id == $userId || optional($assessment->subject)->sme2_id == $userId) {
            $prefix = 'sme2';
        }

        if (!$prefix) {
            abort(403, 'Unauthorized reviewer.');
        }

        // 2. Validation
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'required|string|min:5' 
        ]);

        // 3. Update the assessment
        // Note: We also save the SME ID into the assessment row at this point 
        // to "lock in" who performed the review.
        $assessment->update([
            "{$prefix}_id" => $userId,
            "{$prefix}_status" => $request->status,
            "{$prefix}_comments" => $request->comments,
            "{$prefix}_verified_at" => now(),
        ]);

        return redirect()->route('sme.verification')->with('success', 'Review submitted successfully!');
    }
}