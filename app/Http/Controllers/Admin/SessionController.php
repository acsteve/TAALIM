<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession; // FIXED: Corrected the import name
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display the session management dashboard.
     */
    public function index()
    {
        // Fetch all academic sessions, newest first
        $sessions = AcademicSession::orderBy('created_at', 'desc')->get();
        
        return view('admin.manage-session', compact('sessions'));
    }

    /**
     * Create a new academic session.
     */
    public function store(Request $request)
    {
        $request->validate([
            // FIXED: Validation must point to 'academic_sessions' table
            'name' => 'required|unique:academic_sessions,name|max:255'
        ]);

        // FIXED: Using AcademicSession model
        $isFirst = AcademicSession::count() === 0;

        AcademicSession::create([
            'name' => $request->name,
            'is_active' => $isFirst
        ]);

        return redirect()->back()->with('success', 'New academic session created successfully.');
    }

    /**
     * Switch the system's active session.
     */
    public function activate($id)
    {
        // FIXED: Using AcademicSession model
        $session = AcademicSession::findOrFail($id);

        // Uses the static logic we built in the Model
        AcademicSession::activate($id);

        return redirect()->back()->with('success', "System is now active for: {$session->name}");
    }

    /**
     * Remove a session.
     */
    public function destroy($id)
    {
        // FIXED: Using AcademicSession model
        $session = AcademicSession::findOrFail($id);

        if ($session->is_active) {
            return redirect()->back()->with('error', 'Cannot delete the currently active session.');
        }

        $session->delete();
        return redirect()->back()->with('success', 'Session removed.');
    }
}