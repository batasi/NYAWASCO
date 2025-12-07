<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportData['type'] }} - NYAWASCO</title>
    <style>
        @page {
            margin: 10mm 10mm 20mm 10mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* Main Container */
        .document {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Letterhead Header - Matching DOCX format */
        .letterhead-header {
            width: 100%;
            margin-bottom: 15px;
            padding-bottom: 10px;
            text-align: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            display: inline-block;
            width: 55mm;
            height: 50mm;
            margin: 0 auto;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin: 5px 0;
            text-align: center;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 10pt;
            color: #000000;
            text-align: center;
            margin: 5px 0;
            line-height: 1.2;
        }

        .company-details p {
            margin: 2px 0;
        }

        .report-header {
            margin: 20px 0 15px 0;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .report-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11pt;
            color: #000000;
            margin-bottom: 5px;
        }

        .report-period {
            text-align: center;
            font-size: 10pt;
            color: #000000;
            font-style: italic;
        }

        /* Content Sections */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 10px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }

        /* Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .summary-item {
            padding: 8px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .summary-label {
            font-size: 9pt;
            color: #666;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 11pt;
            color: #000;
            font-weight: bold;
        }

        /* Tables - Simple and Clean */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .data-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
        }

        .data-table td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            font-size: 9pt;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Totals Row */
        .totals-row {
            background-color: #e8e8e8 !important;
            font-weight: bold;
            border-top: 2px solid #000 !important;
        }

        /* Status Indicators */
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 9pt;
            text-align: center;
        }

        .footer p {
            margin: 3px 0;
        }

        .signature-section {
            margin-top: 20px;
            padding-top: 10px;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 15px auto 5px auto;
        }

        /* Utility Classes */
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

        .mt-10 {
            margin-top: 10px;
        }

        .page-break {
            page-break-before: always;
            margin-top: 20px;
        }

        /* Prevent widows and orphans */
        p, td, th {
            widows: 3;
            orphans: 3;
        }

        /* Print optimization */
        @media print {
            body {
                font-size: 11pt;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="document">
        <!-- Letterhead Header -->
        <div class="letterhead-header">
            <div class="logo-container">
                <div class="logo">
                    <!-- Make sure the logo exists in public/images/nyawasco-logo.png -->
                    <img src="{{ asset('img/Logo.png') }}">
                </div>
            </div>

            <div class="company-name">
                NYAMIRA WATER AND SANITATION COMPANY LIMITED
            </div>

            <div class="company-details">
                <p>P.O. Box 255 - 40500, NYAMIRA</p>
                <p>Tel: 0787080455 | Email: info@nyawasco.co.ke</p>
                <p>Website: http://www.nyawasco.go.ke</p>
            </div>
        </div>

        <!-- Report Header -->
        <div class="report-header">
            <div class="report-title">
                {{ $reportData['type'] }}
            </div>
            <div class="report-period">
                @if($startDate)
                    Period: {{ $startDate->format('d F Y') }} to {{ $endDate->format('d F Y') }}
                @else
                    All Time Data
                @endif
                | Generated on: {{ now()->format('d F Y H:i:s') }}
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="section">
            <div class="section-title">EXECUTIVE SUMMARY</div>

            <div class="summary-grid">
                @foreach($reportData['summary'] as $key => $value)
                <div class="summary-item">
                    <div class="summary-label">
                        {{ ucwords(str_replace('_', ' ', $key)) }}
                    </div>
                    <div class="summary-value">
                        @if(is_numeric($value))
                            @if(strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false ||
                               strpos($key, 'paid') !== false || strpos($key, 'balance') !== false ||
                               strpos($key, 'arrears') !== false || strpos($key, 'collected') !== false)
                                KSh {{ number_format($value, 2) }}
                            @elseif(strpos($key, 'rate') !== false || strpos($key, 'percentage') !== false ||
                                   strpos($key, 'efficiency') !== false)
                                {{ number_format($value, 2) }}%
                            @elseif(strpos($key, 'consumption') !== false)
                                {{ number_format($value, 2) }} m³
                            @else
                                {{ number_format($value) }}
                            @endif
                        @elseif($value instanceof \Carbon\Carbon)
                            {{ $value->format('d/m/Y') }}
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Detailed Data Table -->
        @if(isset($reportData['bills']) && $reportData['bills']->count() > 0)
        <div class="section">
            <div class="section-title">DETAILED BILLING INFORMATION</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bill No.</th>
                        <th>Customer Name</th>
                        <th>Meter No.</th>
                        <th>Consumption</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Balance</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['bills'] as $bill)
                    <tr>
                        <td>{{ $bill->bill_number }}</td>
                        <td>{{ $bill->customer->first_name }} {{ $bill->customer->last_name }}</td>
                        <td>{{ $bill->meter->meter_number ?? 'N/A' }}</td>
                        <td>{{ number_format($bill->consumption, 2) }} m³</td>
                        <td class="text-right">KSh {{ number_format($bill->total_amount, 2) }}</td>
                        <td class="text-right">KSh {{ number_format($bill->paid_amount, 2) }}</td>
                        <td class="text-right">KSh {{ number_format($bill->balance, 2) }}</td>
                        <td>
                            @if($bill->bill_status == 'paid')
                                <span class="status status-paid">PAID</span>
                            @elseif($bill->bill_status == 'unpaid')
                                <span class="status status-unpaid">UNPAID</span>
                            @else
                                <span class="status status-partial">PARTIAL</span>
                            @endif
                        </td>
                        <td>{{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    @endforeach

                    <!-- Totals -->
                    <tr class="totals-row">
                        <td colspan="4" class="text-bold">TOTALS:</td>
                        <td class="text-right text-bold">KSh {{ number_format($reportData['bills']->sum('total_amount'), 2) }}</td>
                        <td class="text-right text-bold">KSh {{ number_format($reportData['bills']->sum('paid_amount'), 2) }}</td>
                        <td class="text-right text-bold">KSh {{ number_format($reportData['bills']->sum('balance'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Monthly Breakdown -->
        @if(isset($reportData['monthly_breakdown']) && count($reportData['monthly_breakdown']) > 0)
        <div class="section">
            <div class="section-title">MONTHLY REVENUE BREAKDOWN</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>No. of Bills</th>
                        <th class="text-right">Total Billed</th>
                        <th class="text-right">Amount Collected</th>
                        <th class="text-right">Outstanding</th>
                        <th class="text-right">Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['monthly_breakdown'] as $month)
                    @php
                        $outstanding = $month->total_amount - $month->paid_amount;
                        $collectionRate = $month->total_amount > 0 ? ($month->paid_amount / $month->total_amount) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ date('F Y', mktime(0, 0, 0, $month->month, 1, $month->year)) }}</td>
                        <td class="text-center">{{ $month->bill_count }}</td>
                        <td class="text-right">KSh {{ number_format($month->total_amount, 2) }}</td>
                        <td class="text-right">KSh {{ number_format($month->paid_amount, 2) }}</td>
                        <td class="text-right">KSh {{ number_format($outstanding, 2) }}</td>
                        <td class="text-right">{{ number_format($collectionRate, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Category Breakdown -->
        @if(isset($reportData['category_breakdown']) && count($reportData['category_breakdown']) > 0)
        <div class="section">
            <div class="section-title">CATEGORY PERFORMANCE</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Code</th>
                        <th>No. of Bills</th>
                        <th class="text-right">Total Billed</th>
                        <th class="text-right">Amount Collected</th>
                        <th class="text-right">Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['category_breakdown'] as $category)
                    @php
                        $collectionRate = $category->total_amount > 0 ? ($category->paid_amount / $category->total_amount) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ $category->category }}</td>
                        <td>{{ $category->code }}</td>
                        <td class="text-center">{{ $category->bill_count }}</td>
                        <td class="text-right">KSh {{ number_format($category->total_amount, 2) }}</td>
                        <td class="text-right">KSh {{ number_format($category->paid_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($collectionRate, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Age Analysis -->
        @if(isset($reportData['age_analysis']))
        <div class="section">
            <div class="section-title">ARREARS AGE ANALYSIS</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Age Category</th>
                        <th class="text-right">Amount Outstanding</th>
                        <th class="text-right">Percentage</th>
                        <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalArrears = array_sum($reportData['age_analysis']);
                    @endphp

                    @foreach($reportData['age_analysis'] as $category => $amount)
                    @php
                        $percentage = $totalArrears > 0 ? ($amount / $totalArrears) * 100 : 0;

                        if ($category == 'over_90_days') {
                            $risk = 'HIGH';
                            $riskClass = 'status-unpaid';
                        } elseif ($category == '61-90_days') {
                            $risk = 'MEDIUM';
                            $riskClass = 'status-partial';
                        } else {
                            $risk = 'LOW';
                            $riskClass = 'status-paid';
                        }
                    @endphp
                    <tr>
                        <td>
                            @if($category == '0-30_days')
                                0-30 Days
                            @elseif($category == '31-60_days')
                                31-60 Days
                            @elseif($category == '61-90_days')
                                61-90 Days
                            @else
                                Over 90 Days
                            @endif
                        </td>
                        <td class="text-right">KSh {{ number_format($amount, 2) }}</td>
                        <td class="text-right">{{ number_format($percentage, 2) }}%</td>
                        <td><span class="status {{ $riskClass }}">{{ $risk }}</span></td>
                    </tr>
                    @endforeach

                    <tr class="totals-row">
                        <td class="text-bold">TOTAL ARREARS:</td>
                        <td class="text-right text-bold">KSh {{ number_format($totalArrears, 2) }}</td>
                        <td class="text-right text-bold">100%</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer and Signatures -->
        <div class="footer">
            <div class="signature-section">
                <table style="width: 100%; border: none; margin-top: 20px;">
                    <tr>
                        <td style="width: 50%; border: none; vertical-align: top; padding-right: 20px;">
                            <p>Prepared by:</p>
                            <div class="signature-line"></div>
                            <p>Name & Signature</p>
                            <p>Date: ________________</p>
                        </td>
                        <td style="width: 50%; border: none; vertical-align: top; padding-left: 20px;">
                            <p>Approved by:</p>
                            <div class="signature-line"></div>
                            <p>Finance Manager</p>
                            <p>Date: ________________</p>
                        </td>
                    </tr>
                </table>
            </div>

            <p style="margin-top: 20px; font-size: 8pt; color: #666;">
                *** CONFIDENTIAL *** This report contains proprietary information of NYAWASCO.
                Unauthorized distribution is prohibited.
            </p>

            <p style="font-size: 8pt; color: #777;">
                Document ID: RPT-{{ strtoupper(substr(md5(now()->toString()), 0, 8)) }} |
                Generated: {{ now()->format('d/m/Y H:i:s') }} |
                NYAWASCO Billing System
            </p>
        </div>
    </div>
</body>
</html>
