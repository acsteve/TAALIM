<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User; 
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('kp')->get();

        // Fetch all lecturers for the dropdown candidates
        $allLecturers = User::where('role', 'lecturer')->orderBy('name')->get();

        // Get IDs of everyone who is currently assigned as a KP to any course
        $assignedKpIds = Course::whereNotNull('kp_id')->pluck('kp_id')->toArray();

        return view('admin.courses', compact('courses', 'allLecturers', 'assignedKpIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
            // Enforce that the kp_id must not already exist in the courses table
            'kp_id'       => 'nullable|exists:users,id|unique:courses,kp_id', 
        ], [
            'kp_id.unique' => 'This lecturer is already appointed as a Ketua Program for another course.'
        ]);

        Course::create($validated);

        return back()->with('success', 'New program created and KP assigned successfully!');
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            // unique check ignores the current course ID
            'course_code' => 'required|string|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            // unique check ignores the current KP of THIS course
            'kp_id'       => 'nullable|exists:users,id|unique:courses,kp_id,' . $course->id,
        ], [
            'kp_id.unique' => 'This lecturer is already a KP elsewhere. Please choose an available lecturer.'
        ]);

        $course->update($validated);

        return back()->with('success', 'Program details updated successfully.');
    }

    public function destroy(Course $course)
    {
        // Check if there are dependencies here if needed (e.g., $course->subjects()->count())
        $course->delete();
        return back()->with('success', 'Program deleted from the system.');
    }
}