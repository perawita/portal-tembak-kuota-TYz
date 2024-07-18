<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WalletController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments', function () {
    return view('payment');
});

Route::get('/send-number', [DeveloperController::class, 'index']);
Route::get('/send-otp', [DeveloperController::class, 'otp']);



Route::get('/cleanup-session', [SessionController::class, 'index'])->name('platform.clear.session');

Route::post('/buy-quota', [TransactionController::class, 'index'])->name('platform.rund.script');
Route::post('/update-wallet', [WalletController::class, 'reduce_balance'])->name('platform.update.wallet');
Route::post('/create-history', [TransactionController::class, 'createHistoryPayment'])->name('platform.create.history');
Route::post('/update-history', [TransactionController::class, 'updatePaymentHistory'])->name('platform.update.history');