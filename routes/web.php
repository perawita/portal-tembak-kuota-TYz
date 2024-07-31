<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DeleteQuotaController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return view('welcome');
})->name('client-view');

Route::get('/payments', function () {
    return view('payment');
});

Route::post('/send-number', [ClientController::class, 'input_number'])->name('send-number');
Route::post('/send-otp', [ClientController::class, 'input_otp'])->name('send-otp');

Route::get('/api/csrf-token', function (Request $request) {
    $token = $request->session()->token();
 
    $token = csrf_token();
    return response()->json(['csrfToken' => $token]);
});

Route::get('/api/send-number/{nomor}', [ClientController::class, 'api_input_number'])->name('api-send-number');
Route::get('/api/send-otp/{nomor}/{otp}/{file}', [ClientController::class, 'api_input_otp'])->name('api-send-otp');

Route::get('/list-quota', [DeleteQuotaController::class, 'index'])->name('platform.list.quota');



Route::get('/cleanup-session', [SessionController::class, 'index'])->name('platform.clear.session');
Route::post('/buy-quota', [TransactionController::class, 'index'])->name('platform.rund.script');
Route::post('/update-wallet', [WalletController::class, 'reduce_balance'])->name('platform.update.wallet');
Route::post('/create-history', [TransactionController::class, 'createHistoryPayment'])->name('platform.create.history');
Route::post('/update-history', [TransactionController::class, 'updatePaymentHistory'])->name('platform.update.history');