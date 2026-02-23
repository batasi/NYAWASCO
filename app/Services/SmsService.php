<?php
// app/Services/SmsService.php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SmsService
{
    protected $username;
    protected $password;
    protected $apiKey;
    protected $senderId;
    protected $baseUrl;
    protected $userId;

    public function __construct()
    {
        $this->username = config('services.hostpinnacle.username');
        $this->password = config('services.hostpinnacle.password');
        $this->apiKey = config('services.hostpinnacle.api_key');
        $this->senderId = config('services.hostpinnacle.sender_id');
        $this->baseUrl = config('services.hostpinnacle.base_url', 'https://smsportal.hostpinnacle.co.ke');
    }

    /**
     * Set the user sending the SMS for logging
     */
    public function byUser($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get or create API key
     */
    public function getApiKey()
    {
        // If API key is provided in config, use it
        if ($this->apiKey) {
            return $this->apiKey;
        }

        // Check cache first
        if (Cache::has('hostpinnacle_api_key')) {
            return Cache::get('hostpinnacle_api_key');
        }

        try {
            // Create API key using username and password
            $response = Http::asForm()->post($this->baseUrl . '/SMSApi/apikey/create', [
                'userid' => $this->username,
                'password' => $this->password,
                'output' => 'json'
            ]);

            $data = $response->json();

            Log::info('HostPinnacle API Key Response', ['response' => $data]);

            // Check if API key creation was successful
            if ($response->successful() &&
                isset($data['response']['status']) &&
                $data['response']['status'] === 'success' &&
                isset($data['response']['apikey'])) {

                $apiKey = $data['response']['apikey'];

                // Cache the API key (typically valid for a long time)
                Cache::put('hostpinnacle_api_key', $apiKey, now()->addDays(30));

                return $apiKey;
            }

            throw new \Exception('Failed to create API key: ' . ($data['response']['msg'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Failed to get HostPinnacle API key', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Send SMS to a single recipient
     */
    public function send($phone, $message, $type = 'manual', $customerId = null, $meterId = null, $metadata = [])
    {
        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($phone);

            if (!$phone) {
                throw new \Exception('Invalid phone number format');
            }

            // Create SMS log
            $smsLog = SmsLog::create([
                'recipient_phone' => $phone,
                'customer_id' => $customerId,
                'meter_id' => $meterId,
                'sender_id' => $this->senderId,
                'message' => $message,
                'message_type' => $type,
                'status' => 'pending',
                'sent_by' => $this->userId,
                'metadata' => $metadata
            ]);

            // Send the SMS
            $result = $this->sendToHostPinnacle($phone, $message, $smsLog);

            if ($result['success']) {
                $smsLog->update([
                    'status' => 'sent',
                    'api_response_code' => $result['status_code'] ?? '200',
                    'api_response_message' => $result['reason'] ?? 'Success',
                    'api_response' => $result['full_response'],
                    'sent_at' => now(),
                    'cost' => $result['cost'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $result,
                    'log_id' => $smsLog->id
                ];
            } else {
                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $result['reason'] ?? 'Unknown error',
                    'api_response' => $result['full_response'],
                    'api_response_code' => $result['status_code'] ?? '500',
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . ($result['reason'] ?? 'Unknown error'),
                    'log_id' => $smsLog->id
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS Exception: ' . $e->getMessage(), [
                'phone' => $phone,
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($smsLog)) {
                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'message' => 'SMS Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to a customer
     */
    public function sendToCustomer($customer, $message, $type = 'manual', $metadata = [])
    {
        if (!$customer->phone) {
            return [
                'success' => false,
                'message' => 'Customer has no phone number'
            ];
        }

        return $this->send(
            $customer->phone,
            $message,
            $type,
            $customer->id,
            null,
            array_merge($metadata, ['customer_name' => $customer->first_name . ' ' . $customer->last_name])
        );
    }

    /**
     * Send SMS to a customer via their meter
     */
    public function sendToMeterCustomer($meter, $message, $type = 'manual', $metadata = [])
    {
        if (!$meter->customer) {
            return [
                'success' => false,
                'message' => 'Meter has no assigned customer'
            ];
        }

        if (!$meter->customer->phone) {
            return [
                'success' => false,
                'message' => 'Customer has no phone number'
            ];
        }

        return $this->send(
            $meter->customer->phone,
            $message,
            $type,
            $meter->customer->id,
            $meter->id,
            array_merge($metadata, [
                'customer_name' => $meter->customer->first_name . ' ' . $meter->customer->last_name,
                'meter_number' => $meter->meter_number
            ])
        );
    }

    /**
     * Send SMS to multiple recipients
     */
    public function sendBulk($recipients, $message, $type = 'bulk', $metadata = [])
    {
        if (empty($recipients)) {
            return [
                'success' => false,
                'message' => 'No recipients provided'
            ];
        }

        // Extract phone numbers
        $phones = [];
        $recipientMap = [];

        foreach ($recipients as $recipient) {
            $phone = $this->formatPhoneNumber($recipient['phone'] ?? $recipient);
            if ($phone) {
                $phones[] = $phone;
                $recipientMap[$phone] = $recipient;
            }
        }

        if (empty($phones)) {
            return [
                'success' => false,
                'message' => 'No valid phone numbers'
            ];
        }

        try {
            // Create a bulk log entry
            $bulkLog = SmsLog::create([
                'recipient_phone' => implode(',', array_slice($phones, 0, 5)) . (count($phones) > 5 ? '...' : ''),
                'message' => $message,
                'message_type' => 'bulk',
                'status' => 'pending',
                'sent_by' => $this->userId,
                'metadata' => array_merge($metadata, ['total_recipients' => count($phones)])
            ]);

            // Get API key
            $apiKey = $this->getApiKey();

            // Join multiple mobiles with comma
            $mobileString = implode(',', $phones);

            $requestData = [
                'sendMethod' => 'quick',
                'mobile' => $mobileString,
                'msg' => $message,
                'senderid' => $this->senderId,
                'msgType' => 'text',
                'duplicatecheck' => 'true',
                'output' => 'json'
            ];

            Log::info('Sending bulk SMS', [
                'recipient_count' => count($phones),
                'first_phone' => $phones[0] ?? null
            ]);

            // Make the request
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'cache-control' => 'no-cache',
                'content-type' => 'application/x-www-form-urlencoded'
            ])->asForm()->post($this->baseUrl . '/SMSApi/send', $requestData);

            $responseData = $response->json();

            // Check if successful
            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
                $bulkLog->update([
                    'status' => 'sent',
                    'api_response_code' => $responseData['statusCode'] ?? '200',
                    'api_response_message' => $responseData['reason'] ?? 'Success',
                    'api_response' => $responseData,
                    'sent_at' => now(),
                ]);

                // Create individual logs for each recipient
                foreach ($phones as $phone) {
                    SmsLog::create([
                        'recipient_phone' => $phone,
                        'customer_id' => $recipientMap[$phone]['customer_id'] ?? null,
                        'meter_id' => $recipientMap[$phone]['meter_id'] ?? null,
                        'sender_id' => $this->senderId,
                        'message' => $message,
                        'message_type' => $type,
                        'status' => 'sent',
                        'sent_by' => $this->userId,
                        'sent_at' => now(),
                        'metadata' => array_merge($metadata, ['bulk_id' => $bulkLog->id])
                    ]);
                }

                return [
                    'success' => true,
                    'message' => "Bulk SMS sent to " . count($phones) . " recipients",
                    'data' => $responseData,
                    'log_id' => $bulkLog->id,
                    'recipient_count' => count($phones),
                    'invalid' => $responseData['invalidMobile'] ?? null,
                    'transaction_id' => $responseData['transactionId'] ?? null,
                    'success_count' => count($phones),
                    'fail_count' => 0
                ];
            } else {
                $errorMsg = $responseData['reason'] ?? 'Unknown error';

                $bulkLog->update([
                    'status' => 'failed',
                    'error_message' => $errorMsg,
                    'api_response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send bulk SMS: ' . $errorMsg,
                    'log_id' => $bulkLog->id,
                    'success_count' => 0,
                    'fail_count' => count($phones)
                ];
            }

        } catch (\Exception $e) {
            Log::error('Bulk SMS Exception: ' . $e->getMessage());

            if (isset($bulkLog)) {
                $bulkLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'message' => 'Bulk SMS Error: ' . $e->getMessage(),
                'success_count' => 0,
                'fail_count' => count($phones)
            ];
        }
    }

    /**
     * Send to HostPinnacle API (single SMS)
     */
    protected function sendToHostPinnacle($phone, $message, $smsLog = null)
    {
        $apiKey = $this->getApiKey();

        $requestData = [
            'sendMethod' => 'quick',
            'mobile' => $phone,
            'msg' => $message,
            'senderid' => $this->senderId,
            'msgType' => 'text',
            'duplicatecheck' => 'true',
            'output' => 'json'
        ];

        // Make the request with API key in header
        $response = Http::withHeaders([
            'apikey' => $apiKey,
            'cache-control' => 'no-cache',
            'content-type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post($this->baseUrl . '/SMSApi/send', $requestData);

        $responseData = $response->json();

        // Check if successful based on their response format
        if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
            return [
                'success' => true,
                'status' => $responseData['status'],
                'mobile' => $responseData['mobile'] ?? $phone,
                'invalid_mobile' => $responseData['invalidMobile'] ?? null,
                'transaction_id' => $responseData['transactionId'] ?? null,
                'status_code' => $responseData['statusCode'] ?? '200',
                'reason' => $responseData['reason'] ?? 'Success',
                'full_response' => $responseData
            ];
        } else {
            return [
                'success' => false,
                'status' => $responseData['status'] ?? 'error',
                'status_code' => $responseData['statusCode'] ?? '500',
                'reason' => $responseData['reason'] ?? 'Unknown error',
                'full_response' => $responseData
            ];
        }
    }

    /**
     * Send SMS using template
     */
    public function sendUsingTemplate($phone, $templateSlug, $data = [], $type = 'template', $customerId = null, $meterId = null)
    {
        $template = SmsTemplate::getBySlug($templateSlug);

        if (!$template) {
            return [
                'success' => false,
                'message' => 'SMS template not found'
            ];
        }

        $message = $template->parseMessage($data);

        return $this->send($phone, $message, $type, $customerId, $meterId, [
            'template' => $templateSlug,
            'template_data' => $data
        ]);
    }

    /**
     * Send bill reminder to a customer
     */
    public function sendBillReminder($customer, $bill)
    {
        $data = [
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'bill_number' => $bill->bill_number,
            'bill_amount' => number_format($bill->total_amount, 2),
            'due_date' => $bill->due_date->format('d/m/Y'),
            'balance' => number_format($bill->balance, 2),
            'meter_number' => $bill->meter->meter_number ?? 'N/A',
            'billing_period' => $bill->billing_period_start->format('M Y')
        ];

        return $this->sendUsingTemplate(
            $customer->phone,
            'bill_reminder',
            $data,
            'bill_reminder',
            $customer->id,
            $bill->meter_id
        );
    }

    /**
     * Send payment receipt
     */
    public function sendPaymentReceipt($customer, $payment, $bill)
    {
        $data = [
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'payment_amount' => number_format($payment->amount, 2),
            'payment_date' => $payment->payment_date->format('d/m/Y H:i'),
            'receipt_number' => $payment->receipt_number ?? $payment->payment_no,
            'bill_number' => $bill->bill_number,
            'meter_number' => $bill->meter->meter_number ?? 'N/A',
            'balance' => number_format($bill->balance, 2)
        ];

        return $this->sendUsingTemplate(
            $customer->phone,
            'payment_receipt',
            $data,
            'payment_receipt',
            $customer->id,
            $bill->meter_id
        );
    }

    /**
     * Send meter reading confirmation
     */
    public function sendReadingConfirmation($customer, $meter, $reading)
    {
        $data = [
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'meter_number' => $meter->meter_number,
            'reading_date' => $reading->reading_date->format('d/m/Y'),
            'current_reading' => number_format($reading->current_reading, 2),
            'consumption' => number_format($reading->consumption, 2),
            'reading_period' => $reading->reading_period
        ];

        return $this->sendUsingTemplate(
            $customer->phone,
            'reading_confirmation',
            $data,
            'reading_confirmation',
            $customer->id,
            $meter->id
        );
    }

    /**
     * Get SMS statistics
     */
    public function getStats($startDate = null, $endDate = null)
    {
        $query = SmsLog::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();
        $sent = (clone $query)->where('status', 'sent')->count();
        $failed = (clone $query)->where('status', 'failed')->count();
        $pending = (clone $query)->where('status', 'pending')->count();

        // Get counts by message type
        $byType = (clone $query)
            ->select('message_type', DB::raw('count(*) as count'))
            ->groupBy('message_type')
            ->pluck('count', 'message_type')
            ->toArray();

        // Get total cost
        $totalCost = (clone $query)->where('status', 'sent')->sum('cost');

        // Get today's count
        $today = (clone $query)->whereDate('created_at', now()->toDateString())->count();

        // Get this month's count
        $thisMonth = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'by_type' => $byType,
            'total_cost' => $totalCost,
            'today' => $today,
            'this_month' => $thisMonth,
        ];
    }

    /**
     * Retry failed SMS
     */
    public function retry($smsLogId)
    {
        $smsLog = SmsLog::findOrFail($smsLogId);

        if ($smsLog->status !== 'failed') {
            return [
                'success' => false,
                'message' => 'Only failed SMS can be retried'
            ];
        }

        $smsLog->increment('retry_count');

        return $this->send(
            $smsLog->recipient_phone,
            $smsLog->message,
            $smsLog->message_type . '_retry',
            $smsLog->customer_id,
            $smsLog->meter_id,
            $smsLog->metadata
        );
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Kenyan number formatting
        if (strlen($phone) == 9 && substr($phone, 0, 1) == '7') {
            return '254' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            return '254' . substr($phone, 1);
        } elseif (strlen($phone) == 12 && substr($phone, 0, 3) == '254') {
            return $phone;
        } elseif (strlen($phone) == 13 && substr($phone, 0, 4) == '2540') {
            return '254' . substr($phone, 4);
        }

        return $phone;
    }

    /**
     * Check account balance (if they have this endpoint)
     */
    public function checkBalance()
    {
        try {
            $apiKey = $this->getApiKey();

            // You'll need to confirm if they have a balance endpoint
            // This is a guess - adjust based on their actual API
            $response = Http::withHeaders([
                'apikey' => $apiKey
            ])->get($this->baseUrl . '/SMSApi/balance');

            return [
                'success' => $response->successful(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Test the API connection
     */
    public function testConnection()
    {
        $results = [
            'credentials' => [
                'username' => $this->username ? '✓ Set' : '✗ Not set',
                'password' => $this->password ? '✓ Set' : '✗ Not set',
                'api_key' => $this->apiKey ? '✓ Set' : '✗ Not set (will be generated)',
                'sender_id' => $this->senderId ? '✓ Set' : '✗ Not set',
                'base_url' => $this->baseUrl,
            ],
            'api_key_test' => null,
            'send_endpoint_test' => null
        ];

        // Test API key creation/generation
        try {
            $apiKey = $this->getApiKey();
            $results['api_key_test'] = [
                'success' => true,
                'message' => '✓ Successfully obtained API key: ' . substr($apiKey, 0, 10) . '...'
            ];
        } catch (\Exception $e) {
            $results['api_key_test'] = [
                'success' => false,
                'message' => '✗ Failed to get API key: ' . $e->getMessage()
            ];
        }

        // Test send endpoint with a test message (without actually sending)
        try {
            $apiKey = $this->getApiKey();

            $response = Http::withHeaders([
                'apikey' => $apiKey,
            ])->asForm()->post($this->baseUrl . '/SMSApi/send', [
                'sendMethod' => 'quick',
                'mobile' => '254712345678', // Test number
                'msg' => 'Test message',
                'senderid' => $this->senderId,
                'msgType' => 'text',
                'testMessage' => 'true', // This should prevent actual sending
                'output' => 'json'
            ]);

            $results['send_endpoint_test'] = [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            $results['send_endpoint_test'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }
}
