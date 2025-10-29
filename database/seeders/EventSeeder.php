<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\EventCategory;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $organizers = User::where('role', 'organizer')->pluck('id');
        $categories = EventCategory::pluck('id');

        $events = [];

        for ($i = 1; $i <= 200; $i++) {
            $startDate = $faker->dateTimeBetween('now', '+1 year');
            $endDate = $faker->dateTimeBetween($startDate, '+1 year');

            $events[] = [
                'organizer_id' => $faker->randomElement($organizers),
                'title' => $faker->words(4, true) . ' Event',
                'description' => $faker->text(500),
                'location' => $faker->address,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'ticket_price' => $faker->randomFloat(2, 0, 500),
                'capacity' => $faker->numberBetween(50, 5000),
                'is_active' => $faker->boolean(85),
                'is_featured' => $faker->boolean(20),
                'status' => $faker->randomElement(['draft', 'pending_approval', 'approved', 'cancelled']),
                'banner_image' => $faker->optional(0.7)->imageUrl(800, 400, 'events', true),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($events, 50) as $chunk) {
            Event::insert($chunk);
        }
    }
}
