<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaterConnectionApplication;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $estate = $request->get('estate');
        $status = $request->get('status');
        
        // Get unique estates for the filter dropdown
        $estates = Customer::whereNotNull('estate')
            ->distinct()
            ->pluck('estate')
            ->sort();

        // Get pending applications
        $pendingApplications = WaterConnectionApplication::where('status', 'pending')
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('national_id', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('house_number', 'like', "%{$search}%")
                      ->orWhere('estate', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Get active customers with enhanced filtering
        $activeCustomers = Customer::with('meter') // Eager load meter relationship
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            }, function($query) {
                // Default to active if no status filter
                return $query->where('status', 'active');
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('customer_number', 'like', "%{$search}%")
                      ->orWhere('account_number', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('house_number', 'like', "%{$search}%")
                      ->orWhere('estate', 'like', "%{$search}%")
                      ->orWhereHas('meter', function($meterQuery) use ($search) {
                          $meterQuery->where('meter_number', 'like', "%{$search}%");
                      });
                });
            })
            ->when($estate, function($query) use ($estate) {
                return $query->where('estate', $estate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Preserve all query parameters in pagination links

        return view('admin.customers.index', compact(
            'pendingApplications', 
            'activeCustomers', 
            'search',
            'estate',
            'status',
            'estates'
        ));
    }

    public function show(Customer $customer)
    {
        // Load the customer with relationships including meter readings
        $customer->load(['meter', 'waterApplication', 'meterReadings' => function($query) {
            $query->orderBy('reading_date', 'desc');
        }]);
        
        // Get reading statistics
        $readingStats = [
            'total_readings' => $customer->meterReadings->count(),
            'last_reading' => $customer->meterReadings->first(),
            'average_consumption' => $customer->meterReadings->avg('consumption'),
            'total_consumption' => $customer->meterReadings->sum('consumption'),
        ];
        
        return view('admin.customers.show', compact('customer', 'readingStats'));
    }
}