<?php

use App\Http\Controllers\SupplyController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Routes for tbl_user
Route::resource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);
Route::resource('supplies', SupplyController::class);
Route::resource('units', UnitController::class);