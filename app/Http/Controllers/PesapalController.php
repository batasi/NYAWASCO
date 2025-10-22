<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Nominee;

class PesapalController extends Controller
{
    public function stkPush(Request $request)
    {
        Log::info('Pesapal STKPush endpoint hit', ['request' => $request->all()]);

        $data = $request->validate([
            'nominee_id' => 'required|integer',
            'contest_id' => 'nullable|integer',
            'phone'      => 'required|string',
            'votes'      => 'required|integer|min:1',
        ]);

        $nominee = Nominee::find($data['nominee_id']);
        if (!$nominee) {
            return response()->json(['error' => 'Nominee not found'], 404);
        }

        $amount = $data['votes'] * (config('voting.price_per_vote', 10));

        // Normalize phone
        $phone = trim($data['phone']);
        if (str_starts_with($phone, '07')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+254')) {
            $phone = substr($phone, 1);
        }
        $phone = preg_replace('/[^0-9]/', '', $phone);

        try {
            // 1️⃣ Get Pesapal Access Token
            $tokenRes = Http::withBasicAuth(
                env('PESAPAL_CONSUMER_KEY'),
                env('PESAPAL_CONSUMER_SECRET')
            )->post(env('PESAPAL_BASE_URL') . '/Auth/RequestToken');

            if (!$tokenRes->successful()) {
                Log::error('Pesapal Token Error', ['response' => $tokenRes->body()]);
                return response()->json(['error' => 'Failed to obtain access token'], 500);
            }

            $accessToken = $tokenRes['token'];

            // 2️⃣ Build order payload
            $order = [
                'id' => 'ORDER-' . uniqid(),
                'currency' => 'KES',
                'amount' => $amount,
                'description' => 'Voting payment for ' . $nominee->name,
                'callback_url' => route('pesapal.callback'),
                'notification_id' => env('PESAPAL_NOTIFICATION_ID'), // from dashboard
                'billing_address' => [
                    'email_address' => 'user@example.com',
                    'phone_number' => $phone,
                    'country_code' => 'KE',
                    'first_name' => 'Voter',
                    'last_name' => 'User',
                ],
            ];

            // 3️⃣ Submit order request (STK Push)
            $response = Http::withToken($accessToken)
                ->post(env('PESAPAL_BASE_URL') . '/Transactions/SubmitOrderRequest', $order);

            Log::info('Pesapal STKPush response', ['response' => $response->json()]);

            return response()->json([
                'status' => 'pending',
                'message' => 'STK Push sent! Check your phone to complete the payment.',
                'nominee' => [
                    'id' => $nominee->id,
                    'name' => $nominee->name,
                    'code' => $nominee->code,
                ],
                'votes' => $data['votes'],
                'total_amount' => $amount,
                'pesapal_response' => $response->json(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Pesapal Error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Pesapal request failed: ' . $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Pesapal Callback:', $request->all());
        return response()->json(['status' => 'received']);
    }

}

