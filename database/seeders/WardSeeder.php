<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ward;
use App\Models\SubCounty;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wards = [
            [
                'name' => 'Nyamira Township',
                'code' => 'NT',
                'description' => 'Central business district',
                'sub_county_id' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Bogichora',
                'code' => 'BC',
                'description' => 'Residential area',
                'sub_county_id' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ekerenyo',
                'code' => 'EK',
                'description' => 'Rural ward',
                'sub_county_id' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Keroka',
                'code' => 'KR',
                'description' => 'Market center',
                'sub_county_id' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($wards as $ward) {
            Ward::create($ward);
        }
    }
}
