<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\HRController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/coordinator/login', [AuthController::class, 'showCoordinatorLogin'])->name('coordinator.login');
Route::post('/coordinator/login', [AuthController::class, 'coordinatorLogin'])->name('coordinator.login.post');

Route::get('/applicant/login', [AuthController::class, 'showApplicantLogin'])->name('applicant.login');
Route::post('/applicant/login', [AuthController::class, 'applicantLogin'])->name('applicant.login.post');
Route::get('/applicant/register', [AuthController::class, 'showApplicantRegister'])->name('applicant.register');
Route::post('/applicant/register', [AuthController::class, 'applicantRegister'])->name('applicant.register.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

use App\Http\Controllers\Admin\UserController;

Route::middleware([])->prefix('admin')->group(function() {
    Route::get('/', function(){ return view('admin.dashboard'); });
    Route::resource('users', UserController::class, ['as' => 'admin']);
});
