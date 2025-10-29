<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VotePurchase;
use App\Models\User;
use App\Models\Nominee;

class VotePurchaseSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $users = User::where('role', 'attendee')->pluck('id');
        $nominees = Nominee::all();

        $purchases = [];

        for ($i = 1; $i <= 200; $i++) {
            $nominee = $faker->randomElement($nominees);
            $votesCount = $faker->numberBetween(1, 20);
            $amount = $nominee->votingContest->price_per_vote * $votesCount;

            $purchases[] = [
                'user_id' => $faker->randomElement($users),
                'nominee_id' => $nominee->id,
                'votes_count' => $votesCount,
                'amount' => $amount,
                'status' => $faker->randomElement(['pending', 'paid']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($purchases, 50) as $chunk) {
            VotePurchase::insert($chunk);
        }
    }
}
