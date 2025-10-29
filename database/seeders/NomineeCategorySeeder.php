<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NomineeCategory;
use Illuminate\Support\Str;

class NomineeCategorySeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categories = [
            ['name' => 'Best Artist', 'color' => '#3B82F6', 'icon' => 'palette'],
            ['name' => 'Best Performer', 'color' => '#EF4444', 'icon' => 'music'],
            ['name' => 'Best Innovator', 'color' => '#10B981', 'icon' => 'lightbulb'],
            ['name' => 'Community Hero', 'color' => '#8B5CF6', 'icon' => 'heart'],
            ['name' => 'Rising Star', 'color' => '#F59E0B', 'icon' => 'star'],
            ['name' => 'Lifetime Achievement', 'color' => '#6366F1', 'icon' => 'award'],
            ['name' => 'Best Team', 'color' => '#EC4899', 'icon' => 'users'],
            ['name' => 'Excellence Award', 'color' => '#06B6D4', 'icon' => 'trophy'],
        ];

        $nomineeCategories = [];

        foreach ($categories as $category) {
            $nomineeCategories[] = [
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'color' => $category['color'],
                'icon' => $category['icon'],
                'is_active' => true,
                'sort_order' => $faker->numberBetween(1, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Add more categories to reach 200
        for ($i = 9; $i <= 200; $i++) {
            $name = $faker->words(2, true) . ' Category';
            $nomineeCategories[] = [
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $i,
                'color' => $faker->hexColor,
                'icon' => $faker->randomElement(['star', 'award', 'crown', 'medal', 'ribbon', 'trophy']),
                'is_active' => $faker->boolean(90),
                'sort_order' => $faker->numberBetween(1, 100),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($nomineeCategories, 50) as $chunk) {
            NomineeCategory::insert($chunk);
        }
    }
}
