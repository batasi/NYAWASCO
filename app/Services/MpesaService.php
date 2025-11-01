<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MpesaService
{
    protected $base;
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $stkCallback;

    public function __construct()
    {
        $env = config('mpesa.environment', 'sandbox');

        $this->base = $env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';

        $this->consumerKey   = config('mpesa.consumer_key');
        $this->consumerSecret= config('mpesa.consumer_secret');
        $this->shortcode     = config('mpesa.business_shortcode');
        $this->passkey       = config('mpesa.lnm_passkey');
        $this->stkCallback   = config('mpesa.stk_callback_url');
    }

    /**
     * Get OAuth token from Daraja with retry logic
     */
    public function getAccessToken(): ?string
    {
        $url = "{$this->base}/oauth/v1/generate?grant_type=client_credentials";

        try {
            $res = Http::timeout(10)
                      ->retry(3, 100)
                      ->withBasicAuth($this->consumerKey, $this->consumerSecret)
                      ->get($url);

            if ($res->ok()) {
                return $res->json('access_token');
            }

            Log::error('MPESA token error', [
                'status' => $res->status(),
                'response' => $res->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('MPESA token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Initialize STK Push with enhanced validation
     */
    public function stkPush(string $phone, $amount, string $accountReference = 'Vote Payment', string $transactionDesc = 'Voting')
    {
        $token = $this->getAccessToken();
        if (!$token) {
            throw new \Exception('Failed to get MPESA access token');
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $transactionType = $this->shortcode == '174379'
            ? 'CustomerPayBillOnline'   // Paybill
            : 'CustomerBuyGoodsOnline';  // Till

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $transactionType,
            'Amount'            => (int)ceil($amount),
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->stkCallback,
            'AccountReference'  => Str::limit($accountReference, 12, ''),
            'TransactionDesc'   => Str::limit($transactionDesc, 13, ''),
        ];

        $url = "{$this->base}/mpesa/stkpush/v1/processrequest";

        $res = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        $response = $res->json();

        if ($res->failed()) {
            Log::error('MPESA STK Push failed', [
                'status' => $res->status(),
                'response' => $response,
            ]);
            return [
                'status' => 'error',
                'message' => $response['errorMessage'] ?? 'Unknown error',
                'data' => $response,
            ];
        }

        Log::info('MPESA STK Push Success', ['response' => $response]);

        return $response;
    }

    /**
     * Query STK Push status
     */
    public function queryStkStatus($checkoutRequestId)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $url = "{$this->base}/mpesa/stkpushquery/v1/query";

        try {
            $res = Http::withToken($token)
                      ->post($url, $payload);

            return $res->ok() ? $res->json() : null;

        } catch (\Exception $e) {
            Log::error('MPESA STK Query failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
