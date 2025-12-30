<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/coordinator/login', [AuthController::class, 'showCoordinatorLogin'])->name('coordinator.login');
Route::post('/coordinator/login', [AuthController::class, 'coordinatorLogin'])->name('coordinator.login.post');

Route::get('/applicant/login', [AuthController::class, 'showApplicantLogin'])->name('applicant.login');
Route::post('/applicant/login', [AuthController::class, 'applicantLogin'])->name('applicant.login.post');
Route::get('/applicant/register', [AuthController::class, 'showApplicantRegister'])->name('applicant.register');
Route::post('/applicant/register', [AuthController::class, 'applicantRegister'])->name('applicant.register.post');

use App\Http\Controllers\Admin\UserController;

Route::middleware([])->prefix('admin')->group(function() {
    Route::get('/', function(){ return view('admin.dashboard'); });
    Route::resource('users', UserController::class, ['as' => 'admin']);
});
