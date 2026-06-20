<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SmeController;
use App\Http\Controllers\SubjectCoordinatorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\KpController;
use App\Http\Controllers\AnswerSampleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SubjectAssignmentController; 
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Lecturer\DashboardController;
use App\Http\Controllers\CourseReportController;
use App\Http\Controllers\SubjectSectionController;
use App\Http\Controllers\Admin\SqaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return view('auth.index'); })->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- FORGOT PASSWORD FLOW (The "Forgot Password" sequence) ---
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// The route that handles the actual reset submission
Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('auth.reset-password', [
        'token' => $token, 
        'email' => $request->query('email') // Explicitly grab the email from the URL
    ]);
})->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update_forgotten');
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {


// --- PROFILE MANAGEMENT (When logged in) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    // Renamed to avoid name collision with password.update_forgotten
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.change');
});

/*
|--------------------------------------------------------------------------
| Subject Coordinator Routes
|--------------------------------------------------------------------------
*/

Route::prefix('subjcoordinator')->name('subjcoordinator.')->group(function () {
    
    // 1. The Management Hub
    Route::get('/assessments', [SubjectCoordinatorController::class, 'index'])->name('index');
    //folder view
    // Route::get('/assessment-list', [AssessmentController::class, 'coordinatorIndex'])
    //      ->name('assessment-list');
    Route::get('/assessment/download-zip/{id}', [AssessmentController::class, 'downloadZip'])
        ->name('assessment.download-zip');

    // 2. Uploading New Assessments
    Route::get('/assessment-upload', [SubjectCoordinatorController::class, 'showUploadForm'])->name('upload');
    Route::post('/assessment/store', [SubjectCoordinatorController::class, 'store'])->name('store');

    // 3. Action: Fix & Re-upload
    Route::patch('/reupload/{id}', [SubjectCoordinatorController::class, 'reupload'])->name('reupload');
        
    // 4. Action: Edit Metadata
    Route::patch('/assessment/{id}/metadata', [AssessmentController::class, 'updateMetadata'])->name('metadata');

    // 5. Action: Remove Assessment
    Route::delete('/assessment/{id}', [SubjectCoordinatorController::class, 'destroy'])->name('destroy');

    // 6. Answer Samples Management
    Route::get('/answersample-upload', [SubjectCoordinatorController::class, 'manageSamples'])->name('answersample');
    Route::post('/answer-samples/store', [SubjectCoordinatorController::class, 'storeSamples'])->name('samples.store');


    // Checklist Viewer (Expects subject ID and session string parameters)
    Route::get('/reports/subject/{subject_id}/{session}', [CourseReportController::class, 'index'])
        ->name('reports.index');

    // Form Processing Handler
    Route::post('/reports/subject/{subject_id}/{session}/upload', [CourseReportController::class, 'upload'])
        ->name('reports.upload');

    // Document Row Deletion Handler
    Route::delete('/reports/{id}', [CourseReportController::class, 'destroy'])
        ->name('reports.destroy');

    // =========================================================================
    // NEW: Section Management Engine 
    // =========================================================================
    Route::get('/sections', [SubjectSectionController::class, 'index'])->name('sections.index');
    Route::post('/sections', [SubjectSectionController::class, 'store'])->name('sections.store');
    Route::delete('/sections/{id}', [SubjectSectionController::class, 'destroy'])->name('sections.destroy');

    // 7. Dynamic Slugs / Folders (Always kept at the bottom of the group)
    Route::get('/folder-view/{id}', [SubjectCoordinatorController::class, 'viewFolder'])->name('folder');
});

/*
|--------------------------------------------------------------------------
| SME (Subject Matter Expert) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('sme')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/assessment-verification', [SmeController::class, 'index'])->name('sme.verification');
    
    // Workspace
    Route::get('/review/{id}', [SmeController::class, 'review'])->name('sme.review');
    
    // Status Update logic
    Route::patch('/assessment/{id}/status', [SmeController::class, 'updateStatus'])->name('sme.update-status');
});

    /*
    |--------------------------------------------------------------------------
    | KP (Ketua Program) Routes
    |--------------------------------------------------------------------------
    */
Route::middleware(['auth'])->prefix('kp')->group(function () {
    
    // 1. Existing Assessment Verification Routes
    Route::get('/assessment-verification', [KpController::class, 'index'])
        ->name('kp.verification'); 

    Route::get('/review-assessment/{id}', [KpController::class, 'review'])
        ->name('kp.review');

    Route::patch('/finalize/{id}', [KpController::class, 'finalize'])
        ->name('kp.finalize');

    // 2. 📊 FIXED: Course Report Master Index (Searchable Page)
    // We attach your exact sidebar route name here so the link points to the search grid
    Route::get('/course-reports', [CourseReportController::class, 'kpIndex'])
        ->name('kp.midterm.audit');
    
    // 3. Drill-down Specific Dossier Page
    // This loads the explicit checklist dossier for a single subject when clicked from the grid
    Route::get('/reports/subject/{subject_id}/{session}', [CourseReportController::class, 'kpShow'])
        ->name('reports.show');

    Route::get('/assessment-archive/subject/{subject_id}', [AssessmentController::class, 'kpSubjectAssessments'])
        ->name('kp.subject.assessments');
    Route::get('/assessment-archive', [AssessmentController::class, 'kpSubjectList'])
        ->name('kp.assessment-archive');

    // Route for Viewing (Inline in browser)
    Route::get('/assessment/view/{id}', [AssessmentController::class, 'viewFile'])
        ->name('kp.assessment.view');

    Route::get('/assessment/download/{id}', [AssessmentController::class, 'download'])
        ->name('kp.assessment.download');

    Route::get('/assessment-folder/{id}', [AssessmentController::class, 'kpShowFolder'])
        ->name('kp.assessment.folder');
});
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->prefix('admin')->group(function () {
        

        // Profile Management (Admin Specific)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.password.change');
        
        // Dashboard & High Level Stats
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('/users', [UserController::class, 'manageUser'])->name('admin.users.manage');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
        
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy'); 
        Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');

        // Session Management
        Route::get('/manage-session', [SessionController::class, 'index'])->name('admin.manage-session');
        Route::post('/manage-session', [SessionController::class, 'store'])->name('admin.sessions.store');
        Route::post('/manage-session/{id}/activate', [SessionController::class, 'activate'])->name('admin.sessions.activate');
        Route::delete('/sessions/{id}', [SessionController::class, 'destroy'])->name('admin.sessions.destroy');
        
        // Course Management (Programs)
        Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
        Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('admin.courses.update');

        //sqa
        Route::get('manage-sqa', [SqaController::class, 'index'])->name('admin.sqa.manage');
        Route::post('manage-sqa', [SqaController::class, 'store'])->name('admin.sqas.store');


        // This uses Resource routes for index, store, update, and destroy
        Route::resource('subjects', SubjectController::class)->names([
            'index' => 'admin.subjects.index',
            'store' => 'admin.subjects.store',
            'update' => 'admin.subjects.update',
            'destroy' => 'admin.subjects.destroy'
        ]);

    Route::prefix('assignments')->group(function () {
        // Main view for managing assignments
        Route::get('/', [SubjectAssignmentController::class, 'index'])->name('admin.assignments.index');
        
        // Store/Update an assignment (Workflow Deployment)
        Route::post('/deploy', [SubjectAssignmentController::class, 'store'])->name('admin.assignments.store');
        
        // Revoke/Delete an assignment
        // Changed parameter to {assignment} to match Laravel's Implicit Model Binding
        Route::delete('/{assignment}', [SubjectAssignmentController::class, 'destroy'])->name('admin.assignments.destroy');

    });
    //csv functionality
    Route::get('/subjects/download-template/{type}', [SubjectController::class, 'downloadTemplate'])->name('admin.subjects.download.template');
    Route::post('/subjects/import-create', [SubjectController::class, 'importCreate'])->name('admin.subjects.import.create');
    Route::post('/subjects/bulk-update', [SubjectController::class, 'bulkUpdate'])->name('admin.subjects.bulk-update');
    Route::post('/subjects/import-assign', [SubjectController::class, 'importAssign'])->name('admin.subjects.import.assign');

    });

    Route::middleware(['auth', 'role:lecturer'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('lecturer.dashboard');
    });

    //sqa routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/sqa/dashboard', [App\Http\Controllers\Admin\SqaController::class, 'sqaDashboard'])->name('sqa.dashboard');
        Route::get('/sqa/subject/{id}', [App\Http\Controllers\Admin\SqaController::class, 'sqaSubjectDetails'])->name('sqa.subject.show');

        Route::get('/sqa/assessment/{id}', [SqaController::class, 'showAssessment'])
        ->name('sqa.assessment.show');

    });



});