<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HRController;
use App\Livewire\Register;

Route::get('/', function () {
    return view('landing');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/coordinator/login', [AuthController::class, 'showCoordinatorLogin'])->name('coordinator.login');
Route::post('/coordinator/login', [AuthController::class, 'coordinatorLogin'])->name('coordinator.login.post');

Route::get('/applicant/login', [AuthController::class, 'showApplicantLogin'])->name('applicant.login');
Route::post('/applicant/login', [AuthController::class, 'applicantLogin'])->name('applicant.login.post');

Route::get('/register', Register::class)->name('register');

use App\Http\Controllers\HR\JobPostingController;
use App\Http\Controllers\HR\UserController;

Route::middleware(['auth'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    
    // HR Routes
    Route::prefix('hr')->name('hr.')->group(function() {
        Route::get('interns', [HRController::class, 'interns'])->name('interns');
        Route::resource('job-postings', JobPostingController::class);
        Route::resource('users', UserController::class);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
