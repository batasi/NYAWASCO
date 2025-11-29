<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use App\Models\WaterConnectionApplication;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $statusCounts = [
            'all' => Customer::count(),
            'new' => Customer::new()->count(),
            'active' => Customer::active()->count(),
            'pending_payment' => Customer::pendingPayment()->count(),
            'sealed' => Customer::sealed()->count(),
            'terminated' => Customer::terminated()->count(),
        ];

        $categories = MeterCategory::active()->ordered()->get();

        return view('admin.customers.index', compact('customers', 'statusCounts', 'status', 'search', 'categories'));
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
                        'installation_address' => $customer->physical_address,
                        'installation_date' => $customer->connection_date,
                    ]);

                    // Create initial meter reading
                    if ($meter->initial_reading > 0) {
                        MeterReading::create([
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
            Log::error('Customer creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
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

        // Calculate billing statistics
        $billingStats = [
            'total_bills' => $customer->bills->count(),
            'unpaid_bills' => $customer->bills()->where('bill_status', 'unpaid')->count(),
            'paid_bills' => $customer->bills()->where('bill_status', 'paid')->count(),
            'account_balance' => $customer->current_balance,
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

    // Enhanced Status Management Method
   public function updateStatus(Request $request, Customer $customer)
    {
        Log::info('=== UPDATE STATUS CALLED ===', [
            'customer_id' => $customer->id,
            'current_status' => $customer->status,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'status' => 'required|string|in:new,active,pending_payment,sealed,terminated',
            'notes' => 'required|string|max:500',
            'pending_payment_reason' => 'nullable|string|max:100',
            'sealed_reason' => 'nullable|string|max:100',
            'terminated_reason' => 'nullable|string|max:100',
        ]);

        try {
            $oldStatus = $customer->status;
            $newStatus = $validated['status'];

            Log::info('Attempting status update', [
                'customer_id' => $customer->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Check if activation requirements are met
            if ($newStatus === 'active' && !$customer->canBeActivated()) {
                $requirements = $customer->getActivationRequirements();
                Log::warning('Activation requirements not met', ['requirements' => $requirements]);
                return redirect()->back()
                    ->with('error', 'Cannot activate customer. Requirements not met: ' . implode(', ', $requirements));
            }

            // Determine the specific reason based on status
            $statusReason = null;
            if ($newStatus === 'pending_payment') {
                $statusReason = $validated['pending_payment_reason'];
            } elseif ($newStatus === 'sealed') {
                $statusReason = $validated['sealed_reason'];
            } elseif ($newStatus === 'terminated') {
                $statusReason = $validated['terminated_reason'];
            }

            // Build status note for the general notes field
            $statusNote = "Status changed from {$oldStatus} to {$newStatus} on " . now()->format('Y-m-d H:i:s');
            
            // Add specific reason if provided
            if ($statusReason) {
                $statusNote .= "\nReason: " . $statusReason;
            }
            
            // Add admin notes
            if ($validated['notes']) {
                $statusNote .= "\nNotes: " . $validated['notes'];
            }

            // Update customer with separate status reason and notes
            $customer->update([
                'status' => $newStatus,
                'status_reason' => $statusReason, // Store the specific reason
                'status_notes' => $validated['notes'], // Store the administrative notes separately
                'status_updated_at' => now(), // Track when status was last changed
                'notes' => $customer->notes . "\n" . $statusNote, // Keep in general notes too
            ]);

            Log::info('Status update successful', [
                'customer_id' => $customer->id,
                'old_status' => $oldStatus,
                'new_status' => $customer->fresh()->status,
                'status_reason' => $statusReason
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

                // Check if meter is already assigned to another customer
                if ($meter->customer_id && $meter->customer_id !== $customer->id) {
                    throw new \Exception('This meter is already assigned to another customer.');
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

                // Update customer's current balance
                $customer->update([
                    'current_balance' => ($customer->current_balance + ($request->balance_bf ?? 0))
                ]);
            });

            return back()->with('success', 'Meter assigned successfully!');

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

                // Update meter
                $meter->update([
                    'customer_id' => null,
                    'status' => 'available',
                    'installation_address' => null,
                    'installation_date' => null,
                ]);

                // Update customer notes
                $customer->update([
                    'notes' => $customer->notes . "\nMeter unassigned: " . $meter->meter_number . " on " . now()->format('Y-m-d H:i:s'),
                ]);
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
                ->with('success', 'Documents uploaded successfully! Customer can now be activated.');

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
}