<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\Zone;
use App\Models\WalkRoute;
use App\Models\MeterCategory;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportWaterData extends Command
{
    protected $signature = 'app:import-water-data';
    protected $description = 'Import water data from XLSX';

    public function handle()
    {
        $file = storage_path('app/import/customers.xlsx');

        if (!file_exists($file)) {
            $this->error("XLSX file not found at: $file");
            return;
        }

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (empty($rows)) {
            $this->error("The spreadsheet is empty!");
            return;
        }

        // 🔹 Find the real header row (contains 'Acc.No.' and 'Name')
        $headerRowIndex = null;
        foreach ($rows as $i => $r) {
            if (in_array('Acc.No.', $r) && in_array('Name', $r)) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($headerRowIndex === null) {
            $this->error("Could not find header row containing 'Acc.No.' and 'Name'");
            return;
        }

        // Remove rows before header
        $rows = array_slice($rows, $headerRowIndex);
        $rawHeader = array_shift($rows); // remove header row

        // Clean headers
        $header = array_map(fn($h) => trim($h), $rawHeader);
        $this->info('Detected headers: ' . implode(' | ', $header));

        $insertedCustomers = 0;
        $insertedMeters = 0;

        foreach ($rows as $index => $data) {
            if (count(array_filter($data)) === 0) continue;

            $row = @array_combine($header, $data);
            if (!$row) {
                $this->error("Row $index failed to combine with header: " . json_encode($data));
                continue;
            }

            // Debug row
            $this->line("Processing row $index: " . json_encode($row));

            // Zone
            $zoneName = $row['Zone'] ?? 'Unknown';
            $zone = Zone::firstOrCreate(['name' => $zoneName]);

            // WalkRoute
            $walkrouteName = $row['Walkroute'] ?? 'Unknown';
            $walkroute = WalkRoute::firstOrCreate([
                'zone_id' => $zone->id,
                'name' => $walkrouteName,
            ]);

            // Meter Category
            $categoryName = $row['Category'] ?? 'Unknown';
            $category = MeterCategory::firstOrCreate([
                'name' => $categoryName,
                'code' => strtolower($categoryName),
            ]);

            // Customer
            $fullName = $row['Name'] ?? 'Unknown';
            $nameParts = explode(" ", $fullName);
            $first = array_shift($nameParts);
            $last = implode(" ", $nameParts);

            $customerNumber = $row['Acc.No.'] ?? null;

            if (!$customerNumber || trim($customerNumber) == "") {
                // Generate new account number based on sequence
                $latestCustomer = Customer::orderBy('id', 'desc')->first();
                $lastNumber = $latestCustomer?->customer_number;

                if (is_numeric($lastNumber)) {
                    $newNumber = intval($lastNumber) + 1;
                } else {
                    // fallback numbering (when DB contains non-numeric values)
                    $newNumber = Customer::max('id') + 10000;
                }

                $customerNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT); // e.g. 000123
                $this->warn("Row $index: Missing account number → Assigned: $customerNumber");
            }


            $customer = Customer::firstOrCreate(
                ['customer_number' => $customerNumber],
                [
                    'first_name' => $first,
                    'last_name' => $last,
                    'phone' => $row['Mobile No'] ?: null,
                    'email' => null,
                    'status' => 'active',
                ]
            );
            $insertedCustomers++;

            // Meter
            $meterNumber = $row['Mtr No.'] ?? null;
            if ($meterNumber && strtoupper($meterNumber) !== 'N/A') {
                $meter = Meter::firstOrCreate(
                    ['meter_number' => $meterNumber],
                    [
                        'meter_category_id' => $category->id,
                        'longitude' => $row['Longitude'] ?? null,
                        'latitude' => $row['Latitude'] ?? null,
                        'status' => strtolower($row['Status'] ?? 'unknown'),
                        'customer_id' => $customer->id,
                        'zone_id' => $zone->id,
                        'walk_route_id' => $walkroute->id,
                        'initial_reading' => 0,
                        'balance_bf' => floatval(str_replace(',', '', $row['Bal b/f'] ?? 0)),
                        'current_balance' => floatval(str_replace(',', '', $row['Acc. Bal'] ?? 0)),
                    ]
                );

                if ($meter->wasRecentlyCreated) {
                    $insertedMeters++;
                    $this->line("Inserted meter: {$meterNumber}");
                }
            } else {
                $this->warn("Row $index: Meter skipped (N/A or empty)");
            }
        }

        $this->info("✅ XLSX import completed!");
        $this->info("Inserted Customers: $insertedCustomers");
        $this->info("Inserted Meters: $insertedMeters");
    }
}
