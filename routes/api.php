<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesapalController;

Route::post('/pesapal/ipn', [PesapalController::class, 'ipn']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('auth:api')->group(function () {
//     Route::post('/mpesa/stkpush', [MpesaController::class, 'stkPush']);
//     Route::get('/payments/{paymentId}/status', [MpesaController::class, 'checkPaymentStatus']);
//     Route::get('/payments/history', [MpesaController::class, 'paymentHistory']);
// });




