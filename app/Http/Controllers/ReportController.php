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
           // Increase memory limit and execution time
        ini_set('memory_limit', '512M');  // Increase to 512MB or even 1G if needed
        ini_set('max_execution_time', 300);

        $request->validate([
            'report_type' => 'required|in:revenue,customer,meter,consumption,collection,arrears,category,zone',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'nullable|in:pdf,excel,csv,view',
            'detail_level' => 'nullable|in:summary,detailed,full',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $detailLevel = $request->detail_level ?? 'summary';

        $reportData = $this->generateReportData($request->report_type, $startDate, $endDate, $detailLevel);

        if ($request->format === 'pdf') {
            return $this->generatePDF($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'excel') {
            return $this->generateExcel($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'csv') {
            return $this->generateCSV($reportData, $request->report_type, $startDate, $endDate);
        }

        return view('reports.show', compact('reportData', 'startDate', 'endDate'));
    }

    private function generateReportData($type, $startDate, $endDate, $detailLevel = 'summary')
    {
        switch ($type) {
            case 'revenue':
                return $this->generateRevenueReport($startDate, $endDate, $detailLevel);
            case 'customer':
                return $this->generateCustomerReport($startDate, $endDate, $detailLevel);
            case 'meter':
                return $this->generateMeterReport($startDate, $endDate, $detailLevel);
            case 'consumption':
                return $this->generateConsumptionReport($startDate, $endDate, $detailLevel);
            case 'collection':
                return $this->generateCollectionReport($startDate, $endDate, $detailLevel);
            case 'arrears':
                return $this->generateArrearsReport($startDate, $endDate, $detailLevel);
            case 'category':
                return $this->generateCategoryReport($startDate, $endDate, $detailLevel);
            case 'zone':
                return $this->generateZoneReport($startDate, $endDate, $detailLevel);
            default:
                return [];
        }
    }

    private function generateRevenueReport($startDate, $endDate, $detailLevel)
    {
        $query = Bill::with(['customer', 'meter.meterCategory', 'meter.zone', 'meter.walkroute']);

        if ($startDate) {
            // Include NULL records when filtering by date
            $query->where(function($q) use ($startDate, $endDate) {
                // Records with actual dates in the range
                $q->whereBetween('billing_period_end', [$startDate, $endDate])
                // OR records with NULL billing_period_end (treat as before start)
                ->orWhereNull('billing_period_end');
            });
        }

        $bills = $query->get();

        // For the breakdown queries, you need to handle NULLs specially
        $monthlyRevenue = DB::table('bills')
            ->select(
                DB::raw('YEAR(IFNULL(billing_period_end, "1900-01-01")) as year'),
                DB::raw('MONTH(IFNULL(billing_period_end, "1900-01-01")) as month'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(consumption) as total_consumption'),
                DB::raw('SUM(CASE WHEN billing_period_end IS NULL THEN 1 ELSE 0 END) as is_legacy')
            )
            // ... rest of query
            ->groupBy(DB::raw('YEAR(IFNULL(billing_period_end, "1900-01-01")), MONTH(IFNULL(billing_period_end, "1900-01-01"))'))
            ->get();
        // Category breakdown with NULL handling
        $categoryRevenue = DB::table('bills')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->join('meter_categories', 'meters.meter_category_id', '=', 'meter_categories.id')
            ->select(
                'meter_categories.name as category',
                'meter_categories.code',
                DB::raw('SUM(bills.total_amount) as total_amount'),
                DB::raw('SUM(bills.paid_amount) as paid_amount'),
                DB::raw('SUM(bills.consumption) as total_consumption'),
                DB::raw('COUNT(*) as bill_count'),
                // Flag for NULL billing periods
                DB::raw('SUM(CASE WHEN bills.billing_period_end IS NULL THEN 1 ELSE 0 END) as legacy_records')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->where(function($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('bills.billing_period_end', [$startDate, $endDate])
                            ->orWhereNull('bills.billing_period_end');
                });
            })
            ->groupBy('meter_categories.id', 'meter_categories.name', 'meter_categories.code')
            ->get();

        // Zone breakdown with NULL handling
        $zoneRevenue = DB::table('bills')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->leftJoin('zones', 'meters.zone_id', '=', 'zones.id')
            ->select(
                DB::raw('COALESCE(zones.name, "Unassigned") as zone_name'),
                DB::raw('SUM(bills.total_amount) as total_amount'),
                DB::raw('SUM(bills.paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(CASE WHEN bills.billing_period_end IS NULL THEN 1 ELSE 0 END) as legacy_records')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->where(function($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('bills.billing_period_end', [$startDate, $endDate])
                            ->orWhereNull('bills.billing_period_end');
                });
            })
            ->groupBy('zones.id', 'zones.name')
            ->get();

        return [
            'type' => 'Revenue Report',
            'detail_level' => $detailLevel,
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
                'average_bill_amount' => $bills->avg('total_amount'),
                'collection_efficiency' => $bills->sum('total_amount') > 0 ?
                    ($bills->sum('paid_amount') / $bills->sum('total_amount')) * 100 : 0,
                // Add legacy records info
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
    private function generateMeterReport($startDate, $endDate, $detailLevel)
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

        $meters = $query->get()->map(function ($meter) {
            $meter->total_billed = $meter->bills->sum('total_amount');
            $meter->total_paid = $meter->bills->sum('paid_amount');
            $meter->total_balance = $meter->bills->sum('balance');
            $meter->total_consumption = $meter->bills->sum('consumption');
            $meter->bill_count = $meter->bills->count();
            $meter->last_reading_date = $meter->bills->max('billing_period_end');
            return $meter;
        });

        // Category breakdown
        $categoryStats = $meters->groupBy('meter_category_id')->map(function ($group) use ($meters) {
            return [
                'category' => $group->first()->meterCategory->name ?? 'Unknown',
                'count' => $group->count(),
                'total_billed' => $group->sum('total_billed'),
                'total_consumption' => $group->sum('total_consumption'),
                'meters_with_customers' => $group->whereNotNull('customer_id')->count(),
                'meters_without_customers' => $group->whereNull('customer_id')->count(),
            ];
        });

        // Zone breakdown
        $zoneStats = $meters->groupBy('zone_id')->map(function ($group) use ($meters) {
            return [
                'zone' => $group->first()->zone->name ?? 'Unassigned',
                'count' => $group->count(),
                'total_billed' => $group->sum('total_billed'),
                'meters_with_customers' => $group->whereNotNull('customer_id')->count(),
            ];
        });

        // Status breakdown - FIXED: Pass $meters to closure
        $statusStats = $meters->groupBy('status')->map(function ($group) use ($meters) {
            return [
                'status' => $group->first()->status,
                'count' => $group->count(),
                'percentage' => ($meters->count() > 0) ? ($group->count() / $meters->count()) * 100 : 0,
            ];
        });

        return [
            'type' => 'Meter Report',
            'detail_level' => $detailLevel,
            'meters' => $meters,
            'category_stats' => $categoryStats,
            'zone_stats' => $zoneStats,
            'status_stats' => $statusStats,
            'summary' => [
                'total_meters' => $meters->count(),
                'active_meters' => $meters->where('status', 'available')->count(),
                'faulty_meters' => $meters->where('status', '!=', 'available')->count(),
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

    private function generateCollectionReport($startDate, $endDate, $detailLevel)
    {
        $query = Payment::with(['bill.customer', 'meter.meterCategory', 'collector']);

        if ($startDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $payments = $query->get();

        // Daily collection
        $dailyCollection = DB::table('payments')
            ->select(
                'payment_date',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(amount) as avg_amount')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('payment_date', [$startDate, $endDate]);
            })
            ->groupBy('payment_date')
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

        // Collector performance
        $collectorPerformance = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name as collector_name',
                DB::raw('SUM(payments.amount) as total_collected'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(payments.amount) as avg_payment')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('payments.payment_date', [$startDate, $endDate]);
            })
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_collected', 'desc')
            ->get();

        // Category collection
        $categoryCollection = DB::table('payments')
            ->join('meters', 'payments.meter_id', '=', 'meters.id')
            ->join('meter_categories', 'meters.meter_category_id', '=', 'meter_categories.id')
            ->select(
                'meter_categories.name as category',
                DB::raw('SUM(payments.amount) as total_collected'),
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('AVG(payments.amount) as avg_payment')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('payments.payment_date', [$startDate, $endDate]);
            })
            ->groupBy('meter_categories.id', 'meter_categories.name')
            ->get();

        return [
            'type' => 'Collection Report',
            'detail_level' => $detailLevel,
            'payments' => $payments,
            'daily_collection' => $dailyCollection,
            'method_breakdown' => $methodBreakdown,
            'collector_performance' => $collectorPerformance,
            'category_collection' => $categoryCollection,
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
        })->sortByDesc('total_arrears')->take(20);

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
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
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

        // Worksheet 2: Monthly Breakdown
        if (isset($reportData['monthly_breakdown'])) {
            $monthlySheet = $spreadsheet->createSheet();
            $monthlySheet->setTitle('Monthly Breakdown');

            $headers = ['Year', 'Month', 'Total Bills', 'Total Amount', 'Amount Paid', 'Outstanding', 'Total Consumption', 'Collection Rate'];
            $this->addSheetHeader($monthlySheet, $headers);

            $row = 2;
            foreach ($reportData['monthly_breakdown'] as $month) {
                $monthlySheet->setCellValue('A' . $row, $month->year);
                $monthlySheet->setCellValue('B' . $row, date('F', mktime(0, 0, 0, $month->month, 1)));
                $monthlySheet->setCellValue('C' . $row, $month->bill_count);
                $monthlySheet->setCellValue('D' . $row, $month->total_amount);
                $monthlySheet->setCellValue('E' . $row, $month->paid_amount);
                $monthlySheet->setCellValue('F' . $row, $month->total_amount - $month->paid_amount);
                $monthlySheet->setCellValue('G' . $row, $month->total_consumption);
                $monthlySheet->setCellValue('H' . $row, $month->total_amount > 0 ? ($month->paid_amount / $month->total_amount) : 0);

                // Format numbers
                $monthlySheet->getStyle('D' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $monthlySheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('0.00%');

                $row++;
            }

            // Add totals
            $this->addSheetTotals($monthlySheet, $row, [
                'C' => 'count',
                'D' => 'sum',
                'E' => 'sum',
                'F' => 'sum',
                'G' => 'sum'
            ]);
        }

        // Worksheet 3: Detailed Bills
        if (isset($reportData['bills']) && $reportData['bills']->count() > 0) {
            $billsSheet = $spreadsheet->createSheet();
            $billsSheet->setTitle('Detailed Bills');

            $headers = [
                'Bill Number', 'Customer Name', 'Customer Acc',
                'Category', 'Zone', 'Billing Period', 'Consumption (m³)',
                'Total Amount', 'Paid Amount', 'Balance', 'Status', 'Due Date'
            ];
            $this->addSheetHeader($billsSheet, $headers);

            $row = 2;
            foreach ($reportData['bills'] as $bill) {
                $billsSheet->setCellValue('A' . $row, $bill->bill_number);
                $billsSheet->setCellValue('B' . $row, $bill->customer->first_name . ' ' . $bill->customer->last_name);
                $billsSheet->setCellValue('C' . $row, $bill->meter->meter_number ?? '');
                $billsSheet->setCellValue('D' . $row, $bill->meter->meterCategory->name ?? '');
                $billsSheet->setCellValue('E' . $row, $bill->meter->zone->name ?? '');
                $billsSheet->setCellValue('F' . $row,
                    ($bill->billing_period_start ? $bill->billing_period_start->format('d/m/Y') : '') . ' - ' .
                    ($bill->billing_period_end ? $bill->billing_period_end->format('d/m/Y') : '')
                );
                $billsSheet->setCellValue('G' . $row, $bill->consumption);
                $billsSheet->setCellValue('H' . $row, $bill->total_amount);
                $billsSheet->setCellValue('I' . $row, $bill->paid_amount);
                $billsSheet->setCellValue('J' . $row, $bill->balance);
                $billsSheet->setCellValue('K' . $row, ucfirst($bill->bill_status));
                $billsSheet->setCellValue('L' . $row, $bill->due_date ? $bill->due_date->format('d/m/Y') : '');

                // Format numbers
                $billsSheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $billsSheet->getStyle('H' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'L') as $column) {
                $billsSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
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

        // Worksheet 2: Meter Details
        if (isset($reportData['meters']) && $reportData['meters']->count() > 0) {
            $metersSheet = $spreadsheet->createSheet();
            $metersSheet->setTitle('Meter Details');

            $headers = [
                'Customer Acc', 'Customer Name','Meter Type', 'Meter Number',
                'Category',
                'Status',  'Zone', 'Walk Route',
                'Initial Reading', 'Balance Bf.',
                'Current Balance', 'Paid Amount'
            ];
            $this->addSheetHeader($metersSheet, $headers);

            $row = 2;
            foreach ($reportData['meters'] as $meter) {
                $metersSheet->setCellValue('A' . $row, $meter->meter_number ?? '');
                $metersSheet->setCellValue('B' . $row, $meter->customer ?
                    trim($meter->customer->first_name . ' ' . $meter->customer->last_name) : '');
                $metersSheet->setCellValue('C' . $row, $meter->meter_type);
                $metersSheet->setCellValue('D' . $row, $meter->meter_number);
                $metersSheet->setCellValue('E' . $row, $meter->meterCategory->name ?? '');
                $metersSheet->setCellValue('F' . $row, ucfirst($meter->status));
                $metersSheet->setCellValue('G' . $row, $meter->zone->name ?? '');
                $metersSheet->setCellValue('H' . $row, $meter->walkroute->name ?? '');
                $metersSheet->setCellValue('I' . $row, $meter->initial_reading);
                $metersSheet->setCellValue('J' . $row, $meter->balance_bf ?? 0);
                $metersSheet->setCellValue('K' . $row, $meter->current_balance);
                $metersSheet->setCellValue('L' . $row, $meter->paid_amount);

                // Format numbers
                $metersSheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $metersSheet->getStyle('K' . $row . ':L' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'L') as $column) {
                $metersSheet->getColumnDimension($column)->setAutoSize(true);
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
        $this->addReportHeader($summarySheet, $reportData['type'], $startDate, $endDate);

        $summaryRow = 5;
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

        // Worksheet 2: Daily Collection
        if (isset($reportData['daily_collection'])) {
            $dailySheet = $spreadsheet->createSheet();
            $dailySheet->setTitle('Daily Collection');

            $headers = ['Date', 'Payment Count', 'Total Amount', 'Average Amount'];
            $this->addSheetHeader($dailySheet, $headers);

            $row = 2;
            foreach ($reportData['daily_collection'] as $day) {
                $dailySheet->setCellValue('A' . $row, $day->payment_date ? Carbon::parse($day->payment_date)->format('d/m/Y') : '');
                $dailySheet->setCellValue('B' . $row, $day->payment_count);
                $dailySheet->setCellValue('C' . $row, $day->total_amount);
                $dailySheet->setCellValue('D' . $row, $day->avg_amount);

                // Format numbers
                $dailySheet->getStyle('C' . $row . ':D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Add totals
            $this->addSheetTotals($dailySheet, $row, [
                'B' => 'count',
                'C' => 'sum',
            ]);
        }

        // Worksheet 3: Payment Details
        if (isset($reportData['payments']) && $reportData['payments']->count() > 0) {
            $paymentsSheet = $spreadsheet->createSheet();
            $paymentsSheet->setTitle('Payment Details');

            $headers = [
                'Payment Date', 'Payment Number', 'Receipt Number', 'Customer Number',
                'Customer Name', 'Meter Number', 'Amount', 'Payment Method',
                'Transaction Reference', 'Payment Status', 'Collector', 'Bill Number'
            ];
            $this->addSheetHeader($paymentsSheet, $headers);

            $row = 2;
            foreach ($reportData['payments'] as $payment) {
                $paymentsSheet->setCellValue('A' . $row, $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '');
                $paymentsSheet->setCellValue('B' . $row, $payment->payment_no);
                $paymentsSheet->setCellValue('C' . $row, $payment->receipt_number);
                $paymentsSheet->setCellValue('D' . $row, $payment->customer->customer_number ?? '');
                $paymentsSheet->setCellValue('E' . $row, $payment->customer ?
                    trim($payment->customer->first_name . ' ' . $payment->customer->last_name) : '');
                $paymentsSheet->setCellValue('F' . $row, $payment->meter->meter_number ?? '');
                $paymentsSheet->setCellValue('G' . $row, $payment->amount);
                $paymentsSheet->setCellValue('H' . $row, ucfirst($payment->payment_method));
                $paymentsSheet->setCellValue('I' . $row, $payment->transaction_reference);
                $paymentsSheet->setCellValue('J' . $row, ucfirst($payment->payment_status));
                $paymentsSheet->setCellValue('K' . $row, $payment->collector->name ?? '');
                $paymentsSheet->setCellValue('L' . $row, $payment->bill->bill_number ?? '');

                // Format numbers
                $paymentsSheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'L') as $column) {
                $paymentsSheet->getColumnDimension($column)->setAutoSize(true);
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
                'Customer Number', 'Customer Name', 'Phone', 'Total Arrears',
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
}
