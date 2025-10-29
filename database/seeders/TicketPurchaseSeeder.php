<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketPurchaseSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $users = User::pluck('id');
        $events = Event::pluck('id');
        $tickets = Ticket::all();

        $purchases = [];

        for ($i = 1; $i <= 200; $i++) {
            $ticket = $faker->randomElement($tickets);
            $quantity = $faker->numberBetween(1, 5);
            $unitPrice = $ticket->price;
            $totalAmount = $unitPrice * $quantity;
            $taxAmount = $totalAmount * 0.16;
            $feeAmount = $totalAmount * 0.02;
            $finalAmount = $totalAmount + $taxAmount + $feeAmount;

            $purchases[] = [
                'user_id' => $faker->randomElement($users),
                'event_id' => $ticket->event_id,
                'ticket_id' => $ticket->id,
                'order_number' => 'ORD' . Str::upper(Str::random(12)),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'fee_amount' => $feeAmount,
                'final_amount' => $finalAmount,
                'currency' => $faker->randomElement(['USD', 'KES', 'EUR', 'GBP']),
                'status' => $faker->randomElement(['pending', 'paid', 'cancelled', 'refunded']),
                'payment_method' => $faker->randomElement(['credit_card', 'mpesa', 'paypal', 'bank_transfer']),
                'payment_id' => $faker->optional(0.8)->uuid,
                'attendee_info' => json_encode(['name' => $faker->name, 'email' => $faker->email]),
                'notes' => $faker->optional(0.3)->sentence,
                'paid_at' => $faker->optional(0.7)->dateTimeBetween('-6 months', 'now'),
                'cancelled_at' => $faker->optional(0.1)->dateTimeBetween('-6 months', 'now'),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($purchases, 50) as $chunk) {
            TicketPurchase::insert($chunk);
        }
    }
}
