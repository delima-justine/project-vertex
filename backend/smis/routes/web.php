<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Routes for tbl_user
Route::resource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);