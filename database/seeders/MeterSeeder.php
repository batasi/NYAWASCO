<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('meters')->insert([
            [
                'meter_number' => 'MTR-1001',
                'meter_type' => 'domestic',
                'meter_model' => 'XJ-100',
                'manufacturer' => 'AquaTech',
                'latitude' => -1.292066,
                'longtitude' => 36.821945,
                'status' => 'available',
                'customer_id' => null,
                'installation_address' => 'Nairobi CBD',
                'installation_date' => Carbon::now()->subDays(10),
                'last_maintenance_date' => null,
                'initial_reading' => 0.00,
                'notes' => 'New domestic meter',
                'zone_id' => null,
                'walk_route_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meter_number' => 'MTR-1002',
                'meter_type' => 'commercial',
                'meter_model' => 'HT-300',
                'manufacturer' => 'HydroFlow',
                'latitude' => null,
                'longtitude' => null,
                'status' => 'available',
                'customer_id' => null,
                'installation_address' => 'Westlands',
                'installation_date' => null,
                'last_maintenance_date' => null,
                'initial_reading' => 0.00,
                'notes' => null,
                'zone_id' => null,
                'walk_route_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
