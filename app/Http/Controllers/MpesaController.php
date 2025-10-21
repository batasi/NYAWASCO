<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Nominee;

class MpesaController extends Controller
{

    public function stkPush(Request $request)
    {
        Log::info('STKPush endpoint hit', ['request' => $request->all()]);
        Log::info('Raw request body', ['body' => $request->getContent()]);
    
        $data = $request->validate([
            'nominee_id' => 'required|integer',
            'contest_id' => 'nullable|integer',
            'phone'      => 'required|string',
            'votes'      => 'required|integer|min:1',
        ]);
    
        try {
            // Fetch nominee from DB
            $nominee = Nominee::find($data['nominee_id']);
            if (!$nominee) {
                return response()->json([
                    'error' => 'Nominee not found'
                ], 404);
            }
    
            // Calculate total
            $amount = $data['votes'] * (config('voting.price_per_vote', 10));
    
            // Normalize phone
            $phone = trim($data['phone']);
            if (str_starts_with($phone, '07')) {
                $phone = '254' . substr($phone, 1);
            } elseif (str_starts_with($phone, '+254')) {
                $phone = substr($phone, 1);
            }
            $phone = preg_replace('/[^0-9]/', '', $phone);
    
            // Credentials
            $consumerKey = env('MPESA_CONSUMER_KEY');
            $consumerSecret = env('MPESA_CONSUMER_SECRET');
            $shortcode = env('MPESA_SHORTCODE', '174379'); // sandbox default
            $passkey = env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
    
            // 1️⃣ Get Access Token
            $tokenResponse = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->withoutVerifying()
                ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    
            if (!$tokenResponse->successful()) {
                return response()->json([
                    'error' => 'Failed to get token',
                    'details' => $tokenResponse->body(),
                ], 500);
            }
            $accessToken = $tokenResponse['access_token'];
    
            // 2️⃣ Build STK payload
            $timestamp = now()->format('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);
    
            $payload = [
                "BusinessShortCode" => $shortcode,
                "Password"          => $password,
                "Timestamp"         => $timestamp,
                "TransactionType"   => "CustomerPayBillOnline",
                "Amount"            => $amount,
                "PartyA"            => $phone,
                "PartyB"            => $shortcode,
                "PhoneNumber"       => $phone,
                "CallBackURL"       => env('MPESA_CALLBACK_URL'),
                "AccountReference"  => "Vote_" . $nominee->name, // Use nominee code
                "TransactionDesc"   => "Voting payment for " . $nominee->name, // descriptive
            ];
    
            // 3️⃣ Send STK push
            $response = Http::withToken($accessToken)
                ->withoutVerifying()
                ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $payload);
    
            Log::info('STKPush response', ['response' => $response->json()]);
    
            return response()->json([
                'status' => 'pending',
                'message' => 'STK Push sent. Check your phone to complete the payment.',
                'nominee' => [
                    'id'   => $nominee->id,
                    'name' => $nominee->name,
                    'code' => $nominee->code,
                ],
                'votes' => $data['votes'],
                'total_amount' => $amount,
                'mpesa_response' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('MPESA ERROR', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function callback(Request $request)
    {
        Log::info('MPESA CALLBACK', $request->all());

        $resultCode = $request->input('Body.stkCallback.ResultCode');

        if ($resultCode == 0) {
            // Payment success: credit the votes here
            $amount = $request->input('Body.stkCallback.CallbackMetadata.Item.0.Value');
            $account = $request->input('Body.stkCallback.AccountReference'); // e.g., "Vote_15"
            $nominee_id = (int) str_replace('Vote_', '', $account);

            // find nominee, increment vote count, save payment log, etc.
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }
}
