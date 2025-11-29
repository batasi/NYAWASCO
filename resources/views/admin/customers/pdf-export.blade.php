<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers Report - NYAWASCO</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .estate-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .estate-header {
            background-color: #1e40af;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            font-size: 10px;
        }
        .customer-row:hover {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-active { color: #059669; font-weight: bold; }
        .status-pending_payment { color: #d97706; font-weight: bold; }
        .status-sealed { color: #dc2626; font-weight: bold; }
        .status-terminated { color: #6b7280; font-weight: bold; }
        .status-new { color: #2563eb; font-weight: bold; }
        .negative-balance { color: #dc2626; }
        .positive-balance { color: #059669; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .summary-info {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NYAWASCO - CUSTOMERS REPORT</h1>
        <div class="subtitle">
            Status: {{ ucfirst($status) }} | Total Customers: {{ $totalCustomers }} | Generated: {{ $exportDate }}
        </div>
    </div>

    <div class="summary-info">
        <strong>Report Summary:</strong> 
        Showing {{ $totalCustomers }} customers filtered by "{{ $status }}" status
        @if($search)
            matching search "{{ $search }}"
        @endif
    </div>

    @foreach($groupedCustomers as $estate => $estateCustomers)
    <div class="estate-section">
        <div class="estate-header">
            ESTATE: {{ $estate ?: 'UNCATEGORIZED' }} ({{ $estateCustomers->count() }} customers)
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="8%">ACC No</th>
                    <th width="12%">Name</th>
                    <th width="8%">Category</th>
                    <th width="8%">Status</th>
                    <th width="10%">Phone No</th>
                    <th width="10%">Meter No</th>
                    <th width="8%">Bal B/F</th>
                    <th width="8%">Current Bal</th>
                    <th width="8%">Acc Bal</th>
                    <th width="8%">Arrears</th>
                    <th width="12%">Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estateCustomers as $customer)
                @php
                    $primaryMeter = $customer->meters->first();
                    $totalMeterBalance = $customer->meters->sum('current_balance');
                    $arrears = $customer->current_balance > 0 ? $customer->current_balance : 0;
                @endphp
                <tr class="customer-row">
                    <td>{{ $customer->customer_number }}</td>
                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td>{{ ucfirst($customer->connection_type) }}</td>
                    <td class="status-{{ $customer->status }}">
                        {{ strtoupper(str_replace('_', ' ', $customer->status)) }}
                    </td>
                    <td>{{ $customer->phone }}</td>
                    <td>
                        @if($primaryMeter)
                            {{ $primaryMeter->meter_number }}
                            @if($customer->meters->count() > 1)
                                +{{ $customer->meters->count() - 1 }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right">
                        @if($primaryMeter)
                            {{ number_format($primaryMeter->balance_bf, 2) }}
                        @else
                            0.00
                        @endif
                    </td>
                    <td class="text-right">
                        @if($primaryMeter)
                            {{ number_format($primaryMeter->current_balance, 2) }}
                        @else
                            0.00
                        @endif
                    </td>
                    <td class="text-right {{ $customer->current_balance < 0 ? 'positive-balance' : 'negative-balance' }}">
                        {{ number_format(abs($customer->current_balance), 2) }}
                    </td>
                    <td class="text-right negative-balance">
                        {{ number_format($arrears, 2) }}
                    </td>
                    <td>{{ $customer->plot_number }} {{ $customer->house_number ? 'Hse: ' . $customer->house_number : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if(!$loop->last)
        <div style="margin-bottom: 20px; border-bottom: 1px dashed #e5e7eb;"></div>
    @endif
    @endforeach

    <div class="footer">
        <p>Generated by NYAWASCO Billing System | Page {{ $pdf->getPage() }} of {!! $pdf->getPage() !!}</p>
        <p>Confidential - For Internal Use Only</p>
    </div>
</body>
</html>