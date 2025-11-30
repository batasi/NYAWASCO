<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\User;
use App\Models\Meter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'meter.customer']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('payment_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('meter', function($q) use ($search) {
                      $q->where('meter_number', 'like', "%{$search}%");
                  });
        }

        // Status filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        // Sorting
        $query->orderBy('payment_date', 'desc');

        $payments = $query->paginate(10)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'meter_no' => 'required|exists:meters,meter_number',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        try {
            // Find the meter record by meter_number
            $meter = Meter::where('meter_number', $request->meter_no)->firstOrFail();

            // Create the payment linked to the meter
            $payment = Payment::create([
                'meter_id' => $meter->id,
                'user_id' => auth()->id(),
                'payment_no' => 'PAY-' . Str::upper(Str::random(8)),
                'amount' => $request->amount,
                'payment_date' => Carbon::now()->toDateString(),
                'payment_status' => 'completed',
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
            ]);

            /** -----------------------------
             *  APPLY PAYMENT TO BILLS
             * ----------------------------- */
            $remaining = $request->amount;

            // Fetch unpaid bills for this meter sorted by oldest
            $unpaidBills = Bill::where('meter_id', $meter->id)
                ->whereColumn('total_amount', '>', 'paid_amount')
                ->orderBy('billing_period_start', 'asc')
                ->get();

            foreach ($unpaidBills as $bill) {
                if ($remaining <= 0) {
                    break;
                }

                $billDue = $bill->total_amount - $bill->paid_amount;

                if ($remaining >= $billDue) {
                    // Fully pay this bill
                    $bill->paid_amount = $bill->total_amount;
                    $bill->bill_status = 'paid';
                    $bill->payment_date = Carbon::now()->toDateString();
                    $bill->save();

                    $remaining -= $billDue;
                } else {
                    // Partially pay this bill
                    $bill->paid_amount += $remaining;
                    $bill->bill_status = 'partial';
                    $bill->save();

                    $remaining = 0;
                }
            }

            return redirect()->route('payments.index')
                ->with('success', 'Payment created successfully and applied to outstanding bills.');

        } catch (\Exception $e) {
            return redirect()->route('payments.index')
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'meter.customer']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $meters = Meter::all();
        $users = User::all();
        return view('payments.edit', compact('payment', 'meters', 'users'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'meter_id' => 'required|exists:meters,id',
            'user_id' => 'required|exists:users,id',
            'payment_no' => 'required|unique:payments,payment_no,' . $payment->id,
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'meter_id' => $request->meter_id,
            'user_id' => $request->user_id,
            'payment_no' => $request->payment_no,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }



/**
 * Get meter details for AJAX request - FIXED CUSTOMER ACCESS
 */
public function getMeterDetails($meterNumber)
{
    try {
        // Trim the meter number
        $meterNumber = trim($meterNumber);

        Log::info('Searching for assigned meter:', ['meter_number' => $meterNumber]);

        // First try exact match
        $meter = Meter::with([
            'customer',
            'meterCategory',
            'bills' => function($query) {
                $query->whereColumn('total_amount', '>', 'paid_amount')
                      ->orderBy('billing_period_start', 'asc');
            }
        ])
        ->where('status', 'assigned')
        ->whereNotNull('customer_id')
        ->where('meter_number', $meterNumber) // EXACT MATCH FIRST
        ->first();

        // If exact match not found, try partial match from autocomplete
        if (!$meter) {
            $meter = Meter::with([
                'customer',
                'meterCategory',
                'bills' => function($query) {
                    $query->whereColumn('total_amount', '>', 'paid_amount')
                          ->orderBy('billing_period_start', 'asc');
                }
            ])
            ->where('status', 'assigned')
            ->whereNotNull('customer_id')
            ->where('meter_number', 'LIKE', $meterNumber . '%') // PARTIAL MATCH
            ->first();
        }

        if (!$meter) {
            Log::warning('Assigned meter not found for search:', ['meter_number' => $meterNumber]);
            return response()->json([
                'success' => false,
                'message' => 'Assigned meter not found'
            ], 404);
        }

        Log::info('Assigned meter found:', [
            'meter_id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'customer_id' => $meter->customer_id,
            'has_customer_relation' => !is_null($meter->customer),
            'customer_object' => $meter->customer ? get_class($meter->customer) : 'null'
        ]);

        // DEBUG: Check customer data directly
        if ($meter->customer) {
            Log::info('Customer data:', [
                'customer_id' => $meter->customer->id,
                'customer_name' => $meter->customer->name,
                'customer_attributes' => $meter->customer->getAttributes()
            ]);
        }

        // Get unpaid bills
        $unpaidBills = $meter->bills->map(function($bill) {
            return [
                'bill_number' => $bill->bill_number ?? 'BILL-' . $bill->id,
                'total_amount' => number_format($bill->total_amount, 2),
                'paid_amount' => number_format($bill->paid_amount, 2),
                'due' => number_format($bill->total_amount - $bill->paid_amount, 2),
                'billing_period' => $bill->billing_period_start ?
                    \Carbon\Carbon::parse($bill->billing_period_start)->format('M Y') : 'N/A'
            ];
        });

        // Calculate total due
        $totalDue = $meter->bills->sum(function($bill) {
            return $bill->total_amount - $bill->paid_amount;
        });

        // Get customer info - FIXED: Check if customer exists and has name
        $customerInfo = [
            'name' => 'Customer Not Found',
            'email' => 'N/A',
            'phone' => 'N/A'
        ];

        if ($meter->customer) {
            // Try different possible name fields
            $name = $meter->customer->name
                  ?? $meter->customer->full_name
                  ?? $meter->customer->customer_name
                  ?? 'Unknown Customer';

            $customerInfo = [
                'name' => $name,
                'email' => $meter->customer->email ?? 'N/A',
                'phone' => $meter->customer->phone ?? $meter->customer->phone_number ?? 'N/A'
            ];
        }

        $response = [
            'success' => true,
            'meter' => [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'model' => $meter->meter_model ?? 'Standard Model',
                'type' => $meter->meter_type ?? 'Domestic',
                'location' => $meter->installation_address ?? ($meter->customer->estate ?? 'Unknown Location'),
                'status' => $meter->status,
                'category' => $meter->meterCategory->name ?? 'General'
            ],
            'customer' => $customerInfo,
            'unpaid_bills' => $unpaidBills,
            'total_due' => number_format($totalDue, 2),
            'is_assigned' => true,
            'debug' => [
                'customer_id' => $meter->customer_id,
                'customer_loaded' => !is_null($meter->customer),
                'customer_name_field' => $meter->customer ? ($meter->customer->name ?? 'no name field') : 'no customer'
            ]
        ];

        Log::info('Meter details response:', $response);

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('Error fetching assigned meter details: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error fetching meter details'
        ], 500);
    }
}

/**
 * Search assigned meters for autocomplete
 */
public function searchMeters(Request $request)
{
    $search = $request->get('search', '');

    if (strlen($search) < 2) {
        return response()->json([]);
    }

    // Search assigned meters with partial match
    $meters = Meter::with('customer')
        ->whereNotNull('customer_id')
        ->where('meter_number', 'LIKE', $search . '%') // Partial match from start
        ->limit(10)
        ->get()
        ->map(function($meter) {
            return [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'customer_name' => $meter->customer->name ?? 'Unknown Customer',
                'status' => $meter->status,
                'display_text' => "{$meter->meter_number} - {$meter->customer->name}"
            ];
        });

    return response()->json($meters);
}

    /**
 * Test endpoint to list all meters
 */
public function listAllMeters()
{
    $meters = Meter::all(['id', 'meter_number', 'customer_id', 'status']);
    return response()->json($meters);
}

}
