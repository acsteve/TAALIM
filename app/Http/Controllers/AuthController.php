<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle the authentication attempt.
     */
    public function login(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'staff_id' => 'required',
            'password' => 'required',
            'role'     => 'required',
        ]);

        // 2. Find user by staff_id and role
        $user = User::where('staff_id', $request->staff_id)
                    ->where('role', $request->role)
                    ->first();

        // 3. Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid staff ID, password, or role selection.');
        }

        // 4. Log the user in
        Auth::login($user);

        // 5. Redirect based on the unified role structure
        return match ($user->role) {
            'admin'    => redirect()->route('admin.manage-session'),
            'sqa'      => redirect()->route('sqa.dashboard'), // Added SQA route
            'lecturer' => redirect()->route('lecturer.dashboard'),
            default    => redirect('/'),
        };
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout()
    {
        Auth::logout();

        // Security best practice: invalidate and regenerate token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/'); 
    }
}