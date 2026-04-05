<?php

use App\Http\Controllers\RolesController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplyRequestController;

Route::get('/', function () {
    return view('welcome');
});

// Routes for tbl_user
Route::resource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);
Route::resource('supplies', SupplyController::class);
Route::resource('units', UnitController::class);
Route::resource('roles', RolesController::class);
Route::resource('supply-requests', SupplyRequestController::class);