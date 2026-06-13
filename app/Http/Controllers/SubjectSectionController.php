<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectSectionController extends Controller
{
    /**
     * Display the section workspace for the single managed subject
     */
    public function index()
    {
        // Scope directly to the coordinator's singular managed course
        $subject = Subject::with('sections')
            ->where('coordinator_id', Auth::id())
            ->firstOrFail();

        return view('subjcoordinator.manage-sections', compact('subject'));
    }

    /**
     * Persist a new section record safely
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'section_name'  => 'required|string|max:255',
            'lecturer_name' => 'required|string|max:255',
        ]);

        // Security Guard: double check they actually own the subject they are submitting for
        $subject = Subject::where('id', $request->subject_id)
            ->where('coordinator_id', Auth::id())
            ->firstOrFail();

        Section::create([
            'subject_id'    => $subject->id,
            'section_name'  => $request->section_name,
            'lecturer_name' => $request->lecturer_name,
        ]);

        return redirect()->back()->with('success', 'New class section tracking node initialized successfully.');
    }

    /**
     * Purge a section record
     */
    public function destroy($id)
    {
        // Enforce ownership: Section must belong to a subject coordinated by the current user
        $section = Section::whereHas('subject', function($query) {
            $query->where('coordinator_id', Auth::id());
        })->findOrFail($id);

        $section->delete();

        return redirect()->back()->with('success', 'Class section node removed from registration entries.');
    }
}