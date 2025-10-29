<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categories = [
            ['name' => 'Music Concerts', 'color' => '#3B82F6', 'icon' => 'music'],
            ['name' => 'Sports Events', 'color' => '#EF4444', 'icon' => 'trophy'],
            ['name' => 'Business Conferences', 'color' => '#10B981', 'icon' => 'briefcase'],
            ['name' => 'Art Exhibitions', 'color' => '#8B5CF6', 'icon' => 'palette'],
            ['name' => 'Food Festivals', 'color' => '#F59E0B', 'icon' => 'utensils'],
            ['name' => 'Tech Meetups', 'color' => '#6366F1', 'icon' => 'code'],
            ['name' => 'Charity Events', 'color' => '#EC4899', 'icon' => 'heart'],
            ['name' => 'Workshops', 'color' => '#06B6D4', 'icon' => 'tool'],
            ['name' => 'Theater Shows', 'color' => '#84CC16', 'icon' => 'drama'],
            ['name' => 'Networking Events', 'color' => '#F97316', 'icon' => 'users'],
        ];

        $eventCategories = [];

        foreach ($categories as $category) {
            $eventCategories[] = [
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
            $name = $faker->words(2, true) . ' Events';
            $eventCategories[] = [
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $i,
                'description' => $faker->paragraph,
                'icon' => $faker->randomElement(['star', 'calendar', 'map-pin', 'camera', 'video', 'book', 'coffee', 'shopping-bag']),
                'color' => $faker->hexColor,
                'is_active' => $faker->boolean(90),
                'sort_order' => $faker->numberBetween(1, 100),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($eventCategories, 50) as $chunk) {
            EventCategory::insert($chunk);
        }
    }
}
