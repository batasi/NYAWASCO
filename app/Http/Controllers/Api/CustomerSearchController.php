<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    /**
     * Search customers by account number, name, phone, or meter serial
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:1|max:255',
        ]);

        $searchTerm = $request->input('search');

        // Search in customers table
        $customers = Customer::where(function($query) use ($searchTerm) {
                $query->where('customer_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('id_number', 'LIKE', "%{$searchTerm}%");
            })
            ->where('status', 'active') // Only active customers
            ->with(['meters' => function($query) {
                $query->select('id', 'meter_number', 'customer_id', 'current_reading', 'status');
            }])
            ->limit(20)
            ->get(['id', 'customer_number', 'first_name', 'last_name', 'phone', 'email',
                   'physical_address', 'plot_number', 'house_number', 'status',
                   'credit_balance']);

        // If no results in customers, search by meter number
        if ($customers->isEmpty()) {
            $meter = Meter::where('meter_number', 'LIKE', "%{$searchTerm}%")
                ->where('status', 'active')
                ->with('customer')
                ->first();

            if ($meter && $meter->customer) {
                $customers = collect([$meter->customer]);
            }
        }

        $formattedCustomers = $customers->map(function($customer) {
            return [
                'id' => $customer->id,
                'customer_number' => $customer->customer_number,
                'full_name' => $customer->first_name . ' ' . $customer->last_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->plot_number . ', ' . $customer->house_number,
                'credit_balance' => (float) $customer->credit_balance,
                'status' => $customer->status,
                'meters' => $customer->meters->map(function($meter) {
                    return [
                        'meter_number' => $meter->meter_number,
                        'current_reading' => (float) $meter->current_reading,
                        'status' => $meter->status,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedCustomers,
            'count' => $formattedCustomers->count()
        ]);
    }
}
