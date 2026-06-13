<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // Check if the current route is an admin route
        if ($request->route()->getName() === 'admin.profile.edit') {
            return view('admin.profile-page');
        }

        return view('profile.profile-page');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
        {
            $user = $request->user();

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users')->ignore($user->id)
                ],
            ]);

            $user->update($request->only('name', 'email'));

            return back()->with('success', 'Profile updated successfully.');
        }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
        {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => [
                    'required', 
                    'confirmed', 
                    Password::min(8)->letters()->numbers()
                ],
            ]);

            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password changed successfully.');
        }
}