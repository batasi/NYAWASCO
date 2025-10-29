<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VotingCategory;
use Illuminate\Support\Str;

class VotingCategorySeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categories = [
            ['name' => 'Music Awards', 'color' => '#8B5CF6', 'icon' => 'music'],
            ['name' => 'Sports Awards', 'color' => '#EF4444', 'icon' => 'trophy'],
            ['name' => 'Business Awards', 'color' => '#10B981', 'icon' => 'briefcase'],
            ['name' => 'Art & Design', 'color' => '#EC4899', 'icon' => 'palette'],
            ['name' => 'Technology', 'color' => '#3B82F6', 'icon' => 'code'],
            ['name' => 'Education', 'color' => '#F59E0B', 'icon' => 'graduation-cap'],
            ['name' => 'Healthcare', 'color' => '#84CC16', 'icon' => 'heart-pulse'],
            ['name' => 'Community', 'color' => '#06B6D4', 'icon' => 'users'],
            ['name' => 'Entertainment', 'color' => '#F97316', 'icon' => 'film'],
            ['name' => 'Lifestyle', 'color' => '#6366F1', 'icon' => 'coffee'],
        ];

        $votingCategories = [];

        foreach ($categories as $category) {
            $votingCategories[] = [
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $faker->paragraph,
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_active' => true,
                'sort_order' => $faker->numberBetween(1, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Add more categories to reach 200
        for ($i = 11; $i <= 200; $i++) {
            $name = $faker->words(2, true) . ' Awards';
            $votingCategories[] = [
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $i,
                'description' => $faker->paragraph,
                'icon' => $faker->randomElement(['award', 'star', 'crown', 'medal', 'ribbon']),
                'color' => $faker->hexColor,
                'is_active' => $faker->boolean(90),
                'sort_order' => $faker->numberBetween(1, 100),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($votingCategories, 50) as $chunk) {
            VotingCategory::insert($chunk);
        }
    }
}
