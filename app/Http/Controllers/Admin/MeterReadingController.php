<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
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
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
            'initial_reading' => 'nullable|numeric|min:0', // For initial readings
        ]);

        $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');

        // Check if this is an initial reading
        $isInitialReading = $request->has('is_initial_reading') && $request->is_initial_reading == '1';

        // Check for duplicate reading for the same meter and period
        if (!$isInitialReading) {
            $existingReading = MeterReading::where('customer_id', $request->customer_id)
                ->where('meter_id', $request->meter_id)
                ->where('reading_period', $readingPeriod)
                ->first();

            if ($existingReading && !$request->has('force_duplicate')) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "A meter reading for meter {$existingReading->meter->meter_number} in {$readingPeriod} already exists.",
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

        try {
            // Define variables outside transaction
            $meter = null;
            $customer = null;
            $readingType = 'monthly';

            DB::transaction(function () use ($request, $readingPeriod, $isInitialReading, &$meter, &$customer, &$readingType) {
                // Get customer and meter
                $customer = Customer::findOrFail($request->customer_id);
                $meter = Meter::findOrFail($request->meter_id);

                // Verify the meter belongs to the customer
                if ($meter->customer_id != $customer->id) {
                    throw new \Exception('Selected meter does not belong to this customer.');
                }

                // HANDLE INITIAL READING
                if ($isInitialReading) {
                    // Set initial reading on meter
                    $initialReading = $request->input('initial_reading', $request->current_reading);

                    $meter->update([
                        'initial_reading' => $initialReading,
                        'current_reading' => $request->current_reading
                    ]);

                    $previousReadingValue = $initialReading;
                    $readingType = 'initial';

                } else {
                    // Get previous reading for this meter
                    $previousReading = MeterReading::where('meter_id', $meter->id)
                        ->latest()
                        ->first();

                    // If no previous reading exists, check meter's initial_reading
                    if (!$previousReading) {
                        $previousReadingValue = $meter->initial_reading ?? 0;
                        // Update meter's current reading if it's the first reading
                        $meter->update(['current_reading' => $request->current_reading]);
                    } else {
                        $previousReadingValue = $previousReading->current_reading;

                        // Validate current reading is not less than previous
                        if ($request->current_reading < $previousReadingValue) {
                            throw new \Exception('Current reading (' . number_format($request->current_reading, 2) .
                                ' m³) cannot be less than previous reading (' .
                                number_format($previousReadingValue, 2) . ' m³).');
                        }

                        // Update meter's current reading
                        $meter->update(['current_reading' => $request->current_reading]);
                    }
                }

                // Calculate consumption
                $consumption = max(0, $request->current_reading - $previousReadingValue);

                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('reading_image')) {
                    $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
                }

                // Create reading record
                $reading = MeterReading::create([
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'current_reading' => $request->current_reading,
                    'previous_reading' => $previousReadingValue,
                    'consumption' => $consumption,
                    'reading_date' => $request->reading_date,
                    'reading_type' => $readingType,
                    'reading_period' => $readingPeriod,
                    'billed' => false,
                    'reading_image' => $imagePath,
                    'notes' => $request->notes,
                    'read_by' => auth()->id(),
                ]);

                // AUTO-GENERATE BILL (only for non-initial readings with consumption)
                if (!$isInitialReading && $consumption > 0) {
                    $bill = $this->generateBill($reading, $customer, $meter, $consumption);

                    // Update reading to mark as billed
                    $reading->update([
                        'billed' => true,
                        'billed_by' => auth()->id(),
                        'billed_at' => now(),
                    ]);

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
                    Log::info("Initial meter reading recorded", [
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'reading_id' => $reading->id,
                        'reading_type' => $readingType,
                        'consumption' => $consumption,
                    ]);
                }
            });

            // Return success message
            $message = $isInitialReading
                ? 'Initial meter reading recorded successfully for meter ' . $meter->meter_number . '!'
                : 'Meter reading recorded' . ($consumption > 0 ? ' and bill generated' : '') . ' successfully for meter ' . $meter->meter_number . '!';

            return redirect()->route('admin.customers.show', $customer->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Meter reading creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    /**
 * Check if meter needs initial reading
 */
    private function needsInitialReading($meter)
    {
        // Check if meter has initial_reading set
        if ($meter->initial_reading > 0) {
            return false;
        }

        // Check if there are any readings for this meter
        $hasReadings = MeterReading::where('meter_id', $meter->id)->exists();

        return !$hasReadings;
    }
    /**
     * Generate bill automatically after meter reading for specific meter
     */
    private function generateBill(MeterReading $reading, Customer $customer, Meter $meter, $consumption)
    {
        // Get meter category pricing
        $category = $meter->meterCategory;
        $baseCharge = $category->base_charge ?? 100;
        $consumptionRate = $category->default_rate ?? 50;
        $taxRate = 0.16;

        // Calculate charges
        $consumptionCharge = $consumption * $consumptionRate;
        $taxAmount = ($baseCharge + $consumptionCharge) * $taxRate;
        $totalAmount = $baseCharge + $consumptionCharge;

        // STEP 1: Create bill without bill_number first
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
            'notes' => 'Auto-generated from meter reading #' . $reading->id . ' for meter ' . $meter->meter_number,
            'created_by' => auth()->id(),
        ]);

        // STEP 2: Generate safe bill number using the bill ID
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
     * API endpoint for OCR processing
     */
    public function processOCR(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        try {
            $imagePath = $request->file('image')->store('temp-ocr', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);

            $ocrService = new OCRService();
            $reading = $ocrService->extractMeterReading($fullImagePath);

            // Clean up temporary file
            Storage::disk('public')->delete(str_replace('temp-ocr/', '', $imagePath));

            return response()->json([
                'success' => true,
                'detected_reading' => $reading,
                'message' => $reading ? 'Reading detected successfully' : 'No readable meter numbers found'
            ]);

        } catch (\Exception $e) {
            Log::error('OCR API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }
}
