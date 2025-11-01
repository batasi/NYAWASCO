<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\VotingContest;
use App\Models\Vote;
use App\Models\Nominee;

class VerifyMpesaTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        Log::channel('mpesa')->info('Processing MPESA callback job', $this->payload);

        $resultCode = data_get($this->payload, 'resultCode');
        $merchantRequestId = data_get($this->payload, 'merchantRequestId');
        $checkoutRequestId = data_get($this->payload, 'checkoutRequestId');
        $callbackData = data_get($this->payload, 'callback', []);

        // Find payment by merchant request ID or checkout request ID
        $payment = $this->findPayment($merchantRequestId, $checkoutRequestId, $callbackData);

        if (!$payment) {
            Log::channel('mpesa')->warning('No payment found for callback', $this->payload);
            return;
        }

        // Idempotency check
        if ($payment->status === 'SUCCESSFUL') {
            Log::channel('mpesa')->info('Payment already processed', ['payment_id' => $payment->id]);
            return;
        }

        if ($resultCode == 0) {
            $this->processSuccessfulPayment($payment, $callbackData);
        } else {
            $this->processFailedPayment($payment, $resultCode);
        }
    }

    private function findPayment($merchantRequestId, $checkoutRequestId, $callbackData)
    {
        // Try by merchant request ID
        if ($merchantRequestId) {
            $payment = Payment::where('merchant_reference', $merchantRequestId)
                            ->orWhere('merchant_reference', 'like', "%{$merchantRequestId}%")
                            ->first();
            if ($payment) return $payment;
        }

        // Try by checkout request ID in raw_response
        if ($checkoutRequestId) {
            $payment = Payment::where('raw_response', 'like', "%{$checkoutRequestId}%")
                            ->first();
            if ($payment) return $payment;
        }

        // Fallback: try by phone and amount from callback metadata
        $metadata = data_get($callbackData, 'Body.stkCallback.CallbackMetadata.Item', []);
        $phone = null;
        $amount = null;

        foreach ($metadata as $item) {
            $name = data_get($item, 'Name');
            $value = data_get($item, 'Value');

            if ($name === 'PhoneNumber') $phone = $value;
            if ($name === 'Amount') $amount = $value;
        }

        if ($phone && $amount) {
            $normalizedPhone = $this->normalizePhone($phone);
            return Payment::where('phone_number', $normalizedPhone)
                         ->where('amount', $amount)
                         ->where('status', 'PENDING')
                         ->latest()
                         ->first();
        }

        return null;
    }

    private function processSuccessfulPayment(Payment $payment, array $callbackData)
    {
        DB::transaction(function () use ($payment, $callbackData) {
            // Extract transaction details
            $metadata = data_get($callbackData, 'Body.stkCallback.CallbackMetadata.Item', []);
            $transactionData = [];

            foreach ($metadata as $item) {
                $name = data_get($item, 'Name');
                $value = data_get($item, 'Value');
                $transactionData[$name] = $value;
            }

            // Update payment
            $payment->update([
                'status' => 'SUCCESSFUL',
                'raw_response' => json_encode([
                    'initial_response' => json_decode($payment->raw_response, true),
                    'callback_data' => $callbackData,
                    'transaction_data' => $transactionData
                ])
            ]);

            // Create votes
            for ($i = 0; $i < $payment->votes_count; $i++) {
                Vote::create([
                    'user_id' => $payment->user_id,
                    'voting_contest_id' => $payment->voting_contest_id,
                    'nominee_id' => $payment->nominee_id,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'voted_at' => now(),
                ]);
            }

            // Update counters
            VotingContest::where('id', $payment->voting_contest_id)
                        ->increment('total_votes', $payment->votes_count);

            Nominee::where('id', $payment->nominee_id)
                  ->increment('votes_count', $payment->votes_count);

            Log::channel('mpesa')->info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'votes_created' => $payment->votes_count
            ]);
        });
    }

    private function processFailedPayment(Payment $payment, $resultCode)
    {
        $payment->update([
            'status' => 'FAILED',
            'raw_response' => json_encode(array_merge(
                json_decode($payment->raw_response, true) ?? [],
                ['failure_reason' => $resultCode]
            ))
        ]);

        Log::channel('mpesa')->warning('Payment failed', [
            'payment_id' => $payment->id,
            'result_code' => $resultCode
        ]);
    }

    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (preg_match('/^0/', $phone)) return '254' . substr($phone, 1);
        if (preg_match('/^7/', $phone)) return '254' . $phone;
        return $phone;
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('mpesa')->error('VerifyMpesaTransaction job failed', [
            'error' => $exception->getMessage(),
            'payload' => $this->payload
        ]);
    }
}
