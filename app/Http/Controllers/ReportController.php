<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:revenue,customer,meter,consumption,collection,arrears,category',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'nullable|in:pdf,excel,csv,view',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $reportData = $this->generateReportData($request->report_type, $startDate, $endDate);

        if ($request->format === 'pdf') {
            return $this->generatePDF($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'excel') {
            return $this->generateExcel($reportData, $request->report_type, $startDate, $endDate);
        } elseif ($request->format === 'csv') {
            return $this->generateCSV($reportData, $request->report_type, $startDate, $endDate);
        }

        return view('reports.show', compact('reportData', 'startDate', 'endDate'));
    }

    private function generateReportData($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'revenue':
                return $this->generateRevenueReport($startDate, $endDate);
            case 'customer':
                return $this->generateCustomerReport($startDate, $endDate);
            case 'meter':
                return $this->generateMeterReport($startDate, $endDate);
            case 'consumption':
                return $this->generateConsumptionReport($startDate, $endDate);
            case 'collection':
                return $this->generateCollectionReport($startDate, $endDate);
            case 'arrears':
                return $this->generateArrearsReport($startDate, $endDate);
            case 'category':
                return $this->generateCategoryReport($startDate, $endDate);
            default:
                return [];
        }
    }

    private function generateRevenueReport($startDate, $endDate)
    {
        $query = Bill::with(['customer', 'meter.meterCategory']);

        if ($startDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $bills = $query->get();

        // Monthly breakdown
        $monthlyRevenue = DB::table('bills')
            ->select(
                DB::raw('YEAR(billing_period_end) as year'),
                DB::raw('MONTH(billing_period_end) as month'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('billing_period_end', [$startDate, $endDate]);
            })
            ->groupBy(DB::raw('YEAR(billing_period_end), MONTH(billing_period_end)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Category breakdown
        $categoryRevenue = DB::table('bills')
            ->join('meters', 'bills.meter_id', '=', 'meters.id')
            ->join('meter_categories', 'meters.meter_category_id', '=', 'meter_categories.id')
            ->select(
                'meter_categories.name as category',
                'meter_categories.code',
                DB::raw('SUM(bills.total_amount) as total_amount'),
                DB::raw('SUM(bills.paid_amount) as paid_amount'),
                DB::raw('COUNT(*) as bill_count')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('bills.billing_period_end', [$startDate, $endDate]);
            })
            ->groupBy('meter_categories.id', 'meter_categories.name', 'meter_categories.code')
            ->get();

        return [
            'type' => 'Revenue Report',
            'bills' => $bills,
            'monthly_breakdown' => $monthlyRevenue,
            'category_breakdown' => $categoryRevenue,
            'summary' => [
                'total_amount' => $bills->sum('total_amount'),
                'total_paid' => $bills->sum('paid_amount'),
                'total_balance' => $bills->sum('balance'),
                'bill_count' => $bills->count(),
                'paid_bills' => $bills->where('bill_status', 'paid')->count(),
                'unpaid_bills' => $bills->where('bill_status', 'unpaid')->count(),
                'partial_bills' => $bills->where('bill_status', 'partial')->count(),
            ]
        ];
    }

    private function generateCustomerReport($startDate, $endDate)
    {
        $query = Customer::with(['meters', 'bills' => function ($q) use ($startDate, $endDate) {
            if ($startDate) {
                $q->whereBetween('billing_period_end', [$startDate, $endDate]);
            }
        }]);

        $customers = $query->get()->map(function ($customer) {
            $customer->total_billed = $customer->bills->sum('total_amount');
            $customer->total_paid = $customer->bills->sum('paid_amount');
            $customer->total_balance = $customer->bills->sum('balance');
            $customer->total_consumption = $customer->bills->sum('consumption');
            return $customer;
        });

        // Status breakdown
        $statusCounts = $customers->groupBy('status')->map->count();

        return [
            'type' => 'Customer Report',
            'customers' => $customers,
            'status_breakdown' => $statusCounts,
            'summary' => [
                'total_customers' => $customers->count(),
                'active_customers' => $customers->where('status', 'active')->count(),
                'inactive_customers' => $customers->where('status', 'inactive')->count(),
                'total_billed' => $customers->sum('total_billed'),
                'total_paid' => $customers->sum('total_paid'),
                'total_balance' => $customers->sum('total_balance'),
                'average_consumption' => $customers->avg('total_consumption'),
            ]
        ];
    }

    private function generateMeterReport($startDate, $endDate)
    {
        $query = Meter::with([
            'meterCategory',
            'customer',
            'bills' => function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereBetween('billing_period_end', [$startDate, $endDate]);
                }
            }
        ]);

        $meters = $query->get()->map(function ($meter) {
            $meter->total_billed = $meter->bills->sum('total_amount');
            $meter->total_consumption = $meter->bills->sum('consumption');
            $meter->bill_count = $meter->bills->count();
            return $meter;
        });

        // Category breakdown
        $categoryStats = $meters->groupBy('meter_category_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_billed' => $group->sum('total_billed'),
                'total_consumption' => $group->sum('total_consumption'),
                'category' => $group->first()->meterCategory->name ?? 'Unknown'
            ];
        });

        return [
            'type' => 'Meter Report',
            'meters' => $meters,
            'category_stats' => $categoryStats,
            'summary' => [
                'total_meters' => $meters->count(),
                'active_meters' => $meters->where('status', 'available')->count(),
                'faulty_meters' => $meters->where('status', '!=', 'available')->count(),
                'total_billed' => $meters->sum('total_billed'),
                'total_consumption' => $meters->sum('total_consumption'),
                'meters_without_customers' => $meters->whereNull('customer_id')->count(),
            ]
        ];
    }

    private function generateConsumptionReport($startDate, $endDate)
    {
        $query = MeterReading::with(['customer', 'meter.meterCategory'])
            ->where('billed', true);

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
                DB::raw('AVG(consumption) as avg_consumption')
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
                DB::raw('COUNT(*) as reading_count')
            )
            ->where('meter_readings.billed', true)
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('meter_readings.reading_date', [$startDate, $endDate]);
            })
            ->groupBy('meter_categories.id', 'meter_categories.name')
            ->get();

        return [
            'type' => 'Consumption Report',
            'readings' => $readings,
            'monthly_consumption' => $monthlyConsumption,
            'category_consumption' => $categoryConsumption,
            'summary' => [
                'total_consumption' => $readings->sum('consumption'),
                'average_consumption' => $readings->avg('consumption'),
                'reading_count' => $readings->count(),
                'highest_consumption' => $readings->max('consumption'),
                'lowest_consumption' => $readings->min('consumption'),
            ]
        ];
    }

    private function generateCollectionReport($startDate, $endDate)
    {
        $query = Payment::with(['bill.customer', 'collector']);

        if ($startDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $payments = $query->get();

        // Daily collection
        $dailyCollection = DB::table('payments')
            ->select(
                'payment_date',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('payment_date', [$startDate, $endDate]);
            })
            ->groupBy('payment_date')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Payment method breakdown
        $methodBreakdown = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'total_amount' => $group->sum('amount'),
                'count' => $group->count(),
                'percentage' => ($payments->sum('amount') > 0) ?
                    ($group->sum('amount') / $payments->sum('amount')) * 100 : 0
            ];
        });

        return [
            'type' => 'Collection Report',
            'payments' => $payments,
            'daily_collection' => $dailyCollection,
            'method_breakdown' => $methodBreakdown,
            'summary' => [
                'total_collected' => $payments->sum('amount'),
                'payment_count' => $payments->count(),
                'average_payment' => $payments->avg('amount'),
                'highest_payment' => $payments->max('amount'),
                'lowest_payment' => $payments->min('amount'),
                'collection_efficiency' => ($payments->sum('amount') / $payments->sum('amount') + 1) * 100, // Simplified
            ]
        ];
    }

    private function generateArrearsReport($startDate, $endDate)
    {
        $query = Bill::with(['customer', 'meter.meterCategory'])
            ->where('balance', '>', 0)
            ->where('bill_status', '!=', 'paid');

        if ($startDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        $arrears = $query->get();

        // Age analysis
        $ageAnalysis = [
            '0-30_days' => $arrears->where('due_date', '>=', now()->subDays(30))->sum('balance'),
            '31-60_days' => $arrears->whereBetween('due_date', [now()->subDays(60), now()->subDays(31)])->sum('balance'),
            '61-90_days' => $arrears->whereBetween('due_date', [now()->subDays(90), now()->subDays(61)])->sum('balance'),
            'over_90_days' => $arrears->where('due_date', '<', now()->subDays(90))->sum('balance'),
        ];

        // Top debtors
        $topDebtors = $arrears->groupBy('customer_id')->map(function ($bills, $customerId) {
            $customer = $bills->first()->customer;
            return [
                'customer' => $customer,
                'total_arrears' => $bills->sum('balance'),
                'bill_count' => $bills->count(),
                'oldest_bill' => $bills->min('due_date'),
            ];
        })->sortByDesc('total_arrears')->take(10);

        return [
            'type' => 'Arrears Report',
            'arrears' => $arrears,
            'age_analysis' => $ageAnalysis,
            'top_debtors' => $topDebtors,
            'summary' => [
                'total_arrears' => $arrears->sum('balance'),
                'customer_count' => $arrears->unique('customer_id')->count(),
                'bill_count' => $arrears->count(),
                'average_arrears' => $arrears->avg('balance'),
                'oldest_arrear' => $arrears->min('due_date'),
                'newest_arrear' => $arrears->max('due_date'),
            ]
        ];
    }

    private function generateCategoryReport($startDate, $endDate)
    {
        $categories = MeterCategory::withCount(['meters', 'bills' => function ($q) use ($startDate, $endDate) {
            if ($startDate) {
                $q->whereBetween('billing_period_end', [$startDate, $endDate]);
            }
        }])->with(['pricingTiers' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        // Add statistics to each category
        $categories = $categories->map(function ($category) use ($startDate, $endDate) {
            $billsQuery = $category->bills();
            if ($startDate) {
                $billsQuery->whereBetween('billing_period_end', [$startDate, $endDate]);
            }
            $bills = $billsQuery->get();

            $category->total_revenue = $bills->sum('total_amount');
            $category->total_consumption = $bills->sum('consumption');
            $category->average_consumption = $bills->avg('consumption');
            $category->collection_rate = $bills->sum('total_amount') > 0 ?
                ($bills->sum('paid_amount') / $bills->sum('total_amount')) * 100 : 0;

            return $category;
        });

        return [
            'type' => 'Meter Category Report',
            'categories' => $categories,
            'summary' => [
                'total_categories' => $categories->count(),
                'active_categories' => $categories->where('is_active', true)->count(),
                'total_meters' => $categories->sum('meters_count'),
                'total_revenue' => $categories->sum('total_revenue'),
                'total_consumption' => $categories->sum('total_consumption'),
                'average_rate' => $categories->avg('default_rate'),
            ]
        ];
    }

   private function generatePDF($reportData, $reportType, $startDate, $endDate)
{
    $pdf = PDF::loadView('reports.pdf', compact('reportData', 'reportType', 'startDate', 'endDate'));

    // Professional A4 settings
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'sans-serif',
        'dpi' => 150, // Lower DPI for faster generation, still good quality
        'margin_top' => 20,
        'margin_bottom' => 25,
        'margin_left' => 15,
        'margin_right' => 15,
        'isPhpEnabled' => true, // Enable PHP in PDF for page numbering
        'isFontSubsettingEnabled' => true, // Reduce file size
    ]);

    $filename = 'NYAWASCO_' . str_replace(' ', '_', $reportData['type']) . '_' .
                ($startDate ? $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d') : 'All_Time') .
                '_' . now()->format('Y_m_d') . '.pdf';

    return $pdf->download($filename);
}

    private function generateExcel($reportData, $reportType, $startDate, $endDate)
    {
        // You'll need to install and use Laravel Excel package
        // For now, return a JSON response
        return response()->json([
            'message' => 'Excel export feature requires Laravel Excel package',
            'data' => $reportData
        ]);
    }

    private function generateCSV($reportData, $reportType, $startDate, $endDate)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' .
                strtolower(str_replace(' ', '_', $reportData['type'])) . '_' .
                now()->format('Y_m_d') . '.csv"',
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');

            // Write headers based on report type
            switch ($reportType) {
                case 'revenue':
                    fputcsv($file, ['Bill Number', 'Customer', 'Amount', 'Paid', 'Balance', 'Status', 'Date']);
                    foreach ($reportData['bills'] as $bill) {
                        fputcsv($file, [
                            $bill->bill_number,
                            $bill->customer->first_name . ' ' . $bill->customer->last_name,
                            $bill->total_amount,
                            $bill->paid_amount,
                            $bill->balance,
                            $bill->bill_status,
                            $bill->billing_period_end
                        ]);
                    }
                    break;
                // Add other report types as needed
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
