<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $upcoming_bookings = Booking::where('bookable_type', 'App\Models\User')
            ->where('bookable_id', $user->id)
            ->where('status', 'confirmed')
            ->get();

        $total_bookings = $upcoming_bookings->count();

        $total_revenue = Booking::where('bookable_type', 'App\Models\User')
            ->where('bookable_id', $user->id)
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->sum('amount_paid');

        return view('dashboard.vendor', compact('upcoming_bookings', 'total_bookings', 'total_revenue'));
    }

    public function getVendorsData(Request $request)
    {
        $query = User::role('vendor')
            ->withCount(['bookings as total_bookings']);

        return DataTables::of($query)
            ->addColumn('avatar_url', function ($user) {
                return $user->avatar ?? null;
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('status') && $request->status !== '') {
                    if ($request->status === 'active') {
                        $query->where('is_active', true);
                    } elseif ($request->status === 'inactive') {
                        $query->where('is_active', false);
                    }
                }

                if ($request->has('verification') && $request->verification !== '') {
                    if ($request->verification === 'verified') {
                        $query->where('is_verified', true);
                    } elseif ($request->verification === 'unverified') {
                        $query->where('is_verified', false);
                    }
                }

                // Search filter
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('business_name', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%")
                            ->orWhere('services_offered', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    public function getVendorsStats()
    {
        $totalVendors = User::role('vendor')->count();
        $activeVendors = User::role('vendor')->where('is_active', true)->count();
        $verifiedVendors = User::role('vendor')->where('is_verified', true)->count();
        $totalBookings = Booking::whereHas('user', function ($query) {
            $query->role('vendor');
        })->count();

        return response()->json([
            'total_vendors' => $totalVendors,
            'active_vendors' => $activeVendors,
            'verified_vendors' => $verifiedVendors,
            'total_bookings' => $totalBookings
        ]);
    }

    public function getVendorDetails($id)
    {
        $vendor = User::role('vendor')
            ->with(['bookings' => function ($query) {
                $query->latest()->take(5);
            }])
            ->findOrFail($id);

        $html = view('admin.vendors.partials.vendor-details', compact('vendor'))->render();

        return response()->json([
            'vendor' => $vendor,
            'html' => $html
        ]);
    }
}
