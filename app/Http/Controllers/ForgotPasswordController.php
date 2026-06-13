<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Show the request form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the request to send the reset link.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['staff_id' => 'required|string']);

        $user = User::where('staff_id', $request->staff_id)->first();

        if (!$user) {
            return back()->with('success', 'If an account exists for this Staff ID, a reset link has been sent to your registered email.');
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'A password reset link has been sent to your email.')
            : back()->withErrors(['email' => 'Unable to send reset link.']);
    }

    /**
     * Handle the actual password reset.
     */
    public function reset(Request $request)
    {
        // Debugging: If this fails, check if your hidden email input is populated
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset successfully.')
            : back()->withErrors(['email' => 'This password reset token is invalid.']);
    }
}