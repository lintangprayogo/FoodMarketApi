<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect('admin-dashboard');
});

Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin-dashboard');
    Route::resource('user', UserController::class);
    Route::resource('food', FoodController::class);
    Route::resource('transaction', TransactionController::class);
    Route::get('transaction/{id}/status/{status}', [TransactionController::class,'changeStatus'])
    ->name('transaction.changeStatus');
});


Route::get('midtrans/success', [MidtransController::class, 'success']);
Route::get('midtrans/unfinish', [MidtransController::class, 'unfinish']);
Route::get('midtrans/error', [MidtransController::class, 'error']);
