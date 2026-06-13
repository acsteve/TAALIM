<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectAssignment;
use App\Models\Subject;
use App\Models\User;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;

class SubjectAssignmentController extends Controller
{
public function index()
{
    $activeSession = AcademicSession::where('is_active', true)->first();

    // Get all subjects and eager load their assignment for the current session
    $subjects = Subject::with(['assignments' => function($query) use ($activeSession) {
        $query->where('academic_session_id', $activeSession->id);
    }])->orderBy('subject_code')->get();

    $staff = User::whereIn('role', ['lecturer', 'kp', 'sqa'])->orderBy('name')->get();

    return view('admin.manage-assignment', compact('subjects', 'staff', 'activeSession'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'          => 'required|exists:subjects,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'coordinator_id'      => 'required|exists:users,id',
            'sme1_id'             => 'required|exists:users,id|different:coordinator_id',
            // SME2 is now optional based on your migration change (nullable)
            'sme2_id'             => 'nullable|exists:users,id|different:coordinator_id|different:sme1_id',
            'section'             => 'required|string', // Required because one subject can have multiple sections
        ]);

        try {
            DB::beginTransaction();

            // Updated logic: A unique assignment is defined by Subject + Session + Section
            SubjectAssignment::updateOrCreate(
                [
                    'subject_id'          => $validated['subject_id'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'section'             => $validated['section'], 
                ],
                [
                    'coordinator_id'      => $validated['coordinator_id'],
                    'sme1_id'             => $validated['sme1_id'],
                    'sme2_id'             => $validated['sme2_id'],
                    // 'status' removed because it's no longer in the table
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Subject workflow deployed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to deploy workflow: ' . $e->getMessage());
        }
    }

    public function destroy(SubjectAssignment $assignment)
    {
        // Add a check: if folders are already submitted for this assignment, 
        // you might want to prevent deletion to maintain audit trails (SQA principle).
        $assignment->delete();
        return redirect()->back()->with('success', 'Assignment revoked successfully.');
    }
}