<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Zone;
use App\Models\Meter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentDashboardController extends Controller
{
    /**
     * Display the main payments dashboard
     */
    public function index(Request $request)
    {
        // Get date range filters
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $zoneId = $request->get('zone', 'all');

        // Parse dates
        $startDateParsed = Carbon::parse($startDate);
        $endDateParsed = Carbon::parse($endDate);
        $previousStartDate = $startDateParsed->copy()->subMonth();
        $previousEndDate = $endDateParsed->copy()->subMonth();

        // Base queries with filters
        $paymentQuery = $this->buildPaymentQuery($zoneId, $startDateParsed, $endDateParsed);
        $previousPaymentQuery = $this->buildPaymentQuery($zoneId, $previousStartDate, $previousEndDate);
        $billQuery = $this->buildBillQuery($zoneId);

        // ==================== CARD STATISTICS ====================
        $cardStats = [
            // Total Collections
            'total_collections' => (float) $paymentQuery->clone()
                ->where('payment_status', 'completed')
                ->sum('amount'),

            'previous_total_collections' => (float) $previousPaymentQuery->clone()
                ->where('payment_status', 'completed')
                ->sum('amount'),

            // Today's Collection
            'today_collection' => (float) $paymentQuery->clone()
                ->where('payment_status', 'completed')
                ->whereDate('payment_date', Carbon::today())
                ->sum('amount'),

            // Total Arrears
            'total_arrears' => (float) $billQuery->clone()
                ->where('bill_status', '!=', 'paid')
                ->where('balance', '>', 0)
                ->sum('balance'),


            // Collection Efficiency (Payments vs Arrears)
            'collection_efficiency' => 0,

            // Average Payment Amount
            'avg_payment_amount' => (float) $paymentQuery->clone()
                ->where('payment_status', 'completed')
                ->avg('amount') ?: 0,

            // Payment Count
            'payment_count' => $paymentQuery->clone()
                ->where('payment_status', 'completed')
                ->count(),

            'previous_payment_count' => $previousPaymentQuery->clone()
                ->where('payment_status', 'completed')
                ->count(),

            // Customers with Outstanding Balances
            'customers_with_arrears' => $billQuery->clone()
                ->where('bill_status', '!=', 'paid')
                ->where('balance', '>', 0)
                ->distinct('customer_id')
                ->count('customer_id'),
        ];

        // Calculate collection efficiency
        if ($cardStats['total_arrears'] + $cardStats['total_collections'] > 0) {
            $cardStats['collection_efficiency'] = round(
                ($cardStats['total_collections'] / ($cardStats['total_collections'] + $cardStats['total_arrears'])) * 100,
                2
            );
        }

        // ==================== CHARTS DATA ====================
        $chartsData = $this->getChartsData($paymentQuery, $billQuery, $startDateParsed, $endDateParsed, $zoneId);

        // ==================== ZONE COMPARISON ====================
        $zoneComparison = $this->getZoneComparison($startDateParsed, $endDateParsed);

        // ==================== PAYMENT METHODS ====================
        $paymentMethods = $this->getPaymentMethodsData($paymentQuery);

        // ==================== TOP/BOTTOM PERFORMERS ====================
        $performanceData = $this->getPerformanceData($startDateParsed, $endDateParsed, $zoneId);

        // Get all zones for filter
        $zones = Zone::orderBy('name')->get();

        return view('admin.payments.dashboard', compact(
            'cardStats',
            'chartsData',
            'zoneComparison',
            'paymentMethods',
            'performanceData',
            'zones',
            'zoneId',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Build base payment query with filters
     */
    private function buildPaymentQuery($zoneId, $startDate, $endDate)
    {
        $query = Payment::query();

        // Apply date filter
        $query->whereBetween('payment_date', [$startDate, $endDate]);

        // Apply zone filter
        if ($zoneId && $zoneId !== 'all') {
            $query->whereHas('meter', function($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        }

        return $query;
    }

    /**
     * Build base bill query with filters
     */
    private function buildBillQuery($zoneId)
    {
        $query = Bill::query();

        // Apply zone filter
        if ($zoneId && $zoneId !== 'all') {
            $query->whereHas('meter', function($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        }

        return $query;
    }

    /**
     * Get data for all charts
     */
    private function getChartsData($paymentQuery, $billQuery, $startDate, $endDate, $zoneId)
    {
        $data = [];

        // 1. DAILY COLLECTIONS LINE CHART
        $data['daily_collections'] = $paymentQuery->clone()
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total_amount')
            ->where('payment_status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function($item) {
                return [Carbon::parse($item->date)->format('M d') => (float) $item->total_amount];
            });

        // 2. MONTHLY TREND (Last 6 months)
        $data['monthly_trend'] = Payment::query()
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total_amount')
            ->where('payment_status', 'completed')
            ->where('payment_date', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                return $query->whereHas('meter', function($q) use ($zoneId) {
                    $q->where('zone_id', $zoneId);
                });
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function($item) {
                return [Carbon::parse($item->month)->format('M Y') => (float) $item->total_amount];
            });

        // 3. ARREARS VS COLLECTIONS (Dual Axis)
        $data['arrears_vs_collections'] = $this->getArrearsVsCollections($startDate, $endDate, $zoneId);

        // 4. PAYMENT STATUS DISTRIBUTION
        $data['payment_status_distribution'] = $paymentQuery->clone()
            ->selectRaw('payment_status, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_status')
            ->get();

        // 5. COLLECTION BY HOUR (for today)
        $data['hourly_collections'] = $paymentQuery->clone()
            ->selectRaw('HOUR(created_at) as hour, SUM(amount) as total_amount')
            ->where('payment_status', 'completed')
            ->whereDate('payment_date', Carbon::today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->mapWithKeys(function($item) {
                return [sprintf('%02d:00', $item->hour) => (float) $item->total_amount];
            });

        // 6. BILLING STATUS DISTRIBUTION
        $data['billing_status'] = $billQuery->clone()
            ->selectRaw('
                SUM(CASE WHEN bill_status = "paid" THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN bill_status = "unpaid" THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN bill_status = "overdue" THEN 1 ELSE 0 END) as overdue_count,
                SUM(CASE WHEN bill_status = "partial" THEN 1 ELSE 0 END) as partial_count
            ')
            ->first();

        return $data;
    }

    /**
     * Get arrears vs collections comparison
     */
    private function getArrearsVsCollections($startDate, $endDate, $zoneId)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');

            // Collections for this day
            $collections = Payment::query()
                ->whereDate('payment_date', $currentDate)
                ->where('payment_status', 'completed')
                ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                    return $query->whereHas('meter', function($q) use ($zoneId) {
                        $q->where('zone_id', $zoneId);
                    });
                })
                ->sum('amount');

            // Arrears as of this day (unpaid bills with due date <= this day)
            $arrears = Bill::query()
                ->where('due_date', '<=', $currentDate)
                ->where('bill_status', '!=', 'paid')
                ->where('balance', '>', 0)
                ->when($zoneId && $zoneId !== 'all', function ($query) use ($zoneId) {
                    $query->whereHas('meter', function ($q) use ($zoneId) {
                        $q->where('zone_id', $zoneId);
                    });
                })
                ->sum('balance');


            $data[] = [
                'date' => $currentDate->format('M d'),
                'collections' => (float) $collections,
                'arrears' => (float) $arrears,
                'collection_ratio' => $arrears > 0 ? ($collections / $arrears) * 100 : 0
            ];

            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Get zone-wise comparison data
     */
    private function getZoneComparison($startDate, $endDate)
    {
        $zones = Zone::withCount(['meters as active_meters_count' => function($query) {
            $query->where('status', 'active');
        }])->get();

        $zoneData = [];

        foreach ($zones as $zone) {
            // Collections for this zone
            $collections = Payment::query()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('payment_status', 'completed')
                ->whereHas('meter', function($query) use ($zone) {
                    $query->where('zone_id', $zone->id);
                })
                ->sum('amount');

           // Arrears for this zone
            $arrears = Bill::query()
                ->where('bill_status', '!=', 'paid')
                ->where('balance', '>', 0)
                ->whereHas('meter', function ($query) use ($zone) {
                    $query->where('zone_id', $zone->id);
                })
                ->sum('balance');


            // Number of paying customers
            $payingCustomers = Payment::query()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('payment_status', 'completed')
                ->whereHas('meter', function($query) use ($zone) {
                    $query->where('zone_id', $zone->id);
                })
                ->distinct('customer_id')
                ->count('customer_id');

            // Total customers in zone
            $totalCustomers = Customer::query()
                ->whereHas('meters', function($query) use ($zone) {
                    $query->where('zone_id', $zone->id);
                })
                ->count();

            $zoneData[] = [
                'zone' => $zone->name,
                'collections' => (float) $collections,
                'arrears' => (float) $arrears,
                'collection_rate' => $totalCustomers > 0 ? ($payingCustomers / $totalCustomers) * 100 : 0,
                'active_meters' => $zone->active_meters_count,
                'average_payment' => $payingCustomers > 0 ? $collections / $payingCustomers : 0
            ];
        }

        // Sort by collections descending
        usort($zoneData, function($a, $b) {
            return $b['collections'] <=> $a['collections'];
        });

        return $zoneData;
    }

    /**
     * Get payment methods distribution
     */
    private function getPaymentMethodsData($paymentQuery)
    {
        return $paymentQuery->clone()
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount
            ')
            ->where('payment_status', 'completed')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
    }

    /**
     * Get top and bottom performers
     */
    private function getPerformanceData($startDate, $endDate, $zoneId)
    {
        // Top 5 collectors (meters/customers)
        $topCollectors = Payment::query()
            ->selectRaw('
                customer_id,
                customers.first_name,
                customers.last_name,
                COUNT(payments.id) as payment_count,
                SUM(payments.amount) as total_collected
            ')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('payment_status', 'completed')
            ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                return $query->whereHas('meter', function($q) use ($zoneId) {
                    $q->where('zone_id', $zoneId);
                });
            })
            ->groupBy('customer_id', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_collected')
            ->limit(5)
            ->get();

        // Top 5 arrears (customers with highest outstanding)
        $topArrears = Bill::query()
            ->selectRaw('
                customer_id,
                customers.first_name,
                customers.last_name,
                SUM(balance) as total_arrears,
                COUNT(CASE WHEN due_date < CURDATE() THEN 1 END) as overdue_bills
            ')
            ->join('customers', 'bills.customer_id', '=', 'customers.id')
            ->where('balance', '>', 0)
            ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                return $query->whereHas('meter', function($q) use ($zoneId) {
                    $q->where('zone_id', $zoneId);
                });
            })
            ->groupBy('customer_id', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_arrears')
            ->limit(5)
            ->get();

        return [
            'top_collectors' => $topCollectors,
            'top_arrears' => $topArrears
        ];
    }

    /**
     * Get real-time dashboard data (for AJAX updates)
     */
    public function realtimeData(Request $request)
    {
        $zoneId = $request->get('zone', 'all');

        $data = [
            'today_collection' => Payment::query()
                ->whereDate('payment_date', Carbon::today())
                ->where('payment_status', 'completed')
                ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                    return $query->whereHas('meter', function($q) use ($zoneId) {
                        $q->where('zone_id', $zoneId);
                    });
                })
                ->sum('amount'),

            'recent_payments' => Payment::query()
                ->with(['customer', 'meter'])
                ->where('payment_status', 'completed')
                ->when($zoneId && $zoneId !== 'all', function($query) use ($zoneId) {
                    return $query->whereHas('meter', function($q) use ($zoneId) {
                        $q->where('zone_id', $zoneId);
                    });
                })
                ->orderByDesc('payment_date')
                ->limit(5)
                ->get(),

            'updated_at' => now()->toDateTimeString()
        ];

        return response()->json($data);
    }
    /**
     * Export dashboard data
     */
    public function exportDashboard(Request $request)
    {
        // Get filters from request
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        $zoneId = $request->get('zone', 'all');

        // Build queries
        $paymentQuery = $this->buildPaymentQuery($zoneId, $startDate, $endDate);
        $zoneComparison = $this->getZoneComparison($startDate, $endDate);

        // Create CSV or Excel export
        // You can use Laravel Excel package or simple CSV
        $data = [
            'summary' => [
                'Period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
                'Total Collections' => $paymentQuery->clone()->where('payment_status', 'completed')->sum('amount'),
                'Total Payments' => $paymentQuery->clone()->where('payment_status', 'completed')->count(),
            ],
            'zone_performance' => $zoneComparison,
            'exported_at' => now()->toDateTimeString()
        ];

        // Return JSON for now - you can implement CSV/Excel export
        return response()->json($data);
    }
}
