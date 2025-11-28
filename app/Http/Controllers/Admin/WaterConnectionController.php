<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaterConnectionApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;

class WaterConnectionController extends Controller
{
    public function create()
    {
        return view('services.water-connection-application');
    }

    public function store(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female,Other',
            'kra_pin' => 'required|string|max:20',
            'kra_pin_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'national_id' => 'required|string|max:20',
            'national_id_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'required|string|max:50',
            'estate' => 'nullable|string|max:100',
            'expected_users' => 'nullable|integer|min:1',
            'property_owner' => 'required|string|max:100',
            'title_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'nullable|string|max:100',
            'date' => 'required|date',
        ]);

        try {
            // Handle file uploads
            $fileFields = [];
            foreach (['kra_pin_file', 'national_id_file', 'title_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    // Store in storage/app/public/water-applications
                    $fileFields[$fileField] = $request->file($fileField)->store('water-applications', 'public');
                }
            }

            // Save water connection application only
            WaterConnectionApplication::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'kra_pin' => $validated['kra_pin'],
                'kra_pin_file' => $fileFields['kra_pin_file'] ?? null,
                'national_id' => $validated['national_id'],
                'national_id_file' => $fileFields['national_id_file'] ?? null,
                'plot_number' => $validated['plot_number'],
                'house_number' => $validated['house_number'],
                'estate' => $validated['estate'] ?? null,
                'expected_users' => $validated['expected_users'] ?? null,
                'property_owner' => $validated['property_owner'],
                'title_document' => $fileFields['title_document'] ?? null,
                'signature' => $validated['signature'] ?? null,
                'date' => $validated['date'],
                'status' => 'pending',
            ]);

           return redirect()->route('water-connection.apply')
            ->with('success', 'Application submitted successfully! Your application is under review.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error submitting application: ' . $e->getMessage());
        }
    }

    // Admin methods to view applications
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        
        $applications = WaterConnectionApplication::when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('kra_pin', 'like', "%{$search}%")
                    ->orWhere('plot_number', 'like', "%{$search}%")
                    ->orWhere('house_number', 'like', "%{$search}%")
                    ->orWhere('estate', 'like', "%{$search}%")
                    ->orWhere('property_owner', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admin.water-applications.index', compact('applications', 'status', 'search'));
    }

    public function show(WaterConnectionApplication $application)
    {
        // Load relationships
        $application->load(['customer', 'processedBy']);
        
        // Get meter categories for the approval modal
        $categories = MeterCategory::active()->ordered()->get();
        
        return view('admin.water-applications.show', compact('application', 'categories'));
    }


   public function decline(Request $request, WaterConnectionApplication $application)
{
    $validated = $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    try {
        $application->update([
            'status' => 'declined',
            'decline_reason' => $validated['reason'],
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.water-applications.index')
            ->with('success', 'Application declined successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error declining application: ' . $e->getMessage());
    }
}

public function approve(Request $request, WaterConnectionApplication $application)
{
    $validated = $request->validate([
        'meter_category_id' => 'nullable|exists:meter_categories,id',
        'meter_id' => 'nullable|exists:meters,id',
        'connection_date' => 'required|date',
        'notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::transaction(function () use ($application, $validated) {
            // Use the connection date provided (this is when billing starts)
            $connectionDate = $validated['connection_date'];
            
            // Create Customer
            $customer = Customer::create([
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'email' => $application->email,
                'phone' => $application->phone,
                'id_number' => $application->national_id,
                'physical_address' => $application->plot_number . ', ' . $application->house_number . ($application->estate ? ', ' . $application->estate : ''),
                'plot_number' => $application->plot_number,
                'house_number' => $application->house_number,
                'estate' => $application->estate,
                'connection_type' => 'residential', // Default, can be updated based on category
                'status' => 'active', // Set to active since they're approved
                'kra_pin' => $application->kra_pin,
                'property_owner' => $application->property_owner,
                'expected_users' => $application->expected_users,
                'balance_bf' => 0,
                'current_balance' => 0,
                'connection_date' => $connectionDate, // This is when billing starts
                'notes' => $validated['notes'] ?? 'Approved water connection application. Billing starts from: ' . $connectionDate,
            ]);

            // Assign meter if provided
            if (!empty($validated['meter_id'])) {
                $meter = Meter::findOrFail($validated['meter_id']);
                
                $meter->update([
                    'customer_id' => $customer->id,
                    'meter_category_id' => $validated['meter_category_id'],
                    'installation_address' => $customer->full_address,
                    'installation_date' => $connectionDate, // Use the same connection date
                    'status' => 'assigned',
                ]);

                // Update customer with meter info
                $customer->update([
                    'meter_number' => $meter->meter_number,
                    'meter_type' => $meter->meter_type,
                    'initial_meter_reading' => $meter->initial_reading,
                ]);

                // Create initial meter reading with connection date
                if ($meter->initial_reading > 0) {
                    MeterReading::create([
                        'customer_id' => $customer->id,
                        'meter_id' => $meter->id,
                        'current_reading' => $meter->initial_reading,
                        'previous_reading' => 0,
                        'consumption' => $meter->initial_reading,
                        'reading_date' => $connectionDate, // Use connection date for first reading
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading upon connection approval. Billing starts from this date.',
                    ]);
                }
            }

            // Update application status and link to customer
            $application->update([
                'status' => 'approved',
                'customer_id' => $customer->id,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        });

        $message = empty($validated['meter_id']) 
            ? 'Application approved successfully! Customer created without meter assignment. Billing starts from: ' . $validated['connection_date']
            : 'Application approved successfully! Customer created and meter assigned. Billing starts from: ' . $validated['connection_date'];

        return redirect()->route('admin.water-applications.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error approving application: ' . $e->getMessage());
    }
}
}