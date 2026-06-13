<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\AcademicSession;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard with stats.
     */
    public function dashboard()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_courses'  => Course::count(),
            'total_subjects' => Subject::count(),
            'active_session' => AcademicSession::where('is_active', true)->first(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Manage Users (Lecturers, KPs, SMEs)
     */
    public function manageUsers()
    {
        $users = User::all();
        $courses = Course::all(); // To link a KP to a Course
        return view('admin.users.index', compact('users', 'courses'));
    }
}