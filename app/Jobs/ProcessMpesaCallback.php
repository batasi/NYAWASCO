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
use App\Models\Vote;
use App\Models\VotingContest;
use App\Models\Nominee;

class ProcessMpesaCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $callbackData;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(array $callbackData)
    {
        $this->callbackData = $callbackData;
    }

    public function handle()
    {
        Log::channel('mpesa')->info('Processing MPESA callback job', $this->callbackData);

        $stkCallback = $this->callbackData['Body']['stkCallback'] ?? null;
        if (!$stkCallback) {
            Log::channel('mpesa')->error('Invalid callback data in job');
            return;
        }

        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? 1;
        $resultDesc = $stkCallback['ResultDesc'] ?? 'Unknown error';

        // Find payment by merchant request ID
        $payment = Payment::where('merchant_reference', $merchantRequestId)
                         ->orWhere('raw_response', 'like', "%{$merchantRequestId}%")
                         ->first();

        if (!$payment) {
            Log::channel('mpesa')->warning('Payment not found for callback', [
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId
            ]);
            return;
        }

        // Idempotency check
        if ($payment->status === 'SUCCESSFUL') {
            Log::channel('mpesa')->info('Payment already processed', ['payment_id' => $payment->id]);
            return;
        }

        if ($resultCode == 0) {
            $this->processSuccessfulPayment($payment, $stkCallback);
        } else {
            $this->processFailedPayment($payment, $resultCode, $resultDesc);
        }
    }

    private function processSuccessfulPayment(Payment $payment, array $stkCallback)
    {
        DB::transaction(function () use ($payment, $stkCallback) {
            // Extract transaction details from callback metadata
            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $metadata = [];

            foreach ($callbackMetadata as $item) {
                $name = $item['Name'] ?? null;
                $value = $item['Value'] ?? null;
                if ($name) {
                    $metadata[$name] = $value;
                }
            }

            // Update payment
            $payment->update([
                'status' => 'SUCCESSFUL',
                'raw_response' => json_encode([
                    'initial_response' => json_decode($payment->raw_response, true),
                    'callback_data' => $this->callbackData,
                    'transaction_metadata' => $metadata
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

            Log::channel('mpesa')->info('Payment processed successfully via callback', [
                'payment_id' => $payment->id,
                'votes_created' => $payment->votes_count,
                'mpesa_receipt' => $metadata['MpesaReceiptNumber'] ?? null
            ]);
        });
    }

    private function processFailedPayment(Payment $payment, $resultCode, $resultDesc)
    {
        $payment->update([
            'status' => 'FAILED',
            'raw_response' => json_encode(array_merge(
                json_decode($payment->raw_response, true) ?? [],
                [
                    'callback_data' => $this->callbackData,
                    'failure_reason' => $resultDesc,
                    'result_code' => $resultCode
                ]
            ))
        ]);

        Log::channel('mpesa')->warning('Payment failed via callback', [
            'payment_id' => $payment->id,
            'result_code' => $resultCode,
            'result_desc' => $resultDesc
        ]);
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('mpesa')->error('ProcessMpesaCallback job failed', [
            'error' => $exception->getMessage(),
            'callback_data' => $this->callbackData
        ]);
    }
}
