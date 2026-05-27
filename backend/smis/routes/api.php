<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplyRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\DatabaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user/profile', function (Request $request) {
    $user = $request->user();
    $allPermissions = $user->has_custom_permissions
        ? $user->permissions->pluck('name') 
        : $user->role->permissions->pluck('name');

    return response([
        'user' => $user->load(['role', 'office']),
        'permissions' => $allPermissions,
    ]);
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json([
            'count' => \App\Models\Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count()
        ]);
    });
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    Route::get('/supply-requests/status-counts', [SupplyRequestController::class, 'statusCounts']);
    Route::patch('/supply-requests/batch-update', [SupplyRequestController::class, 'updateBatch']);
    Route::post('/supply-requests/batch-store', [SupplyRequestController::class, 'storeBatch']);
    Route::apiResource('supply-requests', SupplyRequestController::class)->parameters([
        'supply-requests' => 'supply_request'
    ]);
    Route::get('/supplies', [SupplyController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/archives', [ArchiveController::class, 'index']);
    Route::post('/archives', [ArchiveController::class, 'store']);
    Route::post('/archives/{archive}/restore', [ArchiveController::class, 'restore']);
});

Route::middleware(['auth:sanctum', 'role:admin,superadmin'])->group(function () {
    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/offices', [OfficeController::class, 'index']);
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/roles/{role}/permissions', [PermissionController::class, 'getByRole']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/admins', [UserController::class, 'listAdmins']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::patch('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    Route::get('/admin-audits', [AdminAuditController::class, 'index']);
    Route::get('/admin-audits/{admin_audit}', [AdminAuditController::class, 'show']);

    Route::post('/database/backup', [DatabaseController::class, 'backup']);
    Route::middleware(['role:superadmin'])->post('/database/restore', [DatabaseController::class, 'restore']);

    Route::get('/supplies/{supply}/history', [SupplyController::class, 'history']);
    Route::apiResource('supplies', SupplyController::class)->except(['index'])->parameters([
        'supplies' => 'supply'
    ]);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
    Route::apiResource('units', UnitController::class)->except(['index']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/resend-password-link', [AuthController::class, 'resendPasswordLink']);
Route::post('/check-reset-token', [AuthController::class, 'checkResetToken']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
