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

Route::post('/register', [UserController::class, 'register']);
Route::post('/accounts/create', [AccountController::class, 'createAccount'])->middleware('auth:sanctum');
Route::get('/accounts', [AccountController::class, 'getUserAccounts'])->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'getAllUsers'])->middleware('auth:sanctum');
Route::post('/transactions', [TransactionController::class, 'create'])->middleware('auth:sanctum');
Route::get('/transactions/{accountId}', [TransactionController::class, 'getTransactionsForAccount'])->middleware('auth:sanctum');
