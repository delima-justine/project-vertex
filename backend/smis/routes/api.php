<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplyRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user/profile', function (Request $request) {
    $user = $request->user();
    return response([
        'user' => $user->load(['role', 'office']),
        'permissions' => $user->role->permissions->pluck('name'),
    ]);
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    Route::apiResource('supply-requests', SupplyRequestController::class)->parameters([
        'supply-requests' => 'supply_request'
    ]);
    Route::get('/supplies', [SupplyController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/units', [UnitController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:admin,superadmin'])->group(function () {
    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/offices', [OfficeController::class, 'index']);
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::patch('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    Route::apiResource('supplies', SupplyController::class)->except(['index'])->parameters([
        'supplies' => 'supply'
    ]);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
    Route::apiResource('units', UnitController::class)->except(['index']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
