<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicSession;
use App\Models\Subject;
use App\Models\Course;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * 1. Global View Composer
         * Shares the active session with ALL blade files.
         */
        View::composer('*', function ($view) {
            $activeSession = AcademicSession::where('is_active', true)->first();
            $view->with('activeSession', $activeSession);
        });

        /**
         * 2. Targeted View Composer for Lecturer Sidebar
         * This "pushes" the SME, KP status, and Coordinated Subject specifically to your lecturer layout.
         * It only runs when the lecturer layout is loaded, saving system resources.
         */
        View::composer('layouts.lecturerlayout.app', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Logic to check if user is appointed as an SME for any subject
                $isSmeAppointed = Subject::where('sme1_id', $user->id)
                                    ->orWhere('sme2_id', $user->id)
                                    ->exists();
                
                // Logic to check if user is appointed as a KP for any program
                $isKpAppointed = Course::where('kp_id', $user->id)
                                    ->exists();

                // 🛠️ NEW: Fetch the 1 single subject this user coordinates
                $coordinatedSubject = Subject::where('coordinator_id', $user->id)->first();

                // Get the active session code from the subject table, or fall back to global active session if empty
                $activeSession = AcademicSession::where('is_active', true)->first();
                $subjectSession = $coordinatedSubject?->session ?? ($activeSession?->session_code ?? '20252026-01');

                $view->with([
                    'isSmeAppointed' => $isSmeAppointed,
                    'isKpAppointed'  => $isKpAppointed,
                    'activeSubject'  => $coordinatedSubject, // 🚀 Clean layout access
                    'activeSession'  => $subjectSession,     // 🚀 URL ready session string
                ]);
            }
        });
    }
}