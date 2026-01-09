<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class BalanceInquiryController extends Controller
{
    /**
     * Get customer balance inquiry
     *
     * @param Request $request
     * @param int $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance(Request $request, $customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        // Calculate outstanding balance
        $totalBilled = Bill::where('customer_id', $customerId)
            ->whereIn('bill_status', ['unpaid', 'partial'])
            ->sum('total_amount');

        $totalPaid = Payment::where('customer_id', $customerId)
            ->sum('amount');

        $outstandingBalance = $totalBilled - $totalPaid;

        // Get last payment
        $lastPayment = Payment::where('customer_id', $customerId)
            ->latest('payment_date')
            ->first();

        // Get current meter details
        $meter = $customer->meters()->first();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_id' => $customer->id,
                'customer_number' => $customer->customer_number,
                'full_name' => $customer->first_name . ' ' . $customer->last_name,
                'outstanding_balance' => (float) $outstandingBalance,
                'credit_balance' => (float) $customer->credit_balance,
                'net_balance' => (float) $outstandingBalance - $customer->credit_balance,
                'last_payment' => $lastPayment ? [
                    'amount' => (float) $lastPayment->amount,
                    'date' => $lastPayment->payment_date->format('Y-m-d'),
                    'payment_method' => $lastPayment->payment_method
                ] : null,
                'meter_info' => $meter ? [
                    'meter_number' => $meter->meter_number,
                    'current_reading' => (float) $meter->current_reading,
                    'meter_type' => $meter->meter_type
                ] : null
            ]
        ]);
    }
}
