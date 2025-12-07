<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meter;
use App\Models\Customer;

class FixMeterStatuses extends Command
{
    protected $signature = 'meters:fix-statuses';
    protected $description = 'Fix meter and customer status inconsistencies';

    public function handle()
    {
        $this->info('Fixing meter and customer statuses...');

        // Fix meters with 'assigned' status to 'active'
        $assignedMeters = Meter::where('status', 'assigned')->get();
        $this->info("Found {$assignedMeters->count()} meters with 'assigned' status");

        foreach ($assignedMeters as $meter) {
            $meter->update(['status' => Meter::STATUS_ACTIVE]);
            $this->line("Fixed meter {$meter->meter_number} from 'assigned' to 'active'");
        }

        // Sync all customer statuses
        $customers = Customer::all();
        $this->info("Syncing status for {$customers->count()} customers");

        foreach ($customers as $customer) {
            $oldStatus = $customer->status;
            $customer->syncStatusFromMeters();
            $newStatus = $customer->fresh()->status;
            
            if ($oldStatus !== $newStatus) {
                $this->line("Customer {$customer->customer_number}: {$oldStatus} → {$newStatus}");
            }
        }

        $this->info('✓ Status fixing complete!');
        return Command::SUCCESS;
    }
}