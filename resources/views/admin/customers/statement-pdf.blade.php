<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $customer->customer_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-box {
            display: inline-block;
            width: 24%;
            text-align: center;
            padding: 10px;
            margin-right: 1%;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .summary-box:last-child {
            margin-right: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .debit {
            color: #d63031;
        }
        .credit {
            color: #00b894;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #2c3e50;">{{ $company['name'] }}</h1>
        <p style="margin: 5px 0; color: #7f8c8d;">{{ $company['address'] }}</p>
        <p style="margin: 5px 0; color: #7f8c8d;">Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
        <h2 style="margin-top: 20px; color: #2c3e50;">CUSTOMER STATEMENT</h2>
    </div>

    <div class="customer-info">
        <table style="border: none; margin-bottom: 20px;">
            <tr>
                <td style="border: none; padding: 5px 20px 5px 0; width: 50%;">
                    <strong>Customer:</strong> {{ $customer->first_name }} {{ $customer->last_name }}<br>
                    <strong>Account No:</strong> {{ $customer->customer_number }}<br>
                    <strong>Address:</strong> {{ $customer->physical_address }}
                </td>
                <td style="border: none; padding: 5px 0; width: 50%;">
                    <strong>Statement Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}<br>
                    <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}<br>
                    <strong>Meter(s):</strong> {{ $customer->meters->pluck('meter_number')->implode(', ') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <div class="summary-box">
            <h4>Opening Balance</h4>
            <p style="font-size: 14px; font-weight: bold; {{ $openingBalance >= 0 ? 'color: #2980b9;' : 'color: #c0392b;' }}">
                KSh {{ number_format(abs($openingBalance), 2) }}
            </p>
            <small>{{ $openingBalance >= 0 ? 'Credit Balance' : 'Amount Owed' }}</small>
        </div>
        <div class="summary-box">
            <h4>Total Debits</h4>
            <p style="font-size: 14px; font-weight: bold; color: #c0392b;">
                KSh {{ number_format($totalDebits, 2) }}
            </p>
            <small>{{ $bills->count() }} bills</small>
        </div>
        <div class="summary-box">
            <h4>Total Credits</h4>
            <p style="font-size: 14px; font-weight: bold; color: #27ae60;">
                KSh {{ number_format($totalCredits, 2) }}
            </p>
            <small>{{ $payments->count() }} payments</small>
        </div>
        <div class="summary-box">
            <h4>Closing Balance</h4>
            <p style="font-size: 14px; font-weight: bold; {{ $closingBalance >= 0 ? 'color: #2980b9;' : 'color: #c0392b;' }}">
                KSh {{ number_format(abs($closingBalance), 2) }}
            </p>
            <small>{{ $closingBalance >= 0 ? 'Credit Balance' : 'Amount Owed' }}</small>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance -->
            <tr>
                <td>{{ $startDate->format('M d, Y') }}</td>
                <td><strong>Opening</strong></td>
                <td>—</td>
                <td>Opening Balance</td>
                <td class="debit">
                    @if($openingBalance < 0)
                        KSh {{ number_format(abs($openingBalance), 2) }}
                    @else
                        —
                    @endif
                </td>
                <td class="credit">
                    @if($openingBalance > 0)
                        KSh {{ number_format($openingBalance, 2) }}
                    @else
                        —
                    @endif
                </td>
                <td><strong>{{ $openingBalance >= 0 ? 'KSh ' . number_format($openingBalance, 2) : '(KSh ' . number_format(abs($openingBalance), 2) . ')' }}</strong></td>
            </tr>

            @php
                $runningBalance = $openingBalance;
            @endphp

            <!-- Bills -->
            @foreach($bills as $bill)
            @php
                $runningBalance += $bill->total_amount;
            @endphp
            <tr>
                <td>{{ $bill->created_at->format('M d, Y') }}</td>
                <td>Bill</td>
                <td>{{ $bill->bill_number }}</td>
                <td>
                    Water Bill - {{ $bill->billing_period_start?->format('M Y') ?? 'N/A' }}
                    <br>
                    <small>Consumption: {{ number_format($bill->consumption, 2) }} m³</small>
                </td>
                <td class="debit">KSh {{ number_format($bill->total_amount, 2) }}</td>
                <td>—</td>
                <td><strong>{{ $runningBalance >= 0 ? 'KSh ' . number_format($runningBalance, 2) : '(KSh ' . number_format(abs($runningBalance), 2) . ')' }}</strong></td>
            </tr>
            @endforeach

            <!-- Payments -->
            @foreach($payments as $payment)
            @php
                $runningBalance -= $payment->amount;
            @endphp
            <tr>
                <td>{{ $payment->payment_date?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                <td>Payment</td>
                <td>{{ $payment->payment_no ?? $payment->receipt_number }}</td>
                <td>
                    Payment {{ $payment->payment_method ? 'via ' . $payment->payment_method : '' }}
                    @if($payment->bill)
                        <br>
                        <small>For: {{ $payment->bill->bill_number }}</small>
                    @endif
                </td>
                <td>—</td>
                <td class="credit">KSh {{ number_format($payment->amount, 2) }}</td>
                <td><strong>{{ $runningBalance >= 0 ? 'KSh ' . number_format($runningBalance, 2) : '(KSh ' . number_format(abs($runningBalance), 2) . ')' }}</strong></td>
            </tr>
            @endforeach

            <!-- Meter Readings -->
            @foreach($meterReadings as $reading)
            <tr style="background-color: #f9f9f9;">
                <td>{{ $reading->reading_date->format('M d, Y') }}</td>
                <td>Reading</td>
                <td>{{ $reading->meter->meter_number ?? 'N/A' }}</td>
                <td>
                    Meter Reading
                    <br>
                    <small>{{ number_format($reading->previous_reading, 2) }} → {{ number_format($reading->current_reading, 2) }} m³</small>
                </td>
                <td>—</td>
                <td>—</td>
                <td>—</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated statement. No signature is required.</p>
        <p>Page 1 of 1 | Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
