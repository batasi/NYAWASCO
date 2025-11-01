<?php

namespace App\Services;

use Kemboielvis\MpesaSdkPhp\Mpesa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MpesaService
{
    protected $mpesa;
    protected $environment;
    protected $businessCode;
    protected $passkey;
    protected $callbackUrl;

    public function __construct()
    {
        $this->environment = config('mpesa.environment', 'production');
        $this->businessCode = config('mpesa.business_till') ?: config('mpesa.business_shortcode');
        $this->passkey = config('mpesa.lnm_passkey');
        $this->callbackUrl = config('mpesa.stk_callback_url');

        // Initialize M-Pesa SDK
        $this->mpesa = new Mpesa(
            config('mpesa.consumer_key'),
            config('mpesa.consumer_secret'),
            $this->environment === 'production' ? 'live' : 'sandbox'
        );

        // Set cache file in storage directory
       if (!file_exists(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0777, true);
        }
        $this->mpesa->setStoreFile('storage/logs/mpesa_token_cache.json');



        // Enable debug logging
        $this->mpesa->setDebug(true);

        // Set business code and passkey
        $this->mpesa->setBusinessCode($this->businessCode);
        $this->mpesa->setPassKey($this->passkey);

        Log::info('MpesaService Initialized', [
            'environment' => $this->environment,
            'business_code' => $this->businessCode,
            'callback_url' => $this->callbackUrl
        ]);
    }

    /**
     * Initiate STK Push for voting
     */
    public function initiateSTKPush(string $phone, float $amount, string $reference, string $description = 'Vote Payment'): array
    {
        try {
            // Validate amount
            if ($amount < 1) {
                throw new \Exception('Amount must be at least KES 1');
            }

            $phone = $this->normalizePhone($phone);

            // Determine transaction type based on business code
            $transactionType = $this->getTransactionType();

            Log::info('Initiating STK Push', [
                'phone' => $phone,
                'amount' => $amount,
                'reference' => $reference,
                'transaction_type' => $transactionType,
                'business_code' => $this->businessCode
            ]);

            // Initiate STK Push using the SDK
            $response = $this->mpesa->stk()
                ->setTransactionType($transactionType)
                ->setAmount($amount)
                ->setPhoneNumber($phone)
                ->setCallbackUrl($this->callbackUrl)
                ->setAccountReference($reference)
                ->setTransactionDesc($description)
                ->push()
                ->getResponse();

            Log::info('STK Push Response', $response);

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'response_code' => $response['ResponseCode'],
                    'response_description' => $response['ResponseDescription'],
                    'customer_message' => $response['CustomerMessage'],
                    'raw_response' => $response,
                ];
            } else {
                $errorMessage = $response['errorMessage'] ?? $response['ResponseDescription'] ?? 'STK Push failed';

                Log::error('STK Push Failed', [
                    'error' => $errorMessage,
                    'response' => $response
                ]);

                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'raw_response' => $response,
                ];
            }

        } catch (\Throwable $e) {
            Log::error('STK Push Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query STK Push status
     */
    public function querySTKStatus(string $checkoutRequestId): ?array
    {
        try {
            Log::info('Querying STK Status', ['checkout_request_id' => $checkoutRequestId]);

            $response = $this->mpesa->stk()
                ->query($checkoutRequestId)
                ->getResponse();

            Log::info('STK Query Response', $response);

            return $response;

        } catch (\Throwable $e) {
            Log::error('STK Query Exception', [
                'error' => $e->getMessage(),
                'checkout_request_id' => $checkoutRequestId
            ]);

            return null;
        }
    }

    /**
     * Determine transaction type based on business code
     */
    private function getTransactionType(): string
    {
        // If it's a till number (Buy Goods), use CustomerBuyGoodsOnline
        // If it's a shortcode (PayBill), use CustomerPayBillOnline
        $tillNumber = config('mpesa.business_till');

        return $tillNumber ? 'CustomerBuyGoodsOnline' : 'CustomerPayBillOnline';
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

    /**
     * Validate configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];

        if (empty($this->businessCode)) {
            $errors[] = "Business code is not configured";
        }

        if (empty($this->passkey)) {
            $errors[] = "LNM passkey is not configured";
        }

        if (empty($this->callbackUrl)) {
            $errors[] = "Callback URL is not configured";
        }

        if (!filter_var($this->callbackUrl, FILTER_VALIDATE_URL)) {
            $errors[] = "Callback URL is not a valid URL";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'configuration' => [
                'environment' => $this->environment,
                'business_code' => $this->businessCode,
                'transaction_type' => $this->getTransactionType(),
                'callback_url' => $this->callbackUrl,
            ]
        ];
    }

    /**
     * Clear token cache (useful for debugging)
     */
    public function clearTokenCache(): void
    {
        $this->mpesa->clearTokenCache();
        Log::info('MPESA token cache cleared');
    }

    /**
     * Get cache file path
     */
    public function getCacheFilePath(): string
    {
        return $this->mpesa->getResolvedStoreFilePath();
    }
}
