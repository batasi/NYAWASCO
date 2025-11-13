<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillController extends Controller
{
    /**
     * Display a listing of bills.
     */
    public function index()
    {
        $bills = Bill::with(['user'])->latest()->paginate(20);
        return view('bills.index', compact('bills'));
    }

    /**
     * Store a newly created bill in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'meter_id' => 'nullable|numeric',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date',
            'consumption' => 'nullable|numeric',
            'base_charge' => 'nullable|numeric',
            'consumption_charge' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'due_date' => 'nullable|date',
            'bill_status' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate unique bill number
        $latestId = Bill::max('id') + 1;
        $billNumber = 'BILL-' . str_pad($latestId, 5, '0', STR_PAD_LEFT);

        $bill = Bill::create([
            ...$validated,
            'bill_number' => $billNumber,
            'created_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Bill created successfully',
                'bill' => $bill,
            ]);
        }

        return redirect()->back()->with('success', 'Bill created successfully.');
    }

    /**
     * Display a single bill.
     */
    public function show(Bill $bill)
    {
        return view('bills.show', compact('bill'));
    }

    /**
     * Update a bill.
     */
    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date',
            'consumption' => 'nullable|numeric',
            'base_charge' => 'nullable|numeric',
            'consumption_charge' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'due_date' => 'nullable|date',
            'bill_status' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $bill->update($validated);

        return response()->json([
            'message' => 'Bill updated successfully',
            'bill' => $bill,
        ]);
    }

    /**
     * Remove the specified bill.
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();

        return response()->json(['message' => 'Bill deleted successfully.']);
    }
   public function info(Bill $bill)
{
    $bill->load('user', 'payments'); // make sure relationships are loaded

    $paidAmount = $bill->payments->sum('amount');
    $dueAmount = $bill->total_amount - $paidAmount;

    return response()->json([
        'user_id' => $bill->user_id,
        'user_name' => $bill->user?->name ?? 'Unknown',
        'due_amount' => $dueAmount,
    ]);
}


}
