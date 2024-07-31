<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DeleteQuotaController;
use App\Http\Controllers\ClientController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/api/csrf-token', function (Request $request) {
    $token = $request->session()->token();
    $token = csrf_token();
    return response()->json(['csrfToken' => $token]);
});

Route::post('/api/send-number', [ClientController::class, 'api_input_number'])->name('api-send-number');
