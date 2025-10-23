<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalController extends Controller
{
    /**
     * Generate access token from Pesapal.
     */
    private function getAccessToken()
    {
        $url = env('PESAPAL_BASE_URL') . '/api/Auth/RequestToken';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($url, [
            'consumer_key' => env('PESAPAL_CONSUMER_KEY'),
            'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
        ]);

        if ($response->successful() && isset($response['token'])) {
            return $response['token'];
        }

        Log::error('Pesapal Auth Error', ['response' => $response->body()]);
        return null;
    }

    /**
     * Register IPN (only needs to be done once).
     */
    public function registerIpn()
    {
        $token = $this->getAccessToken();
        if (!$token) return response()->json(['error' => 'Failed to get access token']);

        $url = env('PESAPAL_BASE_URL') . '/api/URLSetup/RegisterIPN';

        $response = Http::withToken($token)->post($url, [
            'url' => route('pesapal.callback'), // your callback URL
            'ipn_notification_type' => 'POST'
        ]);

        return $response->json();
    }

    /**
     * Initiate STK Push
     */
    public function stkPush(Request $request)
    {
        Log::info('Pesapal STKPush endpoint hit', ['request' => $request->all()]);

        $token = $this->getAccessToken();
        if (!$token) {
            return response()->json(['error' => 'Failed to get access token']);
        }

        $amount = (int) ($request->votes ?? 1) * 10; // Example price per vote = 10
        $callback = route('pesapal.callback');

        $orderTrackingId = uniqid('order_', true);

        $payload = [
            'id' => $orderTrackingId,
            'currency' => 'KES',
            'amount' => $amount,
            'description' => 'Voting Payment',
            'callback_url' => $callback,
            'notification_id' => env('PESAPAL_NOTIFICATION_ID'),
            'branch' => 'Online Voting',
            'payment_method' => 'M-PESA', // 👈 force M-PESA STK push
            'billing_address' => [
                'email_address' => 'noreply@javapa.com',
                'phone_number' => $request->phone,
                'country_code' => 'KE',
                'first_name' => 'Voter',
                'last_name' => 'Online',
            ]
        ];

        $url = env('PESAPAL_BASE_URL') . '/api/Transactions/SubmitOrderRequest';

        $response = Http::withToken($token)->post($url, $payload);
        $data = $response->json();

        Log::info('Pesapal STK Response', ['response' => $data]);

        if (isset($data['redirect_url'])) {
            // Save a pending payment before redirect
            $payment = \App\Models\Payment::create([
                'user_id' => auth()->id() ?? 1,
                'voting_contest_id' => $request->contest_id ?? 1360,
                'nominee_id' => $request->nominee_id,
                'order_tracking_id' => $data['order_tracking_id'], // ✅ use Pesapal's ID
                'merchant_reference' => $data['merchant_reference'] ?? $orderTrackingId,
                'amount' => $amount,
                'currency' => 'KES',
                'status' => 'PENDING',
                'payment_method' => 'M-PESA',
                'phone_number' => $request->phone,
            ]);

            return redirect()->away($data['redirect_url']);
        }

        return back()->with('error', 'Failed to initiate payment. Please try again.');

    }

    /**
     * Handle Pesapal callback (payment status notification).
     */
    public function callback(Request $request)
    {
        Log::info('Pesapal Callback received', ['data' => $request->all()]);
    
        // You could verify status here if needed
    
        return redirect()->route('voting.index')
            ->with('success', 'Payment received! Thank you for your votes.');
    }
    public function callbackReturn(Request $request)
    {
        Log::info('Pesapal User Redirect received', ['query' => $request->all()]);

        // Optional: confirm status from Pesapal API
        $orderTrackingId = $request->query('OrderTrackingId');

        if ($orderTrackingId) {
            $status = $this->verifyTransaction($orderTrackingId);

            if (strtoupper($status) === 'COMPLETED') {
                $this->processSuccessfulPayment($orderTrackingId, $request);
                return redirect()->route('voting.index')
                    ->with('success', 'Payment successful! Thank you for your votes.');
            }

            return redirect()->route('voting.index')
                ->with('info', 'Payment received but still pending confirmation.');
        }

        return redirect()->route('voting.index')
            ->with('error', 'Unable to verify payment. Please contact support.');
    }
    /**
     * Verify a transaction's current status from Pesapal.
     */
    private function verifyTransaction($trackingId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('Pesapal verifyTransaction: Missing access token');
                return null;
            }

            $url = env('PESAPAL_BASE_URL') . '/api/Transactions/GetTransactionStatus?orderTrackingId=' . $trackingId;

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->get($url);

            $data = $response->json();

            Log::info('Pesapal VerifyTransaction', [
                'trackingId' => $trackingId,
                'response' => $data
            ]);

            // Depending on Pesapal response, this might be one of:
            // COMPLETED, PENDING, FAILED, INVALID, etc.
            return $data['payment_status_description'] ?? $data['status'] ?? null;
        } catch (\Exception $e) {
            Log::error('Pesapal verifyTransaction Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    /**
     * Handle Pesapal IPN (server-to-server notification)
     */
    public function ipn(Request $request)
    {
        Log::info('Pesapal IPN received', ['data' => $request->all()]);

        $trackingId = $request->input('OrderTrackingId');

        if (!$trackingId) {
            Log::warning('Pesapal IPN missing OrderTrackingId');
            return response()->json(['error' => 'Missing tracking ID'], 400);
        }

        $status = $this->verifyTransaction($trackingId);

        if (!$status) {
            Log::warning('Pesapal IPN failed to verify transaction', ['trackingId' => $trackingId]);
            return response()->json(['status' => 'failed'], 500);
        }

        if (strtoupper($status) === 'COMPLETED') {
            $this->processSuccessfulPayment($trackingId, $request);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Handle completed Pesapal payments and register votes
     */
    private function processSuccessfulPayment(string $trackingId, Request $request)
    {
        $payment = \App\Models\Payment::where('order_tracking_id', $trackingId)->first();

        if (!$payment) {
            Log::warning('Payment not found during success processing', ['trackingId' => $trackingId]);
            return;
        }
        // prevent duplicate processing
        if ($payment->status === 'COMPLETED') {
            Log::info('Payment already processed', ['trackingId' => $trackingId]);
            return;
        }
        $payment->update([
            'status' => 'COMPLETED',
            'raw_response' => $request->all(),
        ]);
        
        \App\Models\VotePurchase::create([
            'user_id'     => $payment->user_id,
            'nominee_id'  => $payment->nominee_id,
            'votes_count' => 1, // assuming 1 vote per transaction; adjust if needed
            'amount'      => $payment->amount,
            'status'      => 'paid',
        ]);

        \App\Models\Vote::create([
            'user_id' => $payment->user_id,
            'voting_contest_id' => $payment->voting_contest_id,
            'nominee_id' => $payment->nominee_id,
            'ip_address' => $request->ip(),
        ]);

        $nominee = \App\Models\Nominee::find($payment->nominee_id);
        if ($nominee) {
            $nominee->increment('votes_count');
            $nominee->contest->increment('total_votes');
        }

        Log::info('Vote recorded successfully via callback/IPN', [
            'trackingId' => $trackingId,
            'nominee_id' => $payment->nominee_id,
            'contest_id' => $payment->voting_contest_id,
        ]);
        
        
    }
}
