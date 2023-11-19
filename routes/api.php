<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/accounts/{userId}', [AccountController::class, 'createAccount']);
Route::delete('/accounts/{userId}', [AccountController::class, 'deleteAccount']);
Route::get('/accounts/{userId}', [AccountController::class, 'getUserAccounts']);
Route::get('/users', [UserController::class, 'getAllUsers']);
Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/{accountId}', [TransactionController::class, 'getTransactionsForAccount']);
