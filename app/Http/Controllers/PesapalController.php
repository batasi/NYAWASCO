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

        Log::info('Pesapal STK Response', ['response' => $response->json()]);

        return $response->json();
    }

    /**
     * Handle Pesapal callback (payment status notification).
     */
    public function callback(Request $request)
    {
        Log::info('Pesapal Callback received', ['data' => $request->all()]);
        return response()->json(['status' => 'success']);
    }
}
