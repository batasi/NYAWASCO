<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $users = User::pluck('id');
        $events = Event::pluck('id');
        $tickets = Ticket::all();

        $bookings = [];

        for ($i = 1; $i <= 200; $i++) {
            $ticket = $faker->randomElement($tickets);
            $amount = $ticket->price * $faker->numberBetween(1, 5);

            $bookings[] = [
                'user_id' => $faker->randomElement($users),
                'bookable_type' => 'App\\Models\\Event',
                'bookable_id' => $ticket->event_id,
                'ticket_number' => 'TICKET' . Str::upper(Str::random(10)),
                'quantity' => $faker->numberBetween(1, 5),
                'amount' => $amount,
                'status' => $faker->randomElement(['pending', 'confirmed', 'cancelled']),
                'payment_status' => $faker->randomElement(['unpaid', 'paid', 'refunded']),
                'amount_paid' => $faker->randomFloat(2, 0, $amount),
                'payment_reference' => $faker->optional(0.7)->uuid,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($bookings, 50) as $chunk) {
            Booking::insert($chunk);
        }
    }
}
