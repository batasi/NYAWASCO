<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\VotingContest;
use App\Models\Nominee;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $users = User::where('role', 'attendee')->pluck('id');
        $contests = VotingContest::pluck('id');
        $nominees = Nominee::all();

        $payments = [];

        for ($i = 1; $i <= 200; $i++) {
            $nominee = $faker->randomElement($nominees);
            $amount = $nominee->votingContest->price_per_vote * $faker->numberBetween(1, 10);

            $payments[] = [
                'user_id' => $faker->randomElement($users),
                'voting_contest_id' => $nominee->voting_contest_id,
                'nominee_id' => $nominee->id,
                'order_tracking_id' => 'TRK' . Str::upper(Str::random(12)),
                'merchant_reference' => 'REF' . Str::upper(Str::random(10)),
                'amount' => $amount,
                'currency' => 'KES',
                'status' => $faker->randomElement(['PENDING', 'COMPLETED', 'FAILED', 'CANCELLED']),
                'payment_method' => $faker->randomElement(['mpesa', 'credit_card', 'paypal']),
                'phone_number' => $faker->phoneNumber,
                'votes_count' => $faker->numberBetween(1, 10),
                'raw_response' => json_encode(['transaction_id' => $faker->uuid, 'status' => 'success']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($payments, 50) as $chunk) {
            Payment::insert($chunk);
        }
    }
}
