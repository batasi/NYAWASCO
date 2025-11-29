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
        $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');
        
        // Check for duplicate reading for the same meter and period
        $existingReading = MeterReading::where('customer_id', $request->customer_id)
            ->where('meter_id', $request->meter_id)
            ->where('reading_period', $readingPeriod)
            ->first();

        if ($existingReading && !$request->has('force_duplicate')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "A meter reading for meter {$existingReading->meter->meter_number} in {$readingPeriod} already exists. Are you sure you want to create another reading for the same period?",
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

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
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
                    ->latest()
                    ->first();

                $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

                // Validate current reading
                if ($request->current_reading < $previousReadingValue) {
                    throw new \Exception('Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ' m³).');
                }

                // Calculate consumption
                $consumption = $request->current_reading - $previousReadingValue;

                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('reading_image')) {
                    $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
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
                    'reading_period' => $readingPeriod,
                    'billed' => false,
                    'reading_image' => $imagePath,
                    'notes' => $request->notes,
                    'read_by' => auth()->id(),
                ]);

                // UPDATE METER'S CURRENT READING
                $meter->update([
                    'current_reading' => $request->current_reading
                ]);

                // AUTO-GENERATE BILL FOR SPECIFIC METER
                $bill = $this->generateBill($reading, $customer, $meter, $consumption);

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
            });

            // Now $meter is available for the success message
            return redirect()->route('admin.customers.show', $request->customer_id)
                ->with('success', 'Meter reading recorded and bill generated successfully for meter ' . $meter->meter_number . '!');

        } catch (\Exception $e) {
            Log::error('Meter reading creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    /**
     * Generate bill automatically after meter reading for specific meter
     */
    private function generateBill(MeterReading $reading, Customer $customer, Meter $meter, $consumption)
    {
        // Get meter category pricing
        $category = $meter->meterCategory;
        $baseCharge = $category->base_charge ?? 100; // Default base charge
        $consumptionRate = $category->rate_per_unit ?? 50; // Rate per cubic meter
        $taxRate = 0.16; // 16% VAT

        // Calculate charges
        $consumptionCharge = $consumption * $consumptionRate;
        $taxAmount = ($baseCharge + $consumptionCharge) * $taxRate;
        $totalAmount = $baseCharge + $consumptionCharge + $taxAmount;

        // Generate bill number
        $latestBill = Bill::latest()->first();
        $billNumber = 'BILL-' . str_pad(($latestBill ? $latestBill->id : 0) + 1, 6, '0', STR_PAD_LEFT);

        // Create bill linked to specific meter and reading
        $bill = Bill::create([
            'customer_id' => $customer->id,
            'meter_id' => $meter->id,
            'meter_reading_id' => $reading->id,
            'bill_number' => $billNumber,
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

        // Update customer balance (sum of all meter balances)
        $totalCustomerBalance = $customer->meters->sum('current_balance');
        $customer->update([
            'current_balance' => $totalCustomerBalance
        ]);

        Log::info("Balances updated - Meter: {$meter->meter_number}, New Balance: {$newMeterBalance}, Customer: {$customer->customer_number}, Total Balance: {$totalCustomerBalance}");
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