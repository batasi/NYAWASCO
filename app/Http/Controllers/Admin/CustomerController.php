<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use App\Models\WaterConnectionApplication;
use App\Models\MeterReading;
use App\Models\Zone;
use App\Models\Bill;
use App\Models\WalkRoute;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $customers = Customer::with(['meters.meterCategory', 'bills', 'payments'])
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
                      ->orWhereHas('meters', function($meterQuery) use ($search) {
                          $meterQuery->where('meter_number', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(20);

        // Update status counts to match database enum
        $statusCounts = [
            'all' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'inactive' => Customer::where('status', 'inactive')->count(),
            'pending' => Customer::where('status', 'pending')->count(),
            'suspended' => Customer::where('status', 'suspended')->count(),
        ];

        $categories = MeterCategory::active()->ordered()->get();

        return view('admin.customers.index', compact('customers', 'statusCounts', 'status', 'search', 'categories'));
    }

    public function create()
    {
        $categories = MeterCategory::active()->ordered()->with(['meters' => function($query) {
            $query->available();
        }])->get();

        $zones = Zone::with('walkRoutes')->get();
        $walkRoutes = WalkRoute::with('zone')->orderBy('zone_id')->orderBy('route_order')->get();

        return view('admin.customers.create', compact('categories', 'zones', 'walkRoutes'));
    }

    public function store(Request $request)
    {
        // Enhanced validation with comprehensive rules
        $validated = $request->validate([
            // Personal Information
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'id_number' => 'required|string|max:20|unique:customers,id_number',
            'kra_pin' => 'nullable|string|max:20|unique:customers,kra_pin',

            // Property Information
            'physical_address' => 'required|string|max:500',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'required|string|max:50',
            'estate' => 'nullable|string|max:100',
            'property_owner' => 'required|string|max:100',
            'expected_users' => 'nullable|integer|min:1|max:1000',

            // Meter Information
            'meter_id' => 'nullable|exists:meters,id',
            'meter_category_id' => 'nullable|exists:meter_categories,id',
            'meter_number' => 'nullable|required_without:meter_id|string|max:50|unique:meters,meter_number',
            'meter_type' => 'nullable|required_with:meter_number|string|in:domestic,commercial,industrial',
            'meter_model' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'initial_reading' => 'nullable|numeric|min:0',

            // Zone and Route Information
            'zone_id' => 'nullable|exists:zones,id',
            'walk_route_id' => 'nullable|exists:walk_routes,id',

            // Financial Information
            'installation_fee' => 'nullable|numeric|min:0',
            'connection_fee' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'balance_bf' => 'nullable|numeric',

            // Account Information
            'status' => 'required|string|in:active,inactive,pending,suspended',
            'status_reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'phone.unique' => 'This phone number is already registered.',
            'id_number.unique' => 'This ID number is already registered.',
            'kra_pin.unique' => 'This KRA PIN is already registered.',
            'meter_number.required_without' => 'Meter number is required when creating a new meter.',
            'meter_number.unique' => 'This meter number already exists.',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Generate unique customer number
                $customerNumber = $this->generateCustomerNumber();

                // Generate bill number if applicable
                $billNumber = null;
                if ($request->has('generate_initial_bill') && $request->input('generate_initial_bill') == '1') {
                    $billNumber = $this->generateBillNumber();
                }

                // Prepare customer data
                $customerData = [
                    'customer_number' => $customerNumber,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? null,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'id_number' => $validated['id_number'],
                    'physical_address' => $validated['physical_address'],
                    'plot_number' => $validated['plot_number'],
                    'house_number' => $validated['house_number'],
                    'estate' => $validated['estate'] ?? null,
                    'kra_pin' => $validated['kra_pin'] ?? null,
                    'property_owner' => $validated['property_owner'],
                    'expected_users' => $validated['expected_users'] ?? null,
                    'status' => $validated['status'],
                    'status_reason' => $validated['status_reason'] ?? null,
                    'status_updated_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                ];

                // Create customer
                $customer = Customer::create($customerData);

                // Handle meter assignment or creation
                $meter = null;
                if (!empty($validated['meter_id'])) {
                    // Assign existing meter
                    $meter = Meter::findOrFail($validated['meter_id']);
                    $meter->update([
                        'customer_id' => $customer->id,
                        'status' => Meter::STATUS_ACTIVE,
                        'installation_address' => $customer->physical_address,
                        'installation_date' => now(),
                        'zone_id' => $validated['zone_id'] ?? null,
                        'walk_route_id' => $validated['walk_route_id'] ?? null,
                        'balance_bf' => $validated['balance_bf'] ?? 0,
                        'current_balance' => $validated['balance_bf'] ?? 0,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                } elseif (!empty($validated['meter_number'])) {
                    // Create new meter
                    $meter = Meter::create([
                        'meter_number' => $validated['meter_number'],
                        'meter_type' => $validated['meter_type'] ?? 'domestic',
                        'meter_category_id' => $validated['meter_category_id'],
                        'meter_model' => $validated['meter_model'] ?? null,
                        'manufacturer' => $validated['manufacturer'] ?? null,
                        'status' => Meter::STATUS_ACTIVE,
                        'customer_id' => $customer->id,
                        'installation_address' => $customer->physical_address,
                        'installation_date' => now(),
                        'initial_reading' => $validated['initial_reading'] ?? 0,
                        'current_reading' => $validated['initial_reading'] ?? 0,
                        'zone_id' => $validated['zone_id'] ?? null,
                        'walk_route_id' => $validated['walk_route_id'] ?? null,
                        'balance_bf' => $validated['balance_bf'] ?? 0,
                        'current_balance' => $validated['balance_bf'] ?? 0,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }

                // Create initial meter reading if meter exists
                if ($meter && $meter->initial_reading > 0) {
                    MeterReading::create([
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'current_reading' => $meter->initial_reading,
                        'previous_reading' => 0,
                        'consumption' => $meter->initial_reading,
                        'reading_date' => now(),
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading upon customer creation',
                    ]);
                }

                // Generate initial bill if requested
                if ($billNumber && $meter) {
                    $meterCategory = $meter->meterCategory;

                    // Calculate initial charges
                    $baseCharge = $meterCategory->base_charge ?? 0;
                    $meterRent = $meterCategory->meter_rent ?? 0;
                    $depositAmount = $meterCategory->deposit_amount ?? 0;

                    // Check if installation and connection fees were paid
                    $installationFee = ($request->input('installation_fee_paid') == '1') ? 0 : ($meterCategory->installation_fee ?? 0);
                    $connectionFee = ($request->input('connection_fee_paid') == '1') ? 0 : ($meterCategory->connection_fee ?? 0);

                    $totalAmount = $baseCharge + $meterRent + $depositAmount + $installationFee + $connectionFee;

                    if ($totalAmount > 0) {
                        Bill::create([
                            'customer_id' => $customer->id,
                            'meter_id' => $meter->id,
                            'bill_number' => $billNumber,
                            'billing_period_start' => now(),
                            'billing_period_end' => now()->addMonth(),
                            'consumption' => 0,
                            'base_charge' => $baseCharge,
                            'consumption_charge' => 0,
                            'tax_amount' => 0,
                            'late_fee' => 0,
                            'total_amount' => $totalAmount,
                            'paid_amount' => 0,
                            'due_date' => now()->addDays(30),
                            'bill_status' => 'unpaid',
                            'notes' => 'Initial connection bill',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }

                // Log the customer creation
                Log::info('Customer created successfully', [
                    'customer_id' => $customer->id,
                    'customer_number' => $customerNumber,
                    'meter_assigned' => $meter ? $meter->meter_number : 'none',
                    'initial_bill' => $billNumber ?? 'not generated',
                    'created_by' => auth()->id(),
                ]);
            });

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Customer creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    private function generateCustomerNumber()
    {
        $year = date('Y');
        $prefix = 'CUST';

        // Check for existing customer numbers
        $lastCustomer = Customer::where('customer_number', 'like', "{$prefix}{$year}%")
            ->orderBy('customer_number', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = (int) substr($lastCustomer->customer_number, strlen($prefix) + 4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$year}{$nextNumber}";
    }

    private function generateBillNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'INV';

        $lastBill = Bill::where('bill_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('bill_number', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->bill_number, strlen($prefix) + 6);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return "{$prefix}{$year}{$month}{$nextNumber}";
    }

    // Additional API method for checking meter availability
    public function checkMeterAvailability(Request $request)
    {
        try {
            $meterNumber = $request->get('meter_number');

            if (empty($meterNumber)) {
                return response()->json(['available' => false, 'message' => 'Meter number is required']);
            }

            $exists = Meter::where('meter_number', $meterNumber)->exists();

            return response()->json([
                'available' => !$exists,
                'message' => $exists ? 'Meter number already exists' : 'Meter number is available'
            ]);

        } catch (\Exception $e) {
            Log::error('Meter availability check error: ' . $e->getMessage());
            return response()->json(['available' => false, 'message' => 'Error checking meter availability'], 500);
        }
    }

    // Method to get meter category details
    public function getMeterCategoryDetails($id)
    {
        try {
            $category = MeterCategory::findOrFail($id);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'installation_fee' => $category->installation_fee,
                    'connection_fee' => $category->connection_fee,
                    'deposit_amount' => $category->deposit_amount,
                    'base_charge' => $category->base_charge,
                    'meter_rent' => $category->meter_rent,
                    'default_rate' => $category->default_rate,
                    'has_tiers' => $category->has_tiers,
                    'additional_charges' => $category->additional_charges,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get meter category details error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }


    public function show(Customer $customer)
    {
        $customer->load([
            'meters.meterCategory',
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

        // Calculate billing statistics from meters
        $accountBalance = $customer->meters->sum('current_balance');

        $billingStats = [
            'total_bills' => $customer->bills->count(),
            'unpaid_bills' => $customer->bills()->where('bill_status', 'unpaid')->count(),
            'paid_bills' => $customer->bills()->where('bill_status', 'paid')->count(),
            'account_balance' => $accountBalance,
            'outstanding_balance' => $customer->bills()->where('bill_status', '!=', 'paid')->sum('total_amount'),
            'arrears' => $customer->bills()->overdue()->sum('total_amount'),
        ];

        // Reading statistics
        $readingStats = [
            'total_readings' => $customer->meterReadings->count(),
            'total_consumption' => $customer->meterReadings->sum('consumption'),
            'average_monthly_consumption' => $customer->meterReadings->count() > 0 ?
                $customer->meterReadings->sum('consumption') / max(1, $customer->meterReadings->count()) : 0,
        ];

        // Recent activity
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

        // Add payments from bills
        foreach ($customer->bills as $bill) {
            foreach ($bill->payments->take(2) as $payment) {
                $recentActivity->push([
                    'type' => 'payment',
                    'description' => 'Payment for bill: ' . $bill->bill_number,
                    'amount' => $payment->amount,
                    'date' => $payment->payment_date,
                    'status' => 'completed',
                    'color' => 'green',
                    'icon' => 'credit-card',
                ]);
            }
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
        $assignedMeters = $customer->meters;

        return view('admin.customers.edit', compact(
            'customer',
            'categories',
            'availableMeters',
            'assignedMeters'
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
            'kra_pin' => 'nullable|string|max:20',
            'property_owner' => 'required|string|max:100',
            'expected_users' => 'nullable|integer|min:1',
            'status' => 'required|string|in:active,inactive,pending,suspended',
            'status_reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Check if status is being changed
            if ($customer->status !== $validated['status']) {
                $validated['status_updated_at'] = now();
            }

            $customer->update($validated);

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', 'Customer updated successfully!');

        } catch (\Exception $e) {
            Log::error('Customer update error: ' . $e->getMessage());
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

            // Unassign all meters
            foreach ($customer->meters as $meter) {
                $meter->update([
                    'customer_id' => null,
                    'status' => 'available',
                    'installation_address' => null,
                ]);
            }

            $customer->delete();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Customer deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Customer $customer)
    {
        Log::info('=== UPDATE STATUS CALLED ===', [
            'customer_id' => $customer->id,
            'current_status' => $customer->status,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive,pending,suspended',
            'notes' => 'required|string|max:500',
            'status_reason' => 'required|string|max:255',
        ]);

        try {
            $oldStatus = $customer->status;
            $newStatus = $validated['status'];

            Log::info('Attempting status update', [
                'customer_id' => $customer->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Build status note
            $statusNote = "Status changed from {$oldStatus} to {$newStatus} on " . now()->format('Y-m-d H:i:s');

            // Add reason
            if ($validated['status_reason']) {
                $statusNote .= "\nReason: " . $validated['status_reason'];
            }

            // Add admin notes
            if ($validated['notes']) {
                $statusNote .= "\nNotes: " . $validated['notes'];
            }

            // Update customer
            $customer->update([
                'status' => $newStatus,
                'status_reason' => $validated['status_reason'],
                'status_notes' => $validated['notes'],
                'status_updated_at' => now(),
                'notes' => $customer->notes . "\n" . $statusNote,
            ]);

            Log::info('Status update successful', [
                'customer_id' => $customer->id,
                'old_status' => $oldStatus,
                'new_status' => $customer->fresh()->status,
                'status_reason' => $validated['status_reason']
            ]);

            return redirect()->back()
                ->with('success', "Customer status updated from {$oldStatus} to {$newStatus} successfully!");

        } catch (\Exception $e) {
            Log::error('Customer status update error: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
                'request_data' => $request->all()
            ]);
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

        try {
            DB::transaction(function () use ($request, $customer) {
                // Find the meter
                $meter = Meter::findOrFail($request->meter_id);

                // Check if meter is available
                if ($meter->status !== Meter::STATUS_AVAILABLE) {
                    throw new \Exception('Meter is not available for assignment. Current status: ' . $meter->status);
                }

                // Update meter to active status
                $meter->update([
                    'customer_id' => $customer->id,
                    'installation_address' => $customer->physical_address,
                    'installation_date' => $request->installation_date,
                    'initial_reading' => $request->initial_reading,
                    'balance_bf' => $request->balance_bf ?? 0,
                    'current_balance' => $request->balance_bf ?? 0,
                    'status' => Meter::STATUS_ACTIVE, // Changed to 'active'
                    'notes' => $request->notes
                ]);

                // Update customer status to active if it's pending
                if ($customer->status === Customer::STATUS_PENDING) {
                    $customer->update([
                        'status' => Customer::STATUS_ACTIVE,
                        'status_updated_at' => now(),
                        'status_reason' => 'Meter assigned',
                    ]);
                }

                // Update customer meter info
                $customer->update([
                    'meter_number' => $meter->meter_number,
                    'meter_type' => $meter->meter_type,
                    'initial_meter_reading' => $request->initial_reading,
                    'initial_reading_date' => $request->installation_date,
                ]);

                // Create initial meter reading
                MeterReading::create([
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'current_reading' => $request->initial_reading,
                    'previous_reading' => 0,
                    'consumption' => $request->initial_reading,
                    'reading_date' => $request->installation_date,
                    'reading_type' => 'initial',
                    'reading_period' => 'Initial Installation',
                    'billed' => false,
                    'read_by' => auth()->id(),
                    'notes' => 'Initial meter reading upon assignment',
                ]);
            });

            return back()->with('success', 'Meter assigned successfully and customer status updated!');

        } catch (\Exception $e) {
            Log::error('Meter assignment error: ' . $e->getMessage());
            return back()->with('error', 'Error assigning meter: ' . $e->getMessage());
        }
    }

    public function unassignMeter(Request $request, Customer $customer, Meter $meter)
    {
        try {
            DB::transaction(function () use ($customer, $meter) {
                if ($meter->customer_id !== $customer->id) {
                    throw new \Exception('Meter is not assigned to this customer.');
                }

                // Update meter status to available
                $meter->update([
                    'customer_id' => null,
                    'status' => Meter::STATUS_AVAILABLE, // Changed to 'available'
                    'installation_address' => null,
                    'installation_date' => null,
                ]);

                // Customer status will be auto-synced via the model observer
            });

            return redirect()->back()
                ->with('success', 'Meter unassigned from customer successfully!');

        } catch (\Exception $e) {
            Log::error('Meter unassignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error unassigning meter: ' . $e->getMessage());
        }
    }

    public function unassignAllMeters(Request $request, Customer $customer)
    {
        try {
            DB::transaction(function () use ($customer) {
                foreach ($customer->meters as $meter) {
                    $meter->update([
                        'customer_id' => null,
                        'status' => 'available',
                        'installation_address' => null,
                        'installation_date' => null,
                    ]);
                }

                $customer->update([
                    'notes' => $customer->notes . "\nAll meters unassigned on " . now()->format('Y-m-d H:i:s'),
                ]);
            });

            return redirect()->back()
                ->with('success', 'All meters unassigned from customer successfully!');

        } catch (\Exception $e) {
            Log::error('Unassign all meters error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error unassigning all meters: ' . $e->getMessage());
        }
    }

    // API Methods for AJAX
    public function getAvailableMeters(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $searchTerm = $request->get('search', '');

            Log::info('Fetching meters for category: ' . $categoryId . ', search: ' . $searchTerm);

            // Query for available meters
            $query = Meter::where('status', 'available')
                ->whereNull('customer_id');

            if ($categoryId) {
                $query->where('meter_category_id', $categoryId);
            }

            // Apply search filter if provided
            if ($searchTerm) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('meter_number', 'like', "%{$searchTerm}%")
                    ->orWhere('meter_model', 'like', "%{$searchTerm}%")
                    ->orWhere('meter_type', 'like', "%{$searchTerm}%");
                });
            }

            $meters = $query->with('meterCategory')
                ->orderBy('meter_number')
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

    // Document Upload Method
    public function uploadDocuments(Request $request, Customer $customer)
    {
        $request->validate([
            'national_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kra_pin_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'title_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'upload_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $customer) {
                // Check if customer has a water application
                $application = $customer->waterApplication;

                Log::info('Starting document upload', [
                    'customer_id' => $customer->id,
                    'has_application' => !is_null($application),
                    'application_id' => $application?->id,
                    'files_received' => array_keys($request->allFiles())
                ]);

                // Prepare base data
                $applicationData = [
                    'customer_id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'gender' => 'Other',
                    'kra_pin' => $customer->kra_pin ?? 'N/A',
                    'national_id' => $customer->id_number,
                    'plot_number' => $customer->plot_number,
                    'house_number' => $customer->house_number,
                    'estate' => $customer->estate,
                    'expected_users' => $customer->expected_users,
                    'property_owner' => $customer->property_owner,
                    'date' => now(),
                    'signature' => 'Admin Upload',
                    'status' => 'approved',
                ];

                // Add processed fields
                $applicationData['processed_by'] = auth()->id();
                $applicationData['processed_at'] = now();

                if (!$application) {
                    Log::info('Creating new water application');
                    $application = WaterConnectionApplication::create($applicationData);
                } else {
                    Log::info('Updating existing water application', [
                        'current_status' => $application->status,
                        'current_files' => [
                            'national_id' => !empty($application->national_id_file),
                            'kra_pin' => !empty($application->kra_pin_file),
                            'title' => !empty($application->title_document),
                        ]
                    ]);
                    $application->update($applicationData);
                }

                // Upload files with detailed logging
                if ($request->hasFile('national_id_file')) {
                    Log::info('Uploading national ID file');
                    if ($application->national_id_file && Storage::disk('public')->exists($application->national_id_file)) {
                        Storage::disk('public')->delete($application->national_id_file);
                    }
                    $nationalIdPath = $request->file('national_id_file')->store('documents/national_ids', 'public');
                    $application->national_id_file = $nationalIdPath;
                    Log::info('National ID file saved', ['path' => $nationalIdPath]);
                }

                if ($request->hasFile('kra_pin_file')) {
                    Log::info('Uploading KRA PIN file');
                    if ($application->kra_pin_file && Storage::disk('public')->exists($application->kra_pin_file)) {
                        Storage::disk('public')->delete($application->kra_pin_file);
                    }
                    $kraPinPath = $request->file('kra_pin_file')->store('documents/kra_pins', 'public');
                    $application->kra_pin_file = $kraPinPath;
                    Log::info('KRA PIN file saved', ['path' => $kraPinPath]);
                }

                if ($request->hasFile('title_document')) {
                    Log::info('Uploading title document');
                    if ($application->title_document && Storage::disk('public')->exists($application->title_document)) {
                        Storage::disk('public')->delete($application->title_document);
                    }
                    $titlePath = $request->file('title_document')->store('documents/titles', 'public');
                    $application->title_document = $titlePath;
                    Log::info('Title document saved', ['path' => $titlePath]);
                }

                $application->save();

                // Reload the application to verify changes
                $application->refresh();

                Log::info('After upload - application file status', [
                    'national_id_file' => !empty($application->national_id_file),
                    'kra_pin_file' => !empty($application->kra_pin_file),
                    'title_document' => !empty($application->title_document),
                ]);

                // Update customer notes
                $customer->update([
                    'notes' => $customer->notes . "\nDocuments uploaded on " . now()->format('Y-m-d H:i:s') . ($request->upload_notes ? "\nNotes: " . $request->upload_notes : ''),
                ]);

                Log::info('Documents upload completed successfully', [
                    'customer_id' => $customer->id,
                    'application_id' => $application->id,
                ]);
            });

            return redirect()->back()
                ->with('success', 'Documents uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Document upload error: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Error uploading documents: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        // Get customers with their meters and balances
        $customers = Customer::with(['meters', 'meters.meterCategory'])
            ->when($status && $status !== 'all', function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('customer_number', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('estate')
            ->orderBy('customer_number')
            ->get();

        // Group customers by estate
        $groupedCustomers = $customers->groupBy('estate');

        $data = [
            'groupedCustomers' => $groupedCustomers,
            'status' => $status ?: 'All',
            'search' => $search,
            'totalCustomers' => $customers->count(),
            'exportDate' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = PDF::loadView('admin.customers.pdf-export', $data);

        $filename = 'customers_' . ($status ?: 'all') . '_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    public function getCustomerDetails($id)
    {
        $customer = Customer::findOrFail($id);

        return response()->json([
            'id' => $customer->id,
            'text' => "{$customer->customer_number} - {$customer->first_name} {$customer->last_name} ({$customer->phone})"
        ]);
    }

    // Customer details + previous reading
    public function details(Request $request, $id)
    {
        $error = $this->validateAuth($request);
        if ($error) return $error;

        $customer = Customer::with(['meters.readings' => function($q) {
            $q->latest()->first();
        }])->find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $balance = $customer->bills()->sum('amount_due') - $customer->payments()->sum('amount');

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'account_number' => $customer->account_number,
            'zone' => $customer->zone,
            'meters' => $customer->meters->map(function($meter) {
                $lastReading = $meter->readings->first();
                return [
                    'id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'category' => $meter->category,
                    'previous_reading' => $lastReading?->current_reading ?? 0,
                    'last_reading_date' => $lastReading?->reading_date,
                ];
            }),
            'balance' => $balance,
        ]);
    }

    // Balance inquiry
    public function balance(Request $request, $id)
    {
        $error = $this->validateAuth($request);
        if ($error) return $error;

        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $balance = $customer->bills()->sum('amount_due') - $customer->payments()->sum('amount');

        $lastPayment = $customer->payments()->latest()->first();

        return response()->json([
            'account_number' => $customer->account_number,
            'customer_name' => $customer->name,
            'outstanding_balance' => $balance,
            'last_payment' => $lastPayment?->amount,
            'last_payment_date' => $lastPayment?->payment_date,
        ]);
    }

    private function validateAuth(Request $request)
    {
        if (!Auth::once($request->only('username', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return null;
    }
    public function statement(Request $request, Customer $customer)
    {
        // Validate date inputs
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Fetch payments for this customer within date range
        $payments = $customer->payments()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                    ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('payment_date')
            ->get();

        // Fetch meter readings for this customer within date range
        $meterReadings = $customer->meterReadings()
            ->whereBetween('reading_date', [$startDate, $endDate])
            ->with('meter')
            ->orderBy('reading_date')
            ->get();

        // FIXED: Get bills using a more flexible approach
        $bills = $customer->bills()
            ->where(function($query) use ($startDate, $endDate) {
                // Option 1: Bill created within date range
                $query->whereBetween('created_at', [$startDate, $endDate])
                    // Option 2: Bill's due date within date range
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->whereNotNull('due_date')
                            ->whereBetween('due_date', [$startDate, $endDate]);
                    })
                    // Option 3: Bill's billing period overlaps with date range
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->whereNotNull('billing_period_start')
                            ->whereNotNull('billing_period_end')
                            ->where(function($sub) use ($startDate, $endDate) {
                                $sub->whereBetween('billing_period_start', [$startDate, $endDate])
                                    ->orWhereBetween('billing_period_end', [$startDate, $endDate])
                                    ->orWhere(function($inner) use ($startDate, $endDate) {
                                        $inner->where('billing_period_start', '<=', $startDate)
                                            ->where('billing_period_end', '>=', $endDate);
                                    });
                            });
                    });
            })
            ->orderBy('created_at')
            ->get();

        // Debug: Log what we're getting
        Log::info('Statement data:', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'payments_count' => $payments->count(),
            'readings_count' => $meterReadings->count(),
            'bills_count' => $bills->count(),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'all_bills_count' => $customer->bills()->count(),
        ]);

        // Calculate opening balance (balance before start date)
        $openingBalance = $this->calculateOpeningBalance($customer, $startDate);

        // Calculate closing balance
        $totalDebits = $bills->sum('total_amount');
        $totalCredits = $payments->sum('amount');
        $closingBalance = $openingBalance + $totalDebits - $totalCredits;

        return view('admin.customers.statement', compact(
            'customer',
            'payments',
            'meterReadings',
            'bills',
            'startDate',
            'endDate',
            'openingBalance',
            'closingBalance',
            'totalDebits',
            'totalCredits'
        ));
    }

    private function calculateOpeningBalance(Customer $customer, Carbon $startDate)
    {
        // Calculate total bills before start date FOR THIS CUSTOMER ONLY
        $totalBillsBefore = $customer->bills()
            ->where('created_at', '<', $startDate)
            ->sum('total_amount');

        // Calculate total payments before start date FOR THIS CUSTOMER ONLY
        $totalPaymentsBefore = $customer->payments()
            ->where('created_at', '<', $startDate)
            ->sum('amount');

        $openingBalance = $totalBillsBefore - $totalPaymentsBefore;

        Log::info('Opening balance calculation:', [
            'total_bills_before' => $totalBillsBefore,
            'total_payments_before' => $totalPaymentsBefore,
            'opening_balance' => $openingBalance,
        ]);

        return $openingBalance;
    }

    public function statementPdf(Request $request, Customer $customer)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Fetch data (using same logic as statement method)
        $payments = $customer->payments()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                    ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('payment_date')
            ->get();

        $meterReadings = $customer->meterReadings()
            ->whereBetween('reading_date', [$startDate, $endDate])
            ->with('meter')
            ->orderBy('reading_date')
            ->get();

        $bills = $customer->bills()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->whereNotNull('due_date')
                            ->whereBetween('due_date', [$startDate, $endDate]);
                    })
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->whereNotNull('billing_period_start')
                            ->whereNotNull('billing_period_end')
                            ->where(function($sub) use ($startDate, $endDate) {
                                $sub->whereBetween('billing_period_start', [$startDate, $endDate])
                                    ->orWhereBetween('billing_period_end', [$startDate, $endDate])
                                    ->orWhere(function($inner) use ($startDate, $endDate) {
                                        $inner->where('billing_period_start', '<=', $startDate)
                                            ->where('billing_period_end', '>=', $endDate);
                                    });
                            });
                    });
            })
            ->orderBy('created_at')
            ->get();

        $openingBalance = $this->calculateOpeningBalance($customer, $startDate);
        $totalDebits = $bills->sum('total_amount');
        $totalCredits = $payments->sum('amount');
        $closingBalance = $openingBalance + $totalDebits - $totalCredits;

        $data = [
            'customer' => $customer,
            'payments' => $payments,
            'meterReadings' => $meterReadings,
            'bills' => $bills,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'totalDebits' => $totalDebits,
            'totalCredits' => $totalCredits,
        ];

        // Configure PDF options
        $pdf = PDF::loadView('admin.customers.statement-pdf', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('defaultFont', 'Helvetica')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $filename = "Statement_{$customer->customer_number}_{$startDate->format('Y_m_d')}_to_{$endDate->format('Y_m_d')}.pdf";

        return $pdf->download($filename);
    }
    /**
     * Show the unassign and reassign form for a meter
     */
    public function showUnassignReassignForm(Customer $customer, Meter $meter)
    {
        // Verify the meter belongs to this customer
        if ($meter->customer_id !== $customer->id) {
            return redirect()->back()->with('error', 'This meter is not assigned to this customer.');
        }

        $categories = MeterCategory::active()->ordered()->get();

        return view('admin.customers.unassign-reassign', compact('customer', 'meter', 'categories'));
    }

    /**
     * Unassign a meter from current customer and reassign to another customer
     */
    public function unassignAndReassign(Request $request, Customer $customer, Meter $meter)
    {
        $request->validate([
            'action' => 'required|in:existing,new',
            'customer_id' => 'required_if:action,existing|exists:customers,id',
            'new_customer' => 'required_if:action,new|array',
            'new_customer.first_name' => 'required_if:action,new|string|max:50',
            'new_customer.last_name' => 'nullable|string|max:50',
            'new_customer.email' => 'required_if:action,new|email|unique:customers,email',
            'new_customer.phone' => 'required_if:action,new|string|max:20|unique:customers,phone',
            'new_customer.id_number' => 'required_if:action,new|string|max:20|unique:customers,id_number',
            'new_customer.physical_address' => 'required_if:action,new|string|max:500',
            'new_customer.plot_number' => 'required_if:action,new|string|max:50',
            'new_customer.house_number' => 'required_if:action,new|string|max:50',
            'new_customer.property_owner' => 'required_if:action,new|string|max:100',
            'new_customer.estate' => 'nullable|string|max:100',
            'new_customer.kra_pin' => 'nullable|string|max:20|unique:customers,kra_pin',
            'new_customer.expected_users' => 'nullable|integer|min:1|max:1000',
            'unassignment_reason' => 'required|string|max:255',
            'transfer_billing_history' => 'nullable|in:1',
            'new_installation_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->action === 'existing' && empty($value)) {
                        $fail('Installation date is required when assigning to an existing customer.');
                    }
                }
            ],
            'new_initial_reading' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->action === 'existing' && empty($value) && $value !== '0') {
                        $fail('Initial reading is required when assigning to an existing customer.');
                    }
                }
            ],
            'new_balance_bf' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_id.required_if' => 'Please select a customer to assign the meter to.',
            'new_customer.first_name.required_if' => 'Customer first name is required.',
            'new_customer.email.required_if' => 'Customer email is required.',
            'new_customer.email.unique' => 'This email is already registered.',
            'new_customer.phone.required_if' => 'Customer phone is required.',
            'new_customer.phone.unique' => 'This phone number is already registered.',
            'new_customer.id_number.required_if' => 'Customer ID number is required.',
            'new_customer.id_number.unique' => 'This ID number is already registered.',
            'new_customer.physical_address.required_if' => 'Physical address is required.',
            'new_customer.plot_number.required_if' => 'Plot number is required.',
            'new_customer.house_number.required_if' => 'House number is required.',
            'new_customer.property_owner.required_if' => 'Property owner is required.',
        ]);

        try {
            // Declare targetCustomer outside the transaction so it's accessible
            $targetCustomer = null;

            DB::transaction(function () use ($request, $customer, $meter, &$targetCustomer) {
                // 1. Record unassignment from current customer
                $unassignmentNote = sprintf(
                    "Meter %s unassigned from customer %s %s (ID: %s) on %s\nReason: %s",
                    $meter->meter_number,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->customer_number,
                    now()->format('Y-m-d H:i:s'),
                    $request->unassignment_reason
                );

                // 2. Determine target customer
                if ($request->action === 'existing') {
                    $targetCustomer = Customer::findOrFail($request->customer_id);

                    Log::info('Assigning to existing customer', [
                        'customer_id' => $targetCustomer->id,
                        'customer_number' => $targetCustomer->customer_number,
                        'meter_count' => $targetCustomer->meters()->count()
                    ]);
                } else {
                    // Create new customer with proper field mapping
                    $customerData = [
                        'customer_number' => $this->generateCustomerNumber(),
                        'first_name' => $request->new_customer['first_name'],
                        'last_name' => $request->new_customer['last_name'] ?? null,
                        'email' => $request->new_customer['email'],
                        'phone' => $request->new_customer['phone'],
                        'id_number' => $request->new_customer['id_number'],
                        'physical_address' => $request->new_customer['physical_address'],
                        'plot_number' => $request->new_customer['plot_number'],
                        'house_number' => $request->new_customer['house_number'],
                        'estate' => $request->new_customer['estate'] ?? null,
                        'kra_pin' => $request->new_customer['kra_pin'] ?? null,
                        'property_owner' => $request->new_customer['property_owner'],
                        'expected_users' => $request->new_customer['expected_users'] ?? null,
                        'status' => 'active',
                        'status_updated_at' => now(),
                        'status_reason' => 'Created during meter reassignment',
                        'notes' => "Customer created on " . now()->format('Y-m-d H:i:s') .
                                " for meter reassignment.\n" .
                                ($request->notes ?? ''),
                        'credit_balance' => 0,
                        'total_payments' => 0,
                    ];

                    $targetCustomer = Customer::create($customerData);

                    Log::info('New customer created for meter reassignment', [
                        'customer_id' => $targetCustomer->id,
                        'customer_number' => $targetCustomer->customer_number,
                        'meter_number' => $meter->meter_number
                    ]);
                }

                // 3. Unassign from current customer
                $meter->update([
                    'customer_id' => null,
                    'status' => Meter::STATUS_AVAILABLE,
                    'installation_address' => null,
                    'installation_date' => null,
                ]);

                // 4. Update current customer notes
                $customer->update([
                    'notes' => ($customer->notes ? $customer->notes . "\n\n" : '') . $unassignmentNote
                ]);

                // 5. Assign to target customer - handle differently based on action
                if ($request->action === 'existing') {
                    // For existing customer, use provided values
                    $installationDate = $request->new_installation_date;
                    $initialReading = $request->new_initial_reading;
                    $balanceBf = $request->new_balance_bf ?? 0;
                } else {
                    // For new customer, use current values or defaults
                    $installationDate = now()->format('Y-m-d');
                    $initialReading = $meter->current_reading; // Use current reading as initial
                    $balanceBf = 0;
                }

                $meter->update([
                    'customer_id' => $targetCustomer->id,
                    'installation_address' => $targetCustomer->physical_address,
                    'installation_date' => $installationDate,
                    'initial_reading' => $initialReading,
                    'balance_bf' => $balanceBf,
                    'current_balance' => $balanceBf,
                    'status' => Meter::STATUS_ACTIVE,
                    'notes' => ($meter->notes ? $meter->notes . "\n\n" : '') .
                            "Reassigned to customer {$targetCustomer->customer_number} on " . now()->format('Y-m-d H:i:s')
                ]);

                // 6. Create initial meter reading for new assignment
                MeterReading::create([
                    'customer_id' => $targetCustomer->id,
                    'meter_id' => $meter->id,
                    'current_reading' => $initialReading,
                    'previous_reading' => 0,
                    'consumption' => $initialReading,
                    'reading_date' => $installationDate,
                    'reading_type' => 'initial',
                    'reading_period' => 'Meter Reassignment',
                    'billed' => false,
                    'read_by' => auth()->id(),
                    'notes' => "Initial reading after reassignment from customer {$customer->customer_number}",
                ]);

                // ========== BILLING HISTORY TRANSFER ==========
                // Check if transfer billing history checkbox is checked
                if ($request->has('transfer_billing_history') && $request->transfer_billing_history == '1') {

                    // Get counts before transfer for logging
                    $billsTransferred = 0;
                    $readingsTransferred = 0;

                    // 6.5.1 Transfer bills that belong to this meter AND the current customer
                    if ($meter->bills()->where('customer_id', $customer->id)->exists()) {
                        $billsTransferred = $meter->bills()
                            ->where('customer_id', $customer->id)
                            ->update([
                                'customer_id' => $targetCustomer->id
                            ]);
                    }

                    // 6.5.2 Transfer meter readings that belong to this meter AND the current customer
                    if ($meter->meterReadings()->where('customer_id', $customer->id)->exists()) {
                        $readingsTransferred = $meter->meterReadings()
                            ->where('customer_id', $customer->id)
                            ->where('reading_status','recorded')
                            ->update([
                                'customer_id' => $targetCustomer->id
                            ]);
                    }

                    // Log the transfer
                    Log::info('Billing history transferred for incorrectly assigned meter', [
                        'meter_id' => $meter->id,
                        'meter_number' => $meter->meter_number,
                        'from_customer' => $customer->id,
                        'to_customer' => $targetCustomer->id,
                        'bills_transferred' => $billsTransferred,
                        'readings_transferred' => $readingsTransferred,
                        'reason' => $request->unassignment_reason
                    ]);

                    // Add note about the transfer to current customer
                    if ($billsTransferred > 0 || $readingsTransferred > 0) {
                        $transferNote = sprintf(
                            "\n\nBILLING HISTORY TRANSFERRED: %d bills and %d meter readings for meter %s were moved to customer %s %s (ID: %s) on %s",
                            $billsTransferred,
                            $readingsTransferred,
                            $meter->meter_number,
                            $targetCustomer->first_name,
                            $targetCustomer->last_name,
                            $targetCustomer->customer_number,
                            now()->format('Y-m-d H:i:s')
                        );

                        $customer->update([
                            'notes' => $customer->notes . $transferNote
                        ]);

                        // Add note about the transfer to target customer
                        $targetTransferNote = sprintf(
                            "\n\nBILLING HISTORY RECEIVED: %d bills and %d meter readings for meter %s were transferred from customer %s %s (ID: %s) on %s",
                            $billsTransferred,
                            $readingsTransferred,
                            $meter->meter_number,
                            $customer->first_name,
                            $customer->last_name,
                            $customer->customer_number,
                            now()->format('Y-m-d H:i:s')
                        );

                        $targetCustomer->update([
                            'notes' => $targetCustomer->notes . $targetTransferNote
                        ]);
                    }
                }
                // ========== END BILLING HISTORY TRANSFER ==========


                // 7. Add assignment note to target customer
                $assignmentNote = sprintf(
                    "Meter %s assigned from customer %s %s (ID: %s) on %s\nInstallation Date: %s\nInitial Reading: %s m³\nBalance B/F: KSh %s",
                    $meter->meter_number,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->customer_number,
                    now()->format('Y-m-d H:i:s'),
                    $installationDate,
                    $initialReading,
                    number_format($balanceBf, 2)
                );

                $targetCustomer->update([
                    'notes' => ($targetCustomer->notes ? $targetCustomer->notes . "\n\n" : '') . $assignmentNote
                ]);

                // 8. Optionally generate initial bill if needed
                if ($request->action === 'new' && $meter->meterCategory) {
                    // You can add logic here to generate an initial bill for the new customer
                    // This is optional based on your business requirements
                }

                Log::info('Meter reassigned successfully', [
                    'meter_id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'from_customer' => $customer->id,
                    'to_customer' => $targetCustomer->id,
                    'action' => $request->action,
                    'installation_date' => $installationDate,
                    'initial_reading' => $initialReading
                ]);
            });

            // Redirect to the new customer's profile if it was a new customer
            if ($request->action === 'new' && $targetCustomer) {
                return redirect()->route('admin.customers.show', $targetCustomer)
                    ->with('success', 'Meter unassigned and reassigned to new customer successfully!');
            }

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', 'Meter unassigned and reassigned successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Meter reassignment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token', 'new_customer'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error reassigning meter: ' . $e->getMessage());
        }
    }
       /**
     * Show quick customer creation form (AJAX)
     */
    public function quickCreateForm()
    {
        return view('admin.customers.quick-create-modal');
    }

    /**
     * Quick create customer for meter assignment
     */
    public function quickCreate(Request $request)
    {

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'id_number' => 'required|string|max:20|unique:customers,id_number',
            'physical_address' => 'required|string|max:500',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'estate' => 'nullable|string|max:100',
            'kra_pin' => 'nullable|string|max:20|unique:customers,kra_pin',
            'property_owner' => 'nullable|string|max:100',
            'expected_users' => 'nullable|integer|min:1|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $customer = Customer::create([
                    'customer_number' => $this->generateCustomerNumber(),
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'] ?? null,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'id_number' => $validated['id_number'],
                    'physical_address' => $validated['physical_address'],
                    'plot_number' => $validated['plot_number'],
                    'house_number' => $validated['house_number'],
                    'estate' => $validated['estate'] ?? null,
                    'kra_pin' => $validated['kra_pin'] ?? null,
                    'property_owner' => $validated['property_owner'],
                    'expected_users' => $validated['expected_users'] ?? null,
                    'status' => 'active',
                    'status_updated_at' => now(),
                    'status_reason' => 'Quick created for meter assignment',
                    'notes' => "Customer created via quick create on " . now()->format('Y-m-d H:i:s'),
                    'credit_balance' => 0,
                    'total_payments' => 0,
                ]);

                return response()->json([
                    'success' => true,
                    'customer' => [
                        'id' => $customer->id,
                        'text' => "{$customer->customer_number} - {$customer->first_name} {$customer->last_name} ({$customer->phone})",
                        'customer_number' => $customer->customer_number,
                        'name' => $customer->first_name . ' ' . $customer->last_name,
                        'phone' => $customer->phone,
                        'address' => $customer->physical_address,
                        'plot' => $customer->plot_number,
                        'has_meter' => false,
                        'meter_count' => 0,
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Quick customer creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers with meter assignment status
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->get('q');
        $includeMeterInfo = $request->get('include_meter_info', false);

        $customers = Customer::query()
            ->where(function($query) use ($search) {
                // Check if search contains a space (likely first and last name)
                if (str_contains($search, ' ')) {
                    $nameParts = explode(' ', $search, 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1];

                    $query->where(function($q) use ($firstName, $lastName) {
                        $q->where('first_name', 'like', "%{$firstName}%")
                        ->where('last_name', 'like', "%{$lastName}%");
                    });
                } else {
                    // Single word search - search in all fields
                    $query->where('customer_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%")
                        ->orWhere('plot_number', 'like', "%{$search}%");
                }
            })
            ->withCount('meters')
            ->limit(20)
            ->get()
            ->map(function($customer) use ($includeMeterInfo) {
                $result = [
                    'id' => $customer->id,
                    'text' => "{$customer->customer_number} - {$customer->first_name} {$customer->last_name}",
                    'customer_number' => $customer->customer_number,
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'phone' => $customer->phone,
                    'address' => $customer->physical_address,
                    'plot' => $customer->plot_number,
                    'status' => $customer->status,
                ];

                if ($includeMeterInfo) {
                    $result['has_meter'] = $customer->meters_count > 0;
                    $result['meter_count'] = $customer->meters_count;
                    $result['meter_info'] = $customer->meters_count > 0
                        ? "Has {$customer->meters_count} meter(s) assigned"
                        : "No meters assigned";
                }

                return $result;
            });

        return response()->json([
            'results' => $customers,
            'pagination' => [
                'more' => false
            ]
        ]);
    }

    /**
     * Check if customer has meters
     */
    public function checkCustomerMeters(Request $request, Customer $customer)
    {
        $meters = $customer->meters()->get();

        return response()->json([
            'has_meters' => $meters->isNotEmpty(),
            'meter_count' => $meters->count(),
            'meters' => $meters->map(function($meter) {
                return [
                    'id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'status' => $meter->status,
                    'installation_date' => $meter->installation_date?->format('Y-m-d'),
                ];
            })
        ]);
    }
}
