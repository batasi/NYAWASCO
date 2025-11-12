<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MeterReading;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['latestMeterReading'])->latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $meterTypes = Customer::getAvailableMeterTypes();
        $connectionTypes = Customer::getConnectionTypes();
        
        return view('admin.customers.create', compact('meterTypes', 'connectionTypes'));
    }

public function store(Request $request)
{
    // Simple validation - remove complex rules
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
        'phone' => 'required|string|max:20',
        'id_number' => 'required|string|max:20|unique:customers,id_number',
        'physical_address' => 'required|string|max:500',
        'connection_type' => 'required|string|in:residential,commercial,industrial,public',
        'meter_number' => 'nullable|string|max:50|unique:customers,meter_number',
        'meter_type' => 'required|string|in:domestic,commercial,industrial',
        'connection_date' => 'required|date',
    ]);

    try {
        // Handle meter number
        if (empty($validated['meter_number'])) {
            $validated['meter_number'] = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (Customer::withMeterNumber($validated['meter_number'])->exists()) {
                $validated['meter_number'] = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        }

        // Set default status
        $validated['status'] = 'active';

        // Create customer
        $customer = Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully with meter ' . $customer->meter_number);

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating customer: ' . $e->getMessage());
    }
}

    public function show(Customer $customer)
    {
        $customer->load(['meterReadings', 'bills', 'latestMeterReading']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $meterTypes = Customer::getAvailableMeterTypes();
        $connectionTypes = Customer::getConnectionTypes();
        
        return view('admin.customers.edit', compact('customer', 'meterTypes', 'connectionTypes'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:customers,id_number,' . $customer->id,
            'physical_address' => 'required|string|max:500',
            'postal_address' => 'nullable|string|max:500',
            'meter_number' => 'required|string|max:50|unique:customers,meter_number,' . $customer->id,
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional',
            'connection_type' => 'required|string|in:residential,commercial,industrial,public',
            'connection_date' => 'required|date',
            'status' => 'required|string|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    // Meter Reading Methods
    public function meterReadings(Customer $customer)
    {
        $readings = $customer->meterReadings()
            ->with(['reader', 'biller'])
            ->latest()
            ->paginate(20);
            
        return view('admin.customers.meter-readings', compact('customer', 'readings'));
    }

    public function createReading(Customer $customer)
    {
        $latestReading = $customer->latestMeterReading;
        $currentPeriod = now()->format('F Y');
        
        return view('admin.customers.create-reading', compact('customer', 'latestReading', 'currentPeriod'));
    }

    public function storeReading(Request $request, Customer $customer)
    {
        $latestReading = $customer->latestMeterReading;
        
        $validated = $request->validate([
            'current_reading' => [
                'required', 
                'numeric', 
                'min:0',
                function ($attribute, $value, $fail) use ($latestReading) {
                    if ($latestReading && $value < $latestReading->current_reading) {
                        $fail('The current reading cannot be less than the previous reading (' . $latestReading->current_reading . ').');
                    }
                },
            ],
            'reading_date' => 'required|date',
            'reading_type' => 'required|in:monthly,special,correction',
            'notes' => 'nullable|string',
            'reading_image' => 'nullable|image|max:2048',
        ]);

        // Get previous reading
        $previousReading = $latestReading ? $latestReading->current_reading : ($customer->initial_meter_reading ?? 0);

        $readingData = [
            'customer_id' => $customer->id,
            'current_reading' => $validated['current_reading'],
            'previous_reading' => $previousReading,
            'consumption' => $validated['current_reading'] - $previousReading,
            'reading_date' => $validated['reading_date'],
            'reading_type' => $validated['reading_type'],
            'reading_period' => Carbon::parse($validated['reading_date'])->format('F Y'),
            'notes' => $validated['notes'],
            'read_by' => auth()->id(),
        ];

        // Handle image upload
        if ($request->hasFile('reading_image')) {
            $path = $request->file('reading_image')->store('meter-readings', 'public');
            $readingData['reading_image'] = $path;
        }

        $meterReading = MeterReading::create($readingData);

        return redirect()->route('admin.customers.meter-readings', $customer)
            ->with('success', 'Meter reading recorded successfully! Consumption: ' . $meterReading->consumption . ' m³');
    }

    public function generateBill(Request $request, Customer $customer, MeterReading $reading)
    {
        // Validate that reading belongs to customer and is not already billed
        if ($reading->customer_id !== $customer->id) {
            abort(404);
        }

        if ($reading->billed) {
            return redirect()->back()
                ->with('error', 'This meter reading has already been billed.');
        }

        // Get billing rates based on meter type
        $rates = $this->getBillingRates($customer->meter_type);
        
        $consumption = $reading->consumption;
        $waterCharge = $consumption * $rates['rate_per_unit'];
        $totalAmount = $waterCharge + $rates['fixed_charge'] + $rates['tax_amount'];

        $billData = [
            'customer_id' => $customer->id,
            'meter_reading_id' => $reading->id,
            'consumption' => $consumption,
            'rate_per_unit' => $rates['rate_per_unit'],
            'fixed_charge' => $rates['fixed_charge'],
            'tax_amount' => $rates['tax_amount'],
            'total_amount' => $totalAmount,
            'billing_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'billing_period' => $reading->reading_period,
            'billing_notes' => "Bill for {$reading->reading_period}",
        ];

        $bill = Bill::create($billData);

        // Mark reading as billed
        $reading->update(['billed' => true]);

        return redirect()->route('admin.customers.bills', $customer)
            ->with('success', "Bill generated successfully! Amount: KSh " . number_format($totalAmount, 2));
    }

    private function getBillingRates($meterType)
    {
        $rates = [
            'domestic' => [
                'rate_per_unit' => 45.00,
                'fixed_charge' => 150.00,
                'tax_amount' => 0.00,
            ],
            'commercial' => [
                'rate_per_unit' => 75.00,
                'fixed_charge' => 300.00,
                'tax_amount' => 50.00,
            ],
            'industrial' => [
                'rate_per_unit' => 100.00,
                'fixed_charge' => 500.00,
                'tax_amount' => 100.00,
            ],
            'institutional' => [
                'rate_per_unit' => 60.00,
                'fixed_charge' => 200.00,
                'tax_amount' => 25.00,
            ],
        ];

        return $rates[$meterType] ?? $rates['domestic'];
    }

    public function bills(Customer $customer)
    {
        $bills = $customer->bills()
            ->with('meterReading')
            ->latest()
            ->paginate(20);
            
        return view('admin.customers.bills', compact('customer', 'bills'));
    }

    // Check if meter number exists (AJAX)
    public function checkMeterNumber(Request $request)
    {
        $exists = Customer::withMeterNumber($request->meter_number)->exists();
        return response()->json(['exists' => $exists]);
    }
}