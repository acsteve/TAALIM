<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display the subject library with assignments.
     */
    public function index()
    {
        // Eager load relationships for performance
        $subjects = Subject::with(['course', 'coordinator', 'sme1', 'sme2'])
            ->orderBy('subject_code', 'asc')
            ->get();
            
        $courses = Course::all();

        // Fetch all users with the lecturer role
        $lecturers = User::where('role', 'lecturer')
            ->orderBy('name', 'asc')
            ->get();

        // Get IDs of lecturers already assigned as coordinators for use in the view
        $assignedCoordinatorIds = Subject::whereNotNull('coordinator_id')
            ->pluck('coordinator_id')
            ->toArray();

        return view('admin.manage-subject', compact('subjects', 'courses', 'lecturers', 'assignedCoordinatorIds'));
    }

    /**
     * Store a new subject with assignments.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSubject($request);

        Subject::create($validated);

        return redirect()->back()->with('success', 'Subject and staff assignments saved successfully!');
    }

    /**
     * Update an existing subject and its assignments.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $this->validateSubject($request, $subject->id);

        $subject->update($validated);

        return redirect()->back()->with('success', 'Subject assignments updated successfully!');
    }

    /**
     * Helper validation to keep store and update logic DRY.
     */
    private function validateSubject(Request $request, $subjectId = null)
    {
        return $request->validate([
            'subject_code'   => ['required', 'string', 'max:10', Rule::unique('subjects', 'subject_code')->ignore($subjectId)],
            'subject_name'   => 'required|string|max:255',
            'course_id'      => 'required|exists:courses,id',
            'coordinator_id' => [
                'nullable', 
                'exists:users,id',
                // Rule to ensure a lecturer can only coordinate ONE subject
                Rule::unique('subjects', 'coordinator_id')->ignore($subjectId),
            ],
            'sme1_id'        => 'nullable|exists:users,id|different:coordinator_id',
            'sme2_id'        => 'nullable|exists:users,id|different:coordinator_id|different:sme1_id',
        ], [
            'coordinator_id.unique' => 'This lecturer is already assigned as a coordinator for another subject.',
            'sme1_id.different'     => 'The SME 1 cannot be the same person as the Subject Coordinator.',
            'sme2_id.different'     => 'The SME 2 must be a different person from the Coordinator and SME 1.',
        ]);
    }

    /**
     * Remove a subject.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject has been removed.');
    }
}