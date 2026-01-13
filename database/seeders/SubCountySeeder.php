<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubCounty;

class SubCountySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCounties = [
            [
                'name' => 'Nyamira North',
                'code' => 'NN',
                'description' => 'Northern part of Nyamira County',
                'is_active' => true,
            ],
            [
                'name' => 'Nyamira South',
                'code' => 'NS',
                'description' => 'Southern part of Nyamira County',
                'is_active' => true,
            ],
        ];

        foreach ($subCounties as $subCounty) {
            SubCounty::create($subCounty);
        }
    }
}
