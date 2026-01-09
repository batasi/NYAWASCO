<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerSearchController;
use App\Http\Controllers\Api\BalanceInquiryController;
use App\Http\Controllers\Api\MeterReadingController;
use App\Http\Controllers\Api\ReadingHistoryController;
use App\Models\Bill;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\CustomerController;
// Use web middleware for authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/customers/search', [CustomerSearchController::class, 'search'])
        ->name('api.customers.search');
});

Route::get('/bills/search', function(Request $request) {
    $search = $request->get('search', '');

    $bills = Bill::with(['customer', 'meter'])
        ->where(function($query) use ($search) {
            $query->where('bill_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('customer_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('meter', function($meterQuery) use ($search) {
                      $meterQuery->where('meter_number', 'like', "%{$search}%");
                  });
        })
        ->limit(10)
        ->get()
        ->map(function($bill) {
            return [
                'id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'total_amount' => $bill->total_amount,
                'bill_status' => $bill->bill_status,
                'customer_id' => $bill->customer->id,
                'customer_name' => $bill->customer->first_name . ' ' . $bill->customer->last_name,
                'meter_number' => $bill->meter ? $bill->meter->meter_number : null,
            ];
        });

    return response()->json($bills);
});

Route::prefix('v1')->group(function () {

    // Login check
    Route::post('auth/login', [AuthController::class, 'login']);

    // 1. Search customers
    Route::get('/customers/search', [CustomerSearchController::class, 'search']);

    // 2. Submit reading and get bill
    Route::post('/meter-readings/submit', [MeterReadingController::class, 'submitReading']);

    // 3. Balance inquiry
    Route::get('/customers/{customerId}/balance', [BalanceInquiryController::class, 'getBalance']);

    // 4. Reading history
    Route::get('/meter-readings/history', [ReadingHistoryController::class, 'getReadingHistory']);

});
