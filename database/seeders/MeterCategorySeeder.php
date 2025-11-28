<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeterCategory;
use App\Models\PricingTier;

class MeterCategorySeeder extends Seeder
{
    public function run()
    {
        // Domestic Category with Tiers
        $domestic = MeterCategory::create([
            'name' => 'Domestic',
            'code' => 'DOM',
            'description' => 'Residential water connections for households',
            'base_charge' => 100.00,
            'meter_rent' => 50.00,
            'has_tiers' => true,
            'default_rate' => 0.0800,
            'is_active' => true,
            'sort_order' => 1,
            'additional_charges' => [
                'installation_fee' => 2500.00,
                'connection_fee' => 2500.00,
                'deposit' => 2500.00
            ]
        ]);

        // Domestic Pricing Tiers
        PricingTier::create([
            'meter_category_id' => $domestic->id,
            'name' => 'Tier 1 - Basic',
            'min_consumption' => 0,
            'max_consumption' => 6,
            'rate_per_unit' => 0.0450,
            'description' => 'First 6 m³ per month',
            'sort_order' => 1,
            'is_active' => true
        ]);

        PricingTier::create([
            'meter_category_id' => $domestic->id,
            'name' => 'Tier 2 - Standard',
            'min_consumption' => 6,
            'max_consumption' => 20,
            'rate_per_unit' => 0.0650,
            'description' => '6-20 m³ per month',
            'sort_order' => 2,
            'is_active' => true
        ]);

        PricingTier::create([
            'meter_category_id' => $domestic->id,
            'name' => 'Tier 3 - High Usage',
            'min_consumption' => 20,
            'max_consumption' => null,
            'rate_per_unit' => 0.0850,
            'description' => 'Above 20 m³ per month',
            'sort_order' => 3,
            'is_active' => true
        ]);

        // Commercial Category
        $commercial = MeterCategory::create([
            'name' => 'Commercial',
            'code' => 'COM',
            'description' => 'Commercial and business water connections',
            'base_charge' => 200.00,
            'meter_rent' => 100.00,
            'has_tiers' => false,
            'default_rate' => 0.1200,
            'is_active' => true,
            'sort_order' => 2,
            'additional_charges' => [
                'installation_fee' => 5000.00,
                'connection_fee' => 5000.00,
                'deposit' => 5000.00
            ]
        ]);

        // Industrial Category
        $industrial = MeterCategory::create([
            'name' => 'Industrial',
            'code' => 'IND',
            'description' => 'Industrial and manufacturing water connections',
            'base_charge' => 500.00,
            'meter_rent' => 200.00,
            'has_tiers' => false,
            'default_rate' => 0.1500,
            'is_active' => true,
            'sort_order' => 3,
            'additional_charges' => [
                'installation_fee' => 10000.00,
                'connection_fee' => 10000.00,
                'deposit' => 10000.00
            ]
        ]);

        // Institutional Category
        $institutional = MeterCategory::create([
            'name' => 'Institutional',
            'code' => 'INS',
            'description' => 'Schools, hospitals, and government institutions',
            'base_charge' => 150.00,
            'meter_rent' => 75.00,
            'has_tiers' => true,
            'default_rate' => 0.0700,
            'is_active' => true,
            'sort_order' => 4,
            'additional_charges' => [
                'installation_fee' => 3000.00,
                'connection_fee' => 3000.00,
                'deposit' => 3000.00
            ]
        ]);

        // Institutional Pricing Tiers
        PricingTier::create([
            'meter_category_id' => $institutional->id,
            'name' => 'Tier 1 - Basic',
            'min_consumption' => 0,
            'max_consumption' => 50,
            'rate_per_unit' => 0.0550,
            'description' => 'First 50 m³ per month',
            'sort_order' => 1,
            'is_active' => true
        ]);

        PricingTier::create([
            'meter_category_id' => $institutional->id,
            'name' => 'Tier 2 - Standard',
            'min_consumption' => 50,
            'max_consumption' => null,
            'rate_per_unit' => 0.0750,
            'description' => 'Above 50 m³ per month',
            'sort_order' => 2,
            'is_active' => true
        ]);
    }
}