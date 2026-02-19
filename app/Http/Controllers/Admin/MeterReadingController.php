<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\PricingTier;
use App\Models\Zone;
use App\Services\OCRService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MeterReadingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $dateFilter = $request->get('date_filter', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $zoneId = $request->get('zone', 'all'); // Zone filter

        $query = MeterReading::with(['customer', 'meter.zone', 'reader'])
            ->select('meter_readings.*')
            ->latest('reading_date');

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($q) use ($search) {
                    $q->where('customer_number', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('meter', function($q) use ($search) {
                    $q->where('meter_number', 'LIKE', "%{$search}%");
                })
                ->orWhere('reading_period', 'LIKE', "%{$search}%")
                ->orWhere('current_reading', 'LIKE', "%{$search}%");
            });
        }

        // Filter by reading status
        if ($status !== 'all') {
            if ($status === 'billed') {
                $query->where('billed', true);
            } elseif ($status === 'unbilled') {
                $query->where('billed', false);
            } else {
                $query->where('reading_status', $status);
            }
        }

        // Filter by reading type
        if ($type !== 'all') {
            $query->where('reading_type', $type);
        }

        // Filter by zone
        if ($zoneId !== 'all') {
            $query->whereHas('meter', function($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        }

        // Filter by date range
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('reading_date', [$startDate, $endDate]);
        } elseif ($dateFilter === 'today') {
            $query->whereDate('reading_date', Carbon::today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('reading_date', Carbon::yesterday());
        } elseif ($dateFilter === 'this_month') {
            $query->whereMonth('reading_date', Carbon::now()->month)
                  ->whereYear('reading_date', Carbon::now()->year);
        } elseif ($dateFilter === 'last_month') {
            $query->whereMonth('reading_date', Carbon::now()->subMonth()->month)
                  ->whereYear('reading_date', Carbon::now()->subMonth()->year);
        }

        $readings = $query->paginate(30);

        // Get zones for filter dropdown
        $zones = Zone::orderBy('name')->get();

        // Get selected zone for display
        $selectedZone = $zoneId !== 'all' ? Zone::find($zoneId) : null;

        // Calculate stats with zone filter applied
        $statsQuery = MeterReading::query();

        if ($zoneId !== 'all') {
            $statsQuery->whereHas('meter', function($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        }

        $stats = [
            'total' => $statsQuery->count(),
            'billed' => $statsQuery->clone()->where('billed', true)->count(),
            'unbilled' => $statsQuery->clone()->where('billed', false)->count(),
            'exceptions' => $statsQuery->clone()->where('reading_status', 'exception')->count(),
            'estimated' => $statsQuery->clone()->where('reading_status', 'estimated')->count(),
            'this_month' => $statsQuery->clone()->whereMonth('reading_date', Carbon::now()->month)
                ->whereYear('reading_date', Carbon::now()->year)->count(),
        ];

        return view('admin.meter-readings.index', compact(
            'readings',
            'stats',
            'status',
            'type',
            'dateFilter',
            'startDate',
            'endDate',
            'zoneId',
            'zones',
            'selectedZone'
        ));
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

                return redirect()->back()
                    ->withInput()
                    ->with('error', "Meter reading for {$readingPeriod} has already been recorded for meter {$existingReading->meter->meter_number}.");
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

            DB::transaction(function () use ($request, $readingPeriod, &$meter, &$bill) {
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
                    $result = $this->updateBalances($meter, $customer, $bill->total_amount);

                    $billBalance = $result['bill_balance'];
                    $amountPaid = $bill->total_amount - $billBalance;

                    // Determine bill status
                    if ($billBalance == 0) {
                        $billStatus = 'paid';
                    } elseif ($billBalance < $bill->total_amount) {
                        $billStatus = 'partial';
                    } else {
                        $billStatus = 'unpaid';
                    }

                    // Update bill record
                    $bill->update([
                        'balance' => $billBalance,
                        'bill_status' => $billStatus,
                        'paid_amount' => $amountPaid,
                    ]);




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

        $taxAmount = ($baseCharge + $consumptionCharge + $meterRent) * $taxRate;
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
            'bill_number' => 'INV-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT)
        ]);

        // Mark reading as billed
        $reading->update([
            'billed' => true,
            'billed_by' => auth()->id(),
            'billed_at' => now(),
        ]);

        return $bill;
    }

    public function calculateTieredCharge($categoryId, $consumption)
    {
        $tier = PricingTier::where('meter_category_id', $categoryId)
            ->where('min_consumption', '<=', $consumption)
            ->where(function($query) use ($consumption) {
                $query->whereNull('max_consumption')
                    ->orWhere('max_consumption', '>=', $consumption);
            })
            ->orderBy('sort_order')
            ->first();

        if (!$tier) return 0;

        return $consumption * $tier->rate_per_unit;
    }


    /**
     * Update meter and customer balances after bill generation
     */

    private function updateBalances(Meter $meter, Customer $customer, $billAmount)
    {
        $previousBalance = $meter->current_balance;

        // increment total billed so far
        $meter->balance_bf += $billAmount;

        // Case 1: Customer has credit (negative balance)
        if ($previousBalance < 0) {

            if (abs($previousBalance) >= $billAmount) {
                // Invoice fully covered by credit
                $billBalance = 0;
                $remainingBalance = $previousBalance + $billAmount; // still ≤ 0

            } else {
                // Partially covered
                $billBalance = $billAmount - abs($previousBalance); // small positive number
                $remainingBalance = $billBalance; // now debt becomes new balance
            }

        } else {
            // No credit: they owe full amount
            $billBalance = $billAmount;
            $remainingBalance = $previousBalance + $billAmount;
        }

        // update meter state
        $meter->update([
            'current_balance' => $remainingBalance
        ]);

        return [
            'bill_balance' => $billBalance,
            'remaining_balance' => $remainingBalance
        ];
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


    /**
     * Show the form for editing a meter reading
     */
    public function edit(MeterReading $meterReading)
    {
        // Prevent editing of billed readings to maintain data integrity

        try {
            // Get meter from the reading first
            $meter = Meter::with(['customer', 'zone', 'meterCategory'])->find($meterReading->meter_id);

            if (!$meter) {
                // Try to get meter from database
                $meter = Meter::find($meterReading->meter_id);

                if (!$meter) {
                    Log::error('Meter not found for reading', [
                        'reading_id' => $meterReading->id,
                        'meter_id' => $meterReading->meter_id
                    ]);

                    return redirect()->route('admin.meter-readings.index')
                        ->with('error', 'Meter not found for this reading. Please check meter records.');
                }
            }

            // Get customer from the meter
            $customer = $meter->customer;

            if (!$customer) {
                // Try to find customer by customer_id in meter
                if ($meter->customer_id) {
                    $customer = Customer::find($meter->customer_id);
                }

                if (!$customer) {
                    Log::warning('Customer not found for meter', [
                        'reading_id' => $meterReading->id,
                        'meter_id' => $meter->id,
                        'meter_customer_id' => $meter->customer_id
                    ]);

                    // We can still show the form but with warnings
                    $customer = new Customer();
                    session()->flash('warning', 'Customer not found for this meter. You may need to assign a customer to this meter first.');
                }
            }

            // Get all meters for this customer if customer exists
            $meters = collect();
            if ($customer && $customer->id) {
                $meters = $customer->meters()->get();
            }

            // Get all readings for this meter to show context
            $allReadings = MeterReading::where('meter_id', $meter->id)
                ->orderBy('reading_date', 'desc')
                ->get();

            // Get the previous reading for reference
            $previousReading = MeterReading::where('meter_id', $meter->id)
                ->where('id', '!=', $meterReading->id)
                ->where('reading_date', '<', $meterReading->reading_date)
                ->where('reading_status', 'recorded')
                ->latest('reading_date')
                ->first();

            // Get the next reading to ensure we don't break the sequence
            $nextReading = MeterReading::where('meter_id', $meter->id)
                ->where('id', '!=', $meterReading->id)
                ->where('reading_date', '>', $meterReading->reading_date)
                ->where('reading_status', 'recorded')
                ->oldest('reading_date')
                ->first();

            return view('admin.meter-readings.edit', compact(
                'meterReading',
                'customer',
                'meter',
                'meters',
                'allReadings',
                'previousReading',
                'nextReading'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading edit form for meter reading: ' . $e->getMessage(), [
                'reading_id' => $meterReading->id,
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.meter-readings.index')
                ->with('error', 'Error loading reading for editing: ' . $e->getMessage());
        }
    }
    public function repairOrphanedReadings()
    {
        try {
            // Find meter readings where meter doesn't exist
            $orphanedReadings = DB::table('meter_readings as mr')
                ->leftJoin('meters as m', 'mr.meter_id', '=', 'm.id')
                ->whereNull('m.id')
                ->select('mr.id', 'mr.meter_id', 'mr.reading_date', 'mr.current_reading')
                ->get();

            if ($orphanedReadings->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No orphaned readings found.'
                ]);
            }

            // Try to find matching meters by reading data
            $repairCount = 0;
            foreach ($orphanedReadings as $reading) {
                // Try to find a meter that might match
                $possibleMeter = Meter::whereHas('meterReadings', function($q) use ($reading) {
                    $q->where('reading_date', '<', $reading->reading_date)
                    ->where('current_reading', $reading->current_reading);
                })->first();

                if ($possibleMeter) {
                    // Update the reading with the found meter
                    MeterReading::where('id', $reading->id)->update(['meter_id' => $possibleMeter->id]);
                    $repairCount++;
                }
            }

            Log::info("Repaired orphaned meter readings", [
                'total_orphaned' => $orphanedReadings->count(),
                'repaired' => $repairCount,
                'still_orphaned' => $orphanedReadings->count() - $repairCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Found {$orphanedReadings->count()} orphaned readings. Repaired {$repairCount}.",
                'data' => [
                    'total' => $orphanedReadings->count(),
                    'repaired' => $repairCount,
                    'remaining' => $orphanedReadings->count() - $repairCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error repairing orphaned readings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error repairing orphaned readings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, MeterReading $meterReading)
    {

        // Load meter with customer relationship
        $meterReading->load(['meter.customer']);

        $meter = $meterReading->meter;

        if (!$meter) {
            return redirect()->back()
                ->with('error', 'Meter not found for this reading.');
        }

        // Get customer from the meter
        $customer = $meter->customer;

        if (!$customer) {
            // Try to get customer from meter's customer_id
            if ($meter->customer_id) {
                $customer = Customer::find($meter->customer_id);
            }

            if (!$customer) {
                return redirect()->back()
                    ->with('error', 'Customer not found for this meter.');
            }
        }

        $request->validate([
            'current_reading' => 'required_if:reading_status,recorded|nullable|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_status' => 'required|in:recorded,exception,estimated',
            'exception_type' => 'required_if:reading_status,exception|nullable|in:inaccessible,faulty,stuck,damaged,vandalized,other',
            'exception_reason' => 'required_if:reading_status,exception|nullable|string|max:500',
            'estimated_consumption' => 'required_if:reading_status,estimated|nullable|numeric|min:0',
            'initial_reading' => 'nullable|numeric|min:0', // Add validation for initial reading
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $meterReading, $meter, $customer) {
                // Store old values for reversal
                $oldReadingStatus = $meterReading->reading_status;
                $oldConsumption = $meterReading->consumption;
                $oldCurrentReading = $meterReading->current_reading;
                $oldEstimatedConsumption = $meterReading->estimated_consumption;

                // Get associated bill if exists
                $bill = Bill::where('meter_reading_id', $meterReading->id)->first();

                // STEP 1: REVERSE EXISTING EFFECTS
                if ($oldReadingStatus !== 'exception' && $oldConsumption > 0 && $bill) {
                    $this->reverseBillEffects($bill, $meter, $customer);
                }

                // STEP 2: UPDATE METER'S INITIAL READING if provided
                if ($request->filled('initial_reading') && $request->initial_reading != $meter->initial_reading) {
                    // Simple validation
                    if ($request->initial_reading < 0) {
                        throw new \Exception('Initial reading cannot be negative.');
                    }

                    Log::info("Meter initial reading updated", [
                        'meter_id' => $meter->id,
                        'old_initial_reading' => $meter->initial_reading,
                        'new_initial_reading' => $request->initial_reading,
                        'updated_by' => auth()->id(),
                        'reading_id' => $meterReading->id
                    ]);

                    // Simply update the initial reading
                    $meter->update([
                        'initial_reading' => $request->initial_reading
                    ]);

                    // The existing logic in updateSubsequentReadings() will handle recalculation
                    // when it recalculates consumption for all affected readings
                }

                // STEP 3: VALIDATE READING SEQUENCE
                // Get previous and next readings for validation
                $previousReading = MeterReading::where('meter_id', $meter->id)
                    ->where('id', '!=', $meterReading->id)
                    ->where('reading_date', '<', $request->reading_date)
                    ->where('reading_status', 'recorded')
                    ->latest('reading_date')
                    ->first();

                $nextReading = MeterReading::where('meter_id', $meter->id)
                    ->where('id', '!=', $meterReading->id)
                    ->where('reading_date', '>', $request->reading_date)
                    ->where('reading_status', 'recorded')
                    ->oldest('reading_date')
                    ->first();

                // Validate reading sequence for recorded readings
                if ($request->reading_status === 'recorded') {
                    $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

                    if ($request->current_reading < $previousReadingValue) {
                        throw new \Exception('Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ' m³).');
                    }

                    if ($nextReading && $request->current_reading > $nextReading->current_reading) {
                        throw new \Exception('Current reading cannot be greater than next reading (' . number_format($nextReading->current_reading, 2) . ' m³).');
                    }
                }

                // STEP 4: CALCULATE NEW CONSUMPTION
                $consumption = 0;
                $estimatedConsumption = null;

                if ($request->reading_status === 'recorded') {
                    $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);
                    $consumption = $request->current_reading - $previousReadingValue;
                } elseif ($request->reading_status === 'estimated') {
                    $estimatedConsumption = $request->estimated_consumption;
                    $consumption = $estimatedConsumption;
                }

                // Handle image upload
                $imagePath = $meterReading->reading_image;
                if ($request->hasFile('reading_image')) {
                    // Delete old image if exists
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
                }

                // Handle exception evidence
                $exceptionEvidence = $meterReading->exception_evidence;
                if ($request->hasFile('exception_evidence')) {
                    if ($exceptionEvidence && Storage::disk('public')->exists($exceptionEvidence)) {
                        Storage::disk('public')->delete($exceptionEvidence);
                    }
                    $exceptionEvidence = $request->file('exception_evidence')->store('exception-evidence', 'public');
                }

                // STEP 5: UPDATE READING RECORD
                $meterReading->update([
                    'current_reading' => $request->current_reading,
                    'previous_reading' => $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0),
                    'consumption' => $consumption,
                    'reading_date' => $request->reading_date,
                    'reading_status' => $request->reading_status,
                    'exception_type' => $request->exception_type,
                    'exception_reason' => $request->exception_reason,
                    'estimated' => $request->reading_status === 'estimated',
                    'estimated_consumption' => $estimatedConsumption,
                    'exception_evidence' => $exceptionEvidence,
                    'reading_image' => $imagePath,
                    'notes' => $request->notes,
                    'updated_by' => auth()->id(),
                    'customer_id' => $customer->id,
                    'reading_period' => Carbon::parse($request->reading_date)->format('F Y'),
                    'billed' => false, // Reset billed status as we'll regenerate bill if needed
                ]);

                // STEP 6: UPDATE METER'S CURRENT READING if this is the latest recorded reading
                if ($request->reading_status === 'recorded') {
                    $latestReading = MeterReading::where('meter_id', $meter->id)
                        ->where('reading_status', 'recorded')
                        ->latest('reading_date')
                        ->first();

                    if ($latestReading && $latestReading->id === $meterReading->id) {
                        $meter->update([
                            'current_reading' => $request->current_reading
                        ]);
                    }
                }

                // STEP 7: UPDATE SUBSEQUENT READINGS if this reading was recorded
                if ($request->reading_status === 'recorded' && $nextReading) {
                    $this->updateSubsequentReadings($meterReading, $request->current_reading);
                }

                // STEP 8: REGENERATE BILL FOR NORMAL AND ESTIMATED READINGS
                if ($request->reading_status !== 'exception' && $consumption >= 0) {
                    $bill = $this->generateBill($meterReading, $customer, $meter, $consumption, $request->reading_status);

                    // UPDATE METER AND CUSTOMER BALANCES
                    $result = $this->updateBalances($meter, $customer, $bill->total_amount);

                    $billBalance = $result['bill_balance'];
                    $amountPaid = $bill->total_amount - $billBalance;

                    // Determine bill status
                    if ($billBalance == 0) {
                        $billStatus = 'paid';
                    } elseif ($billBalance < $bill->total_amount) {
                        $billStatus = 'partial';
                    } else {
                        $billStatus = 'unpaid';
                    }

                    // Update bill record
                    $bill->update([
                        'balance' => $billBalance,
                        'bill_status' => $billStatus,
                        'paid_amount' => $amountPaid,
                    ]);
                }

                // STEP 9: DELETE OLD BILL if reading changed to exception
                if ($oldReadingStatus !== 'exception' && $request->reading_status === 'exception' && $bill) {
                    $bill->delete();
                    Log::info("Old bill deleted as reading changed to exception", [
                        'bill_id' => $bill->id,
                        'reading_id' => $meterReading->id
                    ]);
                }

                Log::info("Meter reading updated successfully", [
                    'reading_id' => $meterReading->id,
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'updated_by' => auth()->id(),
                    'old_status' => $oldReadingStatus,
                    'new_status' => $request->reading_status,
                    'old_consumption' => $oldConsumption,
                    'new_consumption' => $consumption
                ]);
            });

            return redirect()->route('admin.meter-readings.index')
                ->with('success', 'Meter reading updated successfully!');

        } catch (\Exception $e) {
            Log::error('Meter reading update error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Reverse the effects of an existing bill
     */
    private function reverseBillEffects(Bill $bill, Meter $meter, Customer $customer)
    {
        Log::info("Reversing bill effects", [
            'bill_id' => $bill->id,
            'amount' => $bill->total_amount,
            'meter_id' => $meter->id
        ]);

        // Reverse balance calculations
        $currentBalance = $meter->current_balance;
        $billAmount = $bill->total_amount;

        // If bill was partially/fully paid, we need to adjust balances
        if ($bill->paid_amount > 0) {
            // Add back the paid amount to meter's current_balance
            // This effectively reverses the payment
            $newBalance = ($currentBalance + $bill->paid_amount) -$billAmount;

            $meter->update([
                'current_balance' => $newBalance,
                'balance_bf' => $meter->balance_bf - $billAmount // Subtract from billed total
            ]);
        } else {
            // If bill was unpaid, just subtract from balance_bf
            $meter->update([
                'balance_bf' => $meter->balance_bf - $billAmount,
                'current_balance' => $meter->current_balance - $billAmount
            ]);
        }

        Log::info("Bill effects reversed", [
            'bill_id' => $bill->id,
            'old_balance' => $currentBalance,
            'new_balance' => $meter->current_balance,
            'old_balance_bf' => $meter->balance_bf + $billAmount,
            'new_balance_bf' => $meter->balance_bf
        ]);

        // Delete the old bill
        $bill->delete();
    }

    /**
     * Update subsequent readings when a reading is modified
     */
    private function updateSubsequentReadings(MeterReading $updatedReading, $newReadingValue)
    {
        // Get all subsequent readings for this meter
        $subsequentReadings = MeterReading::where('meter_id', $updatedReading->meter_id)
            ->where('reading_date', '>', $updatedReading->reading_date)
            ->where('reading_status', 'recorded')
            ->orderBy('reading_date')
            ->get();

        $previousReadingValue = $newReadingValue;

        foreach ($subsequentReadings as $reading) {
            // Calculate new consumption based on updated previous value
            $newConsumption = $reading->current_reading - $previousReadingValue;

            // Ensure consumption is not negative
            if ($newConsumption < 0) {
                throw new \Exception("Updating reading would cause negative consumption for reading ID: {$reading->id}");
            }

            // Update the reading's previous reading and consumption
            $reading->update([
                'previous_reading' => $previousReadingValue,
                'consumption' => $newConsumption
            ]);

            // If this reading has a bill, we need to regenerate it with new consumption
            if ($reading->billed) {
                $this->regenerateReadingBill($reading);
            }

            $previousReadingValue = $reading->current_reading;

            Log::info("Updated subsequent reading", [
                'reading_id' => $reading->id,
                'meter_id' => $reading->meter_id,
                'new_previous_reading' => $previousReadingValue,
                'new_consumption' => $newConsumption,
                'triggered_by_reading_id' => $updatedReading->id
            ]);
        }
    }

    /**
     * Regenerate bill for a reading (when consumption changes)
     */
    private function regenerateReadingBill(MeterReading $reading)
    {
        $reading->load(['meter', 'customer']);

        // Get existing bill
        $bill = Bill::where('meter_reading_id', $reading->id)->first();

        if ($bill) {
            // Reverse old bill effects
            $this->reverseBillEffects($bill, $reading->meter, $reading->customer);

            // Generate new bill with updated consumption
            $newBill = $this->generateBill(
                $reading,
                $reading->customer,
                $reading->meter,
                $reading->consumption,
                $reading->reading_status
            );

            // Update balances
            $result = $this->updateBalances($reading->meter, $reading->customer, $newBill->total_amount);

            $billBalance = $result['bill_balance'];
            $amountPaid = $newBill->total_amount - $billBalance;

            // Determine bill status
            if ($billBalance == 0) {
                $billStatus = 'paid';
            } elseif ($billBalance < $newBill->total_amount) {
                $billStatus = 'partial';
            } else {
                $billStatus = 'unpaid';
            }

            // Update bill record
            $newBill->update([
                'balance' => $billBalance,
                'bill_status' => $billStatus,
                'paid_amount' => $amountPaid,
            ]);

            Log::info("Reading bill regenerated", [
                'reading_id' => $reading->id,
                'old_bill_id' => $bill->id,
                'new_bill_id' => $newBill->id,
                'new_consumption' => $reading->consumption
            ]);
        }
    }

    // Also update the destroy method for consistency
    public function destroy(MeterReading $meterReading)
    {
        try {
            // Check if reading is billed
            if ($meterReading->billed) {
                return redirect()->back()
                    ->with('error', 'Cannot delete a billed reading. Please delete the associated bill first.');
            }

            DB::transaction(function () use ($meterReading) {
                // Load meter with customer
                $meterReading->load(['meter.customer']);

                $meter = $meterReading->meter;

                if (!$meter) {
                    throw new \Exception('Meter not found for this reading.');
                }

                // Get customer from meter
                $customer = $meter->customer;
                if (!$customer && $meter->customer_id) {
                    $customer = Customer::find($meter->customer_id);
                }

                // Delete associated image files
                if ($meterReading->reading_image && Storage::disk('public')->exists($meterReading->reading_image)) {
                    Storage::disk('public')->delete($meterReading->reading_image);
                }

                if ($meterReading->exception_evidence && Storage::disk('public')->exists($meterReading->exception_evidence)) {
                    Storage::disk('public')->delete($meterReading->exception_evidence);
                }

                // If this was the latest recorded reading, update meter's current reading
                if ($meterReading->reading_status === 'recorded') {
                    $latestReading = MeterReading::where('meter_id', $meter->id)
                        ->where('id', '!=', $meterReading->id)
                        ->where('reading_status', 'recorded')
                        ->latest('reading_date')
                        ->first();

                    if ($latestReading) {
                        $meter->update([
                            'current_reading' => $latestReading->current_reading
                        ]);
                    } else {
                        // No readings left, revert to initial reading
                        $meter->update([
                            'current_reading' => $meter->initial_reading
                        ]);
                    }
                }

                // Delete the reading
                $meterReading->delete();

                Log::info("Meter reading deleted", [
                    'reading_id' => $meterReading->id,
                    'meter_id' => $meter->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'deleted_by' => auth()->id()
                ]);
            });

            return redirect()->route('admin.meter-readings.index')
                ->with('success', 'Meter reading deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Meter reading deletion error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting meter reading: ' . $e->getMessage());
        }
    }
    /**
     * Display meters that don't have readings for the selected month
     */
    public function unread(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $zoneId = $request->get('zone', 'all');
        $search = $request->get('search', ''); // Add this line to get search parameter

        // Parse the selected month
        $selectedDate = Carbon::parse($month . '-01');
        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();
        $monthName = $selectedDate->format('F Y');

        // Build query for meters that DON'T have readings in the selected month
        $query = Meter::with(['customer', 'zone', 'meterCategory'])
            ->where('status', 'active') // Only active meters
            ->whereNotNull('customer_id') // Only meters assigned to customers
            ->whereDoesntHave('meterReadings', function($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('reading_date', [$monthStart, $monthEnd])
                ->whereIn('reading_status', ['recorded', 'estimated']); // Count both recorded and estimated as "read"
            });

        // Apply zone filter
        if ($zoneId !== 'all') {
            $query->where('zone_id', $zoneId);
        }

        // Apply search filter - THIS WAS MISSING
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('meter_number', 'like', "%{$search}%")
                ->orWhere('meter_model', 'like', "%{$search}%")
                ->orWhere('installation_address', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        // Get the unread meters with pagination
        $unreadMeters = $query->orderBy('meter_number')->paginate(20);

        // Get additional stats for each meter
        foreach ($unreadMeters as $meter) {
            // Get last reading for this meter
            $lastReading = MeterReading::where('meter_id', $meter->id)
                ->whereIn('reading_status', ['recorded', 'estimated'])
                ->latest('reading_date')
                ->first();

            $meter->last_reading_date = $lastReading ? $lastReading->reading_date : null;
            $meter->last_reading_value = $lastReading ? $lastReading->current_reading : $meter->initial_reading;
            $meter->last_reading_status = $lastReading ? $lastReading->reading_status : null;

            // Check if meter has any exception in the last 3 months
            $recentExceptions = MeterReading::where('meter_id', $meter->id)
                ->where('reading_status', 'exception')
                ->where('reading_date', '>=', Carbon::now()->subMonths(3))
                ->count();

            $meter->recent_exceptions = $recentExceptions;
        }

        // Get zones for filter dropdown
        $zones = Zone::orderBy('name')->get();

        // Calculate summary statistics - FIX THIS PART TO USE COLLECTION AFTER PAGINATION
        $unreadMetersCollection = collect($unreadMeters->items());

        $stats = [
            'total_unread' => $unreadMeters->total(),
            'total_meters' => Meter::where('status', 'active')->whereNotNull('customer_id')->count(),
            'with_previous_readings' => $unreadMetersCollection->filter(function($meter) {
                return !is_null($meter->last_reading_date);
            })->count(),
            'with_exceptions' => $unreadMetersCollection->filter(function($meter) {
                return $meter->recent_exceptions > 0;
            })->count(),
        ];

        // Get previous months for dropdown
        $previousMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $previousMonths[$date->format('Y-m')] = $date->format('F Y');
        }

        return view('admin.meter-readings.unread', compact(
            'unreadMeters',
            'zones',
            'month',
            'monthName',
            'zoneId',
            'stats',
            'previousMonths'
        ));
    }
    /**
     * Export unread meters to Excel
     */
    public function exportUnread(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $zoneId = $request->get('zone', 'all');
        $search = $request->get('search', '');

        // Parse the selected month
        $selectedDate = Carbon::parse($month . '-01');
        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();
        $monthName = $selectedDate->format('F Y');

        // Build query for meters that DON'T have readings in the selected month
        $query = Meter::with(['customer', 'zone', 'meterCategory'])
            ->where('status', 'active')
            ->whereNotNull('customer_id')
            ->whereDoesntHave('meterReadings', function($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('reading_date', [$monthStart, $monthEnd])
                ->whereIn('reading_status', ['recorded', 'estimated']);
            });

        // Apply zone filter
        if ($zoneId !== 'all') {
            $query->where('zone_id', $zoneId);
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('meter_number', 'like', "%{$search}%")
                ->orWhere('meter_model', 'like', "%{$search}%")
                ->orWhere('installation_address', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        $meters = $query->orderBy('meter_number')->get();

        // Get zone info for filename
        $zoneInfo = null;
        if ($zoneId !== 'all') {
            $zoneInfo = Zone::find($zoneId);
        }

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Remove default sheet
        $spreadsheet->removeSheetByIndex(0);

        // FIRST - Create Summary sheet (will be index 0)
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addUnreadSummarySheet($summarySheet, $meters, $monthName, $zoneInfo, $search);

        // SECOND - Create Unread Meters sheet
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Unread Meters');

        // Add header with report info
        $currentRow = $this->addUnreadReportHeader($sheet, $monthName, $zoneInfo, $search, $meters->count());

        // Add data table headers - EXACTLY as requested
        $headers = [
            'Meter Number',
            'Meter Type',
            'Customer Name',
            'Phone Number',
            'Zone',
            'Installation Date',
            'Initial Reading',
            'Last Reading Date',
            'Last Reading Value',
            'Current Balance (KSh)',
            'Reading Exceptions',
            'Status'
        ];

        // Add headers with styling
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $currentRow, $header);
            $sheet->getStyle($col . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle($col . $currentRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($col . $currentRow)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($col . $currentRow)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($col . $currentRow)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }

        $currentRow++;

        // Add data rows
        foreach ($meters as $meter) {
            // Get last reading for this meter
            $lastReading = MeterReading::where('meter_id', $meter->id)
                ->whereIn('reading_status', ['recorded', 'estimated'])
                ->latest('reading_date')
                ->first();

            $lastReadingDate = $lastReading ? $lastReading->reading_date : null;
            $lastReadingValue = $lastReading ? $lastReading->current_reading : $meter->initial_reading;

            // Check recent exceptions (last 3 months)
            $recentExceptions = MeterReading::where('meter_id', $meter->id)
                ->where('reading_status', 'exception')
                ->where('reading_date', '>=', Carbon::now()->subMonths(3))
                ->count();

            $col = 'A';
            $sheet->setCellValue($col++ . $currentRow, $meter->meter_number);
            $sheet->setCellValue($col++ . $currentRow, ucfirst($meter->meter_type));
            $sheet->setCellValue($col++ . $currentRow, $meter->customer ? trim($meter->customer->first_name . ' ' . $meter->customer->last_name) : 'N/A');
            $sheet->setCellValue($col++ . $currentRow, $meter->customer->phone ?? 'N/A');
            $sheet->setCellValue($col++ . $currentRow, $meter->zone->name ?? 'Unassigned');
            $sheet->setCellValue($col++ . $currentRow, $meter->installation_date ? $meter->installation_date->format('d/m/Y') : 'N/A');
            $sheet->setCellValue($col++ . $currentRow, $meter->initial_reading);
            $sheet->setCellValue($col++ . $currentRow, $lastReadingDate ? $lastReadingDate->format('d/m/Y') : 'No Previous Reading');
            $sheet->setCellValue($col++ . $currentRow, $lastReadingValue);
            $sheet->setCellValue($col++ . $currentRow, $meter->current_balance);
            $sheet->setCellValue($col++ . $currentRow, $recentExceptions > 0 ? $recentExceptions . ' exception(s)' : 'None');
            $sheet->setCellValue($col++ . $currentRow, ucfirst($meter->status));

            // Style the row
            $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Format numbers
            $sheet->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Initial Reading
            $sheet->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Last Reading Value
            $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Current Balance

            // Highlight rows with recent exceptions
            if ($recentExceptions > 0) {
                $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFE6E6'); // Light red
            }

            $currentRow++;
        }

        // Add totals row
        $sheet->setCellValue('A' . $currentRow, 'TOTAL UNREAD METERS:');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $currentRow, $meters->count());
        $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getBorders()
            ->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set active sheet to Summary (first sheet)
        $spreadsheet->setActiveSheetIndex(0);

        // Generate filename
        $filename = 'NYAWASCO_Unread_Meters_' . $month .
                    ($zoneInfo ? '_' . str_replace(' ', '_', $zoneInfo->name) : '') .
                    '_' . now()->format('Y_m_d_His') . '.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Add header to unread report sheet
     */
    private function addUnreadReportHeader($sheet, $monthName, $zoneInfo = null, $search = '', $totalCount = 0)
    {
        $row = 1;

        // Company Name
        $sheet->mergeCells('A' . $row . ':S' . $row);
        $sheet->setCellValue('A' . $row, 'NYAWASCO - UNREAD METERS REPORT');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Company Details
        $sheet->mergeCells('A' . $row . ':S' . $row);
        $sheet->setCellValue('A' . $row, 'P.O. Box 255 - 40500, NYAMIRA | Tel: 0787080455 | Email: info@nyawasco.co.ke');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Report Period
        $sheet->mergeCells('A' . $row . ':S' . $row);
        $sheet->setCellValue('A' . $row, 'Billing Month: ' . $monthName);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Zone filter if applied
        if ($zoneInfo) {
            $sheet->mergeCells('A' . $row . ':S' . $row);
            $sheet->setCellValue('A' . $row, 'Zone: ' . $zoneInfo->name . ' (Filtered)');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // Search term if applied
        if (!empty($search)) {
            $sheet->mergeCells('A' . $row . ':S' . $row);
            $sheet->setCellValue('A' . $row, 'Search: ' . $search);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // Generated date
        $sheet->mergeCells('A' . $row . ':S' . $row);
        $sheet->setCellValue('A' . $row, 'Generated: ' . now()->format('d F Y H:i:s'));
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Total count
        $sheet->mergeCells('A' . $row . ':S' . $row);
        $sheet->setCellValue('A' . $row, 'Total Unread Meters: ' . $totalCount);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        return $row;
    }

    /**
     * Add summary sheet for unread report
     */
    private function addUnreadSummarySheet($sheet, $meters, $monthName, $zoneInfo = null, $search = '')
    {
        $row = 1;

        // Title
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('A' . $row, 'NYAWASCO - UNREAD METERS SUMMARY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        // Report Info
        $info = [
            ['Billing Month:', $monthName],
            ['Generated:', now()->format('d F Y H:i:s')],
            ['Total Unread Meters:', $meters->count()],
        ];

        if ($zoneInfo) {
            $info[] = ['Zone Filter:', $zoneInfo->name];
        }

        if (!empty($search)) {
            $info[] = ['Search Term:', $search];
        }

        foreach ($info as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $row, $item[1]);
            $row++;
        }

        $row += 2;

        // Zone Breakdown
        $zoneBreakdown = $meters->groupBy(function($meter) {
            return $meter->zone->name ?? 'Unassigned';
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'meters' => $group
            ];
        });

        $sheet->setCellValue('A' . $row, 'BREAKDOWN BY ZONE');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Zone');
        $sheet->setCellValue('B' . $row, 'Count');
        $sheet->setCellValue('C' . $row, 'Percentage');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        $row++;

        foreach ($zoneBreakdown as $zone => $data) {
            $sheet->setCellValue('A' . $row, $zone);
            $sheet->setCellValue('B' . $row, $data['count']);
            $sheet->setCellValue('C' . $row, ($data['count'] / $meters->count()) * 100 . '%');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('0.00%');
            $row++;
        }

        $row += 2;

        // Category Breakdown
        $categoryBreakdown = $meters->groupBy(function($meter) {
            return $meter->meterCategory->name ?? 'Uncategorized';
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'meters' => $group
            ];
        });

        $sheet->setCellValue('A' . $row, 'BREAKDOWN BY CATEGORY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Category');
        $sheet->setCellValue('B' . $row, 'Count');
        $sheet->setCellValue('C' . $row, 'Percentage');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        $row++;

        foreach ($categoryBreakdown as $category => $data) {
            $sheet->setCellValue('A' . $row, $category);
            $sheet->setCellValue('B' . $row, $data['count']);
            $sheet->setCellValue('C' . $row, ($data['count'] / $meters->count()) * 100 . '%');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('0.00%');
            $row++;
        }

        $row += 2;

        // Recent Exceptions Summary
        $metersWithExceptions = $meters->filter(function($meter) {
            return $meter->recent_exceptions > 0;
        });

        $sheet->setCellValue('A' . $row, 'METERS WITH RECENT EXCEPTIONS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Meter Number');
        $sheet->setCellValue('B' . $row, 'Customer');
        $sheet->setCellValue('C' . $row, 'Zone');
        $sheet->setCellValue('D' . $row, 'Exceptions Count');
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFE6E6');
        $row++;

        foreach ($metersWithExceptions as $meter) {
            $sheet->setCellValue('A' . $row, $meter->meter_number);
            $sheet->setCellValue('B' . $row, $meter->customer ? trim($meter->customer->first_name . ' ' . $meter->customer->last_name) : 'N/A');
            $sheet->setCellValue('C' . $row, $meter->zone->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $meter->recent_exceptions);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
