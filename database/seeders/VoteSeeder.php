<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vote;
use App\Models\User;
use App\Models\VotingContest;
use App\Models\Nominee;

class VoteSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $users = User::where('role', 'attendee')->pluck('id');
        $contests = VotingContest::pluck('id');
        $nominees = Nominee::all();

        $votes = [];

        for ($i = 1; $i <= 200; $i++) {
            $nominee = $faker->randomElement($nominees);

            $votes[] = [
                'user_id' => $faker->randomElement($users),
                'voting_contest_id' => $nominee->voting_contest_id,
                'nominee_id' => $nominee->id,
                'voted_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'ip_address' => $faker->ipv4,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($votes, 50) as $chunk) {
            Vote::insert($chunk);
        }
    }
}
