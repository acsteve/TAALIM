<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['course', 'coordinator', 'sme1', 'sme2'])
            ->orderBy('subject_code', 'asc')
            ->get();
            
        $courses = Course::all();
        $lecturers = User::where('role', 'lecturer')
            ->orderBy('name', 'asc')
            ->get();

        $assignedLecturerIds = Subject::whereNotNull('coordinator_id')
            ->pluck('coordinator_id')
            ->toArray();

        return view('admin.manage-subject', compact('subjects', 'courses', 'lecturers', 'assignedLecturerIds'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSubject($request);
        Subject::create($validated);
        return redirect()->back()->with('success', 'Subject and staff assignments saved successfully!');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $this->validateSubject($request, $subject->id);
        $subject->update($validated);
        return redirect()->back()->with('success', 'Subject assignments updated successfully!');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'subject_ids'    => 'required|array',
            'subject_ids.*'  => 'exists:subjects,id',
            'coordinator_id' => 'nullable|exists:users,id',
            'sme1_id'        => 'nullable|exists:users,id',
            'sme2_id'        => 'nullable|exists:users,id',
        ]);

        Subject::whereIn('id', $request->subject_ids)->update([
            'coordinator_id' => $request->coordinator_id,
            'sme1_id'        => $request->sme1_id,
            'sme2_id'        => $request->sme2_id,
        ]);

        return redirect()->back()->with('success', 'Bulk assignment processed successfully.');
    }

    public function downloadTemplate($type)
    {
        $config = [
            'create' => [
                'headers' => ['subject_code', 'subject_name'],
                'filename' => 'subject_creation_template.csv'
            ],
            'assign' => [
                'headers' => ['subject_code', 'coordinator_staff_id', 'sme1_staff_id', 'sme2_staff_id'],
                'filename' => 'subject_assignment_template.csv'
            ]
        ];

        $selected = $config[$type] ?? $config['create'];

        return response()->stream(function() use ($selected) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $selected['headers']);
            fclose($file);
        }, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"{$selected['filename']}\"",
        ]);
    }

public function importCreate(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id', 
        'file'      => 'required|file|mimes:csv,txt'
    ]);

    $handle = fopen($request->file('file')->getRealPath(), 'r');
    fgetcsv($handle); // Skip header

    $successCount = 0;
    $errors = [];
    $rowNumber = 1;

    while (($data = fgetcsv($handle)) !== FALSE) {
        $rowNumber++;
        
        if (count($data) < 2) { 
            $errors[] = "Row {$rowNumber}: Missing required columns."; 
            continue; 
        }

        $code = trim($data[0]);
        $name = trim($data[1]);

        // CHECK: Does this code already exist in the database?
        if (Subject::where('subject_code', $code)->exists()) {
            $errors[] = "Row {$rowNumber}: Subject code '{$code}' already exists.";
            continue; // Stop here for this row and move to the next
        }

        // Create new only if it doesn't exist
        Subject::create([
            'subject_code' => $code,
            'subject_name' => $name, 
            'course_id'    => $request->course_id
        ]);
        
        $successCount++;
    }

    fclose($handle);

    if (!empty($errors)) {
        return back()->with('import_errors', $errors);
    }

    return back()->with('success', "{$successCount} subjects imported successfully.");
}


public function importAssign(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt',
        'course_id' => 'required|exists:courses,id'
    ]);

    $handle = fopen($request->file('file')->getRealPath(), 'r');
    fgetcsv($handle); 

    $successCount = 0;
    $errors = [];
    $rowNumber = 1;

    while (($data = fgetcsv($handle)) !== FALSE) {
        $rowNumber++;
        if (count($data) < 4) { $errors[] = "Row {$rowNumber}: Missing columns."; continue; }

        $subject = Subject::where('subject_code', trim($data[0]))->where('course_id', $request->course_id)->first();
        if (!$subject) { $errors[] = "Row {$rowNumber}: Subject not found."; continue; }

        $coordinatorId = !empty($data[1]) ? User::where('staff_id', trim($data[1]))->value('id') : $subject->coordinator_id;

        
        if ($coordinatorId && $coordinatorId != $subject->coordinator_id) {
            $isAlreadyCoordinating = Subject::where('coordinator_id', $coordinatorId)
                ->where('id', '!=', $subject->id)
                ->exists();

            if ($isAlreadyCoordinating) {
                $errors[] = "Row {$rowNumber}: Staff ID " . trim($data[1]) . " is already a coordinator for another subject.";
                continue;
            }
        }
       

        $subject->update([
            'coordinator_id' => $coordinatorId,
            'sme1_id'        => !empty($data[2]) ? User::where('staff_id', trim($data[2]))->value('id') : $subject->sme1_id,
            'sme2_id'        => !empty($data[3]) ? User::where('staff_id', trim($data[3]))->value('id') : $subject->sme2_id,
        ]);
        $successCount++;
    }
    fclose($handle);

    return !empty($errors) 
        ? back()->with('import_errors', $errors)
        : back()->with('success', "Bulk assignment processed: {$successCount} updated.");
}

    private function validateSubject(Request $request, $subjectId = null)
    {
        return $request->validate([
            'subject_code'   => ['required', 'string', 'max:10', Rule::unique('subjects', 'subject_code')->ignore($subjectId)],
            'subject_name'   => 'required|string|max:255',
            'course_id'      => 'required|exists:courses,id',
            'coordinator_id' => [
                'nullable', 'exists:users,id',
                function ($attribute, $value, $fail) use ($subjectId) {
                    if (Subject::where('coordinator_id', $value)->where('id', '!=', $subjectId)->exists()) {
                        $fail('This lecturer is already assigned as a coordinator for another subject.');
                    }
                },
            ],
            'sme1_id'        => ['nullable', 'exists:users,id', 'different:coordinator_id'],
            'sme2_id'        => ['nullable', 'exists:users,id', 'different:coordinator_id', 'different:sme1_id'],
        ], [
            'sme1_id.different' => 'SME 1 cannot be the same as the Coordinator.',
            'sme2_id.different' => 'SME 2 must be different from the Coordinator and SME 1.',
        ]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject has been removed.');
    }
}