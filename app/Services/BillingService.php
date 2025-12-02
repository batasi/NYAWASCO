<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    private $taxRate = 0.16; // 16% VAT

    /**
     * Generate bill from meter reading with enterprise logic
     */
    public function generateBillFromReading(MeterReading $reading, Customer $customer, Meter $meter): Bill
    {
        return DB::transaction(function () use ($reading, $customer, $meter) {
            try {
                // Get meter category pricing
                $category = $meter->meterCategory;

                // Calculate consumption and charges
                $consumption = $reading->consumption;
                $charges = $this->calculateCharges($category, $consumption);

                // Apply any adjustments or discounts
                $charges = $this->applyAdjustments($charges, $customer, $meter);

                // Generate unique bill number
                $billNumber = $this->generateBillNumber();

                // Determine billing period
                $billingPeriod = $this->determineBillingPeriod($reading->reading_date);

                // Create the bill with comprehensive details
                $bill = Bill::create([
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'meter_reading_id' => $reading->id,
                    'bill_number' => $billNumber,
                    'billing_period_start' => $billingPeriod['start'],
                    'billing_period_end' => $billingPeriod['end'],
                    'consumption' => $consumption,
                    'base_charge' => $charges['base_charge'],
                    'consumption_charge' => $charges['consumption_charge'],
                    'meter_rent' => $charges['meter_rent'],
                    'connection_charge' => $charges['connection_charge'],
                    'deposit_charge' => $charges['deposit_charge'],
                    'late_fee' => $charges['late_fee'],
                    'tax_amount' => $charges['tax_amount'],
                    'total_amount' => $charges['total_amount'],
                    'due_date' => Carbon::parse($reading->reading_date)->addDays($customer->payment_terms ?? 30),
                    'bill_status' => 'unpaid',
                    'notes' => $this->generateBillNotes($reading, $customer, $meter),
                    'created_by' => auth()->id(),
                    'breakdown' => json_encode([
                        'category' => $category->name,
                        'consumption_rate' => $category->default_rate,
                        'tax_rate' => $this->taxRate,
                        'calculation_details' => $charges['calculation_details']
                    ])
                ]);

                // Mark reading as billed
                $reading->update([
                    'billed' => true,
                    'billed_by' => auth()->id(),
                    'billed_at' => now(),
                ]);

                // Update balances
                $this->updateFinancialBalances($customer, $meter, $charges['total_amount']);

                // Log audit trail
                $this->logBillingAudit($bill, $customer, $meter, $charges);

                // Trigger notifications
                $this->triggerNotifications($bill, $customer);

                return $bill;

            } catch (\Exception $e) {
                Log::error('Billing generation failed', [
                    'reading_id' => $reading->id,
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Calculate all charges with tiered pricing support
     */
    private function calculateCharges(MeterCategory $category, float $consumption): array
    {
        $baseCharge = $category->base_charge;
        $meterRent = $category->meter_rent;

        // Calculate consumption charge with tiered pricing if applicable
        $consumptionCharge = $this->calculateTieredConsumption($category, $consumption);

        // Calculate tax on applicable charges
        $taxableAmount = $baseCharge + $consumptionCharge + $meterRent;
        $taxAmount = $taxableAmount * $this->taxRate;

        // Additional charges from JSON field
        $additionalCharges = $this->calculateAdditionalCharges($category->additional_charges);

        $totalAmount = $baseCharge + $consumptionCharge + $meterRent + $taxAmount + $additionalCharges;

        return [
            'base_charge' => $baseCharge,
            'consumption_charge' => $consumptionCharge,
            'meter_rent' => $meterRent,
            'tax_amount' => $taxAmount,
            'connection_charge' => $category->connection_fee ?? 0,
            'deposit_charge' => $category->deposit_amount ?? 0,
            'late_fee' => 0, // Will be calculated separately
            'additional_charges' => $additionalCharges,
            'total_amount' => $totalAmount,
            'calculation_details' => [
                'consumption' => $consumption,
                'tier_details' => $this->getTierDetails($category, $consumption),
                'tax_rate' => $this->taxRate,
                'taxable_amount' => $taxableAmount
            ]
        ];
    }

    /**
     * Calculate tiered consumption charges
     */
    private function calculateTieredConsumption(MeterCategory $category, float $consumption): float
    {
        if (!$category->has_tiers) {
            return $consumption * $category->default_rate;
        }

        // Get tier configuration from additional_charges
        $tiers = $category->additional_charges['tiers'] ?? [];

        if (empty($tiers)) {
            return $consumption * $category->default_rate;
        }

        // Sort tiers by threshold
        usort($tiers, function($a, $b) {
            return $a['threshold'] <=> $b['threshold'];
        });

        $totalCharge = 0;
        $remainingConsumption = $consumption;

        foreach ($tiers as $tier) {
            if ($remainingConsumption <= 0) break;

            $tierMax = $tier['threshold'] ?? INF;
            $tierConsumption = min($remainingConsumption, $tierMax);
            $tierRate = $tier['rate'] ?? $category->default_rate;

            $totalCharge += $tierConsumption * $tierRate;
            $remainingConsumption -= $tierConsumption;
        }

        return $totalCharge;
    }

    /**
     * Generate professional bill number
     */
    private function generateBillNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $sequence = Bill::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return "INV-{$year}{$month}-" . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Update financial balances
     */
    private function updateFinancialBalances(Customer $customer, Meter $meter, float $amount): void
    {
        // Update meter balance
        $meter->increment('current_balance', $amount);

        // Recalculate customer balance from all meters
        $totalBalance = $customer->meters()->sum('current_balance');
        $customer->update(['current_balance' => $totalBalance]);

        // Update arrears if applicable
        if ($totalBalance > 0) {
            $customer->increment('arrears', $amount);
        }
    }

    /**
     * Generate comprehensive bill notes
     */
    private function generateBillNotes(MeterReading $reading, Customer $customer, Meter $meter): string
    {
        $notes = [];
        $notes[] = "Auto-generated from meter reading #{$reading->id}";
        $notes[] = "Meter: {$meter->meter_number}";
        $notes[] = "Reading Date: {$reading->reading_date->format('Y-m-d')}";
        $notes[] = "Consumption: {$reading->consumption} m³";

        if ($reading->reading_image) {
            $notes[] = "Photo evidence available";
        }

        if ($reading->notes) {
            $notes[] = "Reading Notes: {$reading->notes}";
        }

        return implode("\n", $notes);
    }

    /**
     * Log billing audit trail
     */
    private function logBillingAudit(Bill $bill, Customer $customer, Meter $meter, array $charges): void
    {
        Log::channel('billing')->info('Bill Generated', [
            'bill_id' => $bill->id,
            'bill_number' => $bill->bill_number,
            'customer_id' => $customer->id,
            'customer_number' => $customer->customer_number,
            'meter_id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'amount' => $bill->total_amount,
            'consumption' => $bill->consumption,
            'charges_breakdown' => $charges,
            'generated_by' => auth()->id(),
            'generated_at' => now()->toIso8601String(),
            'ip_address' => request()->ip()
        ]);
    }
}
