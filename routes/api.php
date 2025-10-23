<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesapalController;

Route::post('/pesapal/ipn', [PesapalController::class, 'ipn']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
