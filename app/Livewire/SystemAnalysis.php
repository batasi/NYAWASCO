<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\MeterReading;

class SystemAnalysis extends Component
{
    public $selectedMetric = 'overview';
    public $timeRange = '30';
    public $search = '';
    public $filterStatus = '';
    public $filterType = '';

    // Overview metrics
    public $overview = [
        'total_users' => 0,
        'active_users' => 0,
        'total_customers' => 0,
        'active_customers' => 0,
        'total_meters' => 0,
        'active_meters' => 0,
        'total_bills' => 0,
        'paid_bills' => 0,
        'total_payments' => 0,
        'pending_payments' => 0,
        'business_permits' => 0,
        'active_businesses' => 0,
    ];

    // Revenue analytics
    public $revenue = [
        'total_revenue' => 0,
        'monthly_trend' => [],
        'daily_trend' => [],
        'by_stream' => [],
        'avg_transaction' => 0,
    ];

    // User analytics
    public $users = [
        'login_patterns' => [],
        'registrations' => [],
        'by_department' => [],
        'total_logins' => 0,
        'peak_hour' => null,
    ];

    // Business insights
    public $business = [
        'total_businesses' => 0,
        'active_businesses' => 0,
        'by_category' => [],
        'registrations' => [],
        'top_performers' => [],
    ];

    // System health
    public $system = [
        'uptime_percentage' => 95,
        'avg_response_time' => 150,
        'active_connections' => 45,
        'storage_usage' => [
            'used' => 2.5,
            'total' => 10,
            'percentage' => 25,
        ],
        'health_score' => 95,
        'security' => [
            'failed_logins' => 12,
            'suspicious_activities' => 3,
        ],
    ];

    protected $listeners = ['metricChanged'];

    public function mount()
    {
        $this->loadOverviewData();
        $this->loadRevenueData();
        $this->loadUserData();
        $this->loadBusinessData();
        $this->loadSystemData();
    }

    public function updatedSelectedMetric()
    {
        $this->dispatch('metricChanged');
    }

    public function loadOverviewData()
    {
        $this->overview = Cache::remember('system_analysis_overview_' . $this->timeRange, 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_customers' => Customer::count(),
                'active_customers' => Customer::where('status', 'active')->count(),
                'total_meters' => Meter::count(),
                'active_meters' => Meter::where('status', 'active')->count(),
                'total_bills' => Bill::count(),
                'paid_bills' => Bill::where('bill_status', 'paid')->count(),
                'total_payments' => Payment::count(),
                'pending_payments' => Payment::where('payment_status', 'pending')->count(),
                'business_permits' => 0, // Placeholder
                'active_businesses' => 0, // Placeholder
            ];
        });
    }

    public function loadRevenueData()
    {
        $this->revenue = Cache::remember('system_analysis_revenue_' . $this->timeRange, 300, function () {
            $startDate = now()->subDays($this->timeRange);

            return [
                'total_revenue' => Payment::where('created_at', '>=', $startDate)->sum('amount'),
                'monthly_trend' => Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as revenue')
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'month' => date('M Y', strtotime($item->month . '-01')),
                            'revenue' => (float) $item->revenue,
                        ];
                    }),
                'daily_trend' => Payment::selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'revenue' => (float) $item->revenue,
                        ];
                    }),
                'by_stream' => Payment::selectRaw('payment_method as name, COUNT(*) as count, SUM(amount) as revenue')
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('payment_method')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => ucfirst($item->name ?? 'Other'),
                            'count' => (int) $item->count,
                            'revenue' => (float) $item->revenue,
                        ];
                    }),
                'avg_transaction' => Payment::where('created_at', '>=', $startDate)->avg('amount') ?? 0,
            ];
        });
    }

    public function loadUserData()
    {
        $this->users = Cache::remember('system_analysis_users_' . $this->timeRange, 300, function () {
            $startDate = now()->subDays($this->timeRange);

            $loginPatterns = DB::table('users')
                ->selectRaw('HOUR(last_login_at) as hour, COUNT(*) as logins, AVG(TIMESTAMPDIFF(MINUTE, last_login_at, FROM_UNIXTIME(last_activity))) as avg_duration')
                ->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', $startDate)
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            return [
                'login_patterns' => $loginPatterns,
                'registrations' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'count' => (int) $item->count,
                        ];
                    }),
                'by_department' => [], // Bypassed as requested - department column not available
                'total_logins' => $loginPatterns->sum('logins'),
                'peak_hour' => $loginPatterns->sortByDesc('logins')->first(),
            ];
        });
    }

    public function loadBusinessData()
    {
        $this->business = Cache::remember('system_analysis_business_' . $this->timeRange, 300, function () {
            $startDate = now()->subDays($this->timeRange);

            return [
                'total_businesses' => 0, // Placeholder - implement when business model exists
                'active_businesses' => 0, // Placeholder
                'by_category' => [], // Placeholder
                'registrations' => [], // Placeholder
                'top_performers' => [], // Placeholder
            ];
        });
    }

    public function loadSystemData()
    {
        $this->system = Cache::remember('system_analysis_system_' . $this->timeRange, 300, function () {
            // Calculate system metrics
            $uptime = 95; // Placeholder - implement actual uptime calculation
            $responseTime = rand(100, 200); // Placeholder
            $activeConnections = rand(30, 60); // Placeholder

            // Storage calculation (simplified)
            $storageUsed = 2.5; // GB
            $storageTotal = 10; // GB
            $storagePercentage = ($storageUsed / $storageTotal) * 100;

            // Security metrics - tables not available, set to 0
            $failedLogins = 0;
            $suspiciousActivities = 0;

            $healthScore = min(100, 100 - ($failedLogins * 2) - ($suspiciousActivities * 5));

            return [
                'uptime_percentage' => $uptime,
                'avg_response_time' => $responseTime,
                'active_connections' => $activeConnections,
                'storage_usage' => [
                    'used' => $storageUsed,
                    'total' => $storageTotal,
                    'percentage' => round($storagePercentage, 1),
                ],
                'health_score' => $healthScore,
                'security' => [
                    'failed_logins' => $failedLogins,
                    'suspicious_activities' => $suspiciousActivities,
                ],
            ];
        });
    }

    public function updatedTimeRange()
    {
        Cache::forget('system_analysis_overview_' . $this->timeRange);
        Cache::forget('system_analysis_revenue_' . $this->timeRange);
        Cache::forget('system_analysis_users_' . $this->timeRange);
        Cache::forget('system_analysis_business_' . $this->timeRange);
        Cache::forget('system_analysis_system_' . $this->timeRange);

        $this->loadOverviewData();
        $this->loadRevenueData();
        $this->loadUserData();
        $this->loadBusinessData();
        $this->loadSystemData();
    }

    public function render()
    {
        return view('livewire.system-analysis');
    }
}