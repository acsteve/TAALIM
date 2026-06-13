<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SqaController extends Controller
{
    /**
     * Admin: View list of SQA auditors and form to create new one
     */
    public function index()
    {
        $subjects = Subject::all();
        $courses = \App\Models\Course::all();
        return view('admin.manage-sqa', compact('subjects', 'courses'));
    }

    /**
     * Admin: Store a new SQA auditor
     */
public function store(Request $request)
{
    // Define your custom error messages here
    $messages = [
        'name.required'        => 'Please provide the full name of the auditor.',
        'staff_id.required'    => 'A unique Staff ID is required.',
        'staff_id.unique'      => 'This User ID is already exist in the system.',
        'password.required'    => 'An initial password must be set.',
        'password.min'         => 'The password must be at least 6 characters long.',
        'subject_ids.required' => 'Please select at least one subject to assign to this auditor.',
    ];

    // Pass the $messages array as the third argument to validate()
    $request->validate([
        'name'        => 'required|string|max:255',
        'staff_id'    => 'required|string|unique:users,staff_id',
        'password'    => 'required|string|min:6',
        'subject_ids' => 'required|array',
        'subject_ids.*' => 'exists:subjects,id',
    ], $messages);

    DB::transaction(function () use ($request) {
        $user = User::create([
            'name'     => $request->name,
            'staff_id' => $request->staff_id,
            'email'    => $request->staff_id . '@university.edu.my',
            'password' => Hash::make($request->password),
            'role'     => 'sqa',
        ]);

        $user->subjects()->sync($request->subject_ids);
    });

    return redirect()->route('admin.sqa.manage')
                     ->with('success', 'SQA auditor created and subjects assigned.');
}

    /**
     * SQA Auditor: Dashboard showing In-Progress and Completed subjects
     */
    public function sqaDashboard()
    {
        $sqaId = Auth::id();

        $subjects = Subject::whereHas('sqaAssignments', function($q) use ($sqaId) {
                $q->where('sqa_id', $sqaId);
            })
            ->with([
                'assessments' => function($q) {
                    $q->where('status', 'completed');
                },
                'courseReports' 
            ])
            ->get();

        return view('sqa.dashboard', compact('subjects'));
    }

    /**
     * SQA Auditor: Subject details view
     */
    public function sqaSubjectDetails($id)
    {
        $subject = Subject::where('id', $id)
            ->whereHas('sqaAssignments', function($query) {
                $query->where('sqa_id', Auth::id());
            })
            ->with(['course', 'assessments', 'courseReports'])
            ->firstOrFail();

        return view('sqa.subject-details', compact('subject'));
    }

    public function showAssessment($id)
    {
        // Retrieve the assessment with its related data
        $assessment = \App\Models\Assessment::with([
            'subject', 
            'subject.coordinator', 
            'answerSamples', 
            'sme1', 
            'sme2', 
            'kp'
        ])->findOrFail($id);

        return view('sqa.assessment-detail', compact('assessment'));
    }
}