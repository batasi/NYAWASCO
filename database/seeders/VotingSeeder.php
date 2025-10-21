<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\VotingCategory;
use App\Models\VotingContest;
use App\Models\Nominee;
use App\Models\User;

class VotingSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there is at least one organizer (you can adjust the logic)
        $organizer = User::first() ?? User::factory()->create([
            'name' => 'Soloh Organizer',
            'email' => 'soloh@example.com',
        ]);

        // 1️⃣ Create voting categories
        $categories = [
            ['name' => 'Music Awards', 'color' => '#8B5CF6', 'icon' => 'music'],
            ['name' => 'Sports Awards', 'color' => '#10B981', 'icon' => 'trophy'],
            ['name' => 'Tech Innovators', 'color' => '#F59E0B', 'icon' => 'cpu'],
        ];

        foreach ($categories as $data) {
            $category = VotingCategory::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => 'Annual ' . $data['name'] . ' celebrating excellence.',
                'color' => $data['color'],
                'icon' => $data['icon'],
                'is_active' => true,
            ]);

            // 2️⃣ Create contests within each category
            for ($i = 1; $i <= 2; $i++) {
                $title = $data['name'] . " Contest $i";
                $contest = VotingContest::create([
                    'organizer_id' => $organizer->id,
                    'category_id' => $category->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . $i,
                    'description' => "Vote for your favorite nominees in the $title!",
                    'rules' => "Each user can vote up to 5 times per nominee.",
                    'featured_image' => null,
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->addDays(20),
                    'is_active' => true,
                    'is_featured' => rand(0, 1),
                    'requires_approval' => false,
                    'max_votes_per_user' => 5,
                    'total_votes' => 0,
                    'views_count' => 0,
                ]);

                // 3️⃣ Add nominees
                for ($n = 1; $n <= 5; $n++) {
                    Nominee::create([
                        'voting_contest_id' => $contest->id,
                        'name' => fake()->name(),
                        'bio' => fake()->paragraph(),
                        'photo' => 'https://picsum.photos/300?random=' . rand(1, 100),
                        'affiliation' => fake()->company(),
                        'position' => $n,
                        'votes_count' => 0,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
