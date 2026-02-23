<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display SMS dashboard
     */
    public function index(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $dateFilter = $request->get('date_filter', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = SmsLog::with(['customer', 'meter', 'sender'])
            ->latest();

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('recipient_phone', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%")
                  ->orWhere('api_response_message', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('customer_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by message type
        if ($type !== 'all') {
            $query->where('message_type', $type);
        }

        // Filter by date range
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($dateFilter === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateFilter === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateFilter === 'last_month') {
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', Carbon::now()->subMonth()->year);
        }

        $smsLogs = $query->paginate(30);

        // Get statistics
        $stats = $this->smsService->getStats();

        // Get message types for filter
        $messageTypes = SmsLog::distinct('message_type')
            ->pluck('message_type')
            ->toArray();

        return view('admin.sms.index', compact(
            'smsLogs',
            'stats',
            'status',
            'type',
            'dateFilter',
            'startDate',
            'endDate',
            'messageTypes'
        ));
    }

    /**
     * Show form to send SMS
     */
    public function create(Request $request)
    {
        $customerId = $request->get('customer');
        $meterId = $request->get('meter');

        $customer = null;
        $meter = null;

        if ($customerId) {
            $customer = Customer::with('meters')->find($customerId);
        }

        if ($meterId) {
            $meter = Meter::with('customer')->find($meterId);
            if ($meter && $meter->customer) {
                $customer = $meter->customer;
            }
        }

        // Get SMS templates
        $templates = SmsTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get recent customers with phone numbers for quick selection
        $recentCustomers = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.sms.create', compact('customer', 'meter', 'templates', 'recentCustomers'));
    }

    /**
     * Send single SMS
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:manual,customer,meter',
            'phone' => 'required_if:recipient_type,manual|nullable|string',
            'customer_id' => 'required_if:recipient_type,customer|nullable|exists:customers,id',
            'meter_id' => 'required_if:recipient_type,meter|nullable|exists:meters,id',
            'message' => 'required|string|max:1600',
            'template_id' => 'nullable|exists:sms_templates,id',
            'send_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->smsService->byUser(auth()->id());

            $result = null;

            switch ($request->recipient_type) {
                case 'manual':
                    $result = $this->smsService->send(
                        $request->phone,
                        $request->message,
                        'manual'
                    );
                    break;

                case 'customer':
                    $customer = Customer::findOrFail($request->customer_id);
                    $result = $this->smsService->sendToCustomer(
                        $customer,
                        $request->message,
                        'manual'
                    );
                    break;

                case 'meter':
                    $meter = Meter::with('customer')->findOrFail($request->meter_id);
                    $result = $this->smsService->sendToMeterCustomer(
                        $meter,
                        $request->message,
                        'manual'
                    );
                    break;
            }

            if ($result['success']) {
                return redirect()->route('admin.sms.index')
                    ->with('success', 'SMS sent successfully!');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to send SMS: ' . $result['message'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error sending SMS: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show bulk SMS form
     */
    public function bulkCreate(Request $request)
    {
        $zones = \App\Models\Zone::orderBy('name')->get();
        $templates = SmsTemplate::where('is_active', true)->orderBy('name')->get();

        return view('admin.sms.bulk', compact('zones', 'templates'));
    }

    /**
     * Send bulk SMS
     */
    public function bulkSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_filter' => 'required|in:all_customers,zone,custom_list,unpaid_bills,overdue_bills',
            'zone_id' => 'required_if:recipient_filter,zone|nullable|exists:zones,id',
            'custom_phones' => 'required_if:recipient_filter,custom_list|nullable|string',
            'message' => 'required|string|max:1600',
            'template_id' => 'nullable|exists:sms_templates,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Build recipient list based on filter
            $recipients = [];

            switch ($request->recipient_filter) {
                case 'all_customers':
                    $customers = Customer::whereNotNull('phone')
                        ->where('phone', '!=', '')
                        ->where('status', 'active')
                        ->get();

                    foreach ($customers as $customer) {
                        $recipients[] = [
                            'phone' => $customer->phone,
                            'customer_id' => $customer->id,
                            'meter_id' => null
                        ];
                    }
                    break;

                case 'zone':
                    $customers = Customer::whereNotNull('phone')
                        ->where('phone', '!=', '')
                        ->whereHas('meters', function($q) use ($request) {
                            $q->where('zone_id', $request->zone_id);
                        })
                        ->get();

                    foreach ($customers as $customer) {
                        $recipients[] = [
                            'phone' => $customer->phone,
                            'customer_id' => $customer->id,
                            'meter_id' => null
                        ];
                    }
                    break;

                case 'custom_list':
                    $phones = explode("\n", trim($request->custom_phones));
                    foreach ($phones as $phone) {
                        $phone = trim($phone);
                        if (!empty($phone)) {
                            $recipients[] = [
                                'phone' => $phone,
                                'customer_id' => null,
                                'meter_id' => null
                            ];
                        }
                    }
                    break;

                case 'unpaid_bills':
                    $bills = \App\Models\Bill::where('bill_status', 'unpaid')
                        ->where('balance', '>', 0)
                        ->with('customer')
                        ->get();

                    foreach ($bills as $bill) {
                        if ($bill->customer && $bill->customer->phone) {
                            $recipients[] = [
                                'phone' => $bill->customer->phone,
                                'customer_id' => $bill->customer_id,
                                'meter_id' => $bill->meter_id
                            ];
                        }
                    }
                    break;

                case 'overdue_bills':
                    $bills = \App\Models\Bill::where('bill_status', 'unpaid')
                        ->where('balance', '>', 0)
                        ->where('due_date', '<', now())
                        ->with('customer')
                        ->get();

                    foreach ($bills as $bill) {
                        if ($bill->customer && $bill->customer->phone) {
                            $recipients[] = [
                                'phone' => $bill->customer->phone,
                                'customer_id' => $bill->customer_id,
                                'meter_id' => $bill->meter_id
                            ];
                        }
                    }
                    break;
            }

            if (empty($recipients)) {
                return redirect()->back()
                    ->with('error', 'No recipients found matching the selected criteria.')
                    ->withInput();
            }

            // Confirm with user if more than 50 recipients
            if (count($recipients) > 50 && !$request->has('confirmed')) {
                return redirect()->back()
                    ->with('warning', 'You are about to send SMS to ' . count($recipients) . ' recipients. Please confirm to proceed.')
                    ->with('recipients_count', count($recipients))
                    ->withInput();
            }

            $this->smsService->byUser(auth()->id());

            // Send bulk SMS
            $result = $this->smsService->sendBulk(
                $recipients,
                $request->message,
                'bulk',
                ['filter' => $request->recipient_filter]
            );

            if ($result['success_count'] > 0) {
                $message = "Bulk SMS sent: {$result['success_count']} successful, {$result['fail_count']} failed.";

                if ($result['fail_count'] > 0) {
                    return redirect()->route('admin.sms.index')
                        ->with('warning', $message);
                } else {
                    return redirect()->route('admin.sms.index')
                        ->with('success', $message);
                }
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to send any SMS. Please check the logs.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Bulk SMS error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error sending bulk SMS: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show SMS templates
     */
    public function templates(Request $request)
    {
        $templates = SmsTemplate::with('creator')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.sms.templates.index', compact('templates'));
    }

    /**
     * Show create template form
     */
    public function createTemplate()
    {
        $categories = [
            'general' => 'General',
            'bill' => 'Bill Notifications',
            'payment' => 'Payment Notifications',
            'reminder' => 'Reminders',
            'reading' => 'Meter Reading',
            'promotion' => 'Promotional',
            'alert' => 'Alerts'
        ];

        return view('admin.sms.templates.create', compact('categories'));
    }

    /**
     * Store SMS template
     */
    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sms_templates',
            'slug' => 'required|string|max:255|unique:sms_templates',
            'description' => 'nullable|string',
            'message' => 'required|string',
            'category' => 'required|string',
            'placeholders' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Extract placeholders from message if not provided
            if (!$request->has('placeholders')) {
                preg_match_all('/\{\{([^}]+)\}\}/', $request->message, $matches);
                $placeholders = array_unique($matches[1]);
                $request->merge(['placeholders' => $placeholders]);
            }

            SmsTemplate::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'message' => $request->message,
                'placeholders' => $request->placeholders,
                'category' => $request->category,
                'is_active' => $request->has('is_active'),
                'created_by' => auth()->id()
            ]);

            return redirect()->route('admin.sms.templates')
                ->with('success', 'SMS template created successfully!');

        } catch (\Exception $e) {
            Log::error('Template creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit template form
     */
    public function editTemplate(SmsTemplate $template)
    {
        $categories = [
            'general' => 'General',
            'bill' => 'Bill Notifications',
            'payment' => 'Payment Notifications',
            'reminder' => 'Reminders',
            'reading' => 'Meter Reading',
            'promotion' => 'Promotional',
            'alert' => 'Alerts'
        ];

        return view('admin.sms.templates.edit', compact('template', 'categories'));
    }

    /**
     * Update SMS template
     */
    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sms_templates,name,' . $template->id,
            'slug' => 'required|string|max:255|unique:sms_templates,slug,' . $template->id,
            'description' => 'nullable|string',
            'message' => 'required|string',
            'category' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Extract placeholders from message
            preg_match_all('/\{\{([^}]+)\}\}/', $request->message, $matches);
            $placeholders = array_unique($matches[1]);

            $template->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'message' => $request->message,
                'placeholders' => $placeholders,
                'category' => $request->category,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.sms.templates')
                ->with('success', 'SMS template updated successfully!');

        } catch (\Exception $e) {
            Log::error('Template update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete SMS template
     */
    public function destroyTemplate(SmsTemplate $template)
    {
        try {
            $template->delete();

            return redirect()->route('admin.sms.templates')
                ->with('success', 'SMS template deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Template deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting template: ' . $e->getMessage());
        }
    }

    /**
     * View SMS details
     */
    public function show(SmsLog $smsLog)
    {
        $smsLog->load(['customer', 'meter', 'sender']);

        return view('admin.sms.show', compact('smsLog'));
    }

    /**
     * Retry failed SMS
     */
    public function retry(SmsLog $smsLog)
    {
        if ($smsLog->status !== 'failed') {
            return redirect()->back()
                ->with('error', 'Only failed SMS can be retried.');
        }

        try {
            $this->smsService->byUser(auth()->id());
            $result = $this->smsService->retry($smsLog->id);

            if ($result['success']) {
                return redirect()->route('admin.sms.show', $smsLog->id)
                    ->with('success', 'SMS retry initiated successfully!');
            } else {
                return redirect()->route('admin.sms.show', $smsLog->id)
                    ->with('error', 'Failed to retry SMS: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('SMS retry error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error retrying SMS: ' . $e->getMessage());
        }
    }

    /**
     * Export SMS logs to Excel
     */
    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $dateFilter = $request->get('date_filter', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = SmsLog::with(['customer', 'sender']);

        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('message_type', $type);
        }

        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SMS Logs');

        // Headers
        $row = 1;
        $headers = [
            'ID',
            'Date/Time',
            'Recipient Phone',
            'Customer Name',
            'Message',
            'Type',
            'Status',
            'API Response',
            'Cost (KSh)',
            'Sent By',
            'Sent At',
            'Delivered At'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }

        $row++;

        // Data
        foreach ($logs as $log) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $log->id);
            $sheet->setCellValue($col++ . $row, $log->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue($col++ . $row, $log->recipient_phone);
            $sheet->setCellValue($col++ . $row, $log->customer ? $log->customer->full_name : 'N/A');
            $sheet->setCellValue($col++ . $row, $log->message);
            $sheet->setCellValue($col++ . $row, $log->message_type);
            $sheet->setCellValue($col++ . $row, ucfirst($log->status));
            $sheet->setCellValue($col++ . $row, $log->api_response_message ?? 'N/A');
            $sheet->setCellValue($col++ . $row, $log->cost ?? 0);
            $sheet->setCellValue($col++ . $row, $log->sender ? $log->sender->name : 'System');
            $sheet->setCellValue($col++ . $row, $log->sent_at ? $log->sent_at->format('d/m/Y H:i:s') : 'N/A');
            $sheet->setCellValue($col++ . $row, $log->delivered_at ? $log->delivered_at->format('d/m/Y H:i:s') : 'N/A');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'NYAWASCO_SMS_Logs_' . now()->format('Y_m_d_His') . '.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Get customer phone for AJAX
     */
    public function getCustomerPhone(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        if ($customer && $customer->phone) {
            return response()->json([
                'success' => true,
                'phone' => $customer->phone,
                'name' => $customer->full_name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer has no phone number'
        ]);
    }

    /**
     * Get meter customer for AJAX
     */
    public function getMeterCustomer(Request $request)
    {
        $meter = Meter::with('customer')->find($request->meter_id);

        if ($meter && $meter->customer) {
            return response()->json([
                'success' => true,
                'customer_id' => $meter->customer->id,
                'customer_name' => $meter->customer->full_name,
                'phone' => $meter->customer->phone
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Meter has no assigned customer'
        ]);
    }

    /**
     * Preview SMS template
     */
    public function previewTemplate(Request $request)
    {
        $template = SmsTemplate::find($request->template_id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found'
            ]);
        }

        $sampleData = [];

        // Generate sample data based on placeholders
        if ($template->placeholders) {
            foreach ($template->placeholders as $placeholder) {
                $sampleData[$placeholder] = '[' . $placeholder . ']';
            }
        }

        $preview = $template->parseMessage($sampleData);

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'placeholders' => $template->placeholders
        ]);
    }
}
