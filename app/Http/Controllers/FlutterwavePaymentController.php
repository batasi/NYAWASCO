<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Vote;
use App\Models\VotingContest;
use App\Models\Nominee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FlutterwavePaymentController extends Controller
{
    public function initializePayment(Request $request)
    {
        $request->validate([
            'nominee_id' => 'required|exists:nominees,id',
            'contest_id' => 'required|exists:voting_contests,id',
            'phone' => 'required|string',
            'votes' => 'required|integer|min:1',
        ]);

        $contest = VotingContest::findOrFail($request->contest_id);
        $nominee = Nominee::findOrFail($request->nominee_id);
        $amount = $contest->price_per_vote * $request->votes;

        $txRef = 'VOTE-' . strtoupper(Str::random(10));

        // Create payment record
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'voting_contest_id' => $contest->id,
            'nominee_id' => $nominee->id,
            'order_tracking_id' => $txRef,
            'merchant_reference' => $txRef,
            'amount' => $amount,
            'currency' => 'KES',
            'status' => 'PENDING',
            'payment_method' => 'M-PESA',
            'phone_number' => $request->phone,
            'votes_count' => $request->votes,
        ]);

        try {
            // Use direct HTTP API call instead of SDK
            $response = $this->initiateFlutterwavePayment([
                'tx_ref' => $txRef,
                'amount' => $amount,
                'currency' => 'KES',
                'redirect_url' => route('flutterwave.callback'),
                'payment_options' => 'mpesa',
                'customer' => [
                    'email' => auth()->user()->email,
                    'phonenumber' => $this->formatPhoneNumber($request->phone),
                    'name' => auth()->user()->name,
                ],
                'customizations' => [
                    'title' => 'Vote Payment - ' . $contest->title,
                    'description' => 'Voting for ' . $nominee->name,
                ],
                'meta' => [
                    'contest_id' => $contest->id,
                    'nominee_id' => $nominee->id,
                    'user_id' => auth()->id(),
                ]
            ]);

            if ($response['status'] === 'success' && isset($response['data']['link'])) {
                return redirect()->away($response['data']['link']);
            }

            Log::error('Flutterwave payment initiation failed', ['response' => $response]);
            return back()->with('error', 'Failed to initialize payment. Please try again.');

        } catch (\Exception $e) {
            Log::error('Flutterwave payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    private function initiateFlutterwavePayment($payload)
    {
        $secretKey = config('services.flutterwave.secret_key') ?? env('FLW_SECRET_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/payments', $payload);

        return $response->json();
    }

    public function callback(Request $request)
    {
        $status = $request->status;
        $txRef = $request->tx_ref;
        $transactionId = $request->transaction_id;

        Log::info('Flutterwave callback received', $request->all());

        $payment = Payment::where('order_tracking_id', $txRef)->first();

        if (!$payment) {
            Log::error('Payment not found for tx_ref: ' . $txRef);
            return redirect()->route('voting.show', $request->contest_id ?? 1)
                           ->with('error', 'Payment record not found.');
        }

        if ($status === 'successful' && $transactionId) {
            // Verify transaction with Flutterwave
            $verification = $this->verifyTransaction($transactionId);

            if ($verification['status'] === 'success' &&
                $verification['data']['status'] === 'successful' &&
                $verification['data']['amount'] == $payment->amount &&
                $verification['data']['currency'] === 'KES') {

                // Update payment status
                $payment->update([
                    'status' => 'SUCCESSFUL',
                    'raw_response' => json_encode($verification['data'])
                ]);

                // Create votes
                for ($i = 0; $i < $payment->votes_count; $i++) {
                    Vote::create([
                        'user_id' => $payment->user_id,
                        'voting_contest_id' => $payment->voting_contest_id,
                        'nominee_id' => $payment->nominee_id,
                        'ip_address' => $request->ip(),
                    ]);
                }

                // Update contest total votes
                $contest = VotingContest::find($payment->voting_contest_id);
                if ($contest) {
                    $contest->increment('total_votes', $payment->votes_count);
                }

                return redirect()->route('voting.show', $contest->id)
                               ->with('success', 'Your votes have been successfully cast! Thank you for voting.');

            } else {
                // Verification failed
                $payment->update([
                    'status' => 'FAILED',
                    'raw_response' => json_encode($verification)
                ]);

                return redirect()->route('voting.show', $payment->voting_contest_id)
                               ->with('error', 'Payment verification failed. Please contact support.');
            }
        } else {
            $payment->update([
                'status' => strtoupper($status) ?? 'FAILED',
                'raw_response' => json_encode($request->all())
            ]);

            return redirect()->route('voting.show', $payment->voting_contest_id)
                           ->with('error', 'Payment failed or was cancelled. Please try again.');
        }
    }

    private function verifyTransaction($transactionId)
    {
        $secretKey = config('services.flutterwave.secret_key') ?? env('FLW_SECRET_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ])->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

        return $response->json();
    }

    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Convert to 254 format if it starts with 0
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        }

        // If it's already in 254 format, return as is
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            return $phone;
        }

        return $phone;
    }

    // Webhook for server-to-server verification
    public function webhook(Request $request)
    {
        Log::info('Flutterwave webhook received', $request->all());

        $secretHash = env('FLW_WEBHOOK_HASH');
        $signature = $request->header('verif-hash');

        if (!$signature || $signature !== $secretHash) {
            Log::error('Webhook signature verification failed');
            abort(401);
        }

        $event = $request->event;
        $data = $request->data;

        if ($event === 'charge.completed' && $data['status'] === 'successful') {
            $txRef = $data['tx_ref'];

            $payment = Payment::where('order_tracking_id', $txRef)->first();

            if ($payment && $payment->status === 'PENDING') {
                // Update payment status
                $payment->update([
                    'status' => 'SUCCESSFUL',
                    'raw_response' => json_encode($data)
                ]);

                // Create votes
                for ($i = 0; $i < $payment->votes_count; $i++) {
                    Vote::create([
                        'user_id' => $payment->user_id,
                        'voting_contest_id' => $payment->voting_contest_id,
                        'nominee_id' => $payment->nominee_id,
                        'ip_address' => request()->ip(),
                    ]);
                }

                // Update contest total votes
                $contest = VotingContest::find($payment->voting_contest_id);
                if ($contest) {
                    $contest->increment('total_votes', $payment->votes_count);
                }

                Log::info('Webhook: Votes created for payment: ' . $txRef);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
