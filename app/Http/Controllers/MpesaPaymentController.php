<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use App\Models\Payment;
use App\Models\Vote;
use App\Models\VotingContest;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MpesaPaymentController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate STK Push for voting
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:12',
            'votes' => 'required|integer|min:1|max:100',
            'nominee_id' => 'required|exists:nominees,id',
            'contest_id' => 'required|exists:voting_contests,id',
        ]);

        DB::beginTransaction();

        try {
            $contest = VotingContest::findOrFail($request->contest_id);
            $nominee = Nominee::findOrFail($request->nominee_id);

            // Calculate total amount
            $amount = $contest->price_per_vote * $request->votes;

            if ($amount < 1) {
                throw new \Exception('Amount must be at least KES 1');
            }

            // Create unique reference
            $orderTrackingId = 'VOTE-' . Str::upper(Str::random(12));
            $merchantRef = 'VOTE-' . time() . '-' . Str::random(6);

            // Create pending payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'voting_contest_id' => $contest->id,
                'nominee_id' => $nominee->id,
                'order_tracking_id' => $orderTrackingId,
                'merchant_reference' => $merchantRef,
                'amount' => $amount,
                'currency' => 'KES',
                'status' => 'PENDING',
                'payment_method' => 'MPESA',
                'phone_number' => $request->phone,
                'votes_count' => $request->votes,
                'raw_response' => null,
            ]);

            // Initiate STK Push using the SDK
            $stkResponse = $this->mpesaService->initiateSTKPush(
                $request->phone,
                $amount,
                $orderTrackingId,
                "Vote for {$nominee->name}"
            );

            if (!$stkResponse['success']) {
                throw new \Exception($stkResponse['error'] ?? 'Failed to initiate M-PESA payment');
            }

            // Update payment with MPESA response
            $payment->update([
                'merchant_reference' => $stkResponse['merchant_request_id'] ?? $merchantRef,
                'raw_response' => json_encode($stkResponse)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $stkResponse['customer_message'] ?? 'STK Push initiated successfully',
                'checkout_request_id' => $stkResponse['checkout_request_id'] ?? null,
                'payment_id' => $payment->id,
                'data' => [
                    'amount' => $amount,
                    'votes' => $request->votes,
                    'nominee' => $nominee->name,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('MPESA Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle M-PESA STK Callback - COMPREHENSIVE UPDATE
     */
    public function handleCallback(Request $request)
    {
        Log::channel('mpesa')->info('MPESA Callback Received', $request->all());

        // Store raw callback for audit
        DB::table('mpesa_callbacks')->insert([
            'payload' => json_encode($request->all()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $callbackData = $request->all();
        $stkCallback = $callbackData['Body']['stkCallback'] ?? null;

        if (!$stkCallback) {
            Log::channel('mpesa')->error('Invalid STK callback format', $callbackData);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback format']);
        }

        // Process the callback immediately
        $result = $this->processCallback($stkCallback);

        if ($result['success']) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } else {
            Log::channel('mpesa')->error('Callback processing failed', $result);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Processing failed']);
        }
    }

    /**
     * Process callback from M-PESA and update all necessary tables
     */
    private function processCallback(array $stkCallback): array
    {
        DB::beginTransaction();

        try {
            $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
            $resultCode = $stkCallback['ResultCode'] ?? 1;
            $resultDesc = $stkCallback['ResultDesc'] ?? 'Unknown error';

            Log::channel('mpesa')->info('Processing callback', [
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc
            ]);

            // Find payment by merchant request ID or checkout request ID
            $payment = $this->findPayment($merchantRequestId, $checkoutRequestId);

            if (!$payment) {
                Log::channel('mpesa')->warning('Payment not found for callback', [
                    'merchant_request_id' => $merchantRequestId,
                    'checkout_request_id' => $checkoutRequestId
                ]);
                DB::rollBack();
                return ['success' => false, 'error' => 'Payment not found'];
            }

            // Idempotency check - don't process if already completed
            if ($payment->status === 'SUCCESSFUL') {
                Log::channel('mpesa')->info('Payment already processed', [
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);
                DB::rollBack();
                return ['success' => true, 'message' => 'Payment already processed'];
            }

            if ($resultCode == 0) {
                // SUCCESSFUL PAYMENT
                return $this->processSuccessfulPayment($payment, $stkCallback);
            } else {
                // FAILED PAYMENT
                return $this->processFailedPayment($payment, $resultCode, $resultDesc, $stkCallback);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('mpesa')->error('Callback processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Find payment by merchant request ID or checkout request ID
     */
    private function findPayment(?string $merchantRequestId, ?string $checkoutRequestId): ?Payment
    {
        // Try by merchant request ID first
        if ($merchantRequestId) {
            $payment = Payment::where('merchant_reference', $merchantRequestId)->first();
            if ($payment) return $payment;
        }

        // Try by checkout request ID in raw_response
        if ($checkoutRequestId) {
            $payment = Payment::where('raw_response', 'like', "%{$checkoutRequestId}%")->first();
            if ($payment) return $payment;
        }

        // Fallback: try to find by callback metadata (phone + amount)
        return $this->findPaymentByCallbackMetadata();
    }

    /**
     * Find payment by callback metadata (fallback method)
     */
    private function findPaymentByCallbackMetadata(): ?Payment
    {
        // This would require parsing the callback metadata
        // For now, return null and rely on merchant_request_id or checkout_request_id
        return null;
    }

    /**
     * Process successful payment and update all tables
     */
    private function processSuccessfulPayment(Payment $payment, array $stkCallback): array
    {
        try {
            // Extract transaction details from callback metadata
            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $metadata = $this->extractCallbackMetadata($callbackMetadata);

            Log::channel('mpesa')->info('Processing successful payment', [
                'payment_id' => $payment->id,
                'metadata' => $metadata
            ]);

            // Update payment record
            $payment->update([
                'status' => 'SUCCESSFUL',
                'merchant_reference' => $metadata['mpesa_receipt'] ?? $payment->merchant_reference,
                'raw_response' => json_encode([
                    'initial_response' => json_decode($payment->raw_response, true),
                    'callback_data' => $stkCallback,
                    'transaction_metadata' => $metadata,
                    'processed_at' => now()->toISOString()
                ])
            ]);

            // Create vote records
            $votesCreated = $this->createVotes($payment);

            // Update contest total votes
            $this->updateContestVotes($payment);

            // Update nominee votes count
            $this->updateNomineeVotes($payment);

            // Update user voting statistics
            $this->updateUserVotingStats($payment);

            DB::commit();

            Log::channel('mpesa')->info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'votes_created' => $votesCreated,
                'mpesa_receipt' => $metadata['mpesa_receipt'] ?? null,
                'amount' => $payment->amount
            ]);

            return ['success' => true, 'message' => 'Payment processed successfully', 'votes_created' => $votesCreated];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('mpesa')->error('Failed to process successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment(Payment $payment, int $resultCode, string $resultDesc, array $stkCallback): array
    {
        try {
            $payment->update([
                'status' => 'FAILED',
                'raw_response' => json_encode(array_merge(
                    json_decode($payment->raw_response, true) ?? [],
                    [
                        'callback_data' => $stkCallback,
                        'failure_reason' => $resultDesc,
                        'result_code' => $resultCode,
                        'failed_at' => now()->toISOString()
                    ]
                ))
            ]);

            DB::commit();

            Log::channel('mpesa')->warning('Payment marked as failed', [
                'payment_id' => $payment->id,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc
            ]);

            return ['success' => true, 'message' => 'Payment marked as failed'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('mpesa')->error('Failed to mark payment as failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Extract metadata from callback items
     */
    private function extractCallbackMetadata(array $callbackItems): array
    {
        $metadata = [];

        foreach ($callbackItems as $item) {
            $name = $item['Name'] ?? null;
            $value = $item['Value'] ?? null;

            if ($name) {
                switch ($name) {
                    case 'Amount':
                        $metadata['amount'] = $value;
                        break;
                    case 'MpesaReceiptNumber':
                        $metadata['mpesa_receipt'] = $value;
                        break;
                    case 'PhoneNumber':
                        $metadata['phone_number'] = $value;
                        break;
                    case 'TransactionDate':
                        $metadata['transaction_date'] = $value;
                        break;
                    default:
                        $metadata[$name] = $value;
                }
            }
        }

        return $metadata;
    }

    /**
     * Create vote records for the payment
     */
    private function createVotes(Payment $payment): int
    {
        $votesCreated = 0;

        for ($i = 0; $i < $payment->votes_count; $i++) {
            Vote::create([
                'user_id' => $payment->user_id,
                'voting_contest_id' => $payment->voting_contest_id,
                'nominee_id' => $payment->nominee_id,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'voted_at' => now(),
            ]);
            $votesCreated++;
        }

        return $votesCreated;
    }

    /**
     * Update contest total votes
     */
    private function updateContestVotes(Payment $payment): void
    {
        VotingContest::where('id', $payment->voting_contest_id)
                    ->increment('total_votes', $payment->votes_count);
    }

    /**
     * Update nominee votes count
     */
    private function updateNomineeVotes(Payment $payment): void
    {
        Nominee::where('id', $payment->nominee_id)
              ->increment('votes_count', $payment->votes_count);
    }

    /**
     * Update user voting statistics
     */
    private function updateUserVotingStats(Payment $payment): void
    {
        if ($payment->user_id) {
            User::where('id', $payment->user_id)->update([
                'total_votes_cast' => DB::raw('total_votes_cast + ' . $payment->votes_count),
                'total_amount_spent' => DB::raw('total_amount_spent + ' . $payment->amount),
                'last_vote_at' => now(),
            ]);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id'
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        if ($payment->status === 'SUCCESSFUL') {
            return response()->json([
                'status' => 'success',
                'message' => 'Payment completed successfully! Your votes have been cast.',
                'votes_count' => $payment->votes_count,
                'nominee_id' => $payment->nominee_id,
                'contest_id' => $payment->voting_contest_id
            ]);
        }

        if ($payment->status === 'FAILED') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed. Please try again.',
                'payment_id' => $payment->id
            ]);
        }

        // If still pending, try to query MPESA status
        if ($payment->raw_response) {
            $responseData = json_decode($payment->raw_response, true);
            $checkoutRequestId = $responseData['checkout_request_id'] ?? null;

            if ($checkoutRequestId) {
                $statusResponse = $this->mpesaService->querySTKStatus($checkoutRequestId);

                if ($statusResponse && isset($statusResponse['ResultCode'])) {
                    if ($statusResponse['ResultCode'] == '0') {
                        // Payment is complete, process it via callback simulation
                        $this->simulateCallbackForStatusCheck($payment, $statusResponse);
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Payment completed successfully! Your votes have been cast.'
                        ]);
                    } elseif ($statusResponse['ResultCode'] != '1032') { // 1032 = request in progress
                        $payment->update(['status' => 'FAILED']);
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Payment failed or was cancelled.'
                        ]);
                    }
                }
            }
        }

        // Still pending
        return response()->json([
            'status' => 'pending',
            'message' => 'Waiting for payment confirmation. Please check your phone.',
            'payment_id' => $payment->id
        ]);
    }

    /**
     * Simulate callback for status check (when query shows success but no callback received)
     */
    private function simulateCallbackForStatusCheck(Payment $payment, array $statusResponse): void
    {
        $simulatedCallback = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $payment->merchant_reference,
                    'CheckoutRequestID' => $statusResponse['CheckoutRequestID'] ?? null,
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => $payment->amount],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => $statusResponse['MpesaReceiptNumber'] ?? 'STATUS-CHECK'],
                            ['Name' => 'TransactionDate', 'Value' => now()->format('YmdHis')],
                            ['Name' => 'PhoneNumber', 'Value' => $payment->phone_number],
                        ]
                    ]
                ]
            ]
        ];

        // Process the simulated callback
        $this->processCallback($simulatedCallback['Body']['stkCallback']);
    }

    /**
     * Normalize phone number to 254 format
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            return '254' . $phone;
        }

        return $phone;
    }
}
