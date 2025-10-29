<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nominee;
use App\Models\VotingContest;
use App\Models\NomineeCategory;
use Illuminate\Support\Str;

class NomineeSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $contests = VotingContest::pluck('id');
        $categories = NomineeCategory::pluck('id');

        $nominees = [];

        for ($i = 1; $i <= 200; $i++) {
            $nominees[] = [
                'voting_contest_id' => $faker->randomElement($contests),
                'nominee_category_id' => $faker->randomElement($categories),
                'name' => $faker->name,
                'bio' => $faker->paragraph(3),
                'photo' => $faker->optional(0.8)->imageUrl(200, 200, 'people', true),
                'affiliation' => $faker->company,
                'position' => $faker->numberBetween(1, 100),
                'votes_count' => $faker->numberBetween(0, 5000),
                'is_active' => $faker->boolean(95),
                'code' => 'NOM' . Str::upper(Str::random(8)),
                'metadata' => json_encode(['achievements' => $faker->words(5, true)]),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($nominees, 50) as $chunk) {
            Nominee::insert($chunk);
        }
    }
}
