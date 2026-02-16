<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\Zone;
use App\Models\WalkRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $request->validate([
            'report_type' => 'required|in:revenue,customer,meter,consumption,collection,arrears,category,zone',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'nullable|in:pdf,excel,csv,view',
            'detail_level' => 'nullable|in:summary,detailed,full',
            'customer_id' => 'required_if:report_type,statement|exists:customers,id',
            'zone' => 'nullable|exists:zones,id',
            'status' => 'nullable|in:all,paid,unpaid,partial,overdue,completed,pending,failed',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $detailLevel = $request->detail_level ?? 'summary';

        // Pass filters to generateReportData
        $filters = $request->only(['zone', 'status', 'search']);

        $reportData = $this->generateReportData(
            $request->report_type,
            $startDate,
            $endDate,
            $detailLevel,
            $filters // Pass the filters here
        );

        if ($request->format === 'pdf') {
            return $this->generatePDF($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'excel') {
            return $this->generateExcel($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'csv') {
            return $this->generateCSV($reportData, $request->report_type, $startDate, $endDate);
        }

        return view('reports.show', compact('reportData', 'startDate', 'endDate'));
    }

    private function generateReportData($type, $startDate, $endDate, $detailLevel = 'summary', $filters = [])
    {
        switch ($type) {
            case 'revenue':
                return $this->generateRevenueReport($startDate, $endDate, $detailLevel, $filters);
            case 'customer':
                return $this->generateCustomerReport($startDate, $endDate, $detailLevel, $filters);
            case 'meter':
                return $this->generateMeterReport($startDate, $endDate, $detailLevel, $filters);
            case 'consumption':
                return $this->generateConsumptionReport($startDate, $endDate, $detailLevel, $filters);
            case 'collection':
                return $this->generateCollectionReport($startDate, $endDate, $detailLevel, $filters);
            case 'arrears':
                return $this->generateArrearsReport($startDate, $endDate, $detailLevel, $filters);
            case 'statement':
                $customerId = $filters['customer_id'] ?? null;
                if (!$customerId) {
                    throw new \Exception('Customer ID is required for statement report');
                }
                return $this->generateCustomerStatementReport($customerId, $startDate, $endDate, $detailLevel, $filters);
            case 'category':
                return $this->generateCategoryReport($startDate, $endDate, $detailLevel, $filters);
            case 'zone':
                return $this->generateZoneReport($startDate, $endDate, $detailLevel, $filters);
            default:
                return [];
        }
    }

    private function generateRevenueReport($startDate, $endDate, $detailLevel, $filters = [])
    {
        $query = Bill::with(['customer', 'meter.meterCategory', 'meter.zone', 'meter.walkroute']);

        // Apply date filter
        if ($startDate) {
            // Include NULL records when filtering by date
            $query->where(function($q) use ($startDate, $endDate) {
                // Records with actual dates in the range
                $q->whereBetween('billing_period_end', [$startDate, $endDate])
                // OR records with NULL billing_period_end (treat as before start)
                ->orWhereNull('billing_period_end');
            });
        }

        // Apply zone filter
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $query->whereHas('meter', function($q) use ($filters) {
                $q->where('zone_id', $filters['zone']);
            });
        }

        // Apply status filter (for bills, we need to map status)
        if (isset($filters['status']) && $filters['status'] != 'all') {
            // Map collection status to bill status
            $statusMap = [
                'completed' => 'paid', // Assuming 'completed' payments = 'paid' bills
                'pending' => 'partial', // Assuming 'pending' payments = 'partial' bills
                'failed' => 'unpaid' // Assuming 'failed' payments = 'unpaid' bills
            ];

            if (array_key_exists($filters['status'], $statusMap)) {
                $query->where('bill_status', $statusMap[$filters['status']]);
            }
        }

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhere('balance', 'like', "%{$search}%")
                    ->orWhere('consumption', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('customer_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('meter', function($q) use ($search) {
                        $q->where('meter_number', 'like', "%{$search}%");
                    });
            });
        }

        $bills = $query->get();

        // Monthly breakdown with filters
        $monthlyQuery = DB::table('bills')
            ->select(
                DB::raw('YEAR(IFNULL(billing_period_end, "1900-01-01")) as year'),
                DB::raw('MONTH(IFNULL(billing_period_end, "1900-01-01")) as month'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(consumption) as total_consumption'),
                DB::raw('SUM(CASE WHEN billing_period_end IS NULL THEN 1 ELSE 0 END) as is_legacy')
            );

        // Apply date filter to monthly query
        if ($startDate) {
            $monthlyQuery->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('billing_period_end', [$startDate, $endDate])
                    ->orWhereNull('billing_period_end');
            });
        }

        // Apply zone filter to monthly query
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $monthlyQuery->whereExists(function ($query) use ($filters) {
                $query->select(DB::raw(1))
                    ->from('meters')
                    ->whereColumn('meters.id', 'bills.meter_id')
                    ->where('meters.zone_id', $filters['zone']);
            });
        }

        $monthlyRevenue = $monthlyQuery->groupBy(DB::raw('YEAR(IFNULL(billing_period_end, "1900-01-01")), MONTH(IFNULL(billing_period_end, "1900-01-01"))'))
            ->get();

        // Category breakdown with filters
        $categoryQuery = DB::table('bills')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->join('meter_categories', 'meters.meter_category_id', '=', 'meter_categories.id')
            ->select(
                'meter_categories.name as category',
                'meter_categories.code',
                DB::raw('SUM(bills.total_amount) as total_amount'),
                DB::raw('SUM(bills.paid_amount) as paid_amount'),
                DB::raw('SUM(bills.consumption) as total_consumption'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(CASE WHEN bills.billing_period_end IS NULL THEN 1 ELSE 0 END) as legacy_records')
            );

        if ($startDate) {
            $categoryQuery->where(function($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('bills.billing_period_end', [$startDate, $endDate])
                        ->orWhereNull('bills.billing_period_end');
            });
        }

        // Apply zone filter to category query
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $categoryQuery->where('meters.zone_id', $filters['zone']);
        }

        $categoryRevenue = $categoryQuery->groupBy('meter_categories.id', 'meter_categories.name', 'meter_categories.code')
            ->get();

        // Zone breakdown with filters
        $zoneQuery = DB::table('bills')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->leftJoin('zones', 'meters.zone_id', '=', 'zones.id')
            ->select(
                DB::raw('COALESCE(zones.name, "Unassigned") as zone_name'),
                DB::raw('SUM(bills.total_amount) as total_amount'),
                DB::raw('SUM(bills.paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(CASE WHEN bills.billing_period_end IS NULL THEN 1 ELSE 0 END) as legacy_records')
            );

        if ($startDate) {
            $zoneQuery->where(function($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('bills.billing_period_end', [$startDate, $endDate])
                        ->orWhereNull('bills.billing_period_end');
            });
        }

        // Apply zone filter to zone query (if zone is selected)
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $zoneQuery->where('meters.zone_id', $filters['zone']);
        }

        $zoneRevenue = $zoneQuery->groupBy('zones.id', 'zones.name')
            ->get();

        // Add zone information to the report
        $zoneInfo = null;
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $zoneInfo = \App\Models\Zone::find($filters['zone']);
        }

        return [
            'type' => 'Revenue Report',
            'detail_level' => $detailLevel,
            'filters' => $filters,
            'zone_info' => $zoneInfo,
            'bills' => $bills,
            'monthly_breakdown' => $monthlyRevenue,
            'category_breakdown' => $categoryRevenue,
            'zone_breakdown' => $zoneRevenue,
            'summary' => [
                'total_amount' => $bills->sum('total_amount'),
                'total_paid' => $bills->sum('paid_amount'),
                'total_balance' => $bills->sum('balance'),
                'total_consumption' => $bills->sum('consumption'),
                'bill_count' => $bills->count(),
                'paid_bills' => $bills->where('bill_status', 'paid')->count(),
                'unpaid_bills' => $bills->where('bill_status', 'unpaid')->count(),
                'partial_bills' => $bills->where('bill_status', 'partial')->count(),
                'overdue_bills' => $bills->where('is_overdue', true)->count(),
                'average_bill_amount' => $bills->avg('total_amount'),
                'collection_efficiency' => $bills->sum('total_amount') > 0 ?
                    ($bills->sum('paid_amount') / $bills->sum('total_amount')) * 100 : 0,
                'legacy_records_count' => $bills->whereNull('billing_period_end')->count(),
                'dated_records_count' => $bills->whereNotNull('billing_period_end')->count(),
            ]
        ];
    }

    private function generateCustomerReport($startDate, $endDate, $detailLevel)
    {
        // Always load all customers, with optional filtering of bills by date range
        $query = Customer::with(['meters.meterCategory', 'meters.zone', 'meters.walkRoute',
            'bills' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    // Only filter the bills relationship, not the customers
                    $q->whereBetween('billing_period_end', [$startDate, $endDate]);
                }
            }]);

        // REMOVED the whereHas clause - this ensures all customers are included
        // No filtering based on bills - include ALL customers

        $customers = $query->get()->map(function ($customer) use ($startDate) {
            // Calculate totals from the pre-filtered bills relationship
            // If date range is provided, bills are already filtered
            // If no date range, all bills are included

            $customer->total_billed = $customer->bills->sum('total_amount');
            $customer->total_paid = $customer->bills->sum('paid_amount');
            $customer->total_balance = $customer->bills->sum('balance');
            $customer->total_consumption = $customer->bills->sum('consumption');
            $customer->bill_count = $customer->bills->count();
            $customer->meter_count = $customer->meters->count();

            // Add a flag to show if customer had bills in the date range (if date range was provided)
            if ($startDate) {
                $customer->had_bills_in_period = $customer->bills->count() > 0;
            }

            return $customer;
        });

        // Status breakdown - now includes ALL customers
        $statusCounts = $customers->groupBy('status')->map->count();

        // Zone distribution - includes customers with and without meters
        $zoneDistribution = $customers->flatMap(function ($customer) {
            if ($customer->meters->count() > 0) {
                return $customer->meters->map(function ($meter) use ($customer) {
                    return [
                        'customer_id' => $customer->id,
                        'zone_name' => $meter->zone->name ?? 'Unassigned',
                        'customer_number' => $customer->customer_number,
                    ];
                });
            } else {
                // Include customers without meters in "No Meter Assigned" category
                return [[
                    'customer_id' => $customer->id,
                    'zone_name' => 'No Meter Assigned',
                    'customer_number' => $customer->customer_number,
                ]];
            }
        })->groupBy('zone_name')->map(function ($items, $zone) {
            return [
                'zone' => $zone,
                'customer_count' => $items->unique('customer_id')->count(),
            ];
        });

        return [
            'type' => 'Customer Report',
            'detail_level' => $detailLevel,
            'date_range' => $startDate ? [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ] : null,
            'customers' => $customers,
            'status_breakdown' => $statusCounts,
            'zone_distribution' => $zoneDistribution,
            'summary' => [
                'total_customers' => $customers->count(),
                'active_customers' => $customers->where('status', 'active')->count(),
                'inactive_customers' => $customers->where('status', 'inactive')->count(),
                'pending_customers' => $customers->where('status', 'pending')->count(),
                'suspended_customers' => $customers->where('status', 'suspended')->count(),

                // Financial totals - only from filtered bills if date range provided
                'total_billed' => $customers->sum('total_billed'),
                'total_paid' => $customers->sum('total_paid'),
                'total_balance' => $customers->sum('total_balance'),
                'total_consumption' => $customers->sum('total_consumption'),

                // Metrics about bills in period (if date range provided)
                'customers_with_bills_in_period' => $startDate ?
                    $customers->where('had_bills_in_period', true)->count() :
                    $customers->where('bill_count', '>', 0)->count(),
                'customers_without_bills_in_period' => $startDate ?
                    $customers->where('had_bills_in_period', false)->count() :
                    $customers->where('bill_count', 0)->count(),

                'average_consumption_per_customer' => $customers->avg('total_consumption'),
                'average_bills_per_customer' => $customers->avg('bill_count'),
                'customers_with_meters' => $customers->where('meter_count', '>', 0)->count(),
                'customers_without_meters' => $customers->where('meter_count', 0)->count(),
                'customers_without_bills_ever' => $customers->where('bill_count', 0)->count(),
            ]
        ];
    }

    private function generateMeterReport($startDate, $endDate, $detailLevel, $filters = [])
    {
        $query = Meter::with([
            'meterCategory',
            'customer',
            'zone',
            'walkRoute',
            'bills' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereBetween('billing_period_end', [$startDate, $endDate]);
                }
            }
        ]);

        // Apply filters if provided
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $query->where('zone_id', $filters['zone']);
        }

        if (isset($filters['category']) && $filters['category'] != 'all') {
            $query->where('meter_category_id', $filters['category']);
        }

        if (isset($filters['status']) && $filters['status'] != 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('meter_number', 'like', "%{$search}%")
                ->orWhere('meter_model', 'like', "%{$search}%")
                ->orWhere('installation_address', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%");
                });
            });
        }

        $meters = $query->get()->map(function ($meter) {
            $meter->total_billed = $meter->bills->sum('total_amount');
            $meter->total_paid = $meter->bills->sum('paid_amount');
            $meter->total_balance = $meter->bills->sum('balance');
            $meter->total_consumption = $meter->bills->sum('consumption');
            $meter->bill_count = $meter->bills->count();
            $meter->last_reading_date = $meter->bills->max('billing_period_end');
            return $meter;
        });

        // Category breakdown with filters
        $categoryQuery = MeterCategory::withCount(['meters' => function($q) use ($filters) {
            if (isset($filters['zone']) && $filters['zone'] != 'all') {
                $q->where('zone_id', $filters['zone']);
            }
            if (isset($filters['status']) && $filters['status'] != 'all') {
                $q->where('status', $filters['status']);
            }
        }])->get();

        $categoryStats = $categoryQuery->map(function ($category) {
            return [
                'category' => $category->name,
                'count' => $category->meters_count,
                'meters_with_customers' => $category->meters()->whereNotNull('customer_id')->count(),
                'meters_without_customers' => $category->meters()->whereNull('customer_id')->count(),
            ];
        });

        // Zone breakdown
        $zoneQuery = Zone::withCount(['meters' => function($q) use ($filters) {
            if (isset($filters['category']) && $filters['category'] != 'all') {
                $q->where('meter_category_id', $filters['category']);
            }
            if (isset($filters['status']) && $filters['status'] != 'all') {
                $q->where('status', $filters['status']);
            }
        }])->get();

        $zoneStats = $zoneQuery->map(function ($zone) {
            return [
                'zone' => $zone->name,
                'count' => $zone->meters_count,
                'meters_with_customers' => $zone->meters()->whereNotNull('customer_id')->count(),
            ];
        });

        // Status breakdown
        $statusStats = [
            'active' => [
                'status' => 'Active',
                'count' => $meters->where('status', 'active')->count(),
                'percentage' => $meters->count() > 0 ? ($meters->where('status', 'active')->count() / $meters->count()) * 100 : 0,
            ],
            'available' => [
                'status' => 'Available',
                'count' => $meters->where('status', 'available')->count(),
                'percentage' => $meters->count() > 0 ? ($meters->where('status', 'available')->count() / $meters->count()) * 100 : 0,
            ],
            'maintenance' => [
                'status' => 'Maintenance',
                'count' => $meters->where('status', 'maintenance')->count(),
                'percentage' => $meters->count() > 0 ? ($meters->where('status', 'maintenance')->count() / $meters->count()) * 100 : 0,
            ],
        ];

        // Add zone information to the report
        $zoneInfo = null;
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $zoneInfo = \App\Models\Zone::find($filters['zone']);
        }

        return [
            'type' => 'Meter Report',
            'detail_level' => $detailLevel,
            'filters' => $filters,
            'zone_info' => $zoneInfo,
            'meters' => $meters,
            'category_stats' => $categoryStats,
            'zone_stats' => $zoneStats,
            'status_stats' => $statusStats,
            'summary' => [
                'total_meters' => $meters->count(),
                'active_meters' => $meters->where('status', 'active')->count(),
                'available_meters' => $meters->where('status', 'available')->count(),
                'maintenance_meters' => $meters->where('status', 'maintenance')->count(),
                'meters_with_customers' => $meters->whereNotNull('customer_id')->count(),
                'meters_without_customers' => $meters->whereNull('customer_id')->count(),
                'total_billed' => $meters->sum('total_billed'),
                'total_paid' => $meters->sum('total_paid'),
                'total_balance' => $meters->sum('total_balance'),
                'total_consumption' => $meters->sum('total_consumption'),
                'average_consumption_per_meter' => $meters->avg('total_consumption'),
                'meters_with_bills' => $meters->where('bill_count', '>', 0)->count(),
                'meters_without_bills' => $meters->where('bill_count', 0)->count(),
            ]
        ];
    }

    private function generateConsumptionReport($startDate, $endDate, $detailLevel)
    {
        $query = MeterReading::with(['customer', 'meter.meterCategory', 'meter.zone']);

        if ($startDate) {
            $query->whereBetween('reading_date', [$startDate, $endDate]);
        }

        $readings = $query->get();

        // Monthly consumption
        $monthlyConsumption = DB::table('meter_readings')
            ->select(
                DB::raw('YEAR(reading_date) as year'),
                DB::raw('MONTH(reading_date) as month'),
                DB::raw('SUM(consumption) as total_consumption'),
                DB::raw('COUNT(*) as reading_count'),
                DB::raw('AVG(consumption) as avg_consumption'),
                DB::raw('MAX(consumption) as max_consumption'),
                DB::raw('MIN(consumption) as min_consumption')
            )
            ->where('billed', true)
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('reading_date', [$startDate, $endDate]);
            })
            ->groupBy(DB::raw('YEAR(reading_date), MONTH(reading_date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Category consumption
        $categoryConsumption = DB::table('meter_readings')
            ->join('meters', 'meter_readings.meter_id', '=', 'meters.id')
            ->join('meter_categories', 'meters.meter_category_id', '=', 'meter_categories.id')
            ->select(
                'meter_categories.name as category',
                DB::raw('SUM(meter_readings.consumption) as total_consumption'),
                DB::raw('AVG(meter_readings.consumption) as avg_consumption'),
                DB::raw('COUNT(*) as reading_count'),
                DB::raw('MAX(meter_readings.consumption) as max_consumption'),
                DB::raw('MIN(meter_readings.consumption) as min_consumption')
            )
            ->where('meter_readings.billed', true)
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('meter_readings.reading_date', [$startDate, $endDate]);
            })
            ->groupBy('meter_categories.id', 'meter_categories.name')
            ->get();

        // Zone consumption
        $zoneConsumption = DB::table('meter_readings')
            ->join('meters', 'meter_readings.meter_id', '=', 'meters.id')
            ->leftJoin('zones', 'meters.zone_id', '=', 'zones.id')
            ->select(
                'zones.name as zone',
                DB::raw('SUM(meter_readings.consumption) as total_consumption'),
                DB::raw('AVG(meter_readings.consumption) as avg_consumption'),
                DB::raw('COUNT(*) as reading_count')
            )
            ->where('meter_readings.billed', true)
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('meter_readings.reading_date', [$startDate, $endDate]);
            })
            ->groupBy('zones.id', 'zones.name')
            ->get();

        return [
            'type' => 'Consumption Report',
            'detail_level' => $detailLevel,
            'readings' => $readings,
            'monthly_consumption' => $monthlyConsumption,
            'category_consumption' => $categoryConsumption,
            'zone_consumption' => $zoneConsumption,
            'summary' => [
                'total_consumption' => $readings->sum('consumption'),
                'average_consumption' => $readings->avg('consumption'),
                'reading_count' => $readings->count(),
                'highest_consumption' => $readings->max('consumption'),
                'lowest_consumption' => $readings->min('consumption'),
                'estimated_readings' => $readings->where('estimated', true)->count(),
                'actual_readings' => $readings->where('estimated', false)->count(),
                'customers_with_readings' => $readings->unique('customer_id')->count(),
                'meters_with_readings' => $readings->unique('meter_id')->count(),
            ]
        ];
    }

    private function generateCollectionReport($startDate, $endDate, $detailLevel, $filters = [])
    {
        $query = Payment::with(['bill.customer', 'meter.meterCategory', 'collector', 'meter.zone']);

        // Apply date filter
        if ($startDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        // Apply zone filter
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $query->whereHas('meter', function($q) use ($filters) {
                $q->where('zone_id', $filters['zone']);
            });
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status'] != 'all') {
            $query->where('payment_status', $filters['status']);
        }

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('customer_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('meter', function($q) use ($search) {
                        $q->where('meter_number', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->get();

        // Daily collection with filters
        $dailyQuery = DB::table('payments')
            ->select(
                'payment_date',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(amount) as avg_amount')
            );

        // Apply date filter to daily query
        if ($startDate) {
            $dailyQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        // Apply zone filter to daily query
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $dailyQuery->whereExists(function ($query) use ($filters) {
                $query->select(DB::raw(1))
                    ->from('meters')
                    ->whereColumn('meters.id', 'payments.meter_id')
                    ->where('meters.zone_id', $filters['zone']);
            });
        }

        // Apply status filter to daily query
        if (isset($filters['status']) && $filters['status'] != 'all') {
            $dailyQuery->where('payment_status', $filters['status']);
        }

        $dailyCollection = $dailyQuery->groupBy('payment_date')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Payment method breakdown
        $methodBreakdown = $payments->groupBy('payment_method')->map(function ($group, $method) use ($payments) {
            return [
                'method' => $method,
                'total_amount' => $group->sum('amount'),
                'count' => $group->count(),
                'percentage' => ($payments->sum('amount') > 0) ?
                    ($group->sum('amount') / $payments->sum('amount')) * 100 : 0,
                'avg_amount' => $group->avg('amount'),
            ];
        });

        // Collector performance with filters
        $collectorQuery = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name as collector_name',
                DB::raw('SUM(payments.amount) as total_collected'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(payments.amount) as avg_payment')
            );

        if ($startDate) {
            $collectorQuery->whereBetween('payments.payment_date', [$startDate, $endDate]);
        }

        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $collectorQuery->whereExists(function ($query) use ($filters) {
                $query->select(DB::raw(1))
                    ->from('meters')
                    ->whereColumn('meters.id', 'payments.meter_id')
                    ->where('meters.zone_id', $filters['zone']);
            });
        }

        $collectorPerformance = $collectorQuery->groupBy('users.id', 'users.name')
            ->orderBy('total_collected', 'desc')
            ->get();

        // Add zone information to the report
        $zoneInfo = null;
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $zoneInfo = \App\Models\Zone::find($filters['zone']);
        }

        return [
            'type' => 'Collection Report',
            'detail_level' => $detailLevel,
            'filters' => $filters,
            'zone_info' => $zoneInfo,
            'payments' => $payments,
            'daily_collection' => $dailyCollection,
            'method_breakdown' => $methodBreakdown,
            'collector_performance' => $collectorPerformance,
            'summary' => [
                'total_collected' => $payments->sum('amount'),
                'payment_count' => $payments->count(),
                'average_payment' => $payments->avg('amount'),
                'highest_payment' => $payments->max('amount'),
                'lowest_payment' => $payments->min('amount'),
                'successful_payments' => $payments->where('payment_status', 'completed')->count(),
                'pending_payments' => $payments->where('payment_status', 'pending')->count(),
                'failed_payments' => $payments->where('payment_status', 'failed')->count(),
                'voided_payments' => $payments->whereNotNull('voided_at')->count(),
                'unique_customers' => $payments->unique('customer_id')->count(),
                'unique_meters' => $payments->unique('meter_id')->count(),
                'collection_efficiency' => $payments->where('payment_status', 'completed')->sum('amount') /
                    max($payments->sum('amount'), 1) * 100,
            ]
        ];
    }

    private function addReportHeaderWithFilters($sheet, $reportType, $startDate, $endDate, $filters = [])
    {
        // Title
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'NYAWASCO - ' . strtoupper($reportType) . ' REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Period
        $sheet->setCellValue('A2', 'Period:');
        $sheet->setCellValue('B2', $startDate ?
            $startDate->format('d/m/Y') . ' to ' . $endDate->format('d/m/Y') :
            'All Time'
        );

        // Filters
        $row = 3;
        if (isset($filters['zone']) && $filters['zone'] != 'all') {
            $sheet->setCellValue('A' . $row, 'Zone:');
            $zone = \App\Models\Zone::find($filters['zone']);
            $sheet->setCellValue('B' . $row, $zone ? $zone->name : 'Zone ' . $filters['zone']);
            $row++;
        }

        if (isset($filters['status']) && $filters['status'] != 'all') {
            $sheet->setCellValue('A' . $row, 'Status:');
            $sheet->setCellValue('B' . $row, ucfirst($filters['status']));
            $row++;
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $sheet->setCellValue('A' . $row, 'Search:');
            $sheet->setCellValue('B' . $row, $filters['search']);
            $row++;
        }

        // Generated date
        $sheet->setCellValue('A' . $row, 'Generated:');
        $sheet->setCellValue('B' . $row, now()->format('d/m/Y H:i:s'));

        // Style the header
        $sheet->getStyle('A1:D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A2:A' . $row)->getFont()->setBold(true);

        return $row + 2; // Return next row after header
    }

    private function generateArrearsReport($startDate, $endDate, $detailLevel)
    {
        $query = Bill::with(['customer', 'meter.meterCategory', 'meter.zone'])
            ->where('balance', '>', 0)
            ->where('bill_status', '!=', 'paid');

        if ($startDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        $arrears = $query->get();

        // Age analysis
        $ageAnalysis = [
            '0-30_days' => [
                'bills' => $arrears->where('due_date', '>=', now()->subDays(30)),
                'amount' => $arrears->where('due_date', '>=', now()->subDays(30))->sum('balance'),
                'count' => $arrears->where('due_date', '>=', now()->subDays(30))->count(),
            ],
            '31-60_days' => [
                'bills' => $arrears->whereBetween('due_date', [now()->subDays(60), now()->subDays(31)]),
                'amount' => $arrears->whereBetween('due_date', [now()->subDays(60), now()->subDays(31)])->sum('balance'),
                'count' => $arrears->whereBetween('due_date', [now()->subDays(60), now()->subDays(31)])->count(),
            ],
            '61-90_days' => [
                'bills' => $arrears->whereBetween('due_date', [now()->subDays(90), now()->subDays(61)]),
                'amount' => $arrears->whereBetween('due_date', [now()->subDays(90), now()->subDays(61)])->sum('balance'),
                'count' => $arrears->whereBetween('due_date', [now()->subDays(90), now()->subDays(61)])->count(),
            ],
            'over_90_days' => [
                'bills' => $arrears->where('due_date', '<', now()->subDays(90)),
                'amount' => $arrears->where('due_date', '<', now()->subDays(90))->sum('balance'),
                'count' => $arrears->where('due_date', '<', now()->subDays(90))->count(),
            ],
        ];

        // Top debtors
        $topDebtors = $arrears->groupBy('customer_id')->map(function ($bills, $customerId) {
            $customer = $bills->first()->customer;
            return [
                'customer' => $customer,
                'total_arrears' => $bills->sum('balance'),
                'bill_count' => $bills->count(),
                'oldest_bill' => $bills->min('due_date'),
                'newest_bill' => $bills->max('due_date'),
                'average_arrears_per_bill' => $bills->avg('balance'),
            ];
        })->sortByDesc('total_arrears');

        // Category arrears
        $categoryArrears = $arrears->groupBy(function ($bill) {
            return $bill->meter->meterCategory->name ?? 'Unknown';
        })->map(function ($bills, $category) {
            return [
                'category' => $category,
                'total_arrears' => $bills->sum('balance'),
                'bill_count' => $bills->count(),
                'average_arrears' => $bills->avg('balance'),
            ];
        })->sortByDesc('total_arrears');

        // Zone arrears
        $zoneArrears = $arrears->groupBy(function ($bill) {
            return $bill->meter->zone->name ?? 'Unassigned';
        })->map(function ($bills, $zone) {
            return [
                'zone' => $zone,
                'total_arrears' => $bills->sum('balance'),
                'bill_count' => $bills->count(),
                'customer_count' => $bills->unique('customer_id')->count(),
            ];
        })->sortByDesc('total_arrears');

        return [
            'type' => 'Arrears Report',
            'detail_level' => $detailLevel,
            'arrears' => $arrears,
            'age_analysis' => $ageAnalysis,
            'top_debtors' => $topDebtors,
            'category_arrears' => $categoryArrears,
            'zone_arrears' => $zoneArrears,
            'summary' => [
                'total_arrears' => $arrears->sum('balance'),
                'customer_count' => $arrears->unique('customer_id')->count(),
                'bill_count' => $arrears->count(),
                'average_arrears' => $arrears->avg('balance'),
                'oldest_arrear' => $arrears->min('due_date'),
                'newest_arrear' => $arrears->max('due_date'),
                'arrears_per_customer' => $arrears->unique('customer_id')->count() > 0 ?
                    $arrears->sum('balance') / $arrears->unique('customer_id')->count() : 0,
                'highest_single_arrear' => $arrears->max('balance'),
                'arrears_with_overdue_fees' => $arrears->where('late_fee', '>', 0)->count(),
                'total_overdue_fees' => $arrears->sum('late_fee'),
            ]
        ];
    }

    private function generateCategoryReport($startDate, $endDate, $detailLevel)
    {
        $categories = MeterCategory::with(['pricingTiers' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        // Add statistics to each category
        $categories = $categories->map(function ($category) use ($startDate, $endDate) {
            // Get meters count
            $category->meters_count = $category->meters()->count();
            $category->meters_with_customers = $category->meters()->whereNotNull('customer_id')->count();

            // Get bills statistics
            $billsQuery = $category->bills();
            if ($startDate) {
                $billsQuery->whereBetween('billing_period_end', [$startDate, $endDate]);
            }
            $bills = $billsQuery->get();

            // Get consumption statistics
            $consumptionQuery = DB::table('meter_readings')
                ->join('meters', 'meter_readings.meter_id', '=', 'meters.id')
                ->where('meters.meter_category_id', $category->id)
                ->where('meter_readings.billed', true);

            if ($startDate) {
                $consumptionQuery->whereBetween('meter_readings.reading_date', [$startDate, $endDate]);
            }

            $consumptionStats = $consumptionQuery->select(
                DB::raw('SUM(meter_readings.consumption) as total_consumption'),
                DB::raw('AVG(meter_readings.consumption) as avg_consumption'),
                DB::raw('COUNT(*) as reading_count')
            )->first();

            $category->total_revenue = $bills->sum('total_amount');
            $category->total_paid = $bills->sum('paid_amount');
            $category->total_balance = $bills->sum('balance');
            $category->total_consumption = $consumptionStats->total_consumption ?? 0;
            $category->average_consumption = $consumptionStats->avg_consumption ?? 0;
            $category->reading_count = $consumptionStats->reading_count ?? 0;
            $category->bill_count = $bills->count();
            $category->collection_rate = $bills->sum('total_amount') > 0 ?
                ($bills->sum('paid_amount') / $bills->sum('total_amount')) * 100 : 0;
            $category->average_bill_amount = $bills->count() > 0 ?
                $bills->sum('total_amount') / $bills->count() : 0;

            return $category;
        });

        return [
            'type' => 'Meter Category Report',
            'detail_level' => $detailLevel,
            'categories' => $categories,
            'summary' => [
                'total_categories' => $categories->count(),
                'active_categories' => $categories->where('is_active', true)->count(),
                'total_meters' => $categories->sum('meters_count'),
                'meters_with_customers' => $categories->sum('meters_with_customers'),
                'total_revenue' => $categories->sum('total_revenue'),
                'total_collected' => $categories->sum('total_paid'),
                'total_arrears' => $categories->sum('total_balance'),
                'total_consumption' => $categories->sum('total_consumption'),
                'total_readings' => $categories->sum('reading_count'),
                'total_bills' => $categories->sum('bill_count'),
                'average_collection_rate' => $categories->avg('collection_rate'),
                'average_consumption_per_category' => $categories->avg('average_consumption'),
            ]
        ];
    }

    private function generateZoneReport($startDate, $endDate, $detailLevel)
    {
        $zones = Zone::with(['walkRoutes'])->get();

        // Add statistics to each zone
        $zones = $zones->map(function ($zone) use ($startDate, $endDate) {
            // Get meters in zone
            $metersQuery = $zone->meters();
            $meters = $metersQuery->with(['customer', 'meterCategory'])->get();

            // Get bills for meters in zone
            $billsQuery = Bill::whereIn('meter_id', $meters->pluck('id'));
            if ($startDate) {
                $billsQuery->whereBetween('billing_period_end', [$startDate, $endDate]);
            }
            $bills = $billsQuery->get();

            // Get payments for meters in zone
            $paymentsQuery = Payment::whereIn('meter_id', $meters->pluck('id'));
            if ($startDate) {
                $paymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
            }
            $payments = $paymentsQuery->get();

            // Get consumption for meters in zone
            $consumptionQuery = DB::table('meter_readings')
                ->whereIn('meter_id', $meters->pluck('id'))
                ->where('billed', true);

            if ($startDate) {
                $consumptionQuery->whereBetween('reading_date', [$startDate, $endDate]);
            }

            $consumptionStats = $consumptionQuery->select(
                DB::raw('SUM(consumption) as total_consumption'),
                DB::raw('AVG(consumption) as avg_consumption'),
                DB::raw('COUNT(*) as reading_count')
            )->first();

            $zone->meter_count = $meters->count();
            $zone->meters_with_customers = $meters->whereNotNull('customer_id')->count();
            $zone->customer_count = $meters->whereNotNull('customer_id')->unique('customer_id')->count();
            $zone->walk_route_count = $zone->walkRoutes->count();
            $zone->total_revenue = $bills->sum('total_amount');
            $zone->total_collected = $payments->sum('amount');
            $zone->total_arrears = $bills->sum('balance');
            $zone->total_consumption = $consumptionStats->total_consumption ?? 0;
            $zone->average_consumption = $consumptionStats->avg_consumption ?? 0;
            $zone->reading_count = $consumptionStats->reading_count ?? 0;
            $zone->bill_count = $bills->count();
            $zone->payment_count = $payments->count();
            $zone->collection_rate = $bills->sum('total_amount') > 0 ?
                ($payments->sum('amount') / $bills->sum('total_amount')) * 100 : 0;
            $zone->average_bill_amount = $bills->count() > 0 ?
                $bills->sum('total_amount') / $bills->count() : 0;
            $zone->average_payment_amount = $payments->count() > 0 ?
                $payments->sum('amount') / $payments->count() : 0;

            return $zone;
        });

        return [
            'type' => 'Zone Report',
            'detail_level' => $detailLevel,
            'zones' => $zones,
            'summary' => [
                'total_zones' => $zones->count(),
                'total_meters' => $zones->sum('meter_count'),
                'total_customers' => $zones->sum('customer_count'),
                'total_walk_routes' => $zones->sum('walk_route_count'),
                'total_revenue' => $zones->sum('total_revenue'),
                'total_collected' => $zones->sum('total_collected'),
                'total_arrears' => $zones->sum('total_arrears'),
                'total_consumption' => $zones->sum('total_consumption'),
                'total_readings' => $zones->sum('reading_count'),
                'total_bills' => $zones->sum('bill_count'),
                'total_payments' => $zones->sum('payment_count'),
                'average_collection_rate' => $zones->avg('collection_rate'),
                'average_meters_per_zone' => $zones->avg('meter_count'),
                'average_customers_per_zone' => $zones->avg('customer_count'),
            ]
        ];
    }

    private function generatePDF($reportData, $reportType, $startDate, $endDate)
    {
        $pdf = PDF::loadView('reports.pdf', compact('reportData', 'reportType', 'startDate', 'endDate'));

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
            'margin_top' => 20,
            'margin_bottom' => 25,
            'margin_left' => 15,
            'margin_right' => 15,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true,
        ]);

        $filename = 'NYAWASCO_' . str_replace(' ', '_', $reportData['type']) . '_' .
                    ($startDate ? $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') : 'All_Time') .
                    '_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    private function generateExcel($reportData, $reportType, $startDate, $endDate)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Remove default sheet
        $spreadsheet->removeSheetByIndex(0);

        // Generate different worksheets based on report type
        switch ($reportType) {
            case 'revenue':
                $this->generateRevenueExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'customer':
                $this->generateCustomerExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'meter':
                $this->generateMeterExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'consumption':
                $this->generateConsumptionExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'collection':
                $this->generateCollectionExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'arrears':
                $this->generateArrearsExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'statement':
                $this->generateStatementExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'category':
                $this->generateCategoryExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
            case 'zone':
                $this->generateZoneExcel($spreadsheet, $reportData, $startDate, $endDate);
                break;
        }

        // Set active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Generate filename
        $filename = 'NYAWASCO_' . str_replace(' ', '_', $reportData['type']) . '_' .
                    ($startDate ? $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') : 'All_Time') .
                    '_' . now()->format('Y_m_d') . '.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generateRevenueExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');

        // Use new header method with filters
        $startRow = $this->addReportHeaderWithFilters(
            $summarySheet,
            $reportData['type'],
            $startDate,
            $endDate,
            $reportData['filters'] ?? []
        );

        $summaryRow = $startRow;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'paid') !== false || strpos($key, 'balance') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'rate') !== false || strpos($key, 'percentage') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value / 100);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('0.00%');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Auto-size summary columns
        $summarySheet->getColumnDimension('A')->setWidth(30);
        $summarySheet->getColumnDimension('B')->setWidth(25);

        // Worksheet 2: Monthly Breakdown
        if (isset($reportData['monthly_breakdown'])) {
            $monthlySheet = $spreadsheet->createSheet();
            $monthlySheet->setTitle('Monthly Breakdown');

            // Add header with filters
            $monthlyStartRow = $this->addReportHeaderWithFilters(
                $monthlySheet,
                'Monthly Revenue Breakdown',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $monthlyRow = $monthlyStartRow;

            $headers = ['Year', 'Month', 'Total Bills', 'Total Amount', 'Amount Paid', 'Outstanding', 'Total Consumption', 'Collection Rate'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $monthlySheet->setCellValue($col . $monthlyRow, $header);
                $monthlySheet->getStyle($col . $monthlyRow)->getFont()->setBold(true);
                $monthlySheet->getStyle($col . $monthlyRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $monthlySheet->getStyle($col . $monthlyRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $monthlyRow++;

            foreach ($reportData['monthly_breakdown'] as $month) {
                $monthlySheet->setCellValue('A' . $monthlyRow, $month->year);
                $monthlySheet->setCellValue('B' . $monthlyRow, date('F', mktime(0, 0, 0, $month->month, 1)));
                $monthlySheet->setCellValue('C' . $monthlyRow, $month->bill_count);
                $monthlySheet->setCellValue('D' . $monthlyRow, $month->total_amount);
                $monthlySheet->setCellValue('E' . $monthlyRow, $month->paid_amount);
                $monthlySheet->setCellValue('F' . $monthlyRow, $month->total_amount - $month->paid_amount);
                $monthlySheet->setCellValue('G' . $monthlyRow, $month->total_consumption);
                $monthlySheet->setCellValue('H' . $monthlyRow, $month->total_amount > 0 ? ($month->paid_amount / $month->total_amount) : 0);

                // Format numbers
                $monthlySheet->getStyle('D' . $monthlyRow . ':F' . $monthlyRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $monthlySheet->getStyle('H' . $monthlyRow)->getNumberFormat()->setFormatCode('0.00%');

                $monthlyRow++;
            }

            // Add totals
            $monthlySheet->setCellValue('A' . $monthlyRow, 'TOTAL:');
            $monthlySheet->getStyle('A' . $monthlyRow)->getFont()->setBold(true);
            $monthlySheet->setCellValue('C' . $monthlyRow, '=SUM(C' . $monthlyStartRow . ':C' . ($monthlyRow - 1) . ')');
            $monthlySheet->setCellValue('D' . $monthlyRow, '=SUM(D' . $monthlyStartRow . ':D' . ($monthlyRow - 1) . ')');
            $monthlySheet->setCellValue('E' . $monthlyRow, '=SUM(E' . $monthlyStartRow . ':E' . ($monthlyRow - 1) . ')');
            $monthlySheet->setCellValue('F' . $monthlyRow, '=SUM(F' . $monthlyStartRow . ':F' . ($monthlyRow - 1) . ')');
            $monthlySheet->setCellValue('G' . $monthlyRow, '=SUM(G' . $monthlyStartRow . ':G' . ($monthlyRow - 1) . ')');
            $monthlySheet->getStyle('C' . $monthlyRow . ':G' . $monthlyRow)->getFont()->setBold(true);
            $monthlySheet->getStyle('C' . $monthlyRow . ':G' . $monthlyRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $monthlySheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 3: Detailed Bills
        if (isset($reportData['bills']) && $reportData['bills']->count() > 0) {
            $billsSheet = $spreadsheet->createSheet();
            $billsSheet->setTitle('Detailed Bills');

            // Add header with filters
            $billsStartRow = $this->addReportHeaderWithFilters(
                $billsSheet,
                'Detailed Bills',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $billsRow = $billsStartRow;

            $headers = [
                'Bill Number', 'Customer Name','Phone', 'Customer Acc',
                'Category', 'Zone', 'Billing Period', 'Consumption (m³)',
                'Total Amount', 'Paid Amount', 'Balance', 'Status', 'Due Date', 'Overdue'
            ];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $billsSheet->setCellValue($col . $billsRow, $header);
                $billsSheet->getStyle($col . $billsRow)->getFont()->setBold(true);
                $billsSheet->getStyle($col . $billsRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $billsSheet->getStyle($col . $billsRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $billsRow++;

            foreach ($reportData['bills'] as $bill) {
                $billsSheet->setCellValue('A' . $billsRow, $bill->bill_number);
                $billsSheet->setCellValue('B' . $billsRow, $bill->customer->first_name . ' ' . $bill->customer->last_name);
                $billsSheet->setCellValue('C' . $billsRow, $bill->customer->phone ?? '');
                $billsSheet->setCellValue('D' . $billsRow, $bill->meter->meter_number ?? '');
                $billsSheet->setCellValue('E' . $billsRow, $bill->meter->meterCategory->name ?? '');
                $billsSheet->setCellValue('F' . $billsRow, $bill->meter->zone->name ?? 'Unassigned');
                $billsSheet->setCellValue('G' . $billsRow,
                    ($bill->billing_period_start ? $bill->billing_period_start->format('d/m/Y') : '') . ' - ' .
                    ($bill->billing_period_end ? $bill->billing_period_end->format('d/m/Y') : '')
                );
                $billsSheet->setCellValue('H' . $billsRow, $bill->consumption);
                $billsSheet->setCellValue('I' . $billsRow, $bill->total_amount);
                $billsSheet->setCellValue('J' . $billsRow, $bill->paid_amount);
                $billsSheet->setCellValue('K' . $billsRow, $bill->balance);
                $billsSheet->setCellValue('L' . $billsRow, ucfirst($bill->bill_status));
                $billsSheet->setCellValue('M' . $billsRow, $bill->due_date ? $bill->due_date->format('d/m/Y') : '');
                $billsSheet->setCellValue('N' . $billsRow, $bill->is_overdue ? 'Yes' : 'No');

                // Format numbers
                $billsSheet->getStyle('G' . $billsRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $billsSheet->getStyle('I' . $billsRow . ':'K . $billsRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $billsRow++;
            }

            // Add totals
            $billsSheet->setCellValue('A' . $billsRow, 'TOTAL:');
            $billsSheet->getStyle('A' . $billsRow)->getFont()->setBold(true);
            $billsSheet->setCellValue('H' . $billsRow, '=SUM(H' . $billsStartRow . ':H' . ($billsRow - 1) . ')');
            $billsSheet->setCellValue('I' . $billsRow, '=SUM(I' . $billsStartRow . ':I' . ($billsRow - 1) . ')');
            $billsSheet->setCellValue('J' . $billsRow, '=SUM(J' . $billsStartRow . ':J' . ($billsRow - 1) . ')');
            $billsSheet->setCellValue('K' . $billsRow, '=SUM(K' . $billsStartRow . ':K' . ($billsRow - 1) . ')');
            $billsSheet->getStyle('H' . $billsRow . ':K' . $billsRow)->getFont()->setBold(true);
            $billsSheet->getStyle('H' . $billsRow . ':K' . $billsRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $billsSheet->getStyle('H' . $billsRow . ':K' . $billsRow)->getNumberFormat()->setFormatCode('#,##0.00');

            // Auto-size columns
            foreach (range('A', 'N') as $column) {
                $billsSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 4: Category Breakdown
        if (isset($reportData['category_breakdown'])) {
            $categorySheet = $spreadsheet->createSheet();
            $categorySheet->setTitle('Category Breakdown');

            // Add header with filters
            $categoryStartRow = $this->addReportHeaderWithFilters(
                $categorySheet,
                'Category Breakdown',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $categoryRow = $categoryStartRow;

            $headers = ['Category', 'Code', 'Bill Count', 'Total Amount', 'Amount Paid', 'Outstanding', 'Total Consumption'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $categorySheet->setCellValue($col . $categoryRow, $header);
                $categorySheet->getStyle($col . $categoryRow)->getFont()->setBold(true);
                $categorySheet->getStyle($col . $categoryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $categorySheet->getStyle($col . $categoryRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $categoryRow++;

            foreach ($reportData['category_breakdown'] as $category) {
                $categorySheet->setCellValue('A' . $categoryRow, $category->category);
                $categorySheet->setCellValue('B' . $categoryRow, $category->code);
                $categorySheet->setCellValue('C' . $categoryRow, $category->bill_count);
                $categorySheet->setCellValue('D' . $categoryRow, $category->total_amount);
                $categorySheet->setCellValue('E' . $categoryRow, $category->paid_amount);
                $categorySheet->setCellValue('F' . $categoryRow, $category->total_amount - $category->paid_amount);
                $categorySheet->setCellValue('G' . $categoryRow, $category->total_consumption);

                // Format numbers
                $categorySheet->getStyle('D' . $categoryRow . ':F' . $categoryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $categorySheet->getStyle('G' . $categoryRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $categoryRow++;
            }

            // Add totals
            $categorySheet->setCellValue('A' . $categoryRow, 'TOTAL:');
            $categorySheet->getStyle('A' . $categoryRow)->getFont()->setBold(true);
            $categorySheet->setCellValue('C' . $categoryRow, '=SUM(C' . $categoryStartRow . ':C' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('D' . $categoryRow, '=SUM(D' . $categoryStartRow . ':D' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('E' . $categoryRow, '=SUM(E' . $categoryStartRow . ':E' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('F' . $categoryRow, '=SUM(F' . $categoryStartRow . ':F' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('G' . $categoryRow, '=SUM(G' . $categoryStartRow . ':G' . ($categoryRow - 1) . ')');
            $categorySheet->getStyle('C' . $categoryRow . ':G' . $categoryRow)->getFont()->setBold(true);
            $categorySheet->getStyle('C' . $categoryRow . ':G' . $categoryRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $categorySheet->getStyle('D' . $categoryRow . ':G' . $categoryRow)->getNumberFormat()->setFormatCode('#,##0.00');

            // Auto-size columns
            foreach (range('A', 'G') as $column) {
                $categorySheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 5: Zone Breakdown
        if (isset($reportData['zone_breakdown'])) {
            $zoneSheet = $spreadsheet->createSheet();
            $zoneSheet->setTitle('Zone Breakdown');

            // Add header with filters
            $zoneStartRow = $this->addReportHeaderWithFilters(
                $zoneSheet,
                'Zone Breakdown',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $zoneRow = $zoneStartRow;

            $headers = ['Zone', 'Bill Count', 'Total Amount', 'Amount Paid', 'Outstanding', 'Collection Rate'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $zoneSheet->setCellValue($col . $zoneRow, $header);
                $zoneSheet->getStyle($col . $zoneRow)->getFont()->setBold(true);
                $zoneSheet->getStyle($col . $zoneRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $zoneSheet->getStyle($col . $zoneRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $zoneRow++;

            foreach ($reportData['zone_breakdown'] as $zone) {
                $zoneSheet->setCellValue('A' . $zoneRow, $zone->zone_name);
                $zoneSheet->setCellValue('B' . $zoneRow, $zone->bill_count);
                $zoneSheet->setCellValue('C' . $zoneRow, $zone->total_amount);
                $zoneSheet->setCellValue('D' . $zoneRow, $zone->paid_amount);
                $zoneSheet->setCellValue('E' . $zoneRow, $zone->total_amount - $zone->paid_amount);
                $zoneSheet->setCellValue('F' . $zoneRow, $zone->total_amount > 0 ? ($zone->paid_amount / $zone->total_amount) : 0);

                // Format numbers
                $zoneSheet->getStyle('C' . $zoneRow . ':E' . $zoneRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $zoneSheet->getStyle('F' . $zoneRow)->getNumberFormat()->setFormatCode('0.00%');

                $zoneRow++;
            }

            // Add totals
            $zoneSheet->setCellValue('A' . $zoneRow, 'TOTAL:');
            $zoneSheet->getStyle('A' . $zoneRow)->getFont()->setBold(true);
            $zoneSheet->setCellValue('B' . $zoneRow, '=SUM(B' . $zoneStartRow . ':B' . ($zoneRow - 1) . ')');
            $zoneSheet->setCellValue('C' . $zoneRow, '=SUM(C' . $zoneStartRow . ':C' . ($zoneRow - 1) . ')');
            $zoneSheet->setCellValue('D' . $zoneRow, '=SUM(D' . $zoneStartRow . ':D' . ($zoneRow - 1) . ')');
            $zoneSheet->setCellValue('E' . $zoneRow, '=SUM(E' . $zoneStartRow . ':E' . ($zoneRow - 1) . ')');
            $zoneSheet->getStyle('B' . $zoneRow . ':E' . $zoneRow)->getFont()->setBold(true);
            $zoneSheet->getStyle('B' . $zoneRow . ':E' . $zoneRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $zoneSheet->getStyle('C' . $zoneRow . ':E' . $zoneRow)->getNumberFormat()->setFormatCode('#,##0.00');

            // Auto-size columns
            foreach (range('A', 'F') as $column) {
                $zoneSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }
    //  handles bill status mapping
    private function mapBillStatus($status)
    {
        $statusMap = [
            'all' => null,
            'paid' => 'paid',
            'unpaid' => 'unpaid',
            'partial' => 'partial',
            'overdue' => 'overdue',
        ];

        return $statusMap[$status] ?? null;
    }

    private function generateCustomerExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'balance') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'consumption') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Worksheet 2: Customer Details
        if (isset($reportData['customers']) && $reportData['customers']->count() > 0) {
            $customersSheet = $spreadsheet->createSheet();
            $customersSheet->setTitle('Customer Details');

            $headers = [
                'Full Name', 'Phone', 'Status',
                'Meter Count', 'Registration Date'
            ];
            $this->addSheetHeader($customersSheet, $headers);

            $row = 2;
            foreach ($reportData['customers'] as $customer) {

                $customersSheet->setCellValue('A' . $row, trim($customer->first_name . ' ' . $customer->last_name));
                $customersSheet->setCellValue('B' . $row, $customer->phone);

                $customersSheet->setCellValue('C' . $row, ucfirst($customer->status));


                $customersSheet->setCellValue('D' . $row, $customer->meter_count ?? 0);
                $customersSheet->setCellValue('E' . $row, $customer->created_at ? $customer->created_at->format('d/m/Y') : '');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'E') as $column) {
                $customersSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateMeterExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');

        // Use the new header method with filters
        $startRow = $this->addReportHeaderWithFilters(
            $summarySheet,
            $reportData['type'],
            $startDate,
            $endDate,
            $reportData['filters'] ?? []
        );

        $summaryRow = $startRow;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'balance') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'consumption') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Add zone info if filtered
        if (isset($reportData['zone_info']) && $reportData['zone_info']) {
            $summaryRow += 2;
            $summarySheet->setCellValue('A' . $summaryRow, 'FILTERED BY ZONE:');
            $summarySheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            $summaryRow++;
            $summarySheet->setCellValue('A' . $summaryRow, 'Zone Name:');
            $summarySheet->setCellValue('B' . $summaryRow, $reportData['zone_info']->name);
            $summaryRow++;
            $summarySheet->setCellValue('A' . $summaryRow, 'Zone Code:');
            $summarySheet->setCellValue('B' . $summaryRow, $reportData['zone_info']->code ?? 'N/A');
        }

        // Auto-size summary columns
        $summarySheet->getColumnDimension('A')->setWidth(30);
        $summarySheet->getColumnDimension('B')->setWidth(25);

        // Worksheet 2: Meter Details
        if (isset($reportData['meters']) && $reportData['meters']->count() > 0) {
            $metersSheet = $spreadsheet->createSheet();
            $metersSheet->setTitle('Meter Details');

            // Add header with filters
            $metersStartRow = $this->addReportHeaderWithFilters(
                $metersSheet,
                'Meter Details',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $metersRow = $metersStartRow;

            $headers = [
                'Meter Number', 'Meter Type', 'Category',
                'Status', 'Customer Name', 'Customer Number', 'Phone',
                'Zone', 'Walk Route', 'Installation Address',
                'Initial Reading', 'Current Balance', 'Total Billed',
                'Total Paid', 'Total Balance', 'Total Consumption'
            ];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $metersSheet->setCellValue($col . $metersRow, $header);
                $metersSheet->getStyle($col . $metersRow)->getFont()->setBold(true);
                $metersSheet->getStyle($col . $metersRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $metersSheet->getStyle($col . $metersRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $metersRow++;

            foreach ($reportData['meters'] as $meter) {
                $metersSheet->setCellValue('A' . $metersRow, $meter->meter_number);
                $metersSheet->setCellValue('B' . $metersRow, ucfirst($meter->meter_type));
                $metersSheet->setCellValue('C' . $metersRow, $meter->meterCategory->name ?? '');
                $metersSheet->setCellValue('D' . $metersRow, ucfirst($meter->status));
                $metersSheet->setCellValue('E' . $metersRow, $meter->customer ?
                    trim($meter->customer->first_name . ' ' . $meter->customer->last_name) : '');
                $metersSheet->setCellValue('F' . $metersRow, $meter->customer->customer_number ?? '');
                $metersSheet->setCellValue('G' . $metersRow, $meter->customer->phone ?? '');
                $metersSheet->setCellValue('H' . $metersRow, $meter->zone->name ?? '');
                $metersSheet->setCellValue('I' . $metersRow, $meter->walkRoute->name ?? '');
                $metersSheet->setCellValue('J' . $metersRow, $meter->installation_address ?? '');
                $metersSheet->setCellValue('K' . $metersRow, $meter->initial_reading);
                $metersSheet->setCellValue('L' . $metersRow, $meter->current_balance);
                $metersSheet->setCellValue('M' . $metersRow, $meter->total_billed);
                $metersSheet->setCellValue('N' . $metersRow, $meter->total_paid);
                $metersSheet->setCellValue('O' . $metersRow, $meter->total_balance);
                $metersSheet->setCellValue('P' . $metersRow, $meter->total_consumption);

                // Format numbers
                $metersSheet->getStyle('K' . $metersRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $metersSheet->getStyle('L' . $metersRow . ':P' . $metersRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $metersRow++;
            }

            // Add totals
            $metersSheet->setCellValue('A' . $metersRow, 'TOTAL:');
            $metersSheet->getStyle('A' . $metersRow)->getFont()->setBold(true);
            $metersSheet->setCellValue('K' . $metersRow, '=SUM(K' . $metersStartRow . ':K' . ($metersRow - 1) . ')');
            $metersSheet->setCellValue('L' . $metersRow, '=SUM(L' . $metersStartRow . ':L' . ($metersRow - 1) . ')');
            $metersSheet->setCellValue('M' . $metersRow, '=SUM(M' . $metersStartRow . ':M' . ($metersRow - 1) . ')');
            $metersSheet->setCellValue('N' . $metersRow, '=SUM(N' . $metersStartRow . ':N' . ($metersRow - 1) . ')');
            $metersSheet->setCellValue('O' . $metersRow, '=SUM(O' . $metersStartRow . ':O' . ($metersRow - 1) . ')');
            $metersSheet->setCellValue('P' . $metersRow, '=SUM(P' . $metersStartRow . ':P' . ($metersRow - 1) . ')');
            $metersSheet->getStyle('K' . $metersRow . ':P' . $metersRow)->getFont()->setBold(true);
            $metersSheet->getStyle('K' . $metersRow . ':P' . $metersRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

            // Auto-size columns
            foreach (range('A', 'P') as $column) {
                $metersSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 3: Category Breakdown
        if (isset($reportData['category_stats'])) {
            $categorySheet = $spreadsheet->createSheet();
            $categorySheet->setTitle('Category Breakdown');

            // Add header with filters
            $categoryStartRow = $this->addReportHeaderWithFilters(
                $categorySheet,
                'Category Breakdown',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $categoryRow = $categoryStartRow;

            $headers = ['Category', 'Total Meters', 'Meters with Customers', 'Meters without Customers'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $categorySheet->setCellValue($col . $categoryRow, $header);
                $categorySheet->getStyle($col . $categoryRow)->getFont()->setBold(true);
                $categorySheet->getStyle($col . $categoryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $categorySheet->getStyle($col . $categoryRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $categoryRow++;

            foreach ($reportData['category_stats'] as $category) {
                $categorySheet->setCellValue('A' . $categoryRow, $category['category']);
                $categorySheet->setCellValue('B' . $categoryRow, $category['count']);
                $categorySheet->setCellValue('C' . $categoryRow, $category['meters_with_customers']);
                $categorySheet->setCellValue('D' . $categoryRow, $category['meters_without_customers']);
                $categoryRow++;
            }

            // Add totals
            $categorySheet->setCellValue('A' . $categoryRow, 'TOTAL:');
            $categorySheet->getStyle('A' . $categoryRow)->getFont()->setBold(true);
            $categorySheet->setCellValue('B' . $categoryRow, '=SUM(B' . $categoryStartRow . ':B' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('C' . $categoryRow, '=SUM(C' . $categoryStartRow . ':C' . ($categoryRow - 1) . ')');
            $categorySheet->setCellValue('D' . $categoryRow, '=SUM(D' . $categoryStartRow . ':D' . ($categoryRow - 1) . ')');
            $categorySheet->getStyle('B' . $categoryRow . ':D' . $categoryRow)->getFont()->setBold(true);
            $categorySheet->getStyle('B' . $categoryRow . ':D' . $categoryRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

            // Auto-size columns
            foreach (range('A', 'D') as $column) {
                $categorySheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateConsumptionExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'consumption') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Worksheet 2: Monthly Consumption
        if (isset($reportData['monthly_consumption'])) {
            $monthlySheet = $spreadsheet->createSheet();
            $monthlySheet->setTitle('Monthly Consumption');

            $headers = ['Year', 'Month', 'Reading Count', 'Total Consumption', 'Average Consumption', 'Max Consumption', 'Min Consumption'];
            $this->addSheetHeader($monthlySheet, $headers);

            $row = 2;
            foreach ($reportData['monthly_consumption'] as $month) {
                $monthlySheet->setCellValue('A' . $row, $month->year);
                $monthlySheet->setCellValue('B' . $row, date('F', mktime(0, 0, 0, $month->month, 1)));
                $monthlySheet->setCellValue('C' . $row, $month->reading_count);
                $monthlySheet->setCellValue('D' . $row, $month->total_consumption);
                $monthlySheet->setCellValue('E' . $row, $month->avg_consumption);
                $monthlySheet->setCellValue('F' . $row, $month->max_consumption);
                $monthlySheet->setCellValue('G' . $row, $month->min_consumption);

                // Format numbers
                $monthlySheet->getStyle('D' . $row . ':G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Add totals
            $this->addSheetTotals($monthlySheet, $row, [
                'C' => 'count',
                'D' => 'sum',
            ]);
        }

        // Worksheet 3: Detailed Readings
        if (isset($reportData['readings']) && $reportData['readings']->count() > 0) {
            $readingsSheet = $spreadsheet->createSheet();
            $readingsSheet->setTitle('Detailed Readings');

            $headers = [
                'Reading Date', 'Customer Acc', 'Customer Name', 'Meter Number',
                'Category', 'Zone', 'Previous Reading', 'Current Reading',
                'Consumption (m³)', 'Reading Type', 'Reading Status', 'Estimated'
            ];
            $this->addSheetHeader($readingsSheet, $headers);

            $row = 2;
            foreach ($reportData['readings'] as $reading) {
                $readingsSheet->setCellValue('A' . $row, $reading->reading_date ? $reading->reading_date->format('d/m/Y') : '');
                $readingsSheet->setCellValue('B' . $row, $reading->meter->meter_number ?? '');
                $readingsSheet->setCellValue('C' . $row, $reading->customer ?
                    trim($reading->customer->first_name . ' ' . $reading->customer->last_name) : '');
                $readingsSheet->setCellValue('D' . $row, $reading->meter->meter_number ?? '');
                $readingsSheet->setCellValue('E' . $row, $reading->meter->meterCategory->name ?? '');
                $readingsSheet->setCellValue('F' . $row, $reading->meter->zone->name ?? '');
                $readingsSheet->setCellValue('G' . $row, $reading->previous_reading);
                $readingsSheet->setCellValue('H' . $row, $reading->current_reading);
                $readingsSheet->setCellValue('I' . $row, $reading->consumption);
                $readingsSheet->setCellValue('J' . $row, ucfirst($reading->reading_type));
                $readingsSheet->setCellValue('K' . $row, ucfirst($reading->reading_status));
                $readingsSheet->setCellValue('L' . $row, $reading->estimated ? 'Yes' : 'No');

                // Format numbers
                $readingsSheet->getStyle('G' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'L') as $column) {
                $readingsSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateCollectionExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');

        // Use new header method with filters
        $startRow = $this->addReportHeaderWithFilters(
            $summarySheet,
            $reportData['type'],
            $startDate,
            $endDate,
            $reportData['filters'] ?? []
        );

        $summaryRow = $startRow;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'collected') !== false ||
                    strpos($key, 'payment') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'rate') !== false || strpos($key, 'efficiency') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value / 100);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('0.00%');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Auto-size summary columns
        $summarySheet->getColumnDimension('A')->setWidth(30);
        $summarySheet->getColumnDimension('B')->setWidth(25);

        // Worksheet 2: Daily Collection
        if (isset($reportData['daily_collection'])) {
            $dailySheet = $spreadsheet->createSheet();
            $dailySheet->setTitle('Daily Collection');

            // Add header with filters
            $dailyStartRow = $this->addReportHeaderWithFilters(
                $dailySheet,
                'Daily Collection',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $dailyRow = $dailyStartRow;

            $headers = ['Date', 'Payment Count', 'Total Amount', 'Average Amount'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $dailySheet->setCellValue($col . $dailyRow, $header);
                $dailySheet->getStyle($col . $dailyRow)->getFont()->setBold(true);
                $dailySheet->getStyle($col . $dailyRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $dailySheet->getStyle($col . $dailyRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $dailyRow++;

            foreach ($reportData['daily_collection'] as $day) {
                $dailySheet->setCellValue('A' . $dailyRow, $day->payment_date ? Carbon::parse($day->payment_date)->format('d/m/Y') : '');
                $dailySheet->setCellValue('B' . $dailyRow, $day->payment_count);
                $dailySheet->setCellValue('C' . $dailyRow, $day->total_amount);
                $dailySheet->setCellValue('D' . $dailyRow, $day->avg_amount);

                // Format numbers
                $dailySheet->getStyle('C' . $dailyRow . ':D' . $dailyRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $dailyRow++;
            }

            // Add totals
            $dailySheet->setCellValue('A' . $dailyRow, 'TOTAL:');
            $dailySheet->getStyle('A' . $dailyRow)->getFont()->setBold(true);
            $dailySheet->setCellValue('B' . $dailyRow, '=SUM(B' . $dailyStartRow . ':B' . ($dailyRow - 1) . ')');
            $dailySheet->setCellValue('C' . $dailyRow, '=SUM(C' . $dailyStartRow . ':C' . ($dailyRow - 1) . ')');
            $dailySheet->setCellValue('D' . $dailyRow, '=AVERAGE(D' . $dailyStartRow . ':D' . ($dailyRow - 1) . ')');
            $dailySheet->getStyle('B' . $dailyRow . ':D' . $dailyRow)->getFont()->setBold(true);
            $dailySheet->getStyle('B' . $dailyRow . ':D' . $dailyRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $dailySheet->getStyle('C' . $dailyRow . ':D' . $dailyRow)->getNumberFormat()->setFormatCode('#,##0.00');

            // Auto-size columns
            foreach (range('A', 'D') as $column) {
                $dailySheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 3: Payment Details
        if (isset($reportData['payments']) && $reportData['payments']->count() > 0) {
            $paymentsSheet = $spreadsheet->createSheet();
            $paymentsSheet->setTitle('Payment Details');

            // Add header with filters
            $paymentsStartRow = $this->addReportHeaderWithFilters(
                $paymentsSheet,
                'Payment Details',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $paymentsRow = $paymentsStartRow;

            $headers = [
                'Payment Date', 'Payment Number', 'Receipt Number', 'Customer Acc',
                'Customer Name', 'Meter Number', 'Zone', 'Amount', 'Payment Method',
                'Transaction Reference', 'Payment Status', 'Collector', 'Bill Number'
            ];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $paymentsSheet->setCellValue($col . $paymentsRow, $header);
                $paymentsSheet->getStyle($col . $paymentsRow)->getFont()->setBold(true);
                $paymentsSheet->getStyle($col . $paymentsRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $paymentsSheet->getStyle($col . $paymentsRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $paymentsRow++;

            foreach ($reportData['payments'] as $payment) {
                $paymentsSheet->setCellValue('A' . $paymentsRow, $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '');
                $paymentsSheet->setCellValue('B' . $paymentsRow, $payment->payment_no);
                $paymentsSheet->setCellValue('C' . $paymentsRow, $payment->receipt_number ?? 'N/A');
                $paymentsSheet->setCellValue('D' . $paymentsRow, $payment->meter->meter_number ?? '');
                $paymentsSheet->setCellValue('E' . $paymentsRow, $payment->customer ?
                    trim($payment->customer->first_name . ' ' . $payment->customer->last_name) : 'N/A');
                $paymentsSheet->setCellValue('F' . $paymentsRow, $payment->meter->meter_number ?? '');
                $paymentsSheet->setCellValue('G' . $paymentsRow, $payment->meter->zone->name ?? 'N/A');
                $paymentsSheet->setCellValue('H' . $paymentsRow, $payment->amount);
                $paymentsSheet->setCellValue('I' . $paymentsRow, ucfirst($payment->payment_method));
                $paymentsSheet->setCellValue('J' . $paymentsRow, $payment->transaction_reference);
                $paymentsSheet->setCellValue('K' . $paymentsRow, ucfirst($payment->payment_status));
                $paymentsSheet->setCellValue('L' . $paymentsRow, $payment->collector->name ?? 'System');
                $paymentsSheet->setCellValue('M' . $paymentsRow, $payment->bill->bill_number ?? 'N/A');

                // Format numbers
                $paymentsSheet->getStyle('H' . $paymentsRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $paymentsRow++;
            }

            // Add totals
            $paymentsSheet->setCellValue('A' . $paymentsRow, 'TOTAL:');
            $paymentsSheet->getStyle('A' . $paymentsRow)->getFont()->setBold(true);
            $paymentsSheet->setCellValue('H' . $paymentsRow, '=SUM(H' . $paymentsStartRow . ':H' . ($paymentsRow - 1) . ')');
            $paymentsSheet->getStyle('H' . $paymentsRow)->getFont()->setBold(true);
            $paymentsSheet->getStyle('H' . $paymentsRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $paymentsSheet->getStyle('H' . $paymentsRow)->getNumberFormat()->setFormatCode('#,##0.00');

            // Auto-size columns
            foreach (range('A', 'M') as $column) {
                $paymentsSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Worksheet 4: Payment Method Breakdown
        if (isset($reportData['method_breakdown'])) {
            $methodSheet = $spreadsheet->createSheet();
            $methodSheet->setTitle('Payment Methods');

            // Add header with filters
            $methodStartRow = $this->addReportHeaderWithFilters(
                $methodSheet,
                'Payment Methods Breakdown',
                $startDate,
                $endDate,
                $reportData['filters'] ?? []
            );

            $methodRow = $methodStartRow;

            $headers = ['Payment Method', 'Payment Count', 'Total Amount', 'Average Amount', 'Percentage'];

            // Add headers
            $col = 'A';
            foreach ($headers as $header) {
                $methodSheet->setCellValue($col . $methodRow, $header);
                $methodSheet->getStyle($col . $methodRow)->getFont()->setBold(true);
                $methodSheet->getStyle($col . $methodRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $methodSheet->getStyle($col . $methodRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $methodRow++;

            $totalAmount = $reportData['payments']->sum('amount');

            foreach ($reportData['method_breakdown'] as $methodData) {
                $methodSheet->setCellValue('A' . $methodRow, ucfirst($methodData['method']));
                $methodSheet->setCellValue('B' . $methodRow, $methodData['count']);
                $methodSheet->setCellValue('C' . $methodRow, $methodData['total_amount']);
                $methodSheet->setCellValue('D' . $methodRow, $methodData['avg_amount']);
                $methodSheet->setCellValue('E' . $methodRow, $totalAmount > 0 ? ($methodData['total_amount'] / $totalAmount) : 0);

                // Format numbers
                $methodSheet->getStyle('C' . $methodRow . ':D' . $methodRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $methodSheet->getStyle('E' . $methodRow)->getNumberFormat()->setFormatCode('0.00%');

                $methodRow++;
            }

            // Add totals
            $methodSheet->setCellValue('A' . $methodRow, 'TOTAL:');
            $methodSheet->getStyle('A' . $methodRow)->getFont()->setBold(true);
            $methodSheet->setCellValue('B' . $methodRow, '=SUM(B' . $methodStartRow . ':B' . ($methodRow - 1) . ')');
            $methodSheet->setCellValue('C' . $methodRow, '=SUM(C' . $methodStartRow . ':C' . ($methodRow - 1) . ')');
            $methodSheet->setCellValue('D' . $methodRow, '=AVERAGE(D' . $methodStartRow . ':D' . ($methodRow - 1) . ')');
            $methodSheet->setCellValue('E' . $methodRow, '=SUM(E' . $methodStartRow . ':E' . ($methodRow - 1) . ')');
            $methodSheet->getStyle('B' . $methodRow . ':E' . $methodRow)->getFont()->setBold(true);
            $methodSheet->getStyle('B' . $methodRow . ':E' . $methodRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
            $methodSheet->getStyle('C' . $methodRow . ':D' . $methodRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $methodSheet->getStyle('E' . $methodRow)->getNumberFormat()->setFormatCode('0.00%');

            // Auto-size columns
            foreach (range('A', 'E') as $column) {
                $methodSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateArrearsExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'arrears') !== false ||
                    strpos($key, 'balance') !== false || strpos($key, 'fees') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif ($value instanceof \Carbon\Carbon) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value->format('d/m/Y'));
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Worksheet 2: Age Analysis
        if (isset($reportData['age_analysis'])) {
            $ageSheet = $spreadsheet->createSheet();
            $ageSheet->setTitle('Age Analysis');

            $headers = ['Age Category', 'Bill Count', 'Amount Outstanding', 'Percentage'];
            $this->addSheetHeader($ageSheet, $headers);

            $row = 2;
            $totalArrears = array_sum(array_column($reportData['age_analysis'], 'amount'));

            foreach ($reportData['age_analysis'] as $category => $data) {
                $ageSheet->setCellValue('A' . $row, $this->formatAgeCategory($category));
                $ageSheet->setCellValue('B' . $row, $data['count']);
                $ageSheet->setCellValue('C' . $row, $data['amount']);
                $ageSheet->setCellValue('D' . $row, $totalArrears > 0 ? ($data['amount'] / $totalArrears) : 0);

                // Format numbers
                $ageSheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $ageSheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('0.00%');

                $row++;
            }

            // Add totals
            $ageSheet->setCellValue('A' . $row, 'TOTAL:');
            $ageSheet->getStyle('A' . $row)->getFont()->setBold(true);
            $ageSheet->setCellValue('B' . $row, '=SUM(B2:B' . ($row - 1) . ')');
            $ageSheet->setCellValue('C' . $row, '=SUM(C2:C' . ($row - 1) . ')');
            $ageSheet->setCellValue('D' . $row, '=SUM(D2:D' . ($row - 1) . ')');
            $ageSheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);
            $ageSheet->getStyle('B' . $row . ':D' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
        }

        // Worksheet 3: Top Debtors
        if (isset($reportData['top_debtors']) && $reportData['top_debtors']->count() > 0) {
            $debtorsSheet = $spreadsheet->createSheet();
            $debtorsSheet->setTitle('Top Debtors');

            $headers = [
                'Customer', 'Customer Name', 'Phone', 'Total Arrears',
                'Bill Count', 'Oldest Bill', 'Newest Bill', 'Average Arrears per Bill'
            ];
            $this->addSheetHeader($debtorsSheet, $headers);

            $row = 2;
            foreach ($reportData['top_debtors'] as $debtor) {
                $debtorsSheet->setCellValue('A' . $row, $debtor['customer']->customer_number ?? '');
                $debtorsSheet->setCellValue('B' . $row, trim($debtor['customer']->first_name . ' ' . $debtor['customer']->last_name));
                $debtorsSheet->setCellValue('C' . $row, $debtor['customer']->phone ?? '');
                $debtorsSheet->setCellValue('D' . $row, $debtor['total_arrears']);
                $debtorsSheet->setCellValue('E' . $row, $debtor['bill_count']);
                $debtorsSheet->setCellValue('F' . $row, $debtor['oldest_bill'] ? $debtor['oldest_bill']->format('d/m/Y') : '');
                $debtorsSheet->setCellValue('G' . $row, $debtor['newest_bill'] ? $debtor['newest_bill']->format('d/m/Y') : '');
                $debtorsSheet->setCellValue('H' . $row, $debtor['average_arrears_per_bill']);

                // Format numbers
                $debtorsSheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $debtorsSheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $debtorsSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateCategoryExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'collected') !== false || strpos($key, 'arrears') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'consumption') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'rate') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value / 100);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('0.00%');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Worksheet 2: Category Details
        if (isset($reportData['categories']) && $reportData['categories']->count() > 0) {
            $categoriesSheet = $spreadsheet->createSheet();
            $categoriesSheet->setTitle('Category Details');

            $headers = [
                'Category Name', 'Code', 'Default Rate', 'Active', 'Meter Count',
                'Meters with Customers', 'Total Revenue', 'Total Collected',
                'Total Arrears', 'Total Consumption', 'Average Consumption',
                'Reading Count', 'Bill Count', 'Collection Rate', 'Average Bill Amount'
            ];
            $this->addSheetHeader($categoriesSheet, $headers);

            $row = 2;
            foreach ($reportData['categories'] as $category) {
                $categoriesSheet->setCellValue('A' . $row, $category->name);
                $categoriesSheet->setCellValue('B' . $row, $category->code);
                $categoriesSheet->setCellValue('C' . $row, $category->default_rate);
                $categoriesSheet->setCellValue('D' . $row, $category->is_active ? 'Yes' : 'No');
                $categoriesSheet->setCellValue('E' . $row, $category->meters_count);
                $categoriesSheet->setCellValue('F' . $row, $category->meters_with_customers);
                $categoriesSheet->setCellValue('G' . $row, $category->total_revenue);
                $categoriesSheet->setCellValue('H' . $row, $category->total_paid);
                $categoriesSheet->setCellValue('I' . $row, $category->total_balance);
                $categoriesSheet->setCellValue('J' . $row, $category->total_consumption);
                $categoriesSheet->setCellValue('K' . $row, $category->average_consumption);
                $categoriesSheet->setCellValue('L' . $row, $category->reading_count);
                $categoriesSheet->setCellValue('M' . $row, $category->bill_count);
                $categoriesSheet->setCellValue('N' . $row, $category->collection_rate / 100);
                $categoriesSheet->setCellValue('O' . $row, $category->average_bill_amount);

                // Format numbers
                $categoriesSheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $categoriesSheet->getStyle('G' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $categoriesSheet->getStyle('J' . $row . ':K' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $categoriesSheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('0.00%');
                $categoriesSheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'O') as $column) {
                $categoriesSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    private function generateZoneExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        // Worksheet 1: Summary
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
        foreach ($reportData['summary'] as $key => $value) {
            $summarySheet->setCellValue('A' . $summaryRow, $this->formatHeader($key));
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'collected') !== false || strpos($key, 'arrears') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'consumption') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } elseif (strpos($key, 'rate') !== false) {
                    $summarySheet->setCellValue('B' . $summaryRow, $value / 100);
                    $summarySheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('0.00%');
                } else {
                    $summarySheet->setCellValue('B' . $summaryRow, $value);
                }
            } else {
                $summarySheet->setCellValue('B' . $summaryRow, $value);
            }
            $summaryRow++;
        }

        // Worksheet 2: Zone Details
        if (isset($reportData['zones']) && $reportData['zones']->count() > 0) {
            $zonesSheet = $spreadsheet->createSheet();
            $zonesSheet->setTitle('Zone Details');

            $headers = [
                'Zone Name', 'Description', 'Meter Count', 'Customer Count',
                'Walk Route Count', 'Total Revenue', 'Total Collected',
                'Total Arrears', 'Total Consumption', 'Average Consumption',
                'Reading Count', 'Bill Count', 'Payment Count', 'Collection Rate',
                'Average Bill Amount', 'Average Payment Amount'
            ];
            $this->addSheetHeader($zonesSheet, $headers);

            $row = 2;
            foreach ($reportData['zones'] as $zone) {
                $zonesSheet->setCellValue('A' . $row, $zone->name);
                $zonesSheet->setCellValue('B' . $row, $zone->description);
                $zonesSheet->setCellValue('C' . $row, $zone->meter_count);
                $zonesSheet->setCellValue('D' . $row, $zone->customer_count);
                $zonesSheet->setCellValue('E' . $row, $zone->walk_route_count);
                $zonesSheet->setCellValue('F' . $row, $zone->total_revenue);
                $zonesSheet->setCellValue('G' . $row, $zone->total_collected);
                $zonesSheet->setCellValue('H' . $row, $zone->total_arrears);
                $zonesSheet->setCellValue('I' . $row, $zone->total_consumption);
                $zonesSheet->setCellValue('J' . $row, $zone->average_consumption);
                $zonesSheet->setCellValue('K' . $row, $zone->reading_count);
                $zonesSheet->setCellValue('L' . $row, $zone->bill_count);
                $zonesSheet->setCellValue('M' . $row, $zone->payment_count);
                $zonesSheet->setCellValue('N' . $row, $zone->collection_rate / 100);
                $zonesSheet->setCellValue('O' . $row, $zone->average_bill_amount);
                $zonesSheet->setCellValue('P' . $row, $zone->average_payment_amount);

                // Format numbers
                $zonesSheet->getStyle('F' . $row . ':H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $zonesSheet->getStyle('I' . $row . ':J' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $zonesSheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('0.00%');
                $zonesSheet->getStyle('O' . $row . ':P' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'P') as $column) {
                $zonesSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    // Helper methods for Excel generation
    private function addReportHeader($sheet, $reportType, $startDate, $endDate)
    {
        $sheet->setCellValue('A1', 'NYAMIRA WATER AND SANITATION COMPANY LIMITED');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:D1');

        $sheet->setCellValue('A2', 'P.O. Box 255 - 40500, NYAMIRA | Tel: 0787080455 | Email: info@nyawasco.co.ke');
        $sheet->mergeCells('A2:D2');

        $sheet->setCellValue('A3', $reportType);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells('A3:D3');

        $period = $startDate ?
            'Period: ' . $startDate->format('d F Y') . ' to ' . $endDate->format('d F Y') :
            'All Time Data';
        $sheet->setCellValue('A4', $period . ' | Generated: ' . now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A4:D4');

        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function addSheetHeader($sheet, $headers)
    {
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getStyle($col . '1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }
    }

    private function addSheetTotals($sheet, $row, $columns)
    {
        $sheet->setCellValue('A' . $row, 'TOTALS:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        foreach ($columns as $col => $type) {
            if ($type === 'sum') {
                $sheet->setCellValue($col . $row, '=SUM(' . $col . '2:' . $col . ($row - 1) . ')');
            } elseif ($type === 'count') {
                $sheet->setCellValue($col . $row, '=COUNT(' . $col . '2:' . $col . ($row - 1) . ')');
            }
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
        }
    }

    private function formatHeader($key)
    {
        return ucwords(str_replace('_', ' ', $key));
    }

    // Helper method for age category formatting
    private function formatAgeCategory($category)
    {
        $formatted = [
            '0-30_days' => '0-30 Days',
            '31-60_days' => '31-60 Days',
            '61-90_days' => '61-90 Days',
            'over_90_days' => 'Over 90 Days'
        ];

        return $formatted[$category] ?? ucwords(str_replace('_', ' ', $category));
    }

    private function generateCSV($reportData, $reportType, $startDate, $endDate)
    {
        $filename = 'NYAWASCO_' . str_replace(' ', '_', $reportData['type']) . '_' .
                    ($startDate ? $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') : 'All_Time') .
                    '_' . now()->format('Y_m_d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        // Generate CSV based on report type
        switch ($reportType) {
            case 'revenue':
                $this->generateRevenueCSV($output, $reportData);
                break;
            case 'customer':
                $this->generateCustomerCSV($output, $reportData);
                break;
            case 'meter':
                $this->generateMeterCSV($output, $reportData);
                break;
            case 'consumption':
                $this->generateConsumptionCSV($output, $reportData);
                break;
            case 'collection':
                $this->generateCollectionCSV($output, $reportData);
                break;
            case 'arrears':
                $this->generateArrearsCSV($output, $reportData);
                break;
            case 'category':
                $this->generateCategoryCSV($output, $reportData);
                break;
            case 'zone':
                $this->generateZoneCSV($output, $reportData);
                break;
        }

        fclose($output);
        exit;
    }

    private function generateRevenueCSV($output, $reportData)
    {
        // Summary section
        fputcsv($output, ['REVENUE REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'paid') !== false || strpos($key, 'balance') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'rate') !== false || strpos($key, 'percentage') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . '%']);
                } elseif (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['DETAILED BILLS']);

        // Detailed bills
        if (isset($reportData['bills']) && $reportData['bills']->count() > 0) {
            $headers = [
                'Bill Number', 'Customer Number', 'Customer Name', 'Meter Number',
                'Category', 'Zone', 'Billing Period', 'Consumption (m³)',
                'Total Amount', 'Paid Amount', 'Balance', 'Status', 'Due Date'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['bills'] as $bill) {
                fputcsv($output, [
                    $bill->bill_number,
                    $bill->customer->customer_number ?? '',
                    trim($bill->customer->first_name . ' ' . $bill->customer->last_name),
                    $bill->meter->meter_number ?? '',
                    $bill->meter->meterCategory->name ?? '',
                    $bill->meter->zone->name ?? '',
                    ($bill->billing_period_start ? $bill->billing_period_start->format('d/m/Y') : '') . ' - ' .
                    ($bill->billing_period_end ? $bill->billing_period_end->format('d/m/Y') : ''),
                    number_format($bill->consumption, 2),
                    'KSh ' . number_format($bill->total_amount, 2),
                    'KSh ' . number_format($bill->paid_amount, 2),
                    'KSh ' . number_format($bill->balance, 2),
                    ucfirst($bill->bill_status),
                    $bill->due_date ? $bill->due_date->format('d/m/Y') : ''
                ]);
            }
        }
    }

    private function generateCustomerCSV($output, $reportData)
    {
        fputcsv($output, ['CUSTOMER REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'balance') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['CUSTOMER DETAILS']);

        if (isset($reportData['customers']) && $reportData['customers']->count() > 0) {
            $headers = [
                'Customer Number', 'Full Name', 'Phone', 'Email', 'ID Number',
                'Physical Address', 'Plot Number', 'House Number', 'Estate',
                'Status', 'Total Billed', 'Total Paid', 'Total Balance',
                'Total Consumption', 'Bill Count', 'Meter Count'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['customers'] as $customer) {
                fputcsv($output, [
                    $customer->customer_number,
                    trim($customer->first_name . ' ' . $customer->last_name),
                    $customer->phone,
                    $customer->email,
                    $customer->id_number,
                    $customer->physical_address,
                    $customer->plot_number,
                    $customer->house_number,
                    $customer->estate,
                    ucfirst($customer->status),
                    'KSh ' . number_format($customer->total_billed ?? 0, 2),
                    'KSh ' . number_format($customer->total_paid ?? 0, 2),
                    'KSh ' . number_format($customer->total_balance ?? 0, 2),
                    number_format($customer->total_consumption ?? 0, 2) . ' m³',
                    $customer->bill_count ?? 0,
                    $customer->meter_count ?? 0
                ]);
            }
        }
    }

    private function generateMeterCSV($output, $reportData)
    {
        fputcsv($output, ['METER REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'balance') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['METER DETAILS']);

        if (isset($reportData['meters']) && $reportData['meters']->count() > 0) {
            $headers = [
                'Meter Number', 'Category', 'Status', 'Customer Number',
                'Customer Name', 'Zone', 'Total Billed', 'Total Paid',
                'Total Balance', 'Total Consumption', 'Bill Count',
                'Current Balance'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['meters'] as $meter) {
                fputcsv($output, [
                    $meter->meter_number,
                    $meter->meterCategory->name ?? '',
                    ucfirst($meter->status),
                    $meter->customer->customer_number ?? '',
                    $meter->customer ? trim($meter->customer->first_name . ' ' . $meter->customer->last_name) : '',
                    $meter->zone->name ?? '',
                    'KSh ' . number_format($meter->total_billed ?? 0, 2),
                    'KSh ' . number_format($meter->total_paid ?? 0, 2),
                    'KSh ' . number_format($meter->total_balance ?? 0, 2),
                    number_format($meter->total_consumption ?? 0, 2) . ' m³',
                    $meter->bill_count ?? 0,
                    'KSh ' . number_format($meter->current_balance, 2)
                ]);
            }
        }
    }

    private function generateConsumptionCSV($output, $reportData)
    {
        fputcsv($output, ['CONSUMPTION REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['MONTHLY CONSUMPTION']);

        if (isset($reportData['monthly_consumption'])) {
            fputcsv($output, ['Year', 'Month', 'Reading Count', 'Total Consumption', 'Average Consumption', 'Max Consumption', 'Min Consumption']);

            foreach ($reportData['monthly_consumption'] as $month) {
                fputcsv($output, [
                    $month->year,
                    date('F', mktime(0, 0, 0, $month->month, 1)),
                    $month->reading_count,
                    number_format($month->total_consumption, 2) . ' m³',
                    number_format($month->avg_consumption, 2) . ' m³',
                    number_format($month->max_consumption, 2) . ' m³',
                    number_format($month->min_consumption, 2) . ' m³'
                ]);
            }
        }
    }

    private function generateCollectionCSV($output, $reportData)
    {
        fputcsv($output, ['COLLECTION REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'collected') !== false ||
                    strpos($key, 'payment') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'rate') !== false || strpos($key, 'percentage') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . '%']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['PAYMENT DETAILS']);

        if (isset($reportData['payments']) && $reportData['payments']->count() > 0) {
            $headers = [
                'Payment Date', 'Payment Number', 'Customer Name', 'Meter Number',
                'Amount', 'Payment Method', 'Payment Status'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['payments'] as $payment) {
                fputcsv($output, [
                    $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '',
                    $payment->payment_no,
                    $payment->customer ? trim($payment->customer->first_name . ' ' . $payment->customer->last_name) : '',
                    $payment->meter->meter_number ?? '',
                    'KSh ' . number_format($payment->amount, 2),
                    ucfirst($payment->payment_method),
                    ucfirst($payment->payment_status)
                ]);
            }
        }
    }

    private function generateArrearsCSV($output, $reportData)
    {
        fputcsv($output, ['ARREARS REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'arrears') !== false ||
                    strpos($key, 'balance') !== false || strpos($key, 'fees') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif ($value instanceof \Carbon\Carbon) {
                    fputcsv($output, [$label, $value->format('d/m/Y')]);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['TOP DEBTORS']);

        if (isset($reportData['top_debtors']) && $reportData['top_debtors']->count() > 0) {
            $headers = [
                'Customer Number', 'Customer Name', 'Phone', 'Total Arrears',
                'Bill Count', 'Oldest Bill', 'Newest Bill'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['top_debtors'] as $debtor) {
                fputcsv($output, [
                    $debtor['customer']->customer_number ?? '',
                    trim($debtor['customer']->first_name . ' ' . $debtor['customer']->last_name),
                    $debtor['customer']->phone ?? '',
                    'KSh ' . number_format($debtor['total_arrears'], 2),
                    $debtor['bill_count'],
                    $debtor['oldest_bill'] ? $debtor['oldest_bill']->format('d/m/Y') : '',
                    $debtor['newest_bill'] ? $debtor['newest_bill']->format('d/m/Y') : ''
                ]);
            }
        }
    }

    private function generateCategoryCSV($output, $reportData)
    {
        fputcsv($output, ['CATEGORY REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'collected') !== false || strpos($key, 'arrears') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } elseif (strpos($key, 'rate') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . '%']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['CATEGORY DETAILS']);

        if (isset($reportData['categories']) && $reportData['categories']->count() > 0) {
            $headers = [
                'Category Name', 'Code', 'Meter Count', 'Total Revenue',
                'Total Collected', 'Total Arrears', 'Total Consumption',
                'Collection Rate', 'Average Bill Amount'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['categories'] as $category) {
                fputcsv($output, [
                    $category->name,
                    $category->code,
                    $category->meters_count,
                    'KSh ' . number_format($category->total_revenue, 2),
                    'KSh ' . number_format($category->total_paid, 2),
                    'KSh ' . number_format($category->total_balance, 2),
                    number_format($category->total_consumption, 2) . ' m³',
                    number_format($category->collection_rate, 2) . '%',
                    'KSh ' . number_format($category->average_bill_amount, 2)
                ]);
            }
        }
    }

    private function generateZoneCSV($output, $reportData)
    {
        fputcsv($output, ['ZONE REPORT - SUMMARY']);
        fputcsv($output, []);

        foreach ($reportData['summary'] as $key => $value) {
            $label = $this->formatHeader($key);
            if (is_numeric($value)) {
                if (strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                    strpos($key, 'collected') !== false || strpos($key, 'arrears') !== false) {
                    fputcsv($output, [$label, 'KSh ' . number_format($value, 2)]);
                } elseif (strpos($key, 'consumption') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . ' m³']);
                } elseif (strpos($key, 'rate') !== false) {
                    fputcsv($output, [$label, number_format($value, 2) . '%']);
                } else {
                    fputcsv($output, [$label, number_format($value)]);
                }
            } else {
                fputcsv($output, [$label, $value]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['ZONE DETAILS']);

        if (isset($reportData['zones']) && $reportData['zones']->count() > 0) {
            $headers = [
                'Zone Name', 'Meter Count', 'Customer Count', 'Total Revenue',
                'Total Collected', 'Total Arrears', 'Total Consumption',
                'Collection Rate'
            ];
            fputcsv($output, $headers);

            foreach ($reportData['zones'] as $zone) {
                fputcsv($output, [
                    $zone->name,
                    $zone->meter_count,
                    $zone->customer_count,
                    'KSh ' . number_format($zone->total_revenue, 2),
                    'KSh ' . number_format($zone->total_collected, 2),
                    'KSh ' . number_format($zone->total_arrears, 2),
                    number_format($zone->total_consumption, 2) . ' m³',
                    number_format($zone->collection_rate, 2) . '%'
                ]);
            }
        }
    }
    private function generateCustomerStatementReport($customerId, $startDate, $endDate, $detailLevel)
    {
        $customer = Customer::with([
            'meters.meterCategory',
            'meters.zone',
            'meters.walkRoute',
            'bills' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereBetween('billing_period_end', [$startDate, $endDate]);
                }
                $q->with('payments');
            },
            'meterReadings' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereBetween('reading_date', [$startDate, $endDate]);
                }
                $q->with('meter');
            },
            'payments' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereBetween('payment_date', [$startDate, $endDate]);
                }
                $q->with('bill');
            }
        ])->findOrFail($customerId);

        // Calculate summary statistics
        $summary = [
            'total_billed' => $customer->bills->sum('total_amount'),
            'total_paid' => $customer->payments->sum('amount'),
            'outstanding_balance' => $customer->bills->sum('total_amount') - $customer->payments->sum('amount'),
            'total_consumption' => $customer->meterReadings->sum('consumption'),
            'average_monthly_consumption' => $customer->meterReadings->avg('consumption'),
            'bills_count' => $customer->bills->count(),
            'payments_count' => $customer->payments->count(),
            'readings_count' => $customer->meterReadings->count(),
            'meter_count' => $customer->meters->count(),
            'account_age_days' => $customer->created_at ? now()->diffInDays($customer->created_at) : 0,
            'last_payment_date' => $customer->payments->max('payment_date'),
            'last_payment_amount' => $customer->payments->where('payment_date', $customer->payments->max('payment_date'))->first()->amount ?? 0,
            'last_reading_date' => $customer->meterReadings->max('reading_date'),
            'last_bill_date' => $customer->bills->max('billing_period_end'),
        ];

        return [
            'type' => 'Customer Statement',
            'detail_level' => $detailLevel,
            'customer' => $customer,
            'summary' => $summary,
            'bills' => $customer->bills,
            'payments' => $customer->payments,
            'meter_readings' => $customer->meterReadings,
            'meters' => $customer->meters,
        ];
    }

    private function generateStatementExcel($spreadsheet, $reportData, $startDate, $endDate)
    {
        $customer = $reportData['customer'];

        // Worksheet 1: Customer Information
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Customer Information');
        $this->addReportHeader($infoSheet, $reportData['type'], $startDate, $endDate);

        $row = 5;
        $infoSheet->setCellValue('A' . $row, 'CUSTOMER INFORMATION');
        $infoSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row += 2;

        // Customer Details
        $details = [
            ['Customer Number:', $customer->customer_number],
            ['Full Name:', trim($customer->first_name . ' ' . $customer->last_name)],
            ['Phone:', $customer->phone],
            ['Email:', $customer->email],
            ['ID Number:', $customer->id_number],
            ['KRA PIN:', $customer->kra_pin ?? 'N/A'],
            ['Physical Address:', $customer->physical_address],
            ['Plot Number:', $customer->plot_number],
            ['House Number:', $customer->house_number],
            ['Estate:', $customer->estate ?? 'N/A'],
            ['Account Status:', ucfirst($customer->status)],
            ['Property Owner:', $customer->property_owner],
            ['Expected Users:', $customer->expected_users ?? 'N/A'],
            ['Created Date:', $customer->created_at->format('d/m/Y')],
            ['Last Updated:', $customer->updated_at->format('d/m/Y')],
        ];

        foreach ($details as $detail) {
            $infoSheet->setCellValue('A' . $row, $detail[0]);
            $infoSheet->setCellValue('B' . $row, $detail[1]);
            $infoSheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }

        // Account Summary
        $row += 2;
        $infoSheet->setCellValue('A' . $row, 'ACCOUNT SUMMARY');
        $infoSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row += 2;

        $summaryDetails = [
            ['Total Billed:', 'KSh ' . number_format($reportData['summary']['total_billed'], 2)],
            ['Total Paid:', 'KSh ' . number_format($reportData['summary']['total_paid'], 2)],
            ['Outstanding Balance:', 'KSh ' . number_format($reportData['summary']['outstanding_balance'], 2)],
            ['Total Consumption:', number_format($reportData['summary']['total_consumption'], 2) . ' m³'],
            ['Average Monthly Consumption:', number_format($reportData['summary']['average_monthly_consumption'], 2) . ' m³'],
            ['Number of Bills:', $reportData['summary']['bills_count']],
            ['Number of Payments:', $reportData['summary']['payments_count']],
            ['Number of Readings:', $reportData['summary']['readings_count']],
            ['Number of Meters:', $reportData['summary']['meter_count']],
            ['Account Age:', $reportData['summary']['account_age_days'] . ' days'],
            ['Last Payment Date:', $reportData['summary']['last_payment_date'] ? Carbon::parse($reportData['summary']['last_payment_date'])->format('d/m/Y') : 'N/A'],
            ['Last Payment Amount:', 'KSh ' . number_format($reportData['summary']['last_payment_amount'], 2)],
            ['Last Reading Date:', $reportData['summary']['last_reading_date'] ? Carbon::parse($reportData['summary']['last_reading_date'])->format('d/m/Y') : 'N/A'],
            ['Last Bill Date:', $reportData['summary']['last_bill_date'] ? Carbon::parse($reportData['summary']['last_bill_date'])->format('d/m/Y') : 'N/A'],
        ];

        foreach ($summaryDetails as $detail) {
            $infoSheet->setCellValue('A' . $row, $detail[0]);
            $infoSheet->setCellValue('B' . $row, $detail[1]);
            $infoSheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }

        // Auto-size columns
        $infoSheet->getColumnDimension('A')->setWidth(25);
        $infoSheet->getColumnDimension('B')->setWidth(40);

        // Worksheet 2: Bills History (if detailed or full)
        if ($reportData['detail_level'] === 'detailed' || $reportData['detail_level'] === 'full') {
            if ($reportData['bills']->count() > 0) {
                $billsSheet = $spreadsheet->createSheet();
                $billsSheet->setTitle('Bills History');

                $headers = [
                    'Bill Number', 'Billing Period', 'Consumption (m³)',
                    'Total Amount', 'Paid Amount', 'Balance', 'Status', 'Due Date'
                ];
                $this->addSheetHeader($billsSheet, $headers);

                $row = 2;
                foreach ($reportData['bills'] as $bill) {
                    $billsSheet->setCellValue('A' . $row, $bill->bill_number);
                    $billsSheet->setCellValue('B' . $row,
                        ($bill->billing_period_start ? $bill->billing_period_start->format('d/m/Y') : '') . ' - ' .
                        ($bill->billing_period_end ? $bill->billing_period_end->format('d/m/Y') : '')
                    );
                    $billsSheet->setCellValue('C' . $row, $bill->consumption);
                    $billsSheet->setCellValue('D' . $row, $bill->total_amount);
                    $billsSheet->setCellValue('E' . $row, $bill->paid_amount);
                    $billsSheet->setCellValue('F' . $row, $bill->balance);
                    $billsSheet->setCellValue('G' . $row, ucfirst($bill->bill_status));
                    $billsSheet->setCellValue('H' . $row, $bill->due_date ? $bill->due_date->format('d/m/Y') : '');

                    // Format numbers
                    $billsSheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $billsSheet->getStyle('D' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    $row++;
                }

                // Add totals
                $this->addSheetTotals($billsSheet, $row, [
                    'C' => 'sum',
                    'D' => 'sum',
                    'E' => 'sum',
                    'F' => 'sum'
                ]);

                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $billsSheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        }

        // Worksheet 3: Payment History (if detailed or full)
        if ($reportData['detail_level'] === 'detailed' || $reportData['detail_level'] === 'full') {
            if ($reportData['payments']->count() > 0) {
                $paymentsSheet = $spreadsheet->createSheet();
                $paymentsSheet->setTitle('Payment History');

                $headers = [
                    'Payment Date', 'Payment Number', 'Receipt Number', 'Amount',
                    'Payment Method', 'Transaction Reference', 'Status', 'Bill Number'
                ];
                $this->addSheetHeader($paymentsSheet, $headers);

                $row = 2;
                foreach ($reportData['payments'] as $payment) {
                    $paymentsSheet->setCellValue('A' . $row, $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '');
                    $paymentsSheet->setCellValue('B' . $row, $payment->payment_no);
                    $paymentsSheet->setCellValue('C' . $row, $payment->receipt_number);
                    $paymentsSheet->setCellValue('D' . $row, $payment->amount);
                    $paymentsSheet->setCellValue('E' . $row, ucfirst($payment->payment_method));
                    $paymentsSheet->setCellValue('F' . $row, $payment->transaction_reference);
                    $paymentsSheet->setCellValue('G' . $row, ucfirst($payment->payment_status));
                    $paymentsSheet->setCellValue('H' . $row, $payment->bill->bill_number ?? 'N/A');

                    // Format numbers
                    $paymentsSheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    $row++;
                }

                // Add totals
                $this->addSheetTotals($paymentsSheet, $row, ['D' => 'sum']);

                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $paymentsSheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        }

        // Worksheet 4: Meter Details (if full)
        if ($reportData['detail_level'] === 'full') {
            if ($reportData['meters']->count() > 0) {
                $metersSheet = $spreadsheet->createSheet();
                $metersSheet->setTitle('Meter Details');

                $headers = [
                    'Meter Number', 'Meter Type', 'Category', 'Status',
                    'Installation Address', 'Installation Date', 'Initial Reading',
                    'Current Balance', 'Paid Amount', 'Zone', 'Walk Route'
                ];
                $this->addSheetHeader($metersSheet, $headers);

                $row = 2;
                foreach ($reportData['meters'] as $meter) {
                    $metersSheet->setCellValue('A' . $row, $meter->meter_number);
                    $metersSheet->setCellValue('B' . $row, $meter->meter_type);
                    $metersSheet->setCellValue('C' . $row, $meter->meterCategory->name ?? '');
                    $metersSheet->setCellValue('D' . $row, ucfirst($meter->status));
                    $metersSheet->setCellValue('E' . $row, $meter->installation_address);
                    $metersSheet->setCellValue('F' . $row, $meter->installation_date ? $meter->installation_date->format('d/m/Y') : '');
                    $metersSheet->setCellValue('G' . $row, $meter->initial_reading);
                    $metersSheet->setCellValue('H' . $row, $meter->current_balance);
                    $metersSheet->setCellValue('I' . $row, $meter->paid_amount);
                    $metersSheet->setCellValue('J' . $row, $meter->zone->name ?? '');
                    $metersSheet->setCellValue('K' . $row, $meter->walkRoute->name ?? '');

                    // Format numbers
                    $metersSheet->getStyle('G' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    $row++;
                }

                // Auto-size columns
                foreach (range('A', 'K') as $column) {
                    $metersSheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        }
    }
}
