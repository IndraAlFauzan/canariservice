<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('admin/profile', [ProfileController::class, 'adminProfile']);

    // Route::get('/user', [AuthController::class, 'me']);
    // Route::put('admin/role', [RoleController::class, 'update']);
    // Route::get('admin/role', [RoleController::class, 'index']);
    // Route::post('admin/role', [RoleController::class, 'store']);
    // Route::delete('admin/role/{id}', [RoleController::class, 'destroy']);
});

Route::middleware('auth:api', 'role:buyer')->group(function () {
    Route::post('buyer/profile', [ProfileController::class, 'addBuyerProfile']);
});
