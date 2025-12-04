<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Meter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // Get bills with proper relationships and filters
        $status = $request->get('status');
        $sort = $request->get('sort', 'newest');

        $bills = Bill::with([
            'customer',
            'meter.meterCategory',
            'payments',
            'meterReading'
        ])
        ->when($status && $status !== 'all', function($query) use ($status) {
            if ($status === 'overdue') {
                return $query->where('due_date', '<', now())
                            ->where('bill_status', 'unpaid');
            }
            return $query->where('bill_status', $status);
        })
        ->when($sort, function($query) use ($sort) {
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
        })
        ->paginate(10);

        // Get TOTAL counts from the entire dataset (not filtered)
        $totalBillsCount = Bill::count();
        $unpaidBillsCount = Bill::where('bill_status', 'unpaid')->count();
        $paidBillsCount = Bill::where('bill_status', 'paid')->count();
        $partialBillsCount = Bill::where('bill_status', 'partial')->count();
        $overdueBillsCount = Bill::where('due_date', '<', now())
                                ->where('bill_status', 'unpaid')
                                ->count();

        // Get stats for the cards (from filtered data for display)
        $totalRevenue = $bills->sum('total_amount');
        $outstandingBalance = $bills->where('bill_status', '!=', 'paid')->sum('total_amount');
        $totalBills = $bills->total();
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
        $search = $request->get('search');

        $bills = Bill::with(['customer', 'meter.meterCategory', 'payments'])
            ->where(function($query) use ($search) {
                $query->where('bill_number', 'like', "%{$search}%")
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
            ->limit(10)
            ->get();

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
// In your BillController
// public function generateReceipt(Bill $bill)
// {
//     // Check if user has permission to generate receipts
//     $this->authorize('view', $bill);

//     // Get receipt data from model
//     $receiptData = $bill->generateReceipt();

//     // For PDQ devices, you might want different formats
//     $format = request()->get('format', 'print'); // print, pdf, or preview

//     if ($format === 'print') {
//         // Return a view with auto-print JavaScript
//         return view('bills.receipts.print', compact('receiptData', 'bill'));
//     } elseif ($format === 'pdf') {
//         // Temporarily disable PDF or handle differently
//         return redirect()->route('bills.receipt', [
//             'bill' => $bill->id,
//             'format' => 'print'
//         ]);
//     } elseif ($format === 'thermal') {
//         // Return raw thermal format for direct printing
//         return response()->view('bills.receipts.thermal', compact('receiptData'))
//             ->header('Content-Type', 'text/plain');
//     } elseif ($format === 'preview') {
//         // HTML preview
//         return view('bills.receipts.preview', compact('receiptData', 'bill'));
//     }

//     // Default to print format
//     return view('bills.receipts.print', compact('receiptData', 'bill'));
// }

// private function generatePDFReceipt($receiptData)
// {
//     // Using DomPDF or similar PDF library
//     $pdf = \PDF::loadView('bills.receipts.pdf', compact('receiptData'));

//     // Set paper size for receipt (80mm thermal printer width)
//     $pdf->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm width in points

//     return $pdf->download('receipt-' . $receiptData['bill_number'] . '.pdf');
// }

public function printReceipt(Bill $bill)
{
    // Load necessary data
    $bill->load(['customer', 'meter', 'payments', 'meter.meterCategory']);
    
    // Get total paid amount
    $totalPaid = $bill->payments->sum('amount');
    $balance = $bill->total_amount - $totalPaid;
    
    // Prepare receipt data
    $receiptData = [
        'bill_number' => $bill->bill_number,
        'date' => now()->format('Y-m-d H:i:s'),
        'receipt_number' => 'RCP-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT),
        'customer_name' => $bill->customer->first_name . ' ' . $bill->customer->last_name,
        'customer_number' => $bill->customer->customer_number,
        'customer_phone' => $bill->customer->phone ?? 'N/A',
        'meter_number' => $bill->meter->meter_number ?? 'N/A',
        'billing_period' => $bill->billing_period_start 
            ? $bill->billing_period_start->format('M d') . '-' . $bill->billing_period_end->format('M d')
            : 'N/A',
        'consumption' => number_format($bill->consumption, 2) . ' m³',
        'rate' => 'KSh ' . number_format($bill->meter->meterCategory->default_rate ?? 0, 4),
        'subtotal' => 'KSh ' . number_format($bill->total_amount, 2),
        'vat' => 'KSh 0.00',
        'total_amount' => 'KSh ' . number_format($bill->total_amount, 2),
        'amount_paid' => 'KSh ' . number_format($totalPaid, 2),
        'balance' => 'KSh ' . number_format($balance, 2),
        'payment_status' => $bill->bill_status,
        'due_date' => $bill->due_date ? $bill->due_date->format('Y-m-d') : 'N/A',
        'footer_message' => 'Thank you!',
        'printed_date' => now()->format('Y-m-d H:i:s'),
    ];
    
    // Return the 58mm optimized receipt view
    return view('bills.receipts.thermal-58mm', compact('receiptData'));
}
}
