<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use App\Models\Customer;
use App\Models\MeterCategory;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        $filter = $request->get('filter', 'all'); // all, available, assigned, location

        $query = Meter::with(['customer', 'meterCategory', 'meterReadings' => function($query) {
            $query->latest()->limit(1);
        }]);

        // Apply filters based on the filter parameter
        switch ($filter) {
            case 'available':
                $query->where('status', 'available')->whereNull('customer_id');
                break;
            case 'assigned':
                $query->where('status', 'assigned')->whereNotNull('customer_id');
                break;
            case 'location':
                // For location filter, we'll handle it separately in the view
                break;
            default:
                // 'all' - no additional filters
                break;
        }

        // Category filter
        if ($categoryId) {
            $query->where('meter_category_id', $categoryId);
        }

        // Location search for location filter
        if ($filter === 'location' && $request->filled('location')) {
            $location = $request->get('location');
            $query->where(function($q) use ($location) {
                $q->where('installation_address', 'like', "%{$location}%")
                  ->orWhereHas('customer', function($q) use ($location) {
                      $q->where('estate', 'like', "%{$location}%")
                        ->orWhere('plot_number', 'like', "%{$location}%")
                        ->orWhere('house_number', 'like', "%{$location}%");
                  });
            });
        }

        $meters = $query->latest()->paginate(20);

        $stats = [
            'total' => Meter::count(),
            'assigned' => Meter::where('status', 'assigned')->whereNotNull('customer_id')->count(),
            'unassigned' => Meter::where('status', 'available')->whereNull('customer_id')->count(),
            'faulty' => Meter::where('status', 'faulty')->count(),
        ];

        $categories = MeterCategory::active()->ordered()->withCount('meters')->get();

        return view('admin.meters.index', compact('meters', 'stats', 'categories', 'filter'));
    }

    // Remove the separate available(), assigned(), byLocation() methods since we're handling everything in index()

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
                $validated['status'] = $validated['customer_id'] ? 'active' : 'available';

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

        $categories = MeterCategory::all();

        return view('admin.meters.show', compact('meter', 'categories'));
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
            'active' => 'Active',
            'sealed' => 'Sealed',
            'pending_payment' => 'Pending payment',
            'terminated' => 'Terminated',
            'maintenance' => 'Maintenance',
        ];

        return view('admin.meters.edit', compact('meter', 'meterTypes', 'categories', 'customers', 'statuses'));
    }

    // public function update(Request $request, Meter $meter)
    // {
    //     $validated = $request->validate([
    //         'meter_number' => 'required|string|max:50|unique:meters,meter_number,' . $meter->id,
    //         'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
    //         'meter_category_id' => 'required|exists:meter_categories,id',
    //         'meter_model' => 'nullable|string|max:100',
    //         'customer_id' => 'nullable|exists:customers,id',
    //         'installation_address' => 'nullable|string|max:500',
    //         'installation_date' => 'nullable|date',
    //         'status' => 'required|string|in:available,assigned,faulty,maintenance',
    //         'initial_reading' => 'nullable|numeric|min:0',
    //         'installation_fee' => 'nullable|numeric|min:0',
    //         'connection_fee' => 'nullable|numeric|min:0',
    //         'deposit_amount' => 'nullable|numeric|min:0',
    //         'balance_bf' => 'nullable|numeric|min:0',
    //         'notes' => 'nullable|string|max:1000',
    //     ]);

    //     // Update current balance if balance brought forward changes
    //     if ($validated['balance_bf'] != $meter->balance_bf) {
    //         $validated['current_balance'] = $validated['balance_bf'];
    //     }

    //     $meter->update($validated);

    //     return redirect()->route('admin.meters.show', $meter)
    //         ->with('success', 'Meter updated successfully!');
    // }

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

    /**
     * Get customer address for auto-filling installation address
     */
    public function getCustomerAddress($customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return response()->json(['address' => '']);
        }

        $address = $customer->plot_number . ', ' . $customer->house_number;
        if ($customer->estate) {
            $address .= ', ' . $customer->estate;
        }

        return response()->json(['address' => $address]);
    }

    public function getJson(Meter $meter)
    {
        Log::info('Fetching meter JSON data for meter ID: ' . $meter->id);

        return response()->json([
            'id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'meter_type' => $meter->meter_type,
            'meter_category_id' => $meter->meter_category_id,
            'meter_model' => $meter->meter_model,
            'manufacturer' => $meter->manufacturer,
            'latitude' => $meter->latitude,
            'longtitude' => $meter->longtitude,
            'status' => $meter->status,
            'customer_id' => $meter->customer_id,
            'installation_address' => $meter->installation_address,
            'installation_date' => $meter->installation_date ? $meter->installation_date->format('Y-m-d') : null, // Format as Y-m-d
            'last_maintenance_date' => $meter->last_maintenance_date ? $meter->last_maintenance_date->format('Y-m-d') : null, // Format as Y-m-d
            'initial_reading' => (float) $meter->initial_reading,
            'installation_fee' => (float) $meter->installation_fee,
            'connection_fee' => (float) $meter->connection_fee,
            'deposit_amount' => (float) $meter->deposit_amount,
            'balance_bf' => (float) $meter->balance_bf,
            'current_balance' => (float) $meter->current_balance,
            'additional_charges' => $meter->additional_charges,
            'notes' => $meter->notes,
            'zone_id' => $meter->zone_id,
            'walk_route_id' => $meter->walk_route_id,
        ]);
    }

    public function update(Request $request, Meter $meter)
    {
        Log::info('Updating meter ID: ' . $meter->id, $request->all());

        $validated = $request->validate([
            'meter_number' => 'required|string|max:50|unique:meters,meter_number,' . $meter->id,
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
            'meter_category_id' => 'required|exists:meter_categories,id',
            'meter_model' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|string|in:available,active,sealed,terminated,maintenance',
            'customer_id' => 'nullable|exists:customers,id',
            'installation_address' => 'nullable|string|max:500',
            'installation_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'initial_reading' => 'nullable|numeric|min:0',
            'balance_bf' => 'nullable|numeric',
            'current_balance' => 'nullable|numeric',
            'additional_charges' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'zone_id' => 'nullable|exists:zones,id',
            'walk_route_id' => 'nullable|exists:walk_routes,id',
        ]);

        Log::info('Validation passed for meter ID: ' . $meter->id);

        // Handle the 'longitude' to 'longtitude' mapping
        if (isset($validated['longitude'])) {
            $validated['longtitude'] = $validated['longitude'];
            unset($validated['longitude']);
        }

        // Update the meter
        $meter->update($validated);

        Log::info('Meter updated successfully: ' . $meter->id);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Meter updated successfully!',
                'meter' => $meter
            ]);
        }

        return redirect()->route('admin.meters.show', $meter)
            ->with('success', 'Meter updated successfully!');
    }
}
