<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;

class MeterReadingController extends Controller
{
    public function index()
    {
        $readings = MeterReading::with('customer', 'reader')
            ->latest()
            ->paginate(20);
            
        return view('admin.meter-readings.index', compact('readings'));
    }

    public function create(Request $request)
    {
        $customerId = $request->get('customer');
        $customer = null;
        $lastReading = null;
        $meter = null;

        if ($customerId) {
            $customer = Customer::with('meter')->findOrFail($customerId);
            $meter = $customer->meter;
            $lastReading = MeterReading::where('customer_id', $customerId)
                ->latest()
                ->first();
        }

        return view('admin.meter-readings.create', compact('customer', 'lastReading', 'meter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get customer and meter
        $customer = Customer::findOrFail($request->customer_id);
        $meter = $customer->meter;

        if (!$meter) {
            return back()->withErrors(['error' => 'Customer does not have a meter assigned.']);
        }

        // Get previous reading
        $previousReading = MeterReading::where('customer_id', $request->customer_id)
            ->latest()
            ->first();

        $previousReadingValue = $previousReading ? $previousReading->current_reading : $meter->initial_reading;

        // Check if current reading is valid
        if ($request->current_reading < $previousReadingValue) {
            return back()->withErrors(['current_reading' => 'Current reading cannot be less than previous reading (' . $previousReadingValue . ' m³).']);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('reading_image')) {
            $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
        }

        // Create reading
        $reading = MeterReading::create([
            'customer_id' => $request->customer_id,
            'current_reading' => $request->current_reading,
            'previous_reading' => $previousReadingValue,
            'reading_date' => $request->reading_date,
            'reading_type' => 'monthly',
            'reading_period' => \Carbon\Carbon::parse($request->reading_date)->format('F Y'),
            'read_by' => auth()->id(),
            'reading_image' => $imagePath,
            'notes' => $request->notes,
        ]);

        // Update meter's current reading
        $meter->update([
            'current_reading' => $request->current_reading
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Meter reading recorded successfully!');
    }
}