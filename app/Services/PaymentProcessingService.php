<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Meter;
use App\Models\Customer;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentProcessingService
{
    /**
     * Process a payment with proper allocation and balance updates
     */
   public function processPayment(
    Meter $meter,
    float $amount,
    string $paymentMethod,
    ?string $transactionRef,
    Carbon $paymentDate,
    int $userId
): array {
    return DB::transaction(function () use (
        $meter, $amount, $paymentMethod, $transactionRef, $paymentDate, $userId
    ) {

        /* ============================
         | 1. Create Payment Record
         |============================*/
        $payment = Payment::create([
            'meter_id' => $meter->id,
            'customer_id' => $meter->customer_id,
            'user_id' => $userId,
            'payment_no' => 'PAY-' . now()->format('Ym') . '-' . Str::upper(Str::random(6)),
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_status' => 'completed',
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionRef,
            'notes' => 'Processed via system',
        ]);

        /* ============================
         | 2. Allocate Payment to Bills
         |    (Oldest → Newest)
         |============================*/
        $remainingAmount = $amount;
        $appliedBills = [];

        $unpaidBills = Bill::where('customer_id', $meter->customer_id)
            ->where('meter_id', $meter->id)
            ->where('bill_status', '!=', 'paid')
            ->orderBy('billing_period_start')
            ->lockForUpdate()
            ->get();

        foreach ($unpaidBills as $bill) {

            if ($remainingAmount <= 0) {
                break;
            }

            $billBalance = $bill->total_amount - $bill->paid_amount;

            if ($billBalance <= 0) {
                continue;
            }

            $amountToApply = min($remainingAmount, $billBalance);

            // 1. Create allocation FIRST
            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'bill_id' => $bill->id,
                'amount' => $amountToApply,
                'allocated_to_principal' => $amountToApply,
                'allocated_to_late_fee' => 0,
                'allocation_date' => now(),
            ]);

            // 2. Recalculate bill paid amount from allocations (SOURCE OF TRUTH)
            $totalPaid = PaymentAllocation::where('bill_id', $bill->id)
                ->sum('allocated_to_principal');

            $balance = max(0, $bill->total_amount - $totalPaid);

            // 3. Update bill SAFELY
            $bill->update([
                'paid_amount' => $totalPaid,
                'balance' => $balance,
                'bill_status' => $balance == 0 ? 'paid' : 'partial',
                'payment_date' => $balance == 0 ? $paymentDate : null,
            ]);

            $remainingAmount -= $amountToApply;

            $appliedBills[] = [
                'bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'applied_amount' => $amountToApply,
                'new_bill_balance' => $bill->fresh()->balance,
            ];
        }

        /* ============================
         | 3. Handle Overpayment (Credit)
         |============================*/
        $customer = Customer::lockForUpdate()->find($meter->customer_id);

        if ($remainingAmount > 0) {
            $customer->credit_balance += $remainingAmount;
            $payment->update([
                'notes' => trim(($payment->notes ?? '') . " | Credit: {$remainingAmount}")
            ]);
        }

        /* ============================
         | 4. Update Meter Balances - SIMPLE!
         |    Add the paid amount and subtract from current balance
         |============================*/

        // Calculate how much was actually applied to bills (not credit)
        $amountAppliedToBills = $amount - $remainingAmount;

        // Update meter paid_amount (total payments received)
        $meter->paid_amount += $amount;

        // Update meter current_balance (decrease by amount applied to bills)
        // Only decrease by what was applied to bills, not the credit portion
        $meter->current_balance -= $amountAppliedToBills;

        $meter->save();

        /* ============================
         | 5. Update Customer Payment Info
         |============================*/
        $customer->update([
            'last_payment_date' => $paymentDate,
            'last_payment_amount' => $amount,
            'total_payments' => ($customer->total_payments ?? 0) + $amount,
        ]);

        return [
            'payment' => $payment,
            'applied_bills' => $appliedBills,
            'remaining_credit' => $remainingAmount,
            'total_applied' => $amountAppliedToBills,
            'new_meter_balance' => $meter->fresh()->current_balance,
            'new_meter_paid_amount' => $meter->fresh()->paid_amount,
            'customer_credit_balance' => $customer->fresh()->credit_balance,
        ];
    });
}

    /**
     * Calculate late fee for a bill at payment time
     */
    private function calculateLateFeeForPayment(Bill $bill, Carbon $paymentDate): float
    {
        if ($bill->due_date >= $paymentDate || $bill->bill_status === 'paid') {
            return 0;
        }

        $daysLate = $paymentDate->diffInDays($bill->due_date);

        // Example late fee policy: 5% after 30 days, then 1% per month
        if ($daysLate > 30) {
            $monthsLate = ceil(($daysLate - 30) / 30);
            $lateFeeRate = 0.05 + ($monthsLate * 0.01);
            return min($bill->total_amount * $lateFeeRate, $bill->total_amount * 0.25); // Cap at 25%
        }

        return 0;
    }

    /**
     * Void a payment and reverse all allocations
     */
    public function voidPayment(Payment $payment, string $reason, ?string $refundMethod, int $userId): array
    {
        return DB::transaction(function () use ($payment, $reason, $refundMethod, $userId) {
            // 1. Verify payment can be voided
            if ($payment->payment_status === 'voided') {
                throw new \Exception('Payment is already voided.');
            }

            if ($payment->payment_date->diffInDays(now()) > 30) {
                throw new \Exception('Payments older than 30 days cannot be voided.');
            }

            // 2. Reverse bill allocations
            $allocations = $payment->allocations()->with('bill')->get();
            $reversedBills = [];
            $totalAppliedToBills = 0;

            foreach ($allocations as $allocation) {
                $bill = $allocation->bill;
                $totalAppliedToBills += $allocation->allocated_to_principal;

                $bill->update([
                    'paid_amount' => $bill->paid_amount - $allocation->allocated_to_principal,
                    'balance' => $bill->balance + $allocation->allocated_to_principal,
                    'late_fee' => $bill->late_fee - $allocation->allocated_to_late_fee,
                    'bill_status' => $bill->balance > 0 ? 'unpaid' : 'paid',
                    'payment_date' => null,
                ]);

                $reversedBills[] = [
                    'bill_id' => $bill->id,
                    'reversed_principal' => $allocation->allocated_to_principal,
                    'reversed_late_fee' => $allocation->allocated_to_late_fee
                ];
            }

            // 3. Extract credit portion from notes if any
            $creditPortion = 0;
            if ($payment->notes && preg_match('/Credit:\s*([\d,]+(?:\.\d{2})?)/', $payment->notes, $matches)) {
                $creditPortion = (float) str_replace(',', '', $matches[1]);
            }

            // 4. Reverse meter balance - SIMPLE!
            $meter = $payment->meter;

            // Remove the payment from paid_amount
            $meter->paid_amount -= $payment->amount;

            // Add back to current_balance only what was applied to bills (not credit)
            $meter->current_balance += ($payment->amount - $creditPortion);

            $meter->save();

            // 5. Reverse customer credit if any
            if ($creditPortion > 0) {
                $customer = $payment->customer;
                $customer->credit_balance -= $creditPortion;
                $customer->save();
            }

            // 6. Mark payment as voided
            $payment->update([
                'payment_status' => 'voided',
                'voided_at' => now(),
                'voided_by' => $userId,
                'void_reason' => $reason,
                'refund_method' => $refundMethod,
                'notes' => ($payment->notes ?? '') . ' | VOIDED: ' . $reason
            ]);

            // 7. Log the reversal
            $reversal = $payment->reversal()->create([
                'original_payment_id' => $payment->id,
                'reversal_amount' => $payment->amount,
                'reversal_date' => now(),
                'reversed_by' => $userId,
                'reason' => $reason,
                'refund_method' => $refundMethod,
                'metadata' => json_encode($reversedBills)
            ]);

            return [
                'reversal' => $reversal,
                'reversed_bills' => $reversedBills,
                'new_meter_balance' => $meter->fresh()->current_balance,
                'new_meter_paid_amount' => $meter->fresh()->paid_amount,
                'customer_credit_balance' => $payment->customer->fresh()->credit_balance,
            ];
        });
    }
}
