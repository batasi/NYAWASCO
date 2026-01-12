<?php

namespace App\Http\Controllers;


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
        $query = Payment::with(['customer', 'user', 'meter']);

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

        // Sorting
        $query->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc');

        $payments = $query->paginate(10)->withQueryString();

        // Calculate statistics for cards
        $totalPayments = Payment::sum('amount');
        $todayCollection = Payment::whereDate('payment_date', Carbon::today())
            ->where('payment_status', 'completed')
            ->sum('amount');
        $totalPaymentsCount = Payment::count();
        $completedPaymentsCount = Payment::where('payment_status', 'completed')->count();
        $pendingPaymentsCount = Payment::where('payment_status', 'pending')->count();
        $failedPaymentsCount = Payment::where('payment_status', 'failed')->count();

        return view('payments.index', compact(
            'payments',
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

        $payments = Payment::with(['customer', 'meter'])
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
        $sheet = $spreadsheet->getActiveSheet();

        // Get the highest row and column
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Get all cells as an array
        $rows = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, null, true, true, true);

        // First row = headers
        $headers = array_map('trim', $rows[1]);

        // Map header names to column letters
        $headerMap = array_flip($headers);

        Log::info('Headers found:', $headers);

        // 🔒 Ensure required columns exist
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
            throw new \Exception('No date column found. Available columns: ' . implode(', ', array_keys($headerMap)));
        }

        foreach ($requiredColumns as $requiredColumn) {
            if (!isset($headerMap[$requiredColumn])) {
                throw new \Exception("Missing column: {$requiredColumn}. Available: " . implode(', ', array_keys($headerMap)));
            }
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'skipped' => 0,
            'not_found_meters' => [], // Changed from not_found_customers
            'empty_amounts' => 0
        ];

        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber === 1) continue;

            // Skip empty rows or rows with empty particulars
            if (empty($row[$headerMap['Particulars']])) {
                $results['skipped']++;
                continue;
            }

            try {
                // ========== AMOUNT CHECK FIRST ==========
                // Get amount cell FIRST to check if it's empty
                $amountColumnLetter = $headerMap['Credit Amt.'];
                $amountCell = $sheet->getCell($amountColumnLetter . $rowNumber);
                $excelAmountValue = $amountCell->getValue();

                // Check if amount is empty or null
                $isAmountEmpty = false;
                if ($excelAmountValue === null || $excelAmountValue === '' || $excelAmountValue === ' ') {
                    $isAmountEmpty = true;
                } elseif (is_string($excelAmountValue) && trim($excelAmountValue) === '') {
                    $isAmountEmpty = true;
                }

                // Also check the array value
                $arrayAmountValue = $rows[$rowNumber][$headerMap['Credit Amt.']];
                if ($isAmountEmpty && ($arrayAmountValue === null || $arrayAmountValue === '' || trim($arrayAmountValue) === '')) {
                    $isAmountEmpty = true;
                } else {
                    $isAmountEmpty = false;
                }

                // If amount is empty, skip this row - it's not a payment!
                if ($isAmountEmpty) {
                    $results['empty_amounts']++;
                    Log::info("Row {$rowNumber} - Skipping: Empty amount field");
                    continue;
                }

                // ========== DATE PARSING ==========
                $dateColumnLetter = $headerMap[$foundDateColumn];
                $dateCell = $sheet->getCell($dateColumnLetter . $rowNumber);
                $excelDateValue = $dateCell->getValue();

                // Handle date
                if (is_numeric($excelDateValue)) {
                    // Excel serial date - convert to DateTime
                    $utcDays = $excelDateValue - 25569;
                    $paymentDate = Carbon::createFromTimestamp($utcDays * 86400);
                } else {
                    try {
                        $paymentDate = Carbon::parse($excelDateValue);
                    } catch (\Exception $e) {
                        $formattedValue = $dateCell->getFormattedValue();
                        if (preg_match('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $formattedValue, $matches)) {
                            $paymentDate = Carbon::create($matches[1], $matches[2], $matches[3]);
                        } else {
                            throw new \Exception("Could not parse date: {$formattedValue}");
                        }
                    }
                }

                // ========== AMOUNT PARSING ==========
                $amount = null;

                // Try multiple parsing methods
                if (is_numeric($excelAmountValue)) {
                    $amount = (float) $excelAmountValue;
                } elseif (is_string($excelAmountValue)) {
                    $amount = (float) str_replace(',', '', $excelAmountValue);
                } else {
                    $formatted = $amountCell->getFormattedValue();
                    if (preg_match('/[\d,]+\.?\d*/', $formatted, $matches)) {
                        $amount = (float) str_replace(',', '', $matches[0]);
                    }
                }

                // If still null, try array value
                if ($amount === null && is_numeric($arrayAmountValue)) {
                    $amount = (float) $arrayAmountValue;
                }

                // Validate amount
                if ($amount === null || $amount <= 0) {
                    throw new \Exception("Invalid amount. Raw: " . print_r($excelAmountValue, true) .
                                        ", Formatted: " . $amountCell->getFormattedValue());
                }

                // ========== PARTICULAR PARSING ==========
                $particulars = trim($rows[$rowNumber][$headerMap['Particulars']]);

                // Extract METER number (not customer number)
                $meterNumber = null;

                // Pattern 1: # followed by numbers (most common: #13764)
                if (preg_match('/#(\d{3,})/', $particulars, $matches)) {
                    $meterNumber = $matches[1];
                }
                // Pattern 2: Look for standalone 3+ digit numbers
                elseif (preg_match('/\b(\d{3,})\b/', $particulars, $matches)) {
                    $meterNumber = $matches[1];
                }

                if (!$meterNumber) {
                    // Check if this is a non-payment entry (like charges, withdrawals)
                    $nonPaymentKeywords = [
                        'Cash Withdrawal Charge',
                        'Inward Clearing Charge',
                        'Inter Sol Cash Wdrawal charge',
                        'Cash Withdrawal Charge',
                        'JARED ATEMBA', // These might be withdrawals by staff
                        'ChequeNo',
                        'Chq:'
                    ];

                    $isNonPayment = false;
                    foreach ($nonPaymentKeywords as $keyword) {
                        if (stripos($particulars, $keyword) !== false) {
                            $isNonPayment = true;
                            break;
                        }
                    }

                    if ($isNonPayment) {
                        $results['skipped']++;
                        Log::info("Row {$rowNumber} - Skipping non-payment entry: " . substr($particulars, 0, 50));
                        continue;
                    } else {
                        throw new \Exception('Meter number not found in particulars');
                    }
                }

                Log::info("Row {$rowNumber} - Extracted meter number: {$meterNumber}, Amount: {$amount}");

                // ========== FIND METER AND CUSTOMER ==========
                // First, find the meter by meter_number
                $meter = Meter::where('meter_number', $meterNumber)->first();

                if (!$meter) {
                    $results['not_found_meters'][] = [
                        'row' => $rowNumber,
                        'meter_number' => $meterNumber,
                        'amount' => $amount,
                        'date' => $paymentDate->format('Y-m-d'),
                        'particulars' => substr($particulars, 0, 200)
                    ];
                    throw new \Exception("Meter {$meterNumber} not found - skipped");
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

                // ========== PAYMENT PROCESSING ==========
                // Extract transaction reference
                $transactionRef = $this->extractTransactionRef($particulars);

                // Check for duplicate transaction reference
                if ($transactionRef) {
                    $existingPayment = Payment::where('transaction_reference', $transactionRef)
                        ->where('payment_method', 'mpesa')
                        ->where('payment_status', 'completed')
                        ->first();

                    if ($existingPayment) {
                        throw new \Exception('Transaction reference has already been used');
                    }
                }

                // Process payment within transaction
                DB::transaction(function () use ($meter, $amount, $transactionRef, $paymentDate, $meterNumber, $rowNumber) {
                    $this->paymentService->processPayment(
                        $meter,
                        $amount,
                        'mpesa',
                        $transactionRef,
                        $paymentDate,
                        auth()->id()
                    );

                    Log::info("Row {$rowNumber} - Payment processed for meter {$meterNumber}");
                });

                $results['success']++;

            } catch (\Exception $e) {
                // Check error type
                $errorMsg = $e->getMessage();

                if (strpos($errorMsg, 'Meter') !== false &&
                    strpos($errorMsg, 'not found') !== false &&
                    strpos($errorMsg, 'skipped') !== false) {
                    $results['skipped']++;
                    Log::info("Row {$rowNumber} skipped: " . $errorMsg);
                } elseif (strpos($errorMsg, 'Invalid amount') !== false) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'reason' => $errorMsg
                    ];
                    Log::error("Row {$rowNumber} amount error: " . $errorMsg);
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'reason' => $errorMsg
                    ];
                    Log::error("Row {$rowNumber} failed: " . $errorMsg);
                }
            }
        }

        // Create summary
        $summary = [
            'total_rows' => count($rows) - 1,
            'successful' => $results['success'],
            'failed' => $results['failed'],
            'skipped' => $results['skipped'],
            'empty_amounts' => $results['empty_amounts'],
            'not_found_meters' => count($results['not_found_meters'])
        ];

        Log::info('Import completed', $summary);

        return back()->with([
            'import_result' => $results,
            'import_summary' => $summary
        ]);
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
            $result = DB::transaction(function () use ($validated, $meter) {
                return $this->paymentService->processPayment(
                    $meter,
                    $validated['amount'],
                    $validated['payment_method'],
                    $validated['transaction_reference'] ?? null,
                    $validated['payment_date'] ?? now(),
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
