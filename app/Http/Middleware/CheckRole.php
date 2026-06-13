<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if the user is even logged in
        // 2. Check if their role matches the requirement
        if (!Auth::check() || Auth::user()->role !== $role) {
            return redirect('/')->with('error', 'Unauthorized access. Please login with the correct role.');
        }

        return $next($request);
    }
}