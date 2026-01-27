<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentAllocationController extends Controller
{
    public function allocatePayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'allocations' => 'required|array',
            'allocations.*.bill_id' => 'required|exists:bills,id',
            'allocations.*.principal_amount' => 'required|numeric|min:0',
            'allocations.*.late_fee_amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $payment = Payment::findOrFail($request->payment_id);
            $totalAllocated = 0;

            foreach ($request->allocations as $allocation) {
                $bill = Bill::findOrFail($allocation['bill_id']);

                // Create payment allocation record
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'bill_id' => $bill->id,
                    'amount' => $allocation['principal_amount'] + $allocation['late_fee_amount'],
                    'allocated_to_principal' => $allocation['principal_amount'],
                    'allocated_to_late_fee' => $allocation['late_fee_amount'],
                    'allocation_date' => now(),
                ]);

                // Update bill balance
                $bill->increment('paid_amount', $allocation['principal_amount'] + $allocation['late_fee_amount']);
                $bill->decrement('balance', $allocation['principal_amount'] + $allocation['late_fee_amount']);

                // Check if bill is fully paid
                if ($bill->balance <= 0) {
                    $bill->update([
                        'bill_status' => 'paid',
                        'payment_date' => now()
                    ]);
                }

                $totalAllocated += $allocation['principal_amount'] + $allocation['late_fee_amount'];
            }

            // Update payment status if fully allocated
            if ($totalAllocated >= $payment->amount) {
                $payment->update([
                    'payment_status' => 'allocated',
                    'allocated_at' => now()
                ]);
            }

            // Update customer balance
            $customer = $payment->customer;
            $customer->decrement('credit_balance', $totalAllocated);
            $customer->update([
                'last_payment_date' => now(),
                'last_payment_amount' => $payment->amount,
                'total_payments' => DB::raw('total_payments + ' . $payment->amount)
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Payment allocated successfully']);
    }

    public function unallocatedPayments()
    {
        $payments = Payment::with(['customer', 'meter'])
            ->where('payment_status', 'pending')
            ->orWhere('payment_status', 'completed')
            ->whereDoesntHave('allocations')
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('admin.payments.unallocated', compact('payments'));
    }

    public function showAllocationForm(Payment $payment)
    {
        $customer = $payment->customer;
        $unpaidBills = $customer->bills()
            ->where('bill_status', '!=', 'paid')
            ->orderBy('due_date')
            ->get();

        $allocationOptions = [
            'oldest_first' => 'Pay Oldest Bills First',
            'largest_first' => 'Pay Largest Bills First',
            'manual' => 'Manual Allocation'
        ];

        return view('admin.payments.allocate', compact('payment', 'unpaidBills', 'allocationOptions'));
    }

    public function autoAllocate(Request $request, Payment $payment)
    {
        $method = $request->get('method', 'oldest_first');
        $customer = $payment->customer;
        $remainingAmount = $payment->amount;

        $query = $customer->bills()
            ->where('bill_status', '!=', 'paid');

        if ($method === 'oldest_first') {
            $query->orderBy('due_date');
        } else {
            $query->orderByDesc('balance');
        }

        $bills = $query->get();
        $allocations = [];

        foreach ($bills as $bill) {
            if ($remainingAmount <= 0) break;

            $allocateAmount = min($bill->balance, $remainingAmount);

            // Split between principal and late fee proportionally
            $principalRatio = $bill->balance > 0 ? ($bill->total_amount - $bill->late_fee) / $bill->balance : 0;
            $lateFeeRatio = $bill->balance > 0 ? $bill->late_fee / $bill->balance : 0;

            $allocations[] = [
                'bill_id' => $bill->id,
                'principal_amount' => round($allocateAmount * $principalRatio, 2),
                'late_fee_amount' => round($allocateAmount * $lateFeeRatio, 2)
            ];

            $remainingAmount -= $allocateAmount;
        }

        return response()->json([
            'success' => true,
            'allocations' => $allocations,
            'remaining_amount' => $remainingAmount
        ]);
    }

    public function paymentMethodsReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = Payment::selectRaw('
            payment_method,
            COUNT(*) as payment_count,
            SUM(amount) as total_amount,
            AVG(amount) as average_amount
        ')
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->where('payment_status', 'allocated')
        ->groupBy('payment_method')
        ->orderByDesc('total_amount')
        ->get();

        // Daily trend
        $dailyTrend = Payment::selectRaw('
            DATE(payment_date) as date,
            payment_method,
            SUM(amount) as daily_total
        ')
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->where('payment_status', 'allocated')
        ->groupBy('date', 'payment_method')
        ->orderBy('date')
        ->get();

        return view('admin.payments.methods-report', compact('report', 'dailyTrend', 'startDate', 'endDate'));
    }
}
