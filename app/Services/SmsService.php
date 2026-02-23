<?php
// app/Services/SmsService.php

namespace App\Services;

use App\Models\Customer;
use App\Models\Meter;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $baseUrl;
    protected $userId;

    public function __construct()
    {
        $this->apiKey   = config('services.hostpinnacle.api_key');
        $this->senderId = config('services.hostpinnacle.sender_id');
        $this->baseUrl  = config('services.hostpinnacle.base_url');
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
     * Send SMS to a single recipient
     */
    public function send($phone, $message, $type = 'manual', $customerId = null, $meterId = null, $metadata = [])
    {
        try {
            // Validate phone number
            $phone = $this->formatPhoneNumber($phone);

            if (!$phone) {
                throw new \Exception('Invalid phone number format');
            }

            // Log the attempt
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
            $response = Http::post($this->baseUrl, [
                'apikey'   => $this->apiKey,
                'senderid' => $this->senderId,
                'phone'    => $phone,
                'message'  => $message,
            ]);

            $responseData = $response->json();

            // Update log based on response
            if ($response->successful() && isset($responseData['success']) && $responseData['success']) {
                $smsLog->update([
                    'status' => 'sent',
                    'api_response_code' => $responseData['code'] ?? '200',
                    'api_response_message' => $responseData['message'] ?? 'Success',
                    'api_response' => $responseData,
                    'sent_at' => now(),
                    'cost' => $responseData['cost'] ?? null,
                ]);

                Log::info('SMS sent successfully', [
                    'sms_log_id' => $smsLog->id,
                    'phone' => $phone,
                    'type' => $type
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $responseData,
                    'log_id' => $smsLog->id
                ];
            } else {
                $errorMessage = $responseData['message'] ?? 'Unknown error';

                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'api_response' => $responseData,
                    'api_response_code' => $responseData['code'] ?? '500',
                ]);

                Log::error('SMS sending failed', [
                    'sms_log_id' => $smsLog->id,
                    'phone' => $phone,
                    'error' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $errorMessage,
                    'log_id' => $smsLog->id
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS Error: ' . $e->getMessage(), [
                'phone' => $phone,
                'type' => $type
            ]);

            // Log the error if we have a log ID
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
     * Send bulk SMS to multiple recipients
     */
    public function sendBulk($recipients, $message, $type = 'bulk', $metadata = [])
    {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipient) {
            $phone = $recipient['phone'] ?? null;
            $customerId = $recipient['customer_id'] ?? null;
            $meterId = $recipient['meter_id'] ?? null;

            if (!$phone) {
                $failCount++;
                continue;
            }

            $result = $this->send($phone, $message, $type, $customerId, $meterId, $metadata);

            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            $results[] = $result;

            // Add a small delay to avoid rate limiting
            if (count($recipients) > 10) {
                usleep(500000); // 0.5 seconds
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => "Bulk SMS sent: {$successCount} successful, {$failCount} failed",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'total' => count($recipients),
            'results' => $results
        ];
    }

    /**
     * Send SMS using a template
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
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if it's a Kenyan number
        if (strlen($phone) == 9 && substr($phone, 0, 1) == '7') {
            return '254' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            return '254' . substr($phone, 1);
        } elseif (strlen($phone) == 12 && substr($phone, 0, 3) == '254') {
            return $phone;
        } elseif (strlen($phone) == 13 && substr($phone, 0, 4) == '2540') {
            return '254' . substr($phone, 4);
        }

        // If format doesn't match, return false
        return false;
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

        return [
            'total' => $query->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'by_type' => (clone $query)->groupBy('message_type')
                ->select('message_type', DB::raw('count(*) as count'))
                ->pluck('count', 'message_type')
                ->toArray(),
            'total_cost' => (clone $query)->where('status', 'sent')->sum('cost')
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
}
