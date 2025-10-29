<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VotingContest;
use App\Models\User;
use App\Models\VotingCategory;
use Illuminate\Support\Str;

class VotingContestSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $organizers = User::where('role', 'organizer')->pluck('id');
        $categories = VotingCategory::pluck('id');

        $contests = [];

        for ($i = 1; $i <= 200; $i++) {
            $title = $faker->words(3, true) . ' Awards ' . $i;
            $startDate = $faker->dateTimeBetween('-1 month', '+6 months');
            $endDate = $faker->dateTimeBetween($startDate, '+1 year');

            $contests[] = [
                'organizer_id' => $faker->randomElement($organizers),
                'category_id' => $faker->randomElement($categories),
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $faker->text(300),
                'rules' => $faker->text(200),
                'featured_image' => $faker->optional(0.8)->imageUrl(600, 400, 'contests', true),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => $faker->boolean(85),
                'is_featured' => $faker->boolean(25),
                'requires_approval' => $faker->boolean(30),
                'max_votes_per_user' => $faker->numberBetween(1, 10),
                'price_per_vote' => $faker->randomFloat(2, 5, 50),
                'total_votes' => $faker->numberBetween(0, 10000),
                'views_count' => $faker->numberBetween(0, 50000),
                'metadata' => json_encode(['theme' => $faker->word, 'sponsor' => $faker->company]),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($contests, 50) as $chunk) {
            VotingContest::insert($chunk);
        }
    }
}
