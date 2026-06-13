<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function manageUser()
    {
        // Eager load 'course' to show which program a KP belongs to
        $users = User::with('course')->get();
        $courses = Course::all();
        $activeSession = AcademicSession::where('is_active', true)->first();

        return view('admin.manage-user', compact('users', 'courses', 'activeSession'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            // Simplified roles: Admin, Lecturer, KP
            'role' => 'required|in:admin,lecturer,kp',
            // If updating to KP, they MUST have a course (program) assigned
            'course_id' => [
                Rule::requiredIf($request->role === 'kp'),
                'exists:courses,id',
                'nullable'
            ]
        ]);

        $user->update([
            'role' => $request->role,
            'course_id' => $request->role === 'kp' ? $request->course_id : null,
        ]);

        return back()->with('success', "Role and Department updated for {$user->name}");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:admin,lecturer,kp',
            'staff_id'  => 'required|string|unique:users,staff_id', // Recommended for UMPSA systems
            // KP must be linked to a Course (Program)
            'course_id' => 'required_if:role,kp|exists:courses,id|nullable',
        ]);

        // Default password for new staff
        $validated['password'] = Hash::make('Password123');

        // If the user is NOT a KP, ensure course_id is null to keep data clean
        if ($validated['role'] !== 'kp') {
            $validated['course_id'] = null;
        }

        User::create($validated);

        return back()->with('success', 'Staff account created! Default password: Password123');
    }

    public function destroy(User $user)
    {
        try {
            // Prevent accidental self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraints (e.g., if user is assigned as Coordinator or SME)
            if ($e->getCode() == "23000") { 
                return redirect()->back()->with('error', 'Cannot delete: This lecturer is assigned as a Coordinator or SME in an active workflow.');
            }
            
            return redirect()->back()->with('error', 'An error occurred while deleting the user.');
        }
    }
}