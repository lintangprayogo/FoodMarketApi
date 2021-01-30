<?php

use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('user/photo', [UserController::class, 'updatePhoto']);
    Route::get('transaction', [TransactionController::class, 'all']);
    Route::post('transaction/{id}', [TransactionController::class, 'update']);
    Route::post('checkout', [TransactionController::class,'checkout']);
    Route::post('logout', [UserController::class, 'logout']);
});
Route::post('register', [UserController::class, 'register']);
Route::post('callback', [MidtransController::class, 'callback']);
Route::post('login', [UserController::class, 'login']);



