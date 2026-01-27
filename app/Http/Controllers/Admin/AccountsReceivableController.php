<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\User;
use App\Models\Zone;
use App\Models\WriteOff;
use App\Models\CollectionActivity;
use App\Models\AgingBucket;
use App\Models\Meter;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AccountsReceivableController extends Controller
{
    public function dashboard()
    {
        // Get comprehensive summary
        $summary = $this->getDashboardSummary();

        // Get aging analysis
        $agingReport = $this->getAgingAnalysis();

        // Get top delinquent customers with more details
        $delinquentCustomers = Customer::with(['meters.meterCategory', 'latestBill'])
            ->whereHas('bills', function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            })
            ->withCount(['bills as overdue_bill_count' => function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            }])
            ->withSum(['bills as total_overdue' => function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            }], 'balance')
            ->orderByDesc('total_overdue')
            ->limit(2)
            ->get();

        // Get recent collection activities
        $recentActivities = CollectionActivity::with(['customer', 'agent'])
            ->whereDate('activity_date', '>=', now()->subDays(7))
            ->orderByDesc('activity_date')
            ->limit(2)
            ->get();

        // Get collection performance stats
        $collectionStats = $this->getCollectionStats();

        // Get write-off summary with trend
        $writeOffSummary = $this->getWriteOffSummary();
        $writeOffTrend = $this->getWriteOffTrend();

        return view('admin.accounts-receivable.dashboard', compact(
            'summary',
            'agingReport',
            'delinquentCustomers',
            'recentActivities',
            'collectionStats',
            'writeOffSummary',
            'writeOffTrend'
        ));
    }

    private function getDashboardSummary()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth();

        // Total receivables
        $totalReceivables = Bill::where('bill_status', '!=', 'paid')->sum('balance');

        // Active customers with balances
        $activeCustomers = Customer::where('status', 'active')
            ->whereHas('bills', function($query) {
                $query->where('bill_status', '!=', 'paid');
            })
            ->count();

        // Overdue analysis
        $overdueAmount = Bill::where('bill_status', '!=', 'paid')
            ->where('due_date', '<', $now)
            ->sum('balance');

        $overdueCustomers = Bill::where('bill_status', '!=', 'paid')
            ->where('due_date', '<', $now)
            ->distinct('customer_id')
            ->count('customer_id');

        // Collection rate calculation
        $collectedThisMonth = Payment::whereBetween('payment_date', [$monthStart, $now])
            ->where('payment_status', 'allocated')
            ->sum('amount');

        $billedThisMonth = Bill::whereBetween('created_at', [$monthStart, $now])
            ->sum('total_amount');

        $collectionRate = $billedThisMonth > 0 ? ($collectedThisMonth / $billedThisMonth) * 100 : 0;

        // Bad debt ratio
        $writeOffsThisYear = WriteOff::whereYear('created_at', $now->year)
            ->where('status', 'approved')
            ->sum('amount');

        $totalBilledThisYear = Bill::whereYear('created_at', $now->year)->sum('total_amount');
        $badDebtRatio = $totalBilledThisYear > 0 ? ($writeOffsThisYear / $totalBilledThisYear) * 100 : 0;

        // Month-over-month comparison
        $previousMonthReceivables = Bill::where('bill_status', '!=', 'paid')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('balance');

        $changePercentage = $previousMonthReceivables > 0
            ? (($totalReceivables - $previousMonthReceivables) / $previousMonthReceivables) * 100
            : 0;

        return [
            'total_receivables' => $totalReceivables,
            'active_customers' => $activeCustomers,
            'overdue_amount' => $overdueAmount,
            'overdue_customers' => $overdueCustomers,
            'overdue_percentage' => $totalReceivables > 0 ? ($overdueAmount / $totalReceivables) * 100 : 0,
            'collection_rate' => $collectionRate,
            'bad_debt_ratio' => $badDebtRatio,
            'month_change' => $changePercentage,
            'average_balance' => $activeCustomers > 0 ? $totalReceivables / $activeCustomers : 0,
            'aging_score' => $this->calculateAgingScore()
        ];
    }

    private function getAgingAnalysis()
    {
        $buckets = AgingBucket::active()->ordered()->get();
        $report = collect();
        $totalAmount = Bill::where('bill_status', '!=', 'paid')->sum('balance');

        foreach ($buckets as $bucket) {
            $query = Bill::where('bill_status', '!=', 'paid');

            if ($bucket->from_days > 0) {
                $query->where('due_date', '<=', now()->subDays($bucket->from_days));
            }

            if ($bucket->to_days !== null) {
                $query->where('due_date', '>', now()->subDays($bucket->to_days));
            }

            $total = $query->sum('balance');
            $billCount = $query->count();
            $customerCount = $query->distinct('customer_id')->count('customer_id');
            $percentage = $totalAmount > 0 ? ($total / $totalAmount) * 100 : 0;

            $report->push([
                'bucket' => $bucket,
                'total_amount' => $total,
                'bill_count' => $billCount,
                'customer_count' => $customerCount,
                'percentage' => $percentage,
                'average_per_customer' => $customerCount > 0 ? $total / $customerCount : 0,
                'average_per_bill' => $billCount > 0 ? $total / $billCount : 0
            ]);
        }

        return $report;
    }

    private function getCollectionStats()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $lastWeek = now()->subDays(7)->toDateString();

        // Today's activities
        $todayActivities = CollectionActivity::whereDate('activity_date', $today)->count();

        // Today's promises
        $todayPromises = CollectionActivity::whereDate('activity_date', $today)
            ->where('outcome', 'promise_to_pay')
            ->count();

        // Month activities
        $monthActivities = CollectionActivity::whereBetween('activity_date', [$monthStart, $today])->count();
        $monthPromises = CollectionActivity::whereBetween('activity_date', [$monthStart, $today])
            ->where('outcome', 'promise_to_pay')
            ->count();

        // Promise fulfillment analysis
        $recentPromises = CollectionActivity::where('outcome', 'promise_to_pay')
            ->whereDate('promised_date', '>=', $lastWeek)
            ->get();

        $promisesKept = 0;
        $totalPromiseAmount = 0;
        $collectedAmount = 0;

        foreach ($recentPromises as $promise) {
            $customer = $promise->customer;
            $payments = $customer->payments()
                ->where('payment_date', '>=', $promise->promised_date)
                ->where('payment_date', '<=', $promise->promised_date->addDays(3))
                ->sum('amount');

            if ($payments >= $promise->promised_amount * 0.8) { // 80% fulfillment threshold
                $promisesKept++;
                $collectedAmount += $payments;
            }
            $totalPromiseAmount += $promise->promised_amount;
        }

        // Activity outcomes breakdown
        $outcomes = CollectionActivity::whereBetween('activity_date', [$monthStart, $today])
            ->select('outcome', DB::raw('COUNT(*) as count'))
            ->groupBy('outcome')
            ->get();

        $successful = $outcomes->whereIn('outcome', ['payment_made', 'promise_to_pay'])->sum('count');
        $partial = $outcomes->where('outcome', 'contacted')->sum('count');
        $failed = $outcomes->whereIn('outcome', ['no_answer', 'disconnected', 'dispute'])->sum('count');

        return [
            'today_activities' => $todayActivities,
            'today_promises' => $todayPromises,
            'month_activities' => $monthActivities,
            'month_promises' => $monthPromises,
            'promises_kept' => $promisesKept,
            'total_promises' => $recentPromises->count(),
            'promise_fulfillment_rate' => $recentPromises->count() > 0 ? ($promisesKept / $recentPromises->count()) * 100 : 0,
            'avg_promise_amount' => $recentPromises->avg('promised_amount') ?? 0,
            'total_promise_amount' => $totalPromiseAmount,
            'collected_from_promises' => $collectedAmount,
            'successful_activities' => $successful,
            'partial_activities' => $partial,
            'failed_activities' => $failed
        ];
    }

    private function getWriteOffSummary()
    {
        $currentYear = now()->year;

        return WriteOff::selectRaw('
            type,
            SUM(amount) as total_amount,
            COUNT(*) as count,
            AVG(amount) as average_amount
        ')
        ->where('status', 'approved')
        ->whereYear('created_at', $currentYear)
        ->groupBy('type')
        ->orderByDesc('total_amount')
        ->get();
    }

    private function getWriteOffTrend()
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        return WriteOff::selectRaw('
                YEAR(write_off_date) as year,
                MONTH(write_off_date) as month_num,
                SUM(amount) as amount,
                COUNT(*) as count
            ')
            ->where('status', 'approved')
            ->where('write_off_date', '>=', $sixMonthsAgo)
            ->groupBy(
                DB::raw('YEAR(write_off_date)'),
                DB::raw('MONTH(write_off_date)')
            )
            ->orderBy(
                DB::raw('YEAR(write_off_date), MONTH(write_off_date)')
            )
            ->get()
            ->map(function ($row) {
                $row->month = \Carbon\Carbon::create($row->year, $row->month_num, 1)
                    ->format('M Y');
                return $row;
            });
    }

    private function calculateAgingScore()
    {
        $buckets = AgingBucket::active()->ordered()->get();
        $totalScore = 0;
        $maxScore = 0;

        foreach ($buckets as $bucket) {
            $weight = $bucket->collection_priority; // Higher priority = more concerning
            $amount = Bill::where('bill_status', '!=', 'paid')
                ->where('due_date', '<=', now()->subDays($bucket->from_days))
                ->when($bucket->to_days !== null, function($query) use ($bucket) {
                    $query->where('due_date', '>', now()->subDays($bucket->to_days));
                })
                ->sum('balance');

            $totalReceivables = Bill::where('bill_status', '!=', 'paid')->sum('balance');
            $percentage = $totalReceivables > 0 ? ($amount / $totalReceivables) * 100 : 0;

            $totalScore += $percentage * $weight;
            $maxScore += 100 * $weight; // Maximum possible score
        }

        // Normalize to 0-100 scale (lower is better)
        $score = $maxScore > 0 ? 100 - (($totalScore / $maxScore) * 100) : 100;

        return round($score, 1);
    }

    // API endpoint for refreshing dashboard data
    public function refreshDashboard(Request $request)
    {
        $summary = $this->getDashboardSummary();
        $agingReport = $this->getAgingAnalysis();
        $collectionStats = $this->getCollectionStats();

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'agingReport' => $agingReport,
            'collectionStats' => $collectionStats,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    // Quick log activity endpoint
    public function quickLogActivity(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'activity_type' => 'required|in:call,visit,email,sms,letter,promise_to_pay',
            'notes' => 'required|string|max:500',
            'outcome' => 'nullable|in:contacted,promise_to_pay,payment_made,no_answer,disconnected,dispute',
            'promised_amount' => 'nullable|numeric|min:0',
            'promised_date' => 'nullable|date'
        ]);

        try {
            $activity = CollectionActivity::create([
                'customer_id' => $validated['customer_id'],
                'collection_agent_id' => auth()->id(),
                'activity_type' => $validated['activity_type'],
                'notes' => $validated['notes'],
                'activity_date' => now(),
                'outcome' => $validated['outcome'],
                'promised_amount' => $validated['promised_amount'] ?? null,
                'promised_date' => $validated['promised_date'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Activity logged successfully',
                'activity' => $activity->load('customer')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log activity: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export dashboard data
    public function exportDashboard(Request $request)
    {
        $summary = $this->getDashboardSummary();
        $agingReport = $this->getAgingAnalysis();
        $collectionStats = $this->getCollectionStats();
        $writeOffSummary = $this->getWriteOffSummary();

        $data = [
            'summary' => $summary,
            'aging_report' => $agingReport,
            'collection_stats' => $collectionStats,
            'write_off_summary' => $writeOffSummary,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name
        ];

        if ($request->get('format') === 'excel') {
            // Implement Excel export
            return $this->exportToExcel($data);
        }

        // Default to PDF
        return $this->exportToPDF($data);
    }

    private function exportToPDF($data)
    {
        // PDF generation logic
        // return PDF::loadView('admin.accounts-receivable.exports.dashboard-pdf', $data)
        //     ->download('accounts-receivable-dashboard-' . now()->format('Y-m-d') . '.pdf');

        // Placeholder for PDF export
        return response()->json([
            'message' => 'PDF export feature will be implemented'
        ]);
    }

    private function exportToExcel($data)
    {
        // Excel export logic
        // return Excel::download(new DashboardExport($data), 'accounts-receivable-dashboard-' . now()->format('Y-m-d') . '.xlsx');

        // Placeholder for Excel export
        return response()->json([
            'message' => 'Excel export feature will be implemented'
        ]);
    }

    // Additional dashboard endpoints

    public function getAgingChartData(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $agingReport = $this->getAgingAnalysis();

        // Apply filters if needed
        if ($filter !== 'all') {
            // Filter logic based on meter type, zone, etc.
        }

        return response()->json([
            'labels' => $agingReport->pluck('bucket.name'),
            'data' => $agingReport->pluck('total_amount'),
            'colors' => $agingReport->pluck('bucket.color')
        ]);
    }

    public function getPerformanceMetrics(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $metrics = [
            'collection_rate' => $this->calculateCollectionRateForPeriod($startDate, $endDate),
            'promise_fulfillment' => $this->calculatePromiseFulfillment($startDate, $endDate),
            'write_off_ratio' => $this->calculateWriteOffRatio($startDate, $endDate),
            'average_collection_period' => $this->calculateAverageCollectionPeriod($startDate, $endDate)
        ];

        return response()->json($metrics);
    }

    private function calculateCollectionRateForPeriod($startDate, $endDate)
    {
        $collected = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('payment_status', 'allocated')
            ->sum('amount');

        $billed = Bill::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        return $billed > 0 ? ($collected / $billed) * 100 : 0;
    }

    private function calculatePromiseFulfillment($startDate, $endDate)
    {
        $promises = CollectionActivity::where('outcome', 'promise_to_pay')
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->get();

        $fulfilled = 0;
        foreach ($promises as $promise) {
            $payments = $promise->customer->payments()
                ->whereBetween('payment_date', [
                    $promise->promised_date ?? $promise->activity_date,
                    ($promise->promised_date ?? $promise->activity_date)->addDays(7)
                ])
                ->sum('amount');

            if ($payments >= ($promise->promised_amount ?? 0) * 0.8) {
                $fulfilled++;
            }
        }

        return $promises->count() > 0 ? ($fulfilled / $promises->count()) * 100 : 0;
    }

    private function calculateWriteOffRatio($startDate, $endDate)
    {
        $writeOffs = WriteOff::where('status', 'approved')
            ->whereBetween('write_off_date', [$startDate, $endDate])
            ->sum('amount');

        $billed = Bill::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        return $billed > 0 ? ($writeOffs / $billed) * 100 : 0;
    }

    private function calculateAverageCollectionPeriod($startDate, $endDate)
    {
        $payments = Payment::where('payment_status', 'allocated')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('bill')
            ->get();

        if ($payments->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($payments as $payment) {
            if ($payment->bill) {
                $days = $payment->bill->due_date->diffInDays($payment->payment_date);
                $totalDays += max($days, 0); // Only count positive days (collected after due date)
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }

    public function agingReport(Request $request)
    {
        $date = $request->get('as_of_date', now()->toDateString());
        $zoneId = $request->get('zone_id');
        $categoryId = $request->get('category_id');

        $agingData = $this->getDetailedAgingReport($date, $zoneId, $categoryId);
        $agingBuckets = AgingBucket::active()->ordered()->get();
        $summary = $this->getAgingSummary($agingData);

        return view('admin.accounts-receivable.aging-report', compact(
            'agingData',
            'agingBuckets',
            'summary',
            'date'
        ));
    }

    public function collectionsTracking(Request $request)
    {
        $query = CollectionActivity::with(['customer', 'agent'])
            ->when($request->filled('agent_id'), function($query) use ($request) {
                $query->where('collection_agent_id', $request->agent_id);
            })
            ->when($request->filled('activity_type'), function($query) use ($request) {
                $query->where('activity_type', $request->activity_type);
            })
            ->when($request->filled('outcome'), function($query) use ($request) {
                $query->where('outcome', $request->outcome);
            })
            ->when($request->filled('date_from'), function($query) use ($request) {
                $query->where('activity_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                $query->where('activity_date', '<=', $request->date_to . ' 23:59:59');
            })
            ->when($request->filled('follow_up'), function($query) use ($request) {
                if ($request->follow_up === 'pending') {
                    $query->whereNotNull('follow_up_date')
                        ->where('follow_up_date', '>=', now());
                } elseif ($request->follow_up === 'completed') {
                    $query->whereNotNull('follow_up_date')
                        ->where('follow_up_date', '<', now());
                }
            })
            ->orderBy('activity_date', 'desc');

        $activities = $query->paginate(20);

        $agents = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['collector', 'supervisor', 'admin']);
        })->orderBy('name')->get();

        return view('admin.accounts-receivable.collections-tracking', compact('activities', 'agents'));
    }

    public function createCollectionActivity(Request $request)
    {
        $customers = Customer::where('status', 'active')
            ->whereHas('bills', function($query) {
                $query->where('bill_status', '!=', 'paid');
            })
            ->with(['bills' => function($query) {
                $query->where('bill_status', '!=', 'paid');
            }])
            ->orderBy('customer_number')
            ->limit(50)
            ->get()
            ->map(function($customer) {
                $totalBalance = $customer->bills->sum('balance');
                return [
                    'id' => $customer->id,
                    'text' => $customer->customer_number . ' - ' .
                            $customer->first_name . ' ' . $customer->last_name .
                            ' (KSh ' . number_format($totalBalance, 2) . ')',
                    'balance' => $totalBalance
                ];
            });

        return view('admin.accounts-receivable.partials.activity-modal', compact('customers'));
    }

    public function writeOffs(Request $request)
    {
        $writeOffs = WriteOff::with(['customer', 'approver'])
            ->when($request->get('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->get('type'), function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->get('date_from'), function($query, $date) {
                $query->where('write_off_date', '>=', $date);
            })
            ->when($request->get('date_to'), function($query, $date) {
                $query->where('write_off_date', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.accounts-receivable.write-offs', compact('writeOffs'));
    }

    public function createWriteOff(Request $request, Customer $customer)
    {
        $pendingBills = $customer->bills()
            ->where('bill_status', '!=', 'paid')
            ->where('due_date', '<', now()->subDays(90))
            ->get();

        return view('admin.accounts-receivable.create-write-off', compact('customer', 'pendingBills'));
    }

    public function storeWriteOff(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:bad_debt,dispute,adjustment,waiver',
            'reason' => 'required|string|max:255',
            'description' => 'required|string',
            'write_off_date' => 'required|date',
            'bill_ids' => 'nullable|array',
            'bill_ids.*' => 'exists:bills,id'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Check if customer has sufficient balance
                $customer = Customer::find($validated['customer_id']);
                $customerBalance = $customer->bills()->where('bill_status', '!=', 'paid')->sum('balance');

                if ($validated['amount'] > $customerBalance) {
                    throw new \Exception('Write-off amount exceeds customer balance');
                }

                $writeOff = WriteOff::create([
                    'customer_id' => $validated['customer_id'],
                    'amount' => $validated['amount'],
                    'type' => $validated['type'],
                    'reason' => $validated['reason'],
                    'description' => $validated['description'],
                    'write_off_date' => $validated['write_off_date'],
                    'affected_bills' => $validated['bill_ids'] ?? [],
                    'status' => 'pending',
                    'created_by' => auth()->id()
                ]);

                // If write-off is for specific bills, mark them as written off
                if (!empty($validated['bill_ids'])) {
                    Bill::whereIn('id', $validated['bill_ids'])
                        ->update([
                            'bill_status' => 'written_off',
                            'write_off_id' => $writeOff->id,
                            'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nMarked for write-off on " . now()->format('Y-m-d') . "')")
                        ]);
                }

                // Don't update customer balance yet - wait for approval
                // $customer->decrement('credit_balance', $validated['amount']);

                // Log the write-off creation
                Log::info('Write-off created', [
                    'write_off_id' => $writeOff->id,
                    'customer_id' => $validated['customer_id'],
                    'amount' => $validated['amount'],
                    'type' => $validated['type'],
                    'created_by' => auth()->id()
                ]);
            });

            return redirect()->route('admin.accounts-receivable.write-offs.index')
                ->with('success', 'Write-off request submitted successfully for approval.');

        } catch (\Exception $e) {
            Log::error('Error creating write-off: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create write-off: ' . $e->getMessage());
        }
    }

    public function approveWriteOff(WriteOff $writeOff)
    {
        try {
            DB::transaction(function () use ($writeOff) {
                $writeOff->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);

                // Update customer balance
                $customer = $writeOff->customer;
                $customer->decrement('credit_balance', $writeOff->amount);

                // Update bill status if applicable
                if (!empty($writeOff->affected_bills)) {
                    Bill::whereIn('id', $writeOff->affected_bills)
                        ->update([
                            'bill_status' => 'written_off',
                            'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nWrite-off approved on " . now()->format('Y-m-d') . "')")
                        ]);
                }

                // Log the approval
                Log::info('Write-off approved', [
                    'write_off_id' => $writeOff->id,
                    'amount' => $writeOff->amount,
                    'approved_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Write-off approved successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving write-off: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve write-off: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomersWithBalance(Request $request)
    {
        try {
            $customers = Customer::where('status', 'active')
                ->with(['bills' => function($query) {
                    $query->where('bill_status', '!=', 'paid');
                }])
                ->whereHas('bills', function($query) {
                    $query->where('bill_status', '!=', 'paid');
                })
                ->orderBy('customer_number')
                ->limit(100)
                ->get()
                ->map(function($customer) {
                    $totalBalance = $customer->bills->sum('balance');
                    return [
                        'id' => $customer->id,
                        'customer_number' => $customer->customer_number,
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name,
                        'balance' => $totalBalance,
                        'phone' => $customer->phone,
                        'display' => $customer->customer_number . ' - ' .
                                $customer->first_name . ' ' . $customer->last_name .
                                ' (Balance: KSh ' . number_format($totalBalance, 2) . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading customers for write-off: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load customers'
            ], 500);
        }
    }

    public function getCustomerBills(Request $request, $customerId)
    {
        try {
            $bills = Bill::where('customer_id', $customerId)
                ->where('bill_status', '!=', 'paid')
                ->where('balance', '>', 0)
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function($bill) {
                    return [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'due_date' => $bill->due_date->format('Y-m-d'),
                        'balance' => number_format($bill->balance, 2),
                        'total_amount' => number_format($bill->total_amount, 2),
                        'created_at' => $bill->created_at->format('Y-m-d')
                    ];
                });

            return response()->json($bills);
        } catch (\Exception $e) {
            Log::error('Error loading customer bills: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function rejectWriteOff(Request $request, WriteOff $writeOff)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255'
            ]);

            DB::transaction(function () use ($writeOff, $validated) {
                $writeOff->update([
                    'status' => 'rejected',
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                    'rejection_reason' => $validated['reason']
                ]);

                // Log the rejection
                Log::info('Write-off rejected', [
                    'write_off_id' => $writeOff->id,
                    'reason' => $validated['reason'],
                    'rejected_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Write-off rejected successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting write-off: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject write-off'
            ], 500);
        }
    }

    public function reverseWriteOff(Request $request, WriteOff $writeOff)
    {
        try {
            DB::transaction(function () use ($writeOff) {
                $writeOff->update([
                    'status' => 'reversed',
                    'reversed_by' => auth()->id(),
                    'reversed_at' => now()
                ]);

                // Reverse customer balance adjustment
                $customer = $writeOff->customer;
                $customer->increment('credit_balance', $writeOff->amount);

                // Reverse bill status if applicable
                if (!empty($writeOff->affected_bills)) {
                    Bill::whereIn('id', $writeOff->affected_bills)
                        ->update([
                            'bill_status' => 'unpaid',
                            'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nWrite-off reversed on " . now()->format('Y-m-d') . "')")
                        ]);
                }

                // Log the reversal
                Log::info('Write-off reversed', [
                    'write_off_id' => $writeOff->id,
                    'amount' => $writeOff->amount,
                    'reversed_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Write-off reversed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error reversing write-off: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse write-off'
            ], 500);
        }
    }

    public function customerBalances(Request $request)
    {
        $customers = Customer::with(['meters', 'bills' => function($query) {
            $query->where('bill_status', '!=', 'paid');
        }])
        ->withSum(['bills as total_balance' => function($query) {
            $query->where('bill_status', '!=', 'paid');
        }], 'balance')
        ->when($request->get('min_balance'), function($query, $min) {
            $query->having('total_balance', '>=', $min);
        })
        ->when($request->get('max_balance'), function($query, $max) {
            $query->having('total_balance', '<=', $max);
        })
        ->when($request->get('zone_id'), function($query, $zoneId) {
            $query->whereHas('meters', function($meterQuery) use ($zoneId) {
                $meterQuery->where('zone_id', $zoneId);
            });
        })
        ->orderByDesc('total_balance')
        ->paginate(50);

        $zones = Zone::all();
        $totalBalance = $customers->sum('total_balance');

        return view('admin.accounts-receivable.customer-balances', compact('customers', 'zones', 'totalBalance'));
    }

    public function collectionPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $agents = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['collector', 'supervisor']);
        })->with(['collectionActivities' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('activity_date', [$startDate, $endDate]);
        }])->get();

        $performance = [];

        foreach ($agents as $agent) {
            $activities = $agent->collectionActivities;
            $promises = $activities->where('outcome', 'promise_to_pay');
            $payments = $activities->where('outcome', 'payment_made');

            $performance[] = [
                'agent' => $agent,
                'total_activities' => $activities->count(),
                'promises_made' => $promises->count(),
                'payments_collected' => $payments->count(),
                'promise_fulfillment_rate' => $promises->count() > 0 ?
                    ($payments->count() / $promises->count()) * 100 : 0,
                'average_promise_amount' => $promises->avg('promised_amount'),
                'total_collected' => $payments->sum('promised_amount')
            ];
        }

        return view('admin.accounts-receivable.collection-performance', compact('performance', 'startDate', 'endDate'));
    }

    public function storeCollectionActivity(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'activity_type' => 'required|in:call,visit,email,sms,letter,promise_to_pay',
            'notes' => 'required|string|max:1000',
            'activity_date' => 'required|date',
            'follow_up_date' => 'nullable|date|after_or_equal:activity_date',
            'outcome' => 'nullable|in:contacted,promise_to_pay,payment_made,no_answer,disconnected,dispute',
            'promised_amount' => 'nullable|numeric|min:0',
            'promised_date' => 'nullable|date|after_or_equal:activity_date'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $activity = CollectionActivity::create([
                    'customer_id' => $validated['customer_id'],
                    'collection_agent_id' => auth()->id(),
                    'activity_type' => $validated['activity_type'],
                    'notes' => $validated['notes'],
                    'activity_date' => $validated['activity_date'],
                    'follow_up_date' => $validated['follow_up_date'] ?? null,
                    'outcome' => $validated['outcome'] ?? null,
                    'promised_amount' => $validated['promised_amount'] ?? null,
                    'promised_date' => $validated['promised_date'] ?? null
                ]);

                // Add note to customer record
                $customer = Customer::find($validated['customer_id']);
                $customer->update([
                    'notes' => $customer->notes . "\nCollection activity logged: " .
                            ucfirst($validated['activity_type']) . " on " .
                            now()->format('Y-m-d H:i') . " - " .
                            Str::limit($validated['notes'], 100)
                ]);

                // Log the activity
                Log::info('Collection activity logged', [
                    'activity_id' => $activity->id,
                    'customer_id' => $validated['customer_id'],
                    'activity_type' => $validated['activity_type'],
                    'agent_id' => auth()->id()
                ]);
            });

            return redirect()->back()
                ->with('success', 'Collection activity logged successfully!');

        } catch (\Exception $e) {
            Log::error('Error logging collection activity: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }
    // Add this method to your AccountsReceivableController
    public function searchCustomer(Request $request)
    {
        // Log the request for debugging
        Log::info('Customer search request', [
            'search' => $request->search,
            'user' => auth()->user()->id,
            'ip' => $request->ip()
        ]);

        try {
            $searchTerm = trim($request->get('search', ''));

            if (strlen($searchTerm) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter at least 2 characters'
                ]);
            }

            // Escape special characters for LIKE query
            $searchTerm = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);

            $customers = Customer::with(['meters', 'bills' => function($query) {
                    $query->where('bill_status', '!=', 'paid');
                }])
                ->where(function($query) use ($searchTerm) {
                    // Exact match for customer number first
                    $query->where('customer_number', $searchTerm)
                        // Then partial matches
                        ->orWhere('customer_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                        // Meter number search
                        ->orWhereHas('meters', function($meterQuery) use ($searchTerm) {
                            $meterQuery->where('meter_number', $searchTerm)
                                    ->orWhere('meter_number', 'LIKE', "%{$searchTerm}%");
                        });
                })
                ->where('status', 'active')
                ->orderBy('customer_number') // Order by customer number
                ->limit(15)
                ->get()
                ->map(function($customer) {
                    $totalBalance = $customer->bills->sum('balance');
                    $latestBill = $customer->bills->sortByDesc('created_at')->first();

                    return [
                        'id' => $customer->id,
                        'customer_number' => $customer->customer_number,
                        'name' => $customer->first_name . ' ' . $customer->last_name,
                        'phone' => $customer->phone ?? 'N/A',
                        'id_number' => $customer->id_number ?? 'N/A',
                        'balance' => $totalBalance,
                        'meter_numbers' => $customer->meters->pluck('meter_number')->implode(', ') ?: 'N/A',
                        'latest_bill_due' => $latestBill ? $latestBill->due_date->format('Y-m-d') : null,
                        'status' => $customer->status
                    ];
                });

            Log::info('Customer search results', [
                'count' => $customers->count(),
                'search_term' => $searchTerm
            ]);

            return response()->json([
                'success' => true,
                'customers' => $customers,
                'count' => $customers->count(),
                'search_term' => $searchTerm
            ]);

        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage(), [
                'search' => $request->search,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
