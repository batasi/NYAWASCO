<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Event;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $events = Event::pluck('id');
        $tickets = [];

        for ($i = 1; $i <= 200; $i++) {
            $saleStart = $faker->dateTimeBetween('-1 month', '+6 months');
            $saleEnd = $faker->dateTimeBetween($saleStart, '+1 year');

            $tickets[] = [
                'event_id' => $faker->randomElement($events),
                'name' => $faker->randomElement(['General Admission', 'VIP', 'Early Bird', 'Standard', 'Premium']),
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 10, 300),
                'quantity_available' => $faker->numberBetween(10, 1000),
                'quantity_sold' => $faker->numberBetween(0, 500),
                'max_per_order' => $faker->numberBetween(1, 10),
                'sale_start_date' => $saleStart,
                'sale_end_date' => $saleEnd,
                'is_active' => $faker->boolean(90),
                'metadata' => json_encode(['type' => $faker->word, 'benefits' => $faker->words(3, true)]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($tickets, 50) as $chunk) {
            Ticket::insert($chunk);
        }
    }
}
