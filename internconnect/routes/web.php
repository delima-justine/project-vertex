<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\HR\DashboardController;
use App\Livewire\Register;

Route::get('/', function () {
    return view('landing');
});

// Redirect /login to /auth/login for convenience
Route::get('/login', function () {
    return redirect('/auth/login');
})->name('login');

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

    Route::get('/coordinator/login', [AuthController::class, 'showCoordinatorLogin'])->name('auth.coordinator.login');
    Route::post('/coordinator/login', [AuthController::class, 'coordinatorLogin'])->name('auth.coordinator.login.post');

    Route::get('/applicant/login', [AuthController::class, 'showApplicantLogin'])->name('auth.applicant.login');
    Route::post('/applicant/login', [AuthController::class, 'applicantLogin'])->name('auth.applicant.login.post');

    Route::get('/register', Register::class)->name('auth.register');
});

use App\Http\Controllers\HR\JobPostingController;
use App\Http\Controllers\HR\UserController;
use App\Http\Controllers\CoordinatorController;

Route::middleware(['auth'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::get('/coordinator/dashboard', [CoordinatorController::class, 'dashboard'])->name('coordinator.dashboard');
    Route::get('/coordinator/monitor-interns', [CoordinatorController::class, 'monitorInterns'])->name('coordinator.monitor-interns');
    Route::get('/coordinator/support-docs', [CoordinatorController::class, 'supportDocs'])->name('coordinator.support-docs');
    Route::get('/coordinator/settings', [CoordinatorController::class, 'settings'])->name('coordinator.settings');
    Route::post('/coordinator/update-profile', [CoordinatorController::class, 'updateProfile'])->name('coordinator.update-profile');
    Route::post('/coordinator/update-password', [CoordinatorController::class, 'updatePassword'])->name('coordinator.update-password');
    
    // HR Routes
    Route::prefix('hr')->name('hr.')->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('interns', [HRController::class, 'interns'])->name('interns');
        Route::resource('job-postings', JobPostingController::class);
        Route::resource('users', UserController::class);
    });

    // Intern Dashboard Route
    Route::get('/intern/dashboard', function () {
        return view('intern.dashboard');
    })->name('intern.dashboard');

    // Intern Profile Route
    Route::get('/intern/profile', function () {
        return view('intern.profile');
    })->name('intern.profile');

    // Job Search Route
    Route::get('/intern/job-search', function () {
        return view('intern.job_search');
    })->name('intern.job.search');

    Route::get('intern/job-applications', function () {
        return view('intern.job_application');
    })->name('intern.job.application');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
