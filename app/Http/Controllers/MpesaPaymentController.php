<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\VerifyMpesaTransaction;
use App\Models\Payment;
use App\Models\VotingContest;
use App\Models\Vote;
use App\Models\Nominee;
use Illuminate\Support\Facades\Auth;

class MpesaPaymentController extends Controller
{
    protected $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    /**
     * Initiate STK push for voting
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'votes' => 'required|integer|min:1',
            'nominee_id' => 'required|exists:nominees,id',
            'contest_id' => 'required|exists:voting_contests,id',
        ]);

        DB::beginTransaction();

        try {
            $contest = VotingContest::findOrFail($request->contest_id);
            $nominee = Nominee::findOrFail($request->nominee_id);

            // Calculate amount based on votes and price per vote
            $amount = $contest->price_per_vote * $request->votes;

            if ($amount < 1) {
                throw new \Exception('Amount must be at least KES 1');
            }

            // Create unique tracking IDs
            $orderTrackingId = 'VOTE-' . Str::upper(Str::random(10));
            $merchantRef = 'VOTE-' . Str::upper(Str::random(8));

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

            // Initiate STK Push
            $stkResponse = $this->mpesa->stkPush(
                $this->normalizePhone($request->phone),
                $amount,
                "Vote-{$nominee->code}",
                "Vote for {$nominee->name}"
            );

            if (!$stkResponse) {
                throw new \Exception('Failed to initiate MPESA payment');
            }

            // Update payment with MPESA response
            $payment->update([
                'merchant_reference' => $stkResponse['MerchantRequestID'] ?? $merchantRef,
                'raw_response' => json_encode($stkResponse)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $stkResponse['CustomerMessage'] ?? 'Check your phone for STK Push',
                'checkout_request_id' => $stkResponse['CheckoutRequestID'] ?? null,
                'payment_id' => $payment->id,
                'data' => $stkResponse
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('MPESA payment initiation failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle MPESA STK Callback
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
        $stkCallback = data_get($callbackData, 'Body.stkCallback');

        if (!$stkCallback) {
            Log::channel('mpesa')->error('Invalid STK callback format', $callbackData);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback format']);
        }

        $merchantRequestId = data_get($stkCallback, 'MerchantRequestID');
        $checkoutRequestId = data_get($stkCallback, 'CheckoutRequestID');
        $resultCode = data_get($stkCallback, 'ResultCode');
        $resultDesc = data_get($stkCallback, 'ResultDesc');

        // Dispatch verification job
        VerifyMpesaTransaction::dispatch([
            'merchantRequestId' => $merchantRequestId,
            'checkoutRequestId' => $checkoutRequestId,
            'resultCode' => $resultCode,
            'resultDesc' => $resultDesc,
            'callback' => $callbackData,
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
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
                'votes_count' => $payment->votes_count
            ]);
        }

        if ($payment->status === 'FAILED') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed. Please try again.'
            ]);
        }

        // If still pending, try to query MPESA status
        if ($payment->raw_response) {
            $responseData = json_decode($payment->raw_response, true);
            $checkoutRequestId = $responseData['CheckoutRequestID'] ?? null;

            if ($checkoutRequestId) {
                $statusResponse = $this->mpesa->queryStkStatus($checkoutRequestId);

                if ($statusResponse && isset($statusResponse['ResultCode'])) {
                    if ($statusResponse['ResultCode'] == '0') {
                        // Payment is complete, process it
                        $this->processSuccessfulPayment($payment, $statusResponse);
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Payment completed successfully!'
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

        return response()->json([
            'status' => 'pending',
            'message' => 'Waiting for payment confirmation...'
        ]);
    }

    /**
     * Process successful payment and create votes
     */
    private function processSuccessfulPayment(Payment $payment, array $responseData)
    {
        DB::transaction(function () use ($payment, $responseData) {
            // Update payment status
            $payment->update([
                'status' => 'SUCCESSFUL',
                'raw_response' => json_encode(array_merge(
                    json_decode($payment->raw_response, true) ?? [],
                    ['status_check' => $responseData]
                ))
            ]);

            // Create individual vote records
            for ($i = 0; $i < $payment->votes_count; $i++) {
                Vote::create([
                    'user_id' => $payment->user_id,
                    'voting_contest_id' => $payment->voting_contest_id,
                    'nominee_id' => $payment->nominee_id,
                    'ip_address' => request()->ip(),
                    'voted_at' => now(),
                ]);
            }

            // Update contest total votes
            VotingContest::where('id', $payment->voting_contest_id)
                        ->increment('total_votes', $payment->votes_count);

            // Update nominee votes count
            Nominee::where('id', $payment->nominee_id)
                  ->increment('votes_count', $payment->votes_count);

            Log::channel('mpesa')->info('Votes created successfully', [
                'payment_id' => $payment->id,
                'votes_count' => $payment->votes_count,
                'nominee_id' => $payment->nominee_id,
                'contest_id' => $payment->voting_contest_id
            ]);
        });
    }

    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (preg_match('/^0/', $phone)) {
            return '254' . substr($phone, 1);
        }

        if (preg_match('/^7/', $phone)) {
            return '254' . $phone;
        }

        return $phone;
    }
}
