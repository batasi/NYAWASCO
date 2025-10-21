<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function stkPush(Request $request)
    {
        Log::info('STKPush endpoint hit', ['request' => $request->all()]);
        Log::info('Raw request body', ['body' => $request->getContent()]);

        $data = $request->validate([
            'nominee_id' => 'required|integer',
            'contest_id' => 'nullable|integer',
            'phone'      => 'required',
            'votes'      => 'required|integer|min:1',
        ]);
        try {
        $amount = $data['votes'] * (config('voting.price_per_vote', 10));

        // Step 1: Get Access Token
        $tokenResponse = Http::withBasicAuth(env('MPESA_CONSUMER_KEY'), env('MPESA_CONSUMER_SECRET'))
            ->withoutVerifying() 
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        if (!$tokenResponse->successful()) {
            return response()->json([
                'error' => 'Failed to get token',
                'details' => $tokenResponse->body(),
            ], 500);
        }
            
        $access_token = $tokenResponse['access_token'];

        // Step 2: Send STK Push
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);

        $response = Http::withToken($access_token)
            ->withoutVerifying() 
            ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', 
        [
            "BusinessShortCode" => env('MPESA_SHORTCODE'),
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $amount,
            "PartyA" => $data['phone'],
            "PartyB" => env('MPESA_SHORTCODE'),
            "PhoneNumber" => $data['phone'],
            "CallBackURL" => env('MPESA_CALLBACK_URL'),
            "AccountReference" => "Vote_" . $data['nominee_id'],
            "TransactionDesc" => "Voting payment",
        ]);
        Log::info('STKPush response', ['response' => $response->json()]);

        return response()->json([
            'status' => 'pending',
            'message' => 'STK Push sent. Check your phone to complete the payment.',
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
