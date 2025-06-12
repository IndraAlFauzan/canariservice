<?php

use App\Http\Controllers\AnakController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IndukController;
use App\Http\Controllers\PostingJualController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Models\Induk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('admin/profile', [ProfileController::class, 'adminProfile']);
    Route::get('admin/profile', [ProfileController::class, 'getAdminProfile']);


    Route::get('admin/induk/', [IndukController::class, 'index']);
    Route::get('admin/induk/{id}', [IndukController::class, 'show']);
    Route::post('admin/induk', [IndukController::class, 'store']);
    Route::put('admin/induk/{id}', [IndukController::class, 'update']);
    Route::delete('admin/induk/{id}', [IndukController::class, 'destroy']);

    Route::post('admin/anak', [AnakController::class, 'store']);
    Route::get('admin/anak', [AnakController::class, 'index']);
    Route::get('admin/anak/{id}', [AnakController::class, 'show']);
    Route::put('admin/anak/{id}', [AnakController::class, 'update']);
    Route::delete('admin/anak/{id}', [AnakController::class, 'destroy']);


    Route::post('admin/posting-jual', [PostingJualController::class, 'store']);
    Route::get('admin/posting-jual', [PostingJualController::class, 'index']);
    Route::get('admin/posting-jual/{id}', [PostingJualController::class, 'show']);
    Route::put('admin/posting-jual/{id}', [PostingJualController::class, 'updateStatus']);
    Route::delete('admin/posting-jual/{id}', [PostingJualController::class, 'destroy']);
    Route::get('admin/burung-semua', [PostingJualController::class, 'getAllBurung']);



    // Route::get('/user', [AuthController::class, 'me']);
    // Route::put('admin/role', [RoleController::class, 'update']);
    // Route::get('admin/role', [RoleController::class, 'index']);
    // Route::post('admin/role', [RoleController::class, 'store']);
    // Route::delete('admin/role/{id}', [RoleController::class, 'destroy']);
});

Route::middleware('auth:api', 'role:buyer')->group(function () {
    Route::post('buyer/profile', [ProfileController::class, 'addBuyerProfile']);
    Route::get('buyer/profile', [ProfileController::class, 'getBuyerProfile']);
    Route::get('buyer/burung-semua-tersedia', [PostingJualController::class, 'getAllBurungTersedia']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('burung-semua-tersedia', [PostingJualController::class, 'getAllBurungTersedia']);
});
