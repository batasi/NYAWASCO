<?php
namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Bill Reminder',
                'slug' => 'bill_reminder',
                'description' => 'Remind customer about their outstanding bill',
                'message' => "Dear {{customer_name}},\n\nThis is a reminder that your water bill for meter {{meter_number}} for period {{billing_period}} of KSh {{bill_amount}} is due by {{due_date}}. Current balance: KSh {{balance}}.\n\nPlease make payment to avoid service interruption.\n\nThank you,\nNYAWASCO",
                'category' => 'bill',
                'placeholders' => ['customer_name', 'meter_number', 'billing_period', 'bill_amount', 'due_date', 'balance']
            ],
            [
                'name' => 'Payment Receipt',
                'slug' => 'payment_receipt',
                'description' => 'Send payment confirmation receipt',
                'message' => "Dear {{customer_name}},\n\nThank you for your payment of KSh {{payment_amount}} received on {{payment_date}}.\n\nReceipt Number: {{receipt_number}}\nBill Number: {{bill_number}}\nMeter: {{meter_number}}\nOutstanding Balance: KSh {{balance}}\n\nThank you for your prompt payment.\n\nNYAWASCO",
                'category' => 'payment',
                'placeholders' => ['customer_name', 'payment_amount', 'payment_date', 'receipt_number', 'bill_number', 'meter_number', 'balance']
            ],
            [
                'name' => 'Meter Reading Confirmation',
                'slug' => 'reading_confirmation',
                'description' => 'Confirm meter reading to customer',
                'message' => "Dear {{customer_name}},\n\nYour meter reading for meter {{meter_number}} on {{reading_date}} has been recorded.\n\nCurrent Reading: {{current_reading}} m³\nConsumption: {{consumption}} m³\nPeriod: {{reading_period}}\n\nYour bill will be generated shortly.\n\nThank you,\nNYAWASCO",
                'category' => 'reading',
                'placeholders' => ['customer_name', 'meter_number', 'reading_date', 'current_reading', 'consumption', 'reading_period']
            ],
            [
                'name' => 'Welcome Message',
                'slug' => 'welcome',
                'description' => 'Welcome new customer',
                'message' => "Dear {{customer_name}},\n\nWelcome to NYAWASCO! Your account has been successfully created.\n\nCustomer Number: {{customer_number}}\nMeter Number: {{meter_number}}\n\nFor any inquiries, please contact us at 0787080455.\n\nThank you for choosing NYAWASCO.",
                'category' => 'general',
                'placeholders' => ['customer_name', 'customer_number', 'meter_number']
            ],
            [
                'name' => 'Disconnection Notice',
                'slug' => 'disconnection_notice',
                'description' => 'Notify customer of pending disconnection',
                'message' => "Dear {{customer_name}},\n\nThis is a final reminder that your account for meter {{meter_number}} has an outstanding balance of KSh {{balance}}.\n\nIf payment is not received by {{due_date}}, your water supply will be disconnected without further notice.\n\nPlease make immediate payment to avoid disconnection.\n\nNYAWASCO",
                'category' => 'alert',
                'placeholders' => ['customer_name', 'meter_number', 'balance', 'due_date']
            ],
            [
                'name' => 'Meter Exception Alert',
                'slug' => 'meter_exception',
                'description' => 'Alert about meter reading exception',
                'message' => "Dear {{customer_name}},\n\nOur meter reader encountered an issue with meter {{meter_number}} on {{reading_date}}.\n\nIssue: {{exception_reason}}\n\nPlease contact our office to resolve this matter to ensure accurate billing.\n\nThank you,\nNYAWASCO",
                'category' => 'alert',
                'placeholders' => ['customer_name', 'meter_number', 'reading_date', 'exception_reason']
            ],
        ];

        foreach ($templates as $template) {
            SmsTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                array_merge($template, ['is_active' => true])
            );
        }
    }
}
