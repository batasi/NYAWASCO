<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use App\Models\Customer;
use App\Models\MeterCategory;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        
        $meters = Meter::with(['customer', 'meterCategory', 'meterReadings' => function($query) {
            $query->latest()->limit(1);
        }])
        ->when($categoryId, function($query) use ($categoryId) {
            return $query->where('meter_category_id', $categoryId);
        })
        ->latest()
        ->paginate(20);

        $stats = [
            'total' => Meter::count(),
            'assigned' => Meter::where('status', 'assigned')->count(),
            'unassigned' => Meter::where('status', 'available')->count(),
            'faulty' => Meter::where('status', 'faulty')->count(),
        ];

        $categories = MeterCategory::active()->ordered()->withCount('meters')->get();

        return view('admin.meters.index', compact('meters', 'stats', 'categories'));
    }

    public function available()
    {
        $meters = Meter::available()
            ->with(['customer', 'meterCategory', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('admin.meters.available', compact('meters'));
    }

    public function assigned()
    {
        $meters = Meter::assigned()
            ->with(['customer', 'meterCategory', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('admin.meters.assigned', compact('meters'));
    }

    public function byLocation(Request $request)
    {
        $location = $request->get('location', '');
        $meters = Meter::with(['customer', 'meterCategory', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->when($location, function($query) use ($location) {
                return $query->where('installation_address', 'like', "%{$location}%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.meters.by-location', compact('meters', 'location'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meter_number' => 'required|string|max:50|unique:meters,meter_number',
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
            'meter_category_id' => 'required|exists:meter_categories,id',
            'meter_model' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'installation_address' => 'nullable|string|max:500',
            'installation_date' => 'nullable|date',
            'initial_reading' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'connection_fee' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'balance_bf' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Get category to set default fees
                $category = MeterCategory::find($validated['meter_category_id']);
                $additionalCharges = $category->additional_charges ?? [];

                // Determine status based on customer assignment
                $validated['status'] = $validated['customer_id'] ? 'assigned' : 'available';

                // Set default fees from category if not provided
                $validated['installation_fee'] = $validated['installation_fee'] ?? ($additionalCharges['installation_fee'] ?? 0);
                $validated['connection_fee'] = $validated['connection_fee'] ?? ($additionalCharges['connection_fee'] ?? 0);
                $validated['deposit_amount'] = $validated['deposit_amount'] ?? ($additionalCharges['deposit'] ?? 0);
                $validated['current_balance'] = $validated['balance_bf'] ?? 0;

                // Create meter
                $meter = Meter::create($validated);

                // If assigned to customer, update customer record and create initial reading
                if ($validated['customer_id']) {
                    $customer = Customer::find($validated['customer_id']);
                    
                    // Update customer with meter info
                    $customer->update([
                        'meter_number' => $meter->meter_number,
                        'meter_type' => $meter->meter_type,
                        'initial_meter_reading' => $validated['initial_reading'],
                        'initial_reading_date' => $validated['installation_date'] ?? now(),
                    ]);

                    // Create initial meter reading with customer_id
                    MeterReading::create([
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'current_reading' => $validated['initial_reading'],
                        'previous_reading' => 0,
                        'consumption' => $validated['initial_reading'],
                        'reading_date' => $validated['installation_date'] ?? now(),
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading upon installation',
                    ]);
                } else {
                    // Create initial reading for unassigned meter WITHOUT customer_id
                    MeterReading::create([
                        'meter_id' => $meter->id,
                        'current_reading' => $validated['initial_reading'],
                        'previous_reading' => 0,
                        'consumption' => $validated['initial_reading'],
                        'reading_date' => $validated['installation_date'] ?? now(),
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading for unassigned meter',
                    ]);
                }
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $validated['customer_id'] ? 'Meter registered and assigned to customer successfully!' : 'Meter registered successfully!',
                ]);
            }

            return redirect()->route('admin.meters.index')
                ->with('success', $validated['customer_id'] ? 'Meter registered and assigned to customer successfully!' : 'Meter registered successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error registering meter: ' . $e->getMessage());
        }
    }

    public function show(Meter $meter)
    {
        $meter->load(['customer', 'meterCategory', 'meterReadings' => function($query) {
            $query->latest()->limit(10);
        }, 'bills' => function($query) {
            $query->latest()->limit(5);
        }]);

        return view('admin.meters.show', compact('meter'));
    }

    public function edit(Meter $meter)
    {
        $meterTypes = [
            'domestic' => 'Domestic',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'institutional' => 'Institutional',
            'smart' => 'Smart Meter',
            'mechanical' => 'Mechanical',
        ];

        $categories = MeterCategory::active()->ordered()->get();
        $customers = Customer::active()->get();
        $statuses = [
            'available' => 'Available',
            'assigned' => 'Assigned',
            'faulty' => 'Faulty',
            'maintenance' => 'Maintenance',
        ];

        return view('admin.meters.edit', compact('meter', 'meterTypes', 'categories', 'customers', 'statuses'));
    }

    public function update(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'meter_number' => 'required|string|max:50|unique:meters,meter_number,' . $meter->id,
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
            'meter_category_id' => 'required|exists:meter_categories,id',
            'meter_model' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'installation_address' => 'nullable|string|max:500',
            'installation_date' => 'nullable|date',
            'status' => 'required|string|in:available,assigned,faulty,maintenance',
            'initial_reading' => 'nullable|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'connection_fee' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'balance_bf' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update current balance if balance brought forward changes
        if ($validated['balance_bf'] != $meter->balance_bf) {
            $validated['current_balance'] = $validated['balance_bf'];
        }

        $meter->update($validated);

        return redirect()->route('admin.meters.show', $meter)
            ->with('success', 'Meter updated successfully!');
    }

   
public function getAvailableMeters(Request $request)
{
    $categoryId = $request->get('category_id');
    
    $meters = Meter::where('status', 'available')
        ->when($categoryId, function($query) use ($categoryId) {
            return $query->where('meter_category_id', $categoryId);
        })
        ->with('meterCategory')
        ->get()
        ->map(function($meter) {
            return [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'meter_type' => $meter->meter_type,
                'meter_model' => $meter->meter_model,
                'initial_reading' => $meter->initial_reading,
                'category_name' => $meter->meterCategory->name ?? 'No Category'
            ];
        });
    
    return response()->json($meters);
}
}