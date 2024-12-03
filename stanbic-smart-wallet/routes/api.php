<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
});
