<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Meter;
use App\Models\Pricing_Tier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use PDF;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $sort = $request->get('sort', 'newest');
        $user = Auth::user();

        $billsQuery = Bill::with([
            'customer',
            'meter.meterCategory',
            'payments',
            'meterReading'
        ])
        ->when($user->hasRole('biller'), function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })
        ->when($status && $status !== 'all', function ($query) use ($status) {
            if ($status === 'overdue') {
                return $query->where('due_date', '<', now())
                            ->where('bill_status', 'unpaid');
            }
            return $query->where('bill_status', $status);
        })
        ->when($sort, function ($query) use ($sort) {
            switch ($sort) {
                case 'oldest':
                    return $query->oldest();
                case 'amount_high':
                    return $query->orderBy('total_amount', 'desc');
                case 'amount_low':
                    return $query->orderBy('total_amount', 'asc');
                default:
                    return $query->latest();
            }
        });

        // Paginated results
        $bills = $billsQuery->paginate(10);

        // COUNTS FOR CARDS MUST RESPECT BILLER FILTER
        $summaryQuery = Bill::query()
            ->when($user->hasRole('biller'), function ($query) use ($user) {
                $query->where('created_by', $user->id);
            });

        $totalBillsCount = $summaryQuery->count();
        $unpaidBillsCount = $summaryQuery->clone()->where('bill_status', 'unpaid')->count();
        $paidBillsCount = $summaryQuery->clone()->where('bill_status', 'paid')->count();
        $partialBillsCount = $summaryQuery->clone()->where('bill_status', 'partial')->count();
        $overdueBillsCount = $summaryQuery->clone()
            ->where('due_date', '<', now())
            ->where('bill_status', 'unpaid')
            ->count();

        // Dashboard statistics from visible bills
        $totalRevenue = Bill::query()->sum('total_amount');
        $outstandingBalance = Bill::query()->where('bill_status', '!=', 'paid')->sum('total_amount');
        $totalBills = Bill::query()->count();
        $paidBills = $bills->where('bill_status', 'paid')->count();
        $collectionRate = $totalBills > 0 ? ($paidBills / $totalBills) * 100 : 0;

        return view('bills.index', compact(
            'bills',
            'totalRevenue',
            'outstandingBalance',
            'totalBills',
            'collectionRate',
            'totalBillsCount',
            'unpaidBillsCount',
            'paidBillsCount',
            'partialBillsCount',
            'overdueBillsCount'
        ));
    }



    /**
     * Store a newly created bill in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id',
            'meter_reading_id' => 'nullable|exists:meter_readings,id',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date',
            'consumption' => 'required|numeric|min:0',
            'base_charge' => 'required|numeric|min:0',
            'consumption_charge' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'bill_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string|max:500',
        ]);

        // Auto-generate unique bill number
        $latestBill = Bill::latest()->first();
        $latestId = $latestBill ? $latestBill->id + 1 : 1;
        $billNumber = 'BILL-' . str_pad($latestId, 6, '0', STR_PAD_LEFT);

        $bill = Bill::create([
            ...$validated,
            'bill_number' => $billNumber,
            'created_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Bill created successfully',
                'bill' => $bill->load(['customer', 'meter.meterCategory']),
            ]);
        }

        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    /**
     * Display a single bill.
     */
    public function show(Bill $bill)
    {
        $bill->load(['customer', 'meter.meterCategory', 'payments', 'creator', 'meterReading']);

        $paidAmount = $bill->payments->sum('amount');
        $dueAmount = $bill->total_amount - $paidAmount;
        $paymentPercentage = $bill->total_amount > 0 ? ($paidAmount / $bill->total_amount) * 100 : 0;

        return view('bills.show', compact('bill', 'paidAmount', 'dueAmount', 'paymentPercentage'));
    }

    /**
     * Update a bill.
     */
    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date',
            'consumption' => 'required|numeric|min:0',
            'base_charge' => 'required|numeric|min:0',
            'consumption_charge' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'bill_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string|max:500',
        ]);

        $bill->update($validated);

        return response()->json([
            'message' => 'Bill updated successfully',
            'bill' => $bill->load(['customer', 'meter.meterCategory']),
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

    // Simple API for AJAX features
   public function search(Request $request)
    {
        $search = trim($request->get('search'));
        $user = auth()->user();

        $bills = Bill::with(['customer', 'meter.meterCategory', 'payments'])
            ->when($user->hasRole('biller'), function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->where(function($query) use ($search) {
                $query->where('bill_number', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(total_amount AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CAST(balance AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CAST(consumption AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('customer_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('meter', function($q) use ($search) {
                        $q->where('meter_number', 'like', "%{$search}%");
                    });
            })
            ->limit(20)
            ->get();

        // Transform for JS table
        $bills = $bills->map(function($bill) {
            return [
                'id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'bill_status' => $bill->bill_status,
                'total_amount' => $bill->total_amount,
                'balance' => $bill->balance,
                'is_overdue' => $bill->is_overdue,
                'billing_period_start_formatted' => optional($bill->billing_period_start)->format('M d'),
                'billing_period_end_formatted' => optional($bill->billing_period_end)->format('M d, Y'),
                'consumption' => $bill->consumption,
                'payments' => $bill->payments,
                'customer' => $bill->customer,
                'meter' => $bill->meter,
            ];
        });

        return response()->json($bills);
    }


    public function infoByCustomer($customerId)
    {
        // Get the customer
        $customer = Customer::findOrFail($customerId);

        // Fetch all unpaid bills
        $unpaidBills = Bill::where('customer_id', $customerId)
            ->get()
            ->map(function ($bill) {
                $bill->due = $bill->total_amount - $bill->payments()->sum('amount');
                return $bill;
            })
            ->filter(function ($bill) {
                return $bill->due > 0;
            });

        // Total due across all bills
        $totalDue = $unpaidBills->sum('due');

        return response()->json([
            'customer_id'   => $customer->id,
            'customer_name' => $customer->first_name,
            'total_due'     => $totalDue,
            'unpaid_bills'  => $unpaidBills->values(), // optional
        ]);
    }
    public function infoByMeter($meter)
    {
        $meterRecord = Meter::where('meter_number', $meter)->first();

        if (!$meterRecord) {
            return response()->json(['error' => 'Meter not found'], 404);
        }

        $customer = $meterRecord->customer;

        $unpaidBills = Bill::where('meter_id', $meterRecord->id)
            ->whereColumn('total_amount', '>', 'paid_amount')
            ->get();

        $totalDue = $unpaidBills->sum(fn($b) => $b->balance);

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->first_name . ' ' . $customer->last_name,
            ],
            'meter' => [
                'model' => $meterRecord->meter_model,
                'type' => $meterRecord->meter_type,
            ],
            'unpaid_bills' => $unpaidBills->map(function ($b) {
                return [
                    'bill_number' => $b->bill_number,
                    'total_amount' => $b->total_amount,
                    'due' => $b->balance,
                ];
            }),
            'total_due' => $totalDue,
        ]);
    }

 public function printReceipt(Bill $bill)
{
    // Load necessary data
    $bill->load(['customer', 'meter', 'payments', 'meter.meterCategory', 'meterReading']);

    // Get total paid amount
    $totalPaid = $bill->payments->sum('amount');
    $balance = $bill->total_amount - $totalPaid;

    // Get customer's previous unpaid balance (arrears)
    $arrears = Bill::where('customer_id', $bill->customer_id)
        ->where('id', '<', $bill->id)
        ->where('bill_status', '!=', 'paid')
        ->sum('balance');

    // Format customer name
    $customerName = $bill->customer->first_name . ' ' . $bill->customer->last_name;
    if (strlen($customerName) > 20) {
        $customerName = substr($customerName, 0, 17) . '...';
    }

    // Format billing period
    $billingPeriod = $bill->meterReading->reading_period;

    // Get meter rent from meter category (default to 0 if not set)
    $meterRent = $bill->meter->meterCategory->meter_rent ?? 0;

    // Calculate consumption charge from bill data
    $tierRate = Bill::calculateTierRate($bill->meter->meter_category_id, $bill->consumption);

    $consumptionCharge = $bill->consumption_charge ?? ($bill->consumption * $tierRate);
    // Calculate subtotal (sum of all charges BEFORE tax)
    $subtotalBeforeTax = $bill->base_charge
                       + $meterRent
                       + $consumptionCharge
                       + $arrears
                       + ($bill->late_fee ?? 0);

    // Calculate tax amount (16% of base + consumption) - same as your controller
    $taxAmount = $bill->tax_amount;

    // Verify the total matches
    $calculatedTotal = $subtotalBeforeTax + $taxAmount;

    // If there's a mismatch, use the bill's stored total
    if (abs($calculatedTotal - $bill->total_amount) > 0.01) {
        // Adjust tax amount to make totals match
        $taxAmount = $bill->tax_amount;
    }

     // Get printed by user info
    $printedByName = auth()->user()->name ?? 'System';
    if (strlen($printedByName) > 15) {
        $printedByName = substr($printedByName, 0, 12) . '...';
    }

    // Prepare receipt data with correct calculations
    $receiptData = [
        'bill_number' => substr($bill->bill_number, 0, 15),
        'date' => now()->format('d/m/Y H:i'),
        'receipt_number' => 'RCP-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT),
        'customer_name' => $customerName,
        'customer_number' => $bill->customer->customer_number,
        'customer_phone' => $bill->customer->phone ?? 'N/A',
        'meter_number' => $bill->meter->meter_number ?? 'N/A',
        'billing_period' => $billingPeriod,
        'consumption' => number_format($bill->consumption, 1) . ' m³',
        'rate' => number_format($tierRate, 2) . '/m³',


        // Detailed charges
        'base_charge' => 'KSh ' . number_format($bill->base_charge, 2),
        'meter_rent' => 'KSh ' . number_format($meterRent, 2),
        'consumption_charge' => 'KSh ' . number_format($consumptionCharge, 2),
        'arrears' => $arrears > 0 ? 'KSh ' . number_format($arrears, 2) : null,
        'late_fee' => $bill->late_fee > 0 ? 'KSh ' . number_format($bill->late_fee, 2) : null,

        // Subtotal before tax
        'subtotal_before_tax' => 'KSh ' . number_format($subtotalBeforeTax, 2),
        'printed_by' => $printedByName,
        // Tax
        'vat' => 'KSh ' . number_format($taxAmount, 2),

        // Totals - using the actual bill total
        'total_amount' => 'KSh ' . number_format($bill->meter->current_balance, 2),
        'amount_paid' => 'KSh ' . number_format($totalPaid, 2),
        'balance' => 'KSh ' . number_format($balance, 2),
        'calculated_consumption_charge' => 'KSh ' . number_format($consumptionCharge, 2),

        'payment_status' => strtoupper($bill->bill_status),
        'due_date' => $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A',
        'footer_message' => 'Thank you for your payment!',
        'printed_date' => now()->format('d/m/Y H:i'),
    ];

    // Return the 58mm optimized receipt view
    return view('bills.receipts.thermal-58mm', compact('receiptData'));
}


}
