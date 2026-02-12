<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\User;
use App\Models\Meter;
use App\Models\Customer;
use App\Services\PaymentProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'user', 'meter.zone']); // Add zone relationship

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('payment_no', 'like', "%{$search}%")
                ->orWhere('transaction_reference', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('meter', function($q) use ($search) {
                    $q->where('meter_number', 'like', "%{$search}%");
                });
        }

        // Status filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        // Zone filter
        if ($request->filled('zone') && $request->zone != 'all') {
            $query->whereHas('meter', function($q) use ($request) {
                $q->where('zone_id', $request->zone);
            });
        }

        // Sorting
        $query->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc');

        $payments = $query->paginate(10)->withQueryString();

        // Get all zones for filter dropdown
        $zones = \App\Models\Zone::orderBy('name')->get();

        // Get selected zone for display
        $selectedZone = $request->filled('zone') && $request->zone != 'all'
            ? \App\Models\Zone::find($request->zone)
            : null;

        $zoneId = $request->get('zone', 'all');

        // Calculate statistics for cards WITH zone filter applied
        $statsQuery = Payment::query();

        // Apply zone filter to stats if needed
        if ($request->filled('zone') && $request->zone != 'all') {
            $statsQuery->whereHas('meter', function($q) use ($request) {
                $q->where('zone_id', $request->zone);
            });
        }

        $totalPayments = $statsQuery->clone()->sum('amount');
        $todayCollection = $statsQuery->clone()
            ->whereDate('payment_date', Carbon::today())
            ->where('payment_status', 'completed')
            ->sum('amount');
        $totalPaymentsCount = $statsQuery->clone()->count();
        $completedPaymentsCount = $statsQuery->clone()->where('payment_status', 'completed')->count();
        $pendingPaymentsCount = $statsQuery->clone()->where('payment_status', 'pending')->count();
        $failedPaymentsCount = $statsQuery->clone()->where('payment_status', 'failed')->count();

        return view('payments.index', compact(
            'payments',
            'zones',
            'selectedZone',
            'zoneId',
            'totalPayments',
            'todayCollection',
            'totalPaymentsCount',
            'completedPaymentsCount',
            'pendingPaymentsCount',
            'failedPaymentsCount'
        ));
    }

    /**
     * Search payments for AJAX requests
     */
    public function search(Request $request)
    {
        $search = trim($request->get('search'));
        $zoneId = $request->get('zone');

        $payments = Payment::with(['customer', 'meter.zone']) // Add zone
            ->when($zoneId && $zoneId !== 'all', function ($query) use ($zoneId) {
                return $query->whereHas('meter', function($q) use ($zoneId) {
                    $q->where('zone_id', $zoneId);
                });
            })
            ->where(function($query) use ($search) {
                $query->where('payment_no', 'like', "%{$search}%")
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
            })
            ->orderBy('payment_date', 'desc')
            ->limit(50)
            ->get();

        // Transform for JSON response
        $payments = $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'payment_no' => $payment->payment_no,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'transaction_reference' => $payment->transaction_reference,
                'payment_date' => $payment->payment_date,
                'customer' => $payment->customer,
                'meter' => $payment->meter,
                'zone' => $payment->meter->zone, // Include zone
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($payments);
    }


    /**
     * Search assigned meters for autocomplete
     */
    public function searchMeters(Request $request)
    {
        $search = $request->get('search', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Search assigned meters with partial match
        $meters = Meter::with('customer')
            ->where('status', 'active')
            ->whereNotNull('customer_id')
            ->where('meter_number', 'LIKE', $search . '%') // Partial match from start
            ->limit(10)
            ->get()
            ->map(function($meter) {
                return [
                    'id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'customer_name' => $meter->customer->name ?? 'Unknown Customer',
                    'status' => $meter->status,
                    'display_text' => "{$meter->meter_number} - {$meter->customer->name}"
                ];
            });

        return response()->json($meters);
    }




    protected $paymentService;

    public function __construct(PaymentProcessingService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Store a newly created payment with proper transaction handling
     */

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());

        // Initialize results array for ALL sheets
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
            'not_found_meters' => [],
            'empty_amounts' => 0,
            'sheets_processed' => 0,
            'sheets_skipped' => []
        ];

        // Loop through ALL worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $sheetIndex => $sheet) {
            $sheetName = $sheet->getTitle();
            Log::info("Processing sheet: {$sheetName} (Index: {$sheetIndex})");

            try {
                $sheetResults = $this->processWorksheet($sheet);

                // Merge results from this sheet
                $results['success'] += $sheetResults['success'];
                $results['failed'] += $sheetResults['failed'];
                $results['skipped'] += $sheetResults['skipped'];
                $results['empty_amounts'] += $sheetResults['empty_amounts'];
                $results['not_found_meters'] = array_merge(
                    $results['not_found_meters'],
                    $sheetResults['not_found_meters']
                );
                $results['errors'] = array_merge($results['errors'], $sheetResults['errors']);
                $results['sheets_processed']++;

                Log::info("Sheet {$sheetName} processed", [
                    'success' => $sheetResults['success'],
                    'failed' => $sheetResults['failed'],
                    'skipped' => $sheetResults['skipped']
                ]);

            } catch (\Exception $e) {
                $results['sheets_skipped'][] = [
                    'sheet_name' => $sheetName,
                    'reason' => $e->getMessage()
                ];
                Log::error("Failed to process sheet {$sheetName}: " . $e->getMessage());
            }
        }

        // Create summary
        $summary = [
            'total_sheets' => $spreadsheet->getSheetCount(),
            'sheets_processed' => $results['sheets_processed'],
            'sheets_skipped' => count($results['sheets_skipped']),
            'total_rows_processed' => 'See sheet details',
            'successful' => $results['success'],
            'failed' => $results['failed'],
            'skipped' => $results['skipped'],
            'empty_amounts' => $results['empty_amounts'],
            'not_found_meters' => count($results['not_found_meters'])
        ];

        Log::info('Import completed for all sheets', $summary);

        return back()->with([
            'import_result' => $results,
            'import_summary' => $summary
        ]);
    }

    /**
     * Process a single worksheet
     */
    private function processWorksheet($sheet)
    {
        // Get the highest row and column
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Get all cells as an array
        $rows = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, null, true, true, true);

        // Check if sheet is empty
        if (empty($rows) || count($rows) < 2) {
            return [
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'skipped' => 0,
                'not_found_meters' => [],
                'empty_amounts' => 0
            ];
        }

        // First row = headers
        $headers = $rows[1];

        // Map header names to column letters
        $headerMap = [];
        foreach ($headers as $columnLetter => $headerName) {
            if (!empty($headerName)) {
                $cleanHeader = trim($headerName);
                $headerMap[$cleanHeader] = $columnLetter;
            }
        }

        Log::info("Sheet {$sheet->getTitle()} headers:", array_keys($headerMap));

        // Check if this sheet has the required columns
        $requiredColumns = ['Credit Amt.', 'Particulars'];
        $dateColumns = ['Tran. Date', 'Value Date'];

        $foundDateColumn = null;
        foreach ($dateColumns as $dateCol) {
            if (isset($headerMap[$dateCol])) {
                $foundDateColumn = $dateCol;
                break;
            }
        }

        if (!$foundDateColumn) {
            Log::warning("Sheet {$sheet->getTitle()} skipped - No date column found");
            return [
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'skipped' => 0,
                'not_found_meters' => [],
                'empty_amounts' => 0
            ];
        }

        foreach ($requiredColumns as $requiredColumn) {
            if (!isset($headerMap[$requiredColumn])) {
                Log::warning("Sheet {$sheet->getTitle()} skipped - Missing column: {$requiredColumn}");
                return [
                    'success' => 0,
                    'failed' => 0,
                    'errors' => [],
                    'skipped' => 0,
                    'not_found_meters' => [],
                    'empty_amounts' => 0
                ];
            }
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
            'not_found_meters' => [],
            'empty_amounts' => 0
        ];

        // ========== PROCESS TRANSACTIONS BY GROUPING ==========
        $currentDate = null;
        $currentMeterNumber = null;
        $currentParticulars = null;
        $pendingTransactions = [];

        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            if (!isset($rows[$rowNumber])) continue;

            $row = $rows[$rowNumber];

            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Get cell values using the actual column names
            $dateValue = trim($row[$headerMap['Tran. Date']] ?? '');
            $particularsValue = trim($row[$headerMap['Particulars']] ?? '');
            $amountValue = trim($row[$headerMap['Credit Amt.']] ?? '');

            // Value Date is optional
            $valueDateValue = '';
            if (isset($headerMap['Value Date'])) {
                $valueDateValue = trim($row[$headerMap['Value Date']] ?? '');
            }

            // ========== CHECK FOR DATE ==========
            if (!empty($dateValue)) {
                // Process any pending transaction from previous group
                if ($currentDate !== null && $currentMeterNumber !== null) {
                    $this->processPaymentTransaction(
                        $currentDate,
                        $currentMeterNumber,
                        $currentParticulars,
                        $pendingTransactions,
                        $results
                    );
                }

                // Reset for new transaction
                $currentDate = $dateValue;
                $currentMeterNumber = null;
                $currentParticulars = $particularsValue;
                $pendingTransactions = [];

                $pendingTransactions[] = [
                    'row' => $rowNumber,
                    'date' => $dateValue,
                    'amount' => $amountValue,
                    'particulars' => $particularsValue,
                    'value_date' => $valueDateValue,
                    'type' => 'header',
                    'sheet' => $sheet->getTitle()
                ];

                if (preg_match('/#(\d{3,})/', $particularsValue, $matches)) {
                    $currentMeterNumber = $matches[1];
                    Log::info("Sheet {$sheet->getTitle()} - Row {$rowNumber} - Found meter number: {$currentMeterNumber}");
                }
            }
            else {
                $pendingTransactions[] = [
                    'row' => $rowNumber,
                    'date' => $dateValue,
                    'amount' => $amountValue,
                    'particulars' => $particularsValue,
                    'value_date' => $valueDateValue,
                    'type' => empty($amountValue) ? 'info' : 'payment',
                    'sheet' => $sheet->getTitle()
                ];

                if (preg_match('/#(\d{3,})/', $particularsValue, $matches)) {
                    $currentMeterNumber = $matches[1];
                    Log::info("Sheet {$sheet->getTitle()} - Row {$rowNumber} - Found meter number: {$currentMeterNumber}");
                }

                if (!empty($amountValue)) {
                    $amount = $this->parseAmount($amountValue);
                    if ($amount > 0 && $currentMeterNumber) {
                        $this->processPaymentTransaction(
                            $currentDate,
                            $currentMeterNumber,
                            $currentParticulars,
                            [$pendingTransactions[count($pendingTransactions)-1]],
                            $results
                        );
                    }
                }
            }
        }

        // Process the last transaction
        if ($currentDate !== null && $currentMeterNumber !== null) {
            $this->processPaymentTransaction(
                $currentDate,
                $currentMeterNumber,
                $currentParticulars,
                $pendingTransactions,
                $results
            );
        }

        return $results;
    }
    /**
     * Parse amount from string
     */
    private function parseAmount($amountValue)
    {
        if (empty($amountValue) && $amountValue !== 0 && $amountValue !== '0') {
            return 0;
        }

        // If it's already numeric, return as float
        if (is_numeric($amountValue)) {
            return (float) $amountValue; // This will keep 500.00 as 500.00
        }

        // Remove commas (thousand separators)
        $cleaned = str_replace(',', '', $amountValue);

        // If it's numeric after removing commas, return as float
        if (is_numeric($cleaned)) {
            return (float) $cleaned; // This preserves decimal places
        }

        // Try to extract amount using regex
        if (preg_match('/[\d,]+\.?\d*/', $cleaned, $matches)) {
            return (float) $matches[0];
        }

        return 0;
    }
    /**
     * Process a complete payment transaction
     */
    private function processPaymentTransaction($date, $meterNumber, $description, $transactionRows, &$results)
    {
        try {
            // Find the payment amount in this transaction
            $amount = null;
            $paymentRow = null;

            foreach ($transactionRows as $row) {
                if (!empty($row['amount'])) {
                    $amountValue = $row['amount'];
                    $paymentRow = $row['row'];

                    // Parse amount
                    if (is_numeric($amountValue)) {
                        $amount = (float) $amountValue;
                    } else {
                        $amount = (float) str_replace(',', '', $amountValue);
                    }
                    break;
                }
            }

            if (!$amount || $amount <= 0) {
                Log::info("Transaction for meter {$meterNumber} - No valid amount found");
                $results['skipped']++;
                return;
            }

            // ========== DATE PARSING ==========
            $paymentDate = null;
            try {
                // Force interpretation as DD-MM-YYYY (this is your bank statement format)
                $paymentDate = Carbon::createFromFormat('d-m-Y', $date);
            } catch (\Exception $e) {
                try {
                    // Try DD/MM/YYYY format
                    $paymentDate = Carbon::createFromFormat('d/m/Y', $date);
                } catch (\Exception $e2) {
                    // Try other formats as fallback
                    // Format: dd-mm-yyyy with single digits (6-2-2026)
                    if (preg_match('/(\d{1,2})-(\d{1,2})-(\d{4})/', $date, $matches)) {
                        $paymentDate = Carbon::create($matches[3], $matches[2], $matches[1]);
                    }
                    // Format: dd/mm/yyyy with single digits (6/2/2026)
                    elseif (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $date, $matches)) {
                        $paymentDate = Carbon::create($matches[3], $matches[2], $matches[1]);
                    }
                    // Format: yyyy-mm-dd (2026-01-26)
                    elseif (preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})/', $date, $matches)) {
                        $paymentDate = Carbon::create($matches[1], $matches[2], $matches[3]);
                    }
                    // Format: yyyy/mm/dd (2026/01/26)
                    elseif (preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $date, $matches)) {
                        $paymentDate = Carbon::create($matches[1], $matches[2], $matches[3]);
                    }
                    else {
                        throw new \Exception("Could not parse date: {$date}. Expected format: DD-MM-YYYY");
                    }
                }
            }
            Log::info("Processing payment - Meter: {$meterNumber}, Amount: {$amount}, Date: {$paymentDate->format('Y-m-d')}");

            // ========== FIND METER ==========
            $meter = Meter::where('meter_number', $meterNumber)->first();

            if (!$meter) {
                $results['not_found_meters'][] = [
                    'meter_number' => $meterNumber,
                    'amount' => $amount,
                    'date' => $paymentDate->format('Y-m-d'),
                    'description' => substr($description, 0, 200)
                ];
                throw new \Exception("Meter {$meterNumber} not found in database");
            }

            // Check if meter is assigned to a customer
            if (!$meter->customer_id) {
                throw new \Exception("Meter {$meterNumber} is not assigned to any customer");
            }

            // Get the customer from the meter
            $customer = $meter->customer;

            if (!$customer) {
                throw new \Exception("Customer for meter {$meterNumber} not found");
            }

            if ($customer->status !== 'active') {
                throw new \Exception("Customer for meter {$meterNumber} is {$customer->status}");
            }

            // ========== EXTRACT TRANSACTION REFERENCE ==========
            $transactionRef = null;

            // First, find the row that contains the meter number (the one with #)
            foreach ($transactionRows as $row) {
                $particularsToCheck = $row['particulars'] ?? '';

                // This row contains the meter number (has #)
                if (strpos($particularsToCheck, '#') !== false) {
                    // Pattern: Any alphanumeric code (5-20 chars) followed by spaces and then numbers#
                    if (preg_match('/([A-Z0-9]{5,20})\s+\d{3,}#/', $particularsToCheck, $matches)) {
                        $transactionRef = $matches[1];
                        Log::info("Found transaction reference from meter row: {$transactionRef}");
                        break;
                    }
                    // Pattern: Any alphanumeric code at the beginning of the string
                    elseif (preg_match('/^([A-Z0-9]{5,20})\s+\d{3,}#/', $particularsToCheck, $matches)) {
                        $transactionRef = $matches[1];
                        Log::info("Found transaction reference at start: {$transactionRef}");
                        break;
                    }
                }
            }

            // If not found in meter row, check the first row (MPS row)
            if (!$transactionRef && !empty($transactionRows[0]['particulars'])) {
                $firstRowParticulars = $transactionRows[0]['particulars'];

                // Pattern: MPS followed by a code (UB, UAN, TL1, etc.)
                if (preg_match('/MPS\s+([A-Z0-9]{5,20})/', $firstRowParticulars, $matches)) {
                    // Make sure it's not all digits (skip MPS numbers)
                    if (!preg_match('/^\d+$/', $matches[1])) {
                        $transactionRef = $matches[1];
                        Log::info("Found transaction reference from MPS row: {$transactionRef}");
                    }
                }
            }

            // If still not found, check all rows for any alphanumeric code that's not all digits
            if (!$transactionRef) {
                foreach ($transactionRows as $row) {
                    $particularsToCheck = $row['particulars'] ?? '';

                    // Look for alphanumeric codes 5-20 chars that are not all digits
                    if (preg_match_all('/\b([A-Z0-9]{5,20})\b/', $particularsToCheck, $matches)) {
                        foreach ($matches[1] as $code) {
                            // Skip if it's all digits (likely an MPS number or meter number)
                            if (!preg_match('/^\d+$/', $code)) {
                                $transactionRef = $code;
                                Log::info("Found transaction reference from row {$row['row']}: {$transactionRef}");
                                break 2;
                            }
                        }
                    }
                }
            }

            // Last resort: generate a unique reference
            if (!$transactionRef) {
                $datePart = Carbon::parse($date)->format('Ymd');
                $randomPart = strtoupper(substr(uniqid(), -6));
                $transactionRef = "IMP-{$datePart}-{$meterNumber}-{$randomPart}";
                Log::info("Generated transaction reference: {$transactionRef}");
            }

            // Check for duplicate transaction reference
            if ($transactionRef && !str_starts_with($transactionRef, 'IMP-')) {
                $existingPayment = Payment::where('transaction_reference', $transactionRef)
                    ->where('payment_method', 'mpesa')
                    ->where('payment_status', 'completed')
                    ->first();

                if ($existingPayment) {
                    // This payment has already been imported - skip it
                    $results['skipped']++;
                    Log::info("Payment skipped - Transaction reference {$transactionRef} already exists for meter {$meterNumber}");
                    return; // Exit the function, don't process this payment
                }
            }

            // ========== PROCESS PAYMENT ==========
            $paymentResult = DB::transaction(function () use ($meter, $amount, $transactionRef, $paymentDate, $meterNumber) {
                return $this->paymentService->processPayment(
                    $meter,
                    $amount,
                    'mpesa',
                    $transactionRef,
                    $paymentDate,
                    auth()->id()
                );
            });

            // Check if bills were actually paid
            $totalApplied = $paymentResult['total_applied'] ?? 0;
            $remainingCredit = $paymentResult['remaining_credit'] ?? 0;
            $appliedBills = $paymentResult['applied_bills'] ?? [];

            if ($totalApplied > 0) {
                Log::info("Payment allocated to bills for meter {$meterNumber}", [
                    'total_applied' => $totalApplied,
                    'bills_count' => count($appliedBills),
                    'remaining_credit' => $remainingCredit
                ]);
            }

            $results['success']++;
            Log::info("Payment processed successfully for meter {$meterNumber}, amount {$amount}, applied: {$totalApplied}, credit: {$remainingCredit}");

            $results['success']++;
            Log::info("Payment processed successfully for meter {$meterNumber}, amount {$amount}");

        } catch (\Exception $e) {
            $results['failed']++;
            $results['errors'][] = [
                'meter_number' => $meterNumber,
                'amount' => $amount ?? 'unknown',
                'date' => $date,
                'reason' => $e->getMessage()
            ];
            Log::error("Payment failed for meter {$meterNumber}: " . $e->getMessage());
        }
    }

    private function extractTransactionRef(string $text): ?string
    {
        // Extract transaction reference from MPS entries
        // Pattern like: MPS 254712838480 TKUD6BJ0IK 48133#13764
        if (preg_match('/MPS\s+\d+\s+([A-Z0-9]{8,15})\b/', $text, $matches)) {
            return $matches[1]; // Returns TKUD6BJ0IK
        }

        // Alternative: look for any 8-15 char alphanumeric code
        if (preg_match('/\b([A-Z0-9]{8,15})\b/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meter_no' => 'required|exists:meters,meter_number',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                        $fail('Amount must have at most 2 decimal places.');
                    }
                }
            ],
            'payment_method' => 'required|in:mpesa,bank,cash,card,mobile_money',
            'transaction_reference' => [
                'required_if:payment_method,mpesa,bank,card,mobile_money',
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && Payment::where('transaction_reference', $value)
                        ->where('payment_method', $request->payment_method)
                        ->where('payment_status', 'completed')
                        ->exists()) {
                        $fail('This transaction reference has already been used.');
                    }
                }
            ],
            'payment_date' => 'nullable|date|before_or_equal:today',
        ]);

        try {
            // Fetch meter with lock to prevent race conditions
            $meter = Meter::where('meter_number', $validated['meter_no'])
                ->lockForUpdate()
                ->with(['customer' => function ($query) {
                    $query->lockForUpdate();
                }])
                ->firstOrFail();

            // Validate customer is active
            if ($meter->customer->status !== 'active') {
                throw ValidationException::withMessages([
                    'meter_no' => 'Customer account is ' . $meter->customer->status . '. Payments cannot be accepted.'
                ]);
            }

            // Process payment within transaction
            $paymentDate = isset($validated['payment_date'])
                ? Carbon::parse($validated['payment_date'])
                : now();

            $result = DB::transaction(function () use ($validated, $meter, $paymentDate) {
                return $this->paymentService->processPayment(
                    $meter,
                    $validated['amount'],
                    $validated['payment_method'],
                    $validated['transaction_reference'] ?? null,
                    $paymentDate, // ✅ always Carbon
                    auth()->id()
                );
            });


            // Log successful payment
            Log::info('Payment processed successfully', [
                'payment_id' => $result['payment']->id,
                'meter_id' => $meter->id,
                'customer_id' => $meter->customer_id,
                'amount' => $validated['amount'],
                'applied_to_bills' => $result['applied_bills'],
                'remaining_credit' => $result['remaining_credit'],
                'processed_by' => auth()->id()
            ]);

            return redirect()->route('payments.index')
                ->with('success', 'Payment of KSh ' . number_format($validated['amount'], 2) . ' processed successfully.')
                ->with('payment_receipt', [
                    'payment_no' => $result['payment']->payment_no,
                    'amount' => $validated['amount'],
                    'balance' => $meter->fresh()->current_balance,
                    'date' => now()->format('Y-m-d H:i:s')
                ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'meter_no' => $validated['meter_no'],
                'amount' => $validated['amount'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Display payment details with comprehensive information
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'meter.customer',
            'bills' => function ($query) {
                $query->withTrashed(); // Include deleted bills if any
            },
            'user'
        ]);

        // Get payment allocation details
        $allocationDetails = $payment->bills->map(function ($bill) use ($payment) {
            $allocation = $payment->billAllocations()
                ->where('bill_id', $bill->id)
                ->first();

            return [
                'bill' => $bill,
                'allocated_amount' => $allocation->amount ?? 0,
                'allocation_date' => $allocation->created_at ?? null
            ];
        });

        return view('payments.show', compact('payment', 'allocationDetails'));
    }

    /**
     * Void/Reverse a payment (requires authorization)
     */
    public function voidPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'refund_method' => 'nullable|in:credit_note,cash_refund,bank_refund'
        ]);

        if ($payment->payment_status === 'voided') {
            return back()->with('error', 'Payment is already voided.');
        }

        try {
            $result = DB::transaction(function () use ($payment, $request) {
                return $this->paymentService->voidPayment(
                    $payment,
                    $request->reason,
                    $request->refund_method,
                    auth()->id()
                );
            });

            Log::warning('Payment voided', [
                'payment_id' => $payment->id,
                'voided_by' => auth()->id(),
                'reason' => $request->reason,
                'refund_method' => $request->refund_method,
                'reversal_details' => $result
            ]);

            return redirect()->route('payments.show', $payment)
                ->with('warning', 'Payment has been voided and reversed.')
                ->with('reversal_details', $result);

        } catch (\Exception $e) {
            Log::error('Payment void failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to void payment: ' . $e->getMessage());
        }
    }

    /**
     * Get meter details with comprehensive financial info
     */
    public function getMeterDetails($meterNumber)
    {
        try {
            $meterNumber = trim($meterNumber);

            // Use a read-only transaction for consistency
            $meter = DB::transaction(function () use ($meterNumber) {
                return Meter::with([
                    'customer' => function ($query) {
                        $query->select(['id', 'customer_number', 'first_name', 'last_name',
                                      'email', 'phone', 'status']);
                    },
                    'meterCategory',
                    'bills' => function ($query) {
                        $query->where(function ($q) {
                            $q->where('bill_status', '!=', 'paid')
                              ->orWhereColumn('total_amount', '>', 'paid_amount');
                        })
                        ->where('due_date', '>=', now()->subMonths(6)) // Last 6 months only
                        ->orderBy('due_date', 'asc')
                        ->select(['id', 'bill_number', 'total_amount', 'paid_amount',
                                 'due_date', 'billing_period_start', 'bill_status']);
                    }
                ])
                ->where('meter_number', $meterNumber)
                ->where('status', 'assigned')
                ->first();
            }, 5); // 5 attempts for optimistic locking

            if (!$meter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assigned meter not found'
                ], 404);
            }

            // Calculate financial summary
            $unpaidBills = $meter->bills->map(function ($bill) {
                $dueAmount = $bill->total_amount - $bill->paid_amount;
                $isOverdue = $bill->due_date < now();

                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'total_amount' => number_format($bill->total_amount, 2),
                    'paid_amount' => number_format($bill->paid_amount, 2),
                    'due_amount' => number_format($dueAmount, 2),
                    'raw_due' => $dueAmount,
                    'due_date' => $bill->due_date ? $bill->due_date->format('Y-m-d') : null,
                    'is_overdue' => $isOverdue,
                    'billing_period' => $bill->billing_period_start
                        ? $bill->billing_period_start->format('M Y')
                        : 'N/A',
                    'bill_status' => $bill->bill_status
                ];
            });

            $totalDue = $unpaidBills->sum('raw_due');
            $overdueAmount = $unpaidBills
                ->where('is_overdue', true)
                ->sum('raw_due');

            // Calculate credit balance (negative means credit)
            $creditBalance = max(0, -$meter->current_balance);

            $response = [
                'success' => true,
                'meter' => [
                    'id' => $meter->id,
                    'meter_number' => $meter->meter_number,
                    'model' => $meter->meter_model ?? 'Standard',
                    'type' => $meter->meter_type ?? 'Domestic',
                    'location' => $meter->installation_address ?? 'Not specified',
                    'status' => $meter->status,
                    'category' => $meter->meterCategory->name ?? 'General',
                    'current_balance' => number_format($meter->current_balance, 2),
                    'raw_balance' => $meter->current_balance
                ],
                'customer' => [
                    'id' => $meter->customer->id,
                    'name' => $meter->customer->first_name . ' ' . ($meter->customer->last_name ?? ''),
                    'customer_number' => $meter->customer->customer_number,
                    'email' => $meter->customer->email ?? 'N/A',
                    'phone' => $meter->customer->phone ?? 'N/A',
                    'status' => $meter->customer->status
                ],
                'financial_summary' => [
                    'total_due' => number_format($totalDue, 2),
                    'raw_total_due' => $totalDue,
                    'overdue_amount' => number_format($overdueAmount, 2),
                    'credit_balance' => number_format($creditBalance, 2),
                    'unpaid_bill_count' => $unpaidBills->count(),
                    'overdue_bill_count' => $unpaidBills->where('is_overdue', true)->count()
                ],
                'unpaid_bills' => $unpaidBills->values(),
                'can_accept_payment' => $meter->customer->status === 'active'
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error fetching meter details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching meter details. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate payment receipt
     */
    public function generateReceipt(Payment $payment)
    {
        $payment->load(['meter.customer', 'user', 'bills']);

        $receiptData = [
            'receipt_number' => 'RCPT-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'payment' => $payment,
            'generated_at' => now(),
            'generated_by' => auth()->user()->name
        ];

        // Return PDF receipt (implement PDF generation as needed)
        return view('payments.receipt', $receiptData);
    }

    /**
 * Test endpoint to list all meters
 */
public function listAllMeters()
{
    $meters = Meter::all(['id', 'meter_number', 'customer_id', 'status']);
    return response()->json($meters);
}

}
