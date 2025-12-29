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
