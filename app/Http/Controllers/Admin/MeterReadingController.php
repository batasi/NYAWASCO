<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\PricingTier;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\Bill;
use App\Services\OCRService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MeterReadingController extends Controller
{
    public function index()
    {
        $readings = MeterReading::with('customer', 'reader', 'meter')
            ->latest()
            ->paginate(20);

        return view('admin.meter-readings.index', compact('readings'));
    }


    public function create(Request $request)
    {
        $customerId = $request->get('customer');
        $meterId = $request->get('meter');
        $customer = null;
        $lastReading = null;
        $meter = null;
        $meters = [];

        if ($customerId) {
            $customer = Customer::with('meters.meterCategory')->findOrFail($customerId);
            $meters = $customer->meters;

            // If specific meter is selected, get that meter
            if ($meterId) {
                $meter = $meters->firstWhere('id', $meterId);
            } else {
                // Default to first meter if none selected
                $meter = $meters->first();
            }

            // Get last reading for the specific meter
            if ($meter) {
                $lastReading = MeterReading::where('customer_id', $customerId)
                    ->where('meter_id', $meter->id)
                    ->latest()
                    ->first();
            }
        }

        return view('admin.meter-readings.create', compact('customer', 'lastReading', 'meter', 'meters'));
    }

    public function store(Request $request)
    {
        $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');

        // Check for duplicate reading only if it's a normal reading
        if ($request->reading_status !== 'exception' && $request->reading_status !== 'estimated') {
            $existingReading = MeterReading::where('customer_id', $request->customer_id)
                ->where('meter_id', $request->meter_id)
                ->where('reading_period', $readingPeriod)
                ->where('reading_status', 'recorded')
                ->first();

            if ($existingReading && !$request->has('force_duplicate')) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "A normal meter reading for meter {$existingReading->meter->meter_number} in {$readingPeriod} already exists. Are you sure you want to create another reading for the same period?",
                        'requires_confirmation' => true,
                        'existing_reading' => [
                            'id' => $existingReading->id,
                            'reading_date' => $existingReading->reading_date,
                            'current_reading' => $existingReading->current_reading,
                            'reading_period' => $existingReading->reading_period,
                            'meter_number' => $existingReading->meter->meter_number
                        ]
                    ], 422);
                }

                throw new \Exception("Meter reading for {$readingPeriod} has already been recorded for this meter.");
            }
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id',
            'current_reading' => 'nullable|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_status' => 'required|in:recorded,exception,estimated',
            'exception_type' => 'required_if:reading_status,exception|nullable|in:inaccessible,faulty,stuck,damaged,vandalized,other',
            'exception_reason' => 'required_if:reading_status,exception|nullable|string|max:500',
            'estimated_consumption' => 'required_if:reading_status,estimated|nullable|numeric|min:0',
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Define meter variable outside the transaction
            $meter = null;

            DB::transaction(function () use ($request, $readingPeriod, &$meter) {
                // Get customer and meter
                $customer = Customer::findOrFail($request->customer_id);
                $meter = Meter::findOrFail($request->meter_id);

                // Verify the meter belongs to the customer
                if ($meter->customer_id != $customer->id) {
                    throw new \Exception('Selected meter does not belong to this customer.');
                }

                // Get previous reading for this specific meter
                $previousReading = MeterReading::where('customer_id', $request->customer_id)
                    ->where('meter_id', $request->meter_id)
                    ->where('reading_status', 'recorded')
                    ->latest()
                    ->first();

                $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

                // Validate current reading for normal readings
                if ($request->reading_status === 'recorded') {
                    if (!$request->current_reading) {
                        throw new \Exception('Current reading is required for normal readings.');
                    }

                    if ($request->current_reading < $previousReadingValue) {
                        throw new \Exception('Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ' m³).');
                    }
                }

                // Calculate consumption based on reading status
                $consumption = 0;
                $estimatedConsumption = null;

                if ($request->reading_status === 'recorded') {
                    $consumption = $request->current_reading - $previousReadingValue;
                } elseif ($request->reading_status === 'estimated') {
                    $estimatedConsumption = $request->estimated_consumption;
                    $consumption = $estimatedConsumption;
                }
                // For exceptions, consumption remains 0

                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('reading_image')) {
                    $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
                }

                // Handle exception evidence
                $exceptionEvidence = null;
                if ($request->hasFile('exception_evidence')) {
                    $exceptionEvidence = $request->file('exception_evidence')->store('exception-evidence', 'public');
                }

                // Create reading
                $reading = MeterReading::create([
                    'customer_id' => $request->customer_id,
                    'meter_id' => $request->meter_id,
                    'current_reading' => $request->current_reading,
                    'previous_reading' => $previousReadingValue,
                    'consumption' => $consumption,
                    'reading_date' => $request->reading_date,
                    'reading_type' => 'monthly',
                    'reading_status' => $request->reading_status,
                    'exception_type' => $request->exception_type,
                    'exception_reason' => $request->exception_reason,
                    'estimated' => $request->reading_status === 'estimated',
                    'estimated_consumption' => $estimatedConsumption,
                    'exception_evidence' => $exceptionEvidence,
                    'reading_period' => $readingPeriod,
                    'billed' => false,
                    'reading_image' => $imagePath,
                    'notes' => $request->notes,
                    'read_by' => auth()->id(),
                ]);

                // UPDATE METER'S CURRENT READING only for normal readings
                if ($request->reading_status === 'recorded') {
                    $meter->update([
                        'current_reading' => $request->current_reading
                    ]);
                }

                // AUTO-GENERATE BILL FOR NORMAL AND ESTIMATED READINGS
                if ($request->reading_status !== 'exception' && $consumption > 0) {
                    $bill = $this->generateBill($reading, $customer, $meter, $consumption, $request->reading_status);

                    // UPDATE METER AND CUSTOMER BALANCES
                    $this->updateBalances($meter, $customer, $bill->total_amount);

                    Log::info("Meter reading recorded and bill generated", [
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'reading_id' => $reading->id,
                        'bill_id' => $bill->id,
                        'consumption' => $consumption,
                        'amount' => $bill->total_amount
                    ]);
                } else {
                    Log::info("Meter reading exception recorded", [
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'reading_id' => $reading->id,
                        'reading_status' => $request->reading_status,
                        'exception_type' => $request->exception_type
                    ]);
                }
            });

            // Now $meter is available for the success message
            return redirect()->route('admin.customers.show', $request->customer_id)
                ->with('success', 'Meter reading ' . $request->reading_status . ' recorded successfully for meter ' . $meter->meter_number . '!');

        } catch (\Exception $e) {
            Log::error('Meter reading creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Update the generateBill method to handle estimated readings
    private function generateBill(MeterReading $reading, Customer $customer, Meter $meter, $consumption, $readingStatus = 'recorded')
    {
        // Get meter category pricing
        $category = $meter->meterCategory;
        $baseCharge = $category->base_charge ?? 100;
        $meterRent = $category->meter_rent ?? 0; // default 0 if not set

        // Tier pricing calculation
        $tieredCharge = $this->calculateTieredCharge($category->id, $consumption);

        if ($tieredCharge !== null) {
            $consumptionCharge = $tieredCharge;
        } else {
            // No tiers → use flat rate
            $consumptionRate = $category->default_rate ?? 50;
            $consumptionCharge = $consumption * $consumptionRate;
        }


        $taxRate = 0.16;

        // Compute charges before tax

        $taxAmount = ($baseCharge + $consumptionCharge) * $taxRate;
        $totalAmount = $baseCharge + $consumptionCharge + $meterRent;

        // Create bill without bill_number first
        $bill = Bill::create([
            'customer_id' => $customer->id,
            'meter_id' => $meter->id,
            'meter_reading_id' => $reading->id,
            'billing_period_start' => Carbon::parse($reading->reading_date)->startOfMonth(),
            'billing_period_end' => Carbon::parse($reading->reading_date)->endOfMonth(),
            'consumption' => $consumption,
            'base_charge' => $baseCharge,
            'consumption_charge' => $consumptionCharge,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'due_date' => Carbon::parse($reading->reading_date)->addDays(30),
            'bill_status' => 'unpaid',
            'notes' => 'Auto-generated from ' . ($readingStatus === 'estimated' ? 'estimated' : '') . ' meter reading #' . $reading->id . ' for meter ' . $meter->meter_number,
            'created_by' => auth()->id(),
        ]);

        // Generate safe bill number using the bill ID
        $bill->update([
            'bill_number' => 'B-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT)
        ]);

        // Mark reading as billed
        $reading->update([
            'billed' => true,
            'billed_by' => auth()->id(),
            'billed_at' => now(),
        ]);

        return $bill;
    }

    private function calculateTieredCharge($meterCategoryId, $consumption)
    {
        // Fetch tiers sorted properly
        $tiers = PricingTier::where('meter_category_id', $meterCategoryId)
            ->orderBy('min_consumption')
            ->get();

        if ($tiers->isEmpty()) {
            return null; // category has no tiers
        }

        $remaining = $consumption;
        $total = 0;

        foreach ($tiers as $tier) {
            $min = $tier->min_consumption;
            $max = $tier->max_consumption;

            if ($remaining <= 0) break;

            if ($max === null) {
                // Last open tier
                $total += $remaining * $tier->rate_per_unit;
                break;
            }

            // Units consumed inside this tier range
            $allowed = min($remaining, $max - $min + 1); // +1 to close ranges logically
            $total += $allowed * $tier->rate_per_unit;

            $remaining -= $allowed;
        }

        return $total;
    }

    /**
     * Update meter and customer balances after bill generation
     */
    private function updateBalances(Meter $meter, Customer $customer, $billAmount)
    {
        // Update meter balance
        $newMeterBalance = $meter->current_balance + $billAmount;
        $meter->update([
            'current_balance' => $newMeterBalance
        ]);



    }

    /**
     * Get meter readings for a specific customer and meter
     */
    public function getMeterReadings(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id'
        ]);

        $readings = MeterReading::where('customer_id', $request->customer_id)
            ->where('meter_id', $request->meter_id)
            ->with('reader')
            ->latest()
            ->get()
            ->map(function($reading) {
                return [
                    'id' => $reading->id,
                    'current_reading' => $reading->current_reading,
                    'previous_reading' => $reading->previous_reading,
                    'consumption' => $reading->consumption,
                    'reading_date' => $reading->reading_date->format('M d, Y'),
                    'reading_period' => $reading->reading_period,
                    'billed' => $reading->billed,
                    'reader' => $reading->reader->name ?? 'System',
                    'meter_number' => $reading->meter->meter_number ?? 'N/A'
                ];
            });

        return response()->json($readings);
    }

    /**
     * Get last reading for a specific meter
     */
    public function getLastReading(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id'
        ]);

        $lastReading = MeterReading::where('customer_id', $request->customer_id)
            ->where('meter_id', $request->meter_id)
            ->latest()
            ->first();

        $meter = Meter::find($request->meter_id);

        return response()->json([
            'last_reading' => $lastReading ? $lastReading->current_reading : ($meter->initial_reading ?? 0),
            'previous_reading_date' => $lastReading ? $lastReading->reading_date->format('M d, Y') : 'No previous reading',
            'meter_info' => [
                'meter_number' => $meter->meter_number,
                'current_reading' => $meter->current_reading,
                'initial_reading' => $meter->initial_reading,
                'category_name' => $meter->meterCategory->name ?? 'No Category'
            ]
        ]);
    }

        /**
     * Calculate estimated consumption based on history
     */
    public function estimateConsumption(Customer $customer, Meter $meter)
    {
        try {
            // Verify the meter belongs to the customer
            if ($meter->customer_id != $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meter does not belong to this customer'
                ], 400);
            }

            // Get last 3 normal readings
            $previousReadings = MeterReading::where('customer_id', $customer->id)
                ->where('meter_id', $meter->id)
                ->where('reading_status', MeterReading::STATUS_RECORDED)
                ->where('billed', true)
                ->orderBy('reading_date', 'desc')
                ->limit(3)
                ->get();

            if ($previousReadings->isEmpty()) {
                // No history, use default based on category
                $defaultConsumption = $meter->meterCategory->default_rate * 10;
                $estimatedConsumption = max(5, min($defaultConsumption, 50)); // Between 5-50 m³

                return response()->json([
                    'success' => true,
                    'estimated_consumption' => $estimatedConsumption,
                    'message' => 'Estimated based on category default (no history available)'
                ]);
            }

            // Calculate average consumption from history
            $averageConsumption = $previousReadings->avg('consumption');
            $estimatedConsumption = round($averageConsumption, 2);

            return response()->json([
                'success' => true,
                'estimated_consumption' => $estimatedConsumption,
                'history_count' => $previousReadings->count(),
                'message' => 'Estimated based on ' . $previousReadings->count() . ' previous readings'
            ]);

        } catch (\Exception $e) {
            Log::error('Estimation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error calculating estimation'
            ], 500);
        }
    }

        /**
     * Show meter reading exceptions report
     */
    public function exceptions(Request $request)
    {
        $query = MeterReading::with(['customer', 'meter', 'reader'])
            ->where('reading_status', MeterReading::STATUS_EXCEPTION)
            ->latest();

        // Apply filters
        if ($request->filled('exception_type')) {
            $query->where('exception_type', $request->exception_type);
        }

        if ($request->filled('start_date')) {
            $query->where('reading_date', '>=', $request->start_date);
        }

        $exceptions = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => MeterReading::where('reading_status', MeterReading::STATUS_EXCEPTION)->count(),
            'inaccessible' => MeterReading::where('exception_type', 'inaccessible')->count(),
            'faulty' => MeterReading::where('exception_type', 'faulty')->count(),
            'stuck' => MeterReading::where('exception_type', 'stuck')->count(),
        ];

        return view('admin.meter-readings.exceptions', compact('exceptions', 'stats'));
    }


}
