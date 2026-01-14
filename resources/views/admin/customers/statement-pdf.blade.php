<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Water Account Statement - {{ $customer->customer_number }}</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        /* Header Styles */
        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .company-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 200px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 10pt;
            color: #555;
            margin-bottom: 3px;
        }

        .statement-title {
            text-align: center;
            margin: 25px 0;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .statement-title h1 {
            font-size: 18pt;
            color: #2c3e50;
            margin: 0;
        }

        .statement-title .subtitle {
            font-size: 11pt;
            color: #666;
            margin-top: 5px;
        }

        /* Customer Information */
        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 10pt;
            display: block;
        }

        .info-value {
            color: #333;
            font-size: 11pt;
        }

        /* Balance Summary */
        .balance-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .balance-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            background: #fff;
        }

        .balance-box.opening {
            border-top: 3px solid #3498db;
        }

        .balance-box.debits {
            border-top: 3px solid #e74c3c;
        }

        .balance-box.credits {
            border-top: 3px solid #27ae60;
        }

        .balance-box.closing {
            border-top: 3px solid #9b59b6;
        }

        .balance-label {
            font-size: 10pt;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-amount {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .balance-amount.positive {
            color: #27ae60;
        }

        .balance-amount.negative {
            color: #e74c3c;
        }

        .balance-detail {
            font-size: 9pt;
            color: #777;
        }

        /* Transactions Table */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .transactions-table thead th {
            background: #2c3e50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
            border: 1px solid #1a252f;
        }

        .transactions-table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10pt;
        }

        .transactions-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .transactions-table tbody tr:hover {
            background: #e9f7fe;
        }

        .transaction-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            min-width: 70px;
        }

        .type-opening {
            background: #95a5a6;
            color: white;
        }

        .type-bill {
            background: #e74c3c;
            color: white;
        }

        .type-payment {
            background: #27ae60;
            color: white;
        }

        .type-reading {
            background: #3498db;
            color: white;
        }

        .debit-amount {
            color: #e74c3c;
            font-weight: bold;
            text-align: right;
        }

        .credit-amount {
            color: #27ae60;
            font-weight: bold;
            text-align: right;
        }

        .balance-amount-cell {
            font-weight: bold;
            text-align: right;
        }

        .balance-positive {
            color: #27ae60;
        }

        .balance-negative {
            color: #e74c3c;
        }

        .transaction-details {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }

        /* Statement Summary */
        .statement-summary {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #2c3e50;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .summary-item {
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2c3e50;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .footer-notes {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 9pt;
            color: #666;
        }

        .disclaimer {
            margin-top: 20px;
            font-size: 8pt;
            color: #888;
            font-style: italic;
        }

        /* Page breaks for printing */
        .page-break {
            page-break-after: always;
        }

        /* Utility classes */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .no-border {
            border: none;
        }

        .bg-light {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-header">
            <div class="company-info">
                <div class="company-name">NYAWASCO</div>
                <div class="company-address">P.O Box 255-40500, NYAMIRA</div>
                <div class="company-address">Tel: 0787 080 455 | Email: info@nyawasco.co.ke</div>
                <div class="company-address">Website: www.nyawasco.co.ke</div>
            </div>
            <div class="statement-info">
                <div class="info-item">
                    <span class="info-label">Statement No:</span>
                    <span class="info-value">STMT-{{ strtoupper(uniqid()) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Generated:</span>
                    <span class="info-value">{{ now()->format('F d, Y h:i A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Page:</span>
                    <span class="info-value">1 of 1</span>
                </div>
            </div>
        </div>

        <div class="statement-title">
            <h1>WATER ACCOUNT STATEMENT</h1>
            <div class="subtitle">Statement Period: {{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="customer-info">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Account Number:</span>
                <span class="info-value">{{ $customer->customer_number }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $customer->first_name }} {{ $customer->last_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Phone Number:</span>
                <span class="info-value">{{ $customer->phone }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email Address:</span>
                <span class="info-value">{{ $customer->email ?? 'Not provided' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Physical Address:</span>
                <span class="info-value">{{ $customer->plot_number }}, {{ $customer->house_number }}, {{ $customer->estate ?? '' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">ID Number:</span>
                <span class="info-value">{{ $customer->id_number }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Meter Number(s):</span>
                <span class="info-value">
                    @foreach($customer->meters as $meter)
                        {{ $meter->meter_number }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Account Status:</span>
                <span class="info-value">
                    <span style="color: {{ $customer->status == 'active' ? '#27ae60' : ($customer->status == 'suspended' ? '#e74c3c' : '#f39c12') }}; font-weight: bold;">
                        {{ strtoupper($customer->status) }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- Balance Summary -->
    <div class="balance-summary">
        <div class="balance-box opening">
            <div class="balance-label">Opening Balance</div>
            <div class="balance-amount {{ $openingBalance >= 0 ? 'positive' : 'negative' }}">
                KSh {{ number_format(abs($openingBalance), 2) }}
            </div>
            <div class="balance-detail">
                {{ $openingBalance >= 0 ? 'Credit Balance' : 'Amount Owed' }}
            </div>
        </div>

        <div class="balance-box debits">
            <div class="balance-label">Total Debits</div>
            <div class="balance-amount negative">
                KSh {{ number_format($totalDebits, 2) }}
            </div>
            <div class="balance-detail">
                {{ $bills->count() }} bill(s)
            </div>
        </div>

        <div class="balance-box credits">
            <div class="balance-label">Total Credits</div>
            <div class="balance-amount positive">
                KSh {{ number_format($totalCredits, 2) }}
            </div>
            <div class="balance-detail">
                {{ $payments->count() }} payment(s)
            </div>
        </div>

        <div class="balance-box closing">
            <div class="balance-label">Closing Balance</div>
            <div class="balance-amount {{ $closingBalance >= 0 ? 'positive' : 'negative' }}">
                KSh {{ number_format(abs($closingBalance), 2) }}
            </div>
            <div class="balance-detail">
                {{ $closingBalance >= 0 ? 'Credit Balance' : 'Amount Owed' }}
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <h3 style="color: #2c3e50; margin-bottom: 15px; border-bottom: 2px solid #3498db; padding-bottom: 5px;">
        TRANSACTION DETAILS
    </h3>

    <table class="transactions-table">
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="15%">Type</th>
                <th width="15%">Reference</th>
                <th width="28%">Description</th>
                <th width="15%" class="text-right">Debit (KSh)</th>
                <th width="15%" class="text-right">Credit (KSh)</th>
                <th width="15%" class="text-right">Balance (KSh)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = $openingBalance;
            @endphp

            <!-- Opening Balance Row -->
            <tr>
                <td>{{ $startDate->format('d/m/Y') }}</td>
                <td>
                    <span class="transaction-type type-opening">OPENING</span>
                </td>
                <td>—</td>
                <td>Opening Balance</td>
                <td class="text-right">
                    @if($openingBalance < 0)
                        <span class="debit-amount">{{ number_format(abs($openingBalance), 2) }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="text-right">
                    @if($openingBalance > 0)
                        <span class="credit-amount">{{ number_format($openingBalance, 2) }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="balance-amount-cell {{ $runningBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    {{ $runningBalance >= 0 ? '' : '(' }}{{ number_format(abs($runningBalance), 2) }}{{ $runningBalance >= 0 ? '' : ')' }}
                </td>
            </tr>

            <!-- Bills (Debits) -->
            @foreach($bills as $bill)
                @php
                    $runningBalance += $bill->total_amount;
                @endphp
                <tr>
                    <td>{{ $bill->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="transaction-type type-bill">BILL</span>
                    </td>
                    <td>{{ $bill->bill_number }}</td>
                    <td>
                        Water Consumption Bill
                        <div class="transaction-details">
                            Period:
                            @if($bill->billing_period_start && $bill->billing_period_end)
                                {{ $bill->billing_period_start->format('M Y') }}
                            @else
                                N/A
                            @endif
                            | Consumption: {{ number_format($bill->consumption, 2) }} m³
                        </div>
                    </td>
                    <td class="debit-amount">{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="text-right">—</td>
                    <td class="balance-amount-cell {{ $runningBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                        {{ $runningBalance >= 0 ? '' : '(' }}{{ number_format(abs($runningBalance), 2) }}{{ $runningBalance >= 0 ? '' : ')' }}
                    </td>
                </tr>
            @endforeach

            <!-- Payments (Credits) -->
            @foreach($payments as $payment)
                @php
                    $runningBalance -= $payment->amount;
                @endphp
                <tr>
                    <td>{{ ($payment->payment_date ?? $payment->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <span class="transaction-type type-payment">PAYMENT</span>
                    </td>
                    <td>{{ $payment->payment_no ?? $payment->receipt_number ?? 'N/A' }}</td>
                    <td>
                        Payment Received
                        <div class="transaction-details">
                            Method: {{ $payment->payment_method ?? 'Not specified' }}
                            @if($payment->transaction_reference)
                                | Ref: {{ $payment->transaction_reference }}
                            @endif
                        </div>
                    </td>
                    <td class="text-right">—</td>
                    <td class="credit-amount">{{ number_format($payment->amount, 2) }}</td>
                    <td class="balance-amount-cell {{ $runningBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                        {{ $runningBalance >= 0 ? '' : '(' }}{{ number_format(abs($runningBalance), 2) }}{{ $runningBalance >= 0 ? '' : ')' }}
                    </td>
                </tr>
            @endforeach

            <!-- Meter Readings (Informational) -->
            @foreach($meterReadings as $reading)
                <tr class="bg-light">
                    <td>{{ $reading->reading_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="transaction-type type-reading">READING</span>
                    </td>
                    <td>{{ $reading->meter->meter_number ?? 'N/A' }}</td>
                    <td>
                        Meter Reading Recorded
                        <div class="transaction-details">
                            {{ number_format($reading->previous_reading, 2) }} → {{ number_format($reading->current_reading, 2) }} m³
                            (Usage: {{ number_format($reading->consumption, 2) }} m³)
                        </div>
                    </td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Statement Summary -->
    <div class="statement-summary">
        <h3 style="color: #2c3e50; margin-bottom: 15px;">STATEMENT SUMMARY</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="info-label">Total Bills Generated:</span>
                <span class="info-value">{{ $bills->count() }}</span>
            </div>
            <div class="summary-item">
                <span class="info-label">Total Payments Received:</span>
                <span class="info-value">{{ $payments->count() }}</span>
            </div>
            <div class="summary-item">
                <span class="info-label">Total Meter Readings:</span>
                <span class="info-value">{{ $meterReadings->count() }}</span>
            </div>
            <div class="summary-item">
                <span class="info-label">Account Status:</span>
                <span class="info-value" style="color: {{ $customer->status == 'active' ? '#27ae60' : ($customer->status == 'suspended' ? '#e74c3c' : '#f39c12') }};">
                    {{ strtoupper($customer->status) }}
                </span>
            </div>
            <div class="summary-item">
                <span class="info-label">Statement Period:</span>
                <span class="info-value">{{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</span>
            </div>
            <div class="summary-item">
                <span class="info-label">Days in Period:</span>
                <span class="info-value">{{ $startDate->diffInDays($endDate) + 1 }} days</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-notes">
            <p><strong>Important Notes:</strong></p>
            <p>1. All amounts are in Kenya Shillings (KSh)</p>
            <p>2. Payments may take up to 24 hours to reflect in your account</p>
            <p>3. Outstanding balances attract a late payment fee as per company policy</p>
            <p>4. For any discrepancies, please contact our customer service within 7 days</p>
        </div>

        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
            <p><strong>NYAWASCO - Nyanza Water & Sanitation Company</strong></p>
            <p>P.O Box 255-40500, NYAMIRA | Tel: 0787 080 455 | Email: info@nyawasco.co.ke</p>
            <p>Working Hours: Mon-Fri 8:00 AM - 5:00 PM, Sat 9:00 AM - 1:00 PM</p>
        </div>

        <div class="disclaimer">
            <p>This is a computer-generated statement. No signature is required.</p>
            <p>Statement generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p>© {{ date('Y') }} NYAWASCO. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
