<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use App\Models\Customer;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuickBillController extends Controller
{
    /**
     * Display meter selection interface for quick billing
     */
    public function selectMeter()
    {
        return view('admin.bills.quick-select');
    }

    /**
     * Search for meter by number
     */
    public function findMeter(Request $request)
    {
        $request->validate([
            'meter_number' => 'required|string|max:50'
        ]);

        $meter = Meter::with([
                'customer:id,customer_number,first_name,last_name,phone,plot_number,house_number',
                'meterCategory:id,name,code',
                'zone:id,name',
                'latestReading'
            ])
            ->where('meter_number', $request->meter_number)
            ->active()
            ->first();

        if (!$meter) {
            return response()->json([
                'success' => false,
                'message' => 'Meter not found or inactive. Please check the meter number.'
            ], 404);
        }

        // Check if meter has a customer
        if (!$meter->customer) {
            return response()->json([
                'success' => false,
                'message' => 'Meter is not assigned to any customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'meter' => [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'meter_type' => $meter->meter_type,
                'status' => $meter->status,
                'current_reading' => $meter->current_reading,
                'customer' => $meter->customer ? [
                    'id' => $meter->customer->id,
                    'name' => $meter->customer->full_name,
                    'customer_number' => $meter->customer->customer_number,
                    'phone' => $meter->customer->phone,
                    'address' => $meter->customer->formatted_address,
                ] : null,
                'category' => $meter->meterCategory ? [
                    'name' => $meter->meterCategory->name,
                    'code' => $meter->meterCategory->code
                ] : null,
                'zone' => $meter->zone ? $meter->zone->name : 'Unassigned',
                'last_reading' => $meter->latestReading ? [
                    'reading' => $meter->latestReading->current_reading,
                    'date' => $meter->latestReading->reading_date->format('M d, Y')
                ] : null
            ]
        ]);
    }

    /**
     * Create meter reading for selected meter
     */
    public function createReading(Meter $meter)
    {
        // Check if meter exists and is active
        if ($meter->status !== 'active') {
            return redirect()->route('bills.quick')
                ->with('error', 'Selected meter is not active.');
        }

        // Check if meter has customer
        if (!$meter->customer) {
            return redirect()->route('bills.quick')
                ->with('error', 'Meter is not assigned to any customer.');
        }

        // Get last reading
        $lastReading = MeterReading::where('meter_id', $meter->id)
            ->latest()
            ->first();

        // Calculate suggested reading date (first of current month if no reading this month)
        $suggestedDate = now();
        if ($lastReading && $lastReading->reading_date->isCurrentMonth()) {
            $suggestedDate = $lastReading->reading_date->addMonth();
        }

        return view('admin.meter-readings.create', [
            'meter' => $meter,
            'customer' => $meter->customer,
            'lastReading' => $lastReading,
            'meters' => collect([$meter]),
            'suggestedDate' => $suggestedDate->format('Y-m-d')
        ]);
    }
}
