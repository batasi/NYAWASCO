<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use App\Models\WaterConnectionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        
        $customers = Customer::with(['meter.meterCategory', 'bills', 'payments'])
            ->when($status && $status !== 'all', function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('customer_number', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhereHas('meter', function($meterQuery) use ($search) {
                          $meterQuery->where('meter_number', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(20);

        $statusCounts = [
            'all' => Customer::count(),
            'new' => Customer::new()->count(),
            'active' => Customer::active()->count(),
            'pending_payment' => Customer::pendingPayment()->count(),
            'sealed' => Customer::sealed()->count(),
            'terminated' => Customer::terminated()->count(),
        ];

        return view('admin.customers.index', compact('customers', 'statusCounts', 'status', 'search'));
    }

    public function create()
    {
        $categories = MeterCategory::active()->ordered()->get();
        $availableMeters = Meter::available()->with('meterCategory')->get();
        
        return view('admin.customers.create', compact('categories', 'availableMeters'));
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:customers,id_number',
            'physical_address' => 'required|string|max:500',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'required|string|max:50',
            'estate' => 'nullable|string|max:100',
            'connection_type' => 'required|string|in:residential,commercial,industrial,public',
            'kra_pin' => 'nullable|string|max:20',
            'property_owner' => 'required|string|max:100',
            'expected_users' => 'nullable|integer|min:1',
            'balance_bf' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:new,active,pending_payment,sealed,terminated',
            'meter_id' => 'nullable|exists:meters,id',
            'connection_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create customer
                $customer = Customer::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'id_number' => $validated['id_number'],
                    'physical_address' => $validated['physical_address'],
                    'plot_number' => $validated['plot_number'],
                    'house_number' => $validated['house_number'],
                    'estate' => $validated['estate'] ?? null,
                    'connection_type' => $validated['connection_type'],
                    'kra_pin' => $validated['kra_pin'] ?? null,
                    'property_owner' => $validated['property_owner'],
                    'expected_users' => $validated['expected_users'] ?? null,
                    'balance_bf' => $validated['balance_bf'] ?? 0,
                    'current_balance' => $validated['balance_bf'] ?? 0,
                    'status' => $validated['status'],
                    'connection_date' => $validated['connection_date'] ?? now(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Assign meter if provided
                if (!empty($validated['meter_id'])) {
                    $meter = Meter::findOrFail($validated['meter_id']);
                    
                    $meter->update([
                        'customer_id' => $customer->id,
                        'status' => 'assigned',
                        'installation_address' => $customer->full_address,
                        'installation_date' => $customer->connection_date,
                    ]);

                    // Update customer with meter info
                    $customer->update([
                        'meter_number' => $meter->meter_number,
                        'meter_type' => $meter->meter_type,
                        'initial_meter_reading' => $meter->initial_reading,
                    ]);

                    // Create initial meter reading
                    if ($meter->initial_reading > 0) {
                        \App\Models\MeterReading::create([
                            'customer_id' => $customer->id,
                            'meter_id' => $meter->id,
                            'current_reading' => $meter->initial_reading,
                            'previous_reading' => 0,
                            'consumption' => $meter->initial_reading,
                            'reading_date' => $customer->connection_date,
                            'reading_type' => 'initial',
                            'reading_period' => 'Initial Installation',
                            'billed' => false,
                            'read_by' => auth()->id(),
                            'notes' => 'Initial meter reading upon customer creation',
                        ]);
                    }
                }
            });

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'meter.meterCategory', 
            'meterReadings' => function($query) {
                $query->latest()->limit(10);
            },
            'bills' => function($query) {
                $query->latest()->limit(10);
            },
            'payments' => function($query) {
                $query->latest()->limit(10);
            },
            'waterApplication'
        ]);

        // Calculate billing statistics
        $billingStats = [
            'total_bills' => $customer->bills->count(),
            'unpaid_bills' => $customer->bills()->where('bill_status', 'unpaid')->count(),
            'paid_bills' => $customer->bills()->where('bill_status', 'paid')->count(),
            'account_balance' => $customer->account_balance,
            'outstanding_balance' => $customer->outstanding_balance,
            'arrears' => $customer->arrears,
        ];

        // Reading statistics
        $readingStats = [
            'total_readings' => $customer->meterReadings->count(),
            'total_consumption' => $customer->total_consumption,
            'average_monthly_consumption' => $customer->average_monthly_consumption,
        ];

        // Recent activity (combine bills and payments)
        $recentActivity = collect();
        
        // Add bills as activity
        foreach ($customer->bills->take(5) as $bill) {
            $recentActivity->push([
                'type' => 'bill',
                'description' => 'Bill generated: ' . $bill->bill_number,
                'amount' => $bill->total_amount,
                'date' => $bill->created_at,
                'status' => $bill->bill_status,
                'color' => $bill->bill_status === 'paid' ? 'green' : 'red',
                'icon' => 'file-invoice',
            ]);
        }

        // Add payments as activity
        foreach ($customer->payments->take(5) as $payment) {
            $recentActivity->push([
                'type' => 'payment',
                'description' => 'Payment received: ' . $payment->payment_method,
                'amount' => $payment->amount,
                'date' => $payment->payment_date,
                'status' => 'completed',
                'color' => 'green',
                'icon' => 'credit-card',
            ]);
        }

        // Sort by date and take latest 8
        $recentActivity = $recentActivity->sortByDesc('date')->take(8);

        $availableMeters = Meter::available()->with('meterCategory')->get();
        $categories = MeterCategory::active()->ordered()->get();

        return view('admin.customers.show', compact(
            'customer', 
            'billingStats', 
            'readingStats', 
            'recentActivity',
            'availableMeters',
            'categories'
        ));
    }

    public function edit(Customer $customer)
    {
        $categories = MeterCategory::active()->ordered()->get();
        $availableMeters = Meter::available()->with('meterCategory')->get();
        $assignedMeter = $customer->meter;

        return view('admin.customers.edit', compact(
            'customer', 
            'categories', 
            'availableMeters',
            'assignedMeter'
        ));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:customers,id_number,' . $customer->id,
            'physical_address' => 'required|string|max:500',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'required|string|max:50',
            'estate' => 'nullable|string|max:100',
            'connection_type' => 'required|string|in:residential,commercial,industrial,public',
            'kra_pin' => 'nullable|string|max:20',
            'property_owner' => 'required|string|max:100',
            'expected_users' => 'nullable|integer|min:1',
            'balance_bf' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:new,active,pending_payment,sealed,terminated',
            'connection_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $customer->update($validated);

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', 'Customer updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has any bills or payments
            if ($customer->bills()->exists() || $customer->payments()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete customer with billing history. Please archive instead.');
            }

            // Unassign meter if assigned
            if ($customer->meter) {
                $customer->meter->update([
                    'customer_id' => null,
                    'status' => 'available',
                    'installation_address' => null,
                ]);
            }

            $customer->delete();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    // Status Management Methods
    public function updateStatus(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,active,pending_payment,sealed,terminated',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $oldStatus = $customer->status;
            $newStatus = $validated['status'];

            // Check if activation requirements are met
            if ($newStatus === 'active' && !$customer->canBeActivated()) {
                $requirements = $customer->getActivationRequirements();
                return redirect()->back()
                    ->with('error', 'Cannot activate customer. Requirements not met: ' . implode(', ', $requirements));
            }

            $customer->update([
                'status' => $newStatus,
                'notes' => $customer->notes . "\nStatus changed from {$oldStatus} to {$newStatus} on " . now()->format('Y-m-d H:i:s') . ($validated['notes'] ? "\nNote: " . $validated['notes'] : ''),
            ]);

            return redirect()->back()
                ->with('success', "Customer status updated from {$oldStatus} to {$newStatus} successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating customer status: ' . $e->getMessage());
        }
    }

    // Meter Assignment Methods
    public function assignMeter(Request $request, Customer $customer)
    {
        $request->validate([
            'meter_id' => 'required|exists:meters,id',
            'initial_reading' => 'required|numeric|min:0',
            'balance_bf' => 'nullable|numeric',
            'installation_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Find the meter
        $meter = Meter::findOrFail($request->meter_id);

        // Check if meter is already assigned to another customer
        if ($meter->customer_id && $meter->customer_id !== $customer->id) {
            return back()->with('error', 'This meter is already assigned to another customer.');
        }

        // Check if this exact meter is already assigned to this customer
        if ($meter->customer_id === $customer->id) {
            return back()->with('error', 'This meter is already assigned to this customer.');
        }

        // Update the meter
        $meter->update([
            'customer_id' => $customer->id,
            'installation_address' => $customer->physical_address,
            'installation_date' => $request->installation_date,
            'initial_reading' => $request->initial_reading,
            'current_reading' => $request->initial_reading,
            'balance_bf' => $request->balance_bf ?? 0,
            'current_balance' => $request->balance_bf ?? 0,
            'status' => 'assigned',
            'notes' => $request->notes
        ]);

        // Update customer's current balance if this is the first meter
        if ($customer->meters()->count() === 1) {
            $customer->update([
                'current_balance' => $request->balance_bf ?? 0
            ]);
        }

        return back()->with('success', 'Meter assigned successfully!');
    }

    public function unassignMeter(Customer $customer)
    {
        try {
            DB::transaction(function () use ($customer) {
                $meter = $customer->meter;

                if (!$meter) {
                    throw new \Exception('Customer does not have a meter assigned.');
                }

                // Update meter
                $meter->update([
                    'customer_id' => null,
                    'status' => 'available',
                    'installation_address' => null,
                    'installation_date' => null,
                ]);

                // Update customer
                $customer->update([
                    'meter_number' => null,
                    'meter_type' => null,
                    'initial_meter_reading' => 0,
                    'notes' => $customer->notes . "\nMeter unassigned: " . $meter->meter_number . " on " . now()->format('Y-m-d H:i:s'),
                ]);
            });

            return redirect()->back()
                ->with('success', 'Meter unassigned from customer successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error unassigning meter: ' . $e->getMessage());
        }
    }

    // API Methods for AJAX
public function getAvailableMeters(Request $request)
{
    try {
        $categoryId = $request->get('category_id');
        
        Log::info('Fetching meters for category: ' . $categoryId);
        
        // Query for available meters - limit to 5
        $query = Meter::where('status', 'available')
            ->whereNull('customer_id');
            
        if ($categoryId) {
            $query->where('meter_category_id', $categoryId);
        }
        
        $meters = $query->with('meterCategory')
            ->limit(5)
            ->get();

        Log::info('Found ' . $meters->count() . ' meters');
        
        $formattedMeters = $meters->map(function($meter) {
            return [
                'id' => $meter->id,
                'meter_number' => $meter->meter_number,
                'meter_type' => $meter->meter_type ?? 'N/A',
                'meter_model' => $meter->meter_model ?? 'N/A',
                'category_name' => $meter->meterCategory->name ?? 'No Category',
                'initial_reading' => $meter->initial_reading ?? 0,
            ];
        });

        return response()->json($formattedMeters);
        
    } catch (\Exception $e) {
        Log::error('Error in getAvailableMeters: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function getCustomerAddress(Customer $customer)
    {
        return response()->json([
            'address' => $customer->physical_address
        ]);
    }


    public function getCustomerMeters(Customer $customer)
{
    $meters = $customer->meters()->with('meterCategory')->get()->map(function($meter) {
        return [
            'id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'meter_type' => $meter->meter_type,
            'meter_model' => $meter->meter_model,
            'status' => $meter->status,
            'current_reading' => (float) $meter->current_reading,
            'initial_reading' => (float) $meter->initial_reading,
            'category_name' => $meter->meterCategory->name ?? 'No Category',
            'total_consumption' => $meter->current_reading - $meter->initial_reading
        ];
    });

    return response()->json($meters);
}
}