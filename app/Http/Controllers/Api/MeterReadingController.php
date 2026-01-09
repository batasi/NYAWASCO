<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MeterReadingController extends Controller
{
    /**
     * Submit meter reading and generate bill
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReading(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_id' => 'required|exists:meters,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'read_by' => 'required|string|max:255',
            'reading_image' => 'nullable|string', // Base64 encoded image
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);
            $meter = Meter::findOrFail($request->meter_id);
            $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');

            // Verify meter belongs to customer
            if ($meter->customer_id != $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meter does not belong to this customer'
                ], 400);
            }

            // Get previous reading
            $previousReading = MeterReading::where('customer_id', $customer->id)
                ->where('meter_id', $meter->id)
                ->where('reading_status', 'recorded')
                ->latest('reading_date')
                ->first();

            $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

            // Validate current reading
            if ($request->current_reading < $previousReadingValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ')'
                ], 400);
            }

            // Calculate consumption
            $consumption = $request->current_reading - $previousReadingValue;

            // Check for duplicate reading in same period
            $existingReading = MeterReading::where('customer_id', $customer->id)
                ->where('meter_id', $meter->id)
                ->where('reading_period', $readingPeriod)
                ->where('reading_status', 'recorded')
                ->first();

            if ($existingReading) {
                return response()->json([
                    'success' => false,
                    'message' => 'A reading for this period already exists'
                ], 400);
            }

            // Create meter reading
            $reading = MeterReading::create([
                'customer_id' => $customer->id,
                'meter_id' => $meter->id,
                'current_reading' => $request->current_reading,
                'previous_reading' => $previousReadingValue,
                'consumption' => $consumption,
                'reading_date' => $request->reading_date,
                'reading_type' => 'monthly',
                'reading_status' => 'recorded',
                'reading_period' => $readingPeriod,
                'billed' => false,
                'read_by' => $request->read_by,
                'notes' => $request->notes,
            ]);

            // Update meter current reading
            $meter->update([
                'current_reading' => $request->current_reading
            ]);

            // Generate bill
            $bill = $this->generateBill($reading, $customer, $meter, $consumption);

            // Update meter balance
            $meter->update([
                'current_balance' => $meter->current_balance + $bill->total_amount
            ]);

            // Mark reading as billed
            $reading->update([
                'billed' => true,
                'billed_by' => null, // System generated
                'billed_at' => now(),
            ]);

            DB::commit();

            // Format bill for printing
            $formattedBill = $this->formatBillForPrint($bill, $customer, $meter, $reading);

            return response()->json([
                'success' => true,
                'message' => 'Reading submitted and bill generated successfully',
                'data' => [
                    'reading_id' => $reading->id,
                    'bill' => $formattedBill,
                    'consumption' => $consumption,
                    'previous_reading' => $previousReadingValue,
                    'current_reading' => $request->current_reading
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Meter reading submission error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error submitting reading: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate bill from reading
     */
    private function generateBill(MeterReading $reading, Customer $customer, Meter $meter, $consumption)
    {
        $category = $meter->meterCategory;
        $baseCharge = $category->base_charge ?? 0;
        $meterRent = $category->meter_rent ?? 0;

        // Calculate consumption charge using tiered pricing
        $consumptionCharge = $this->calculateConsumptionCharge($category->id, $consumption);

        // Add arrears from meter's balance_bf
        $arrears = $meter->balance_bf ?? 0;

        // Calculate total
        $totalAmount = $baseCharge + $meterRent + $consumptionCharge + $arrears;

        // Generate bill number
        $billCount = Bill::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $billNumber = 'BILL-' . now()->format('Ym') . str_pad($billCount + 1, 4, '0', STR_PAD_LEFT);

        // Create bill
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
            'tax_amount' => 0,
            'late_fee' => 0,
            'total_amount' => $totalAmount,
            'due_date' => Carbon::parse($reading->reading_date)->addDays(30),
            'bill_status' => 'unpaid',
            'notes' => 'Auto-generated from meter reading #' . $reading->id,
            'created_by' => null, // System generated
        ]);

        return $bill;
    }

    /**
     * Calculate consumption charge using tiered pricing
     */
    private function calculateConsumptionCharge($categoryId, $consumption)
    {
        $tiers = PricingTier::where('meter_category_id', $categoryId)
            ->orderBy('min_consumption')
            ->get();

        if ($tiers->isEmpty()) {
            $category = MeterCategory::find($categoryId);
            $defaultRate = $category->default_rate ?? 50;
            return $consumption * $defaultRate;
        }

        $remaining = $consumption;
        $totalCharge = 0;

        foreach ($tiers as $tier) {
            if ($remaining <= 0) break;

            $min = $tier->min_consumption;
            $max = $tier->max_consumption;

            if ($max === null) {
                // Last tier with no max
                $totalCharge += $remaining * $tier->rate_per_unit;
                break;
            }

            $tierRange = $max - $min + 1;
            $consumedInTier = min($remaining, $tierRange);
            $totalCharge += $consumedInTier * $tier->rate_per_unit;
            $remaining -= $consumedInTier;
        }

        return $totalCharge;
    }

    /**
     * Format bill for printing
     */
    private function formatBillForPrint(Bill $bill, Customer $customer, Meter $meter, MeterReading $reading)
    {
        return [
            'bill_number' => $bill->bill_number,
            'date' => now()->format('Y-m-d H:i:s'),
            'customer_info' => [
                'customer_number' => $customer->customer_number,
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'phone' => $customer->phone,
                'address' => $customer->plot_number . ', ' . $customer->house_number . ', ' . $customer->estate,
            ],
            'meter_info' => [
                'meter_number' => $meter->meter_number,
                'meter_category' => $meter->meterCategory->name ?? 'N/A',
            ],
            'reading_info' => [
                'previous_reading' => $reading->previous_reading,
                'current_reading' => $reading->current_reading,
                'consumption' => $reading->consumption,
                'reading_date' => $reading->reading_date->format('Y-m-d'),
            ],
            'charges' => [
                'base_charge' => number_format($bill->base_charge, 2),
                'consumption_charge' => number_format($bill->consumption_charge, 2),
                'meter_rent' => $meter->meterCategory->meter_rent ?? 0,
                'arrears' => $meter->balance_bf ?? 0,
            ],
            'total_amount' => number_format($bill->total_amount, 2),
            'due_date' => $bill->due_date->format('Y-m-d'),
            'company_info' => [
                'name' => 'NYAWASCO',
                'address' => 'P.O Box 255-40500 - NYAMIRA',
                'phone' => '0787080455',
            ],
            'payment_instructions' => 'Payments can be made at our offices or through M-Pesa',
        ];
    }
}
