<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        /* RESET ALL MARGINS AND PADDING */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }

        /* SCREEN STYLING */
        @media screen {
            body {
                width: 58mm;
                margin: 20px auto;
                padding: 5px;
                background: white;
                border: 1px solid #ccc;
                font-size: 9pt;
            }
        }

        /* PRINT STYLING - SIMPLIFIED */
        @media print {
            body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 2mm !important;
                font-size: 9pt !important;
                line-height: 1.1;
            }

            .no-print {
                display: none !important;
            }

            /* HIDE EVERYTHING EXCEPT RECEIPT */
            body > *:not(.receipt-container) {
                display: none !important;
            }
        }

        /* RECEIPT CONTAINER */
        .receipt-container {
            width: 54mm;
            margin: 0 auto;
            text-align: left;
        }

        /* LOGO */
        .logo {
            text-align: center;
            margin-bottom: 3px;
        }

        .logo img {
            max-width: 40mm;
            max-height: 15mm;
        }

        /* COMPANY INFO */
        .company-info {
            text-align: center;
            margin-bottom: 3px;
            font-weight: bold;
        }

        .company-name {
            font-size: 10pt;
            font-weight: bold;
        }

        .company-details {
            font-size: 7pt;
        }

        /* DIVIDERS */
        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        /* SECTION TITLES */
        .section-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin: 4px 0;
            font-size: 8pt;
        }

        /* ROWS */
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 8pt;
        }

        .label {
            flex: 1;
            text-align: left;
            font-weight: bold;
        }

        .value {
            flex: 1;
            text-align: right;
            font-weight: bold;
        }

        /* TOTALS */
        .total-row {
            font-weight: bold;
            margin: 3px 0;
        }

        /* PAYMENT INFO */
        .payment-info {
            text-align: center;
            background: #f0f0f0;
            padding: 4px;
            margin: 5px 0;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #ccc;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 8pt;
            padding-top: 4px;
            font-weight: bold;
            border-top: 1px dashed #000;
        }

        /* PRINT BUTTON */
        .print-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        /* UTILITY CLASSES */
        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .receipt-title {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            margin: 5px 0;
            text-transform: uppercase;
        }

        /* CHARGE ITEMS */
        .charge-item {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
            font-size: 8pt;
            font-weight: bold;
        }

        .charge-label {
            flex: 1;
            text-align: left;
        }

        .charge-value {
            flex: 1;
            text-align: right;
        }

        .subtotal {
            border-top: 1px dotted #000;
            margin-top: 3px;
            padding-top: 2px;
            font-weight: bold;
        }

        .grand-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin: 4px 0;
            padding: 3px 0;
            font-weight: bold;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Logo -->
        <div class="logo">
            @if(file_exists(public_path('img/logo.png')))
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
            @else
                <div class="company-name">NYAWASCO WATER</div>
            @endif
        </div>

        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">NYAWASCO WATER COMPANY</div>
            <div class="company-details">P.O Box 255-40500 - NYAMIRA</div>
            <div class="company-details">Tel: 0787080455</div>
        </div>

        <div class="divider"></div>

        <!-- Receipt Title -->
        <div class="receipt-title">OFFICIAL RECEIPT</div>

        <div class="divider"></div>

        <!-- Receipt Info -->
        <div class="row">
            <span class="label">Date:</span>
            <span class="value">{{ $receiptData['date'] }}</span>
        </div>
        <div class="row">
            <span class="label">Receipt No:</span>
            <span class="value">{{ $receiptData['receipt_number'] }}</span>
        </div>
        <div class="row">
            <span class="label">Bill No:</span>
            <span class="value">{{ $receiptData['bill_number'] }}</span>
        </div>

        <div class="divider"></div>

        <!-- Customer Info -->
        <div class="section-title">CUSTOMER INFORMATION</div>

        <div class="row">
            <span class="label">Name:</span>
            <span class="value">{{ $receiptData['customer_name'] }}</span>
        </div>

        <div class="row">
            <span class="label">Phone:</span>
            <span class="value">{{ $receiptData['customer_phone'] }}</span>
        </div>
        <div class="row">
            <span class="label">Acc No:</span>
            <span class="value">{{ $receiptData['meter_number'] }}</span>
        </div>

        <div class="divider"></div>

        <!-- Billing Details -->
        <div class="section-title">BILLING DETAILS</div>

        <div class="row">
            <span class="label">Period:</span>
            <span class="value">{{ $receiptData['billing_period'] }}</span>
        </div>
        <div class="row">
            <span class="label">Consumption:</span>
            <span class="value">{{ $receiptData['consumption'] }}</span>
        </div>
        <div class="row">
            <span class="label">Rate:</span>
            <span class="value">{{ $receiptData['rate'] }}</span>
        </div>

        <div class="divider"></div>

        <!-- Detailed Amount Breakdown -->
        <div class="section-title">DETAILED CHARGES</div>

        <!-- Base Charge -->
        <div class="charge-item">
            <span class="charge-label">Base Charge:</span>
            <span class="charge-value">{{ $receiptData['base_charge'] ?? 'KSh 0.00' }}</span>
        </div>

        <!-- Meter Rent -->
        <div class="charge-item">
            <span class="charge-label">Meter Rent:</span>
            <span class="charge-value">{{ $receiptData['meter_rent'] ?? 'KSh 0.00' }}</span>
        </div>

        <!-- Consumption Charge -->
        <div class="charge-item">
            <span class="charge-label">Consumption:</span>
            <span class="charge-value">{{ $receiptData['consumption_charge'] ?? 'KSh 0.00' }}</span>
        </div>

        <!-- Previous Arrears -->
        @if(isset($receiptData['arrears']) && floatval(str_replace(['KSh', ',', ' '], '', $receiptData['arrears'])) > 0)
        <div class="charge-item">
            <span class="charge-label">Arrears:</span>
            <span class="charge-value">{{ $receiptData['arrears'] }}</span>
        </div>
        @endif

        <!-- Subtotal (Sum of all charges before tax) -->
        <div class="charge-item subtotal">
            <span class="charge-label">Subtotal:</span>
            <span class="charge-value">{{ $receiptData['subtotal_before_tax'] ?? $receiptData['subtotal'] }}</span>
        </div>

        <div class="divider"></div>

        <!-- Grand Total -->
        <div class="row grand-total">
            <span class="label">TOTAL DUE:</span>
            <span class="value">{{ $receiptData['total_amount'] }}</span>
        </div>

        <div class="row">
            <span class="label">Due Date:</span>
            <span class="value">{{ $receiptData['due_date'] }}</span>
        </div>

        <div class="divider"></div>


        <!-- Payment Instructions -->
        <div class="payment-info">
            <br>
            <div>M-PESA PAYBILL: 247 247</div>
            <div>ACC NO: 483133#{{ $receiptData['meter_number'] }}</div>
            <br>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Printed By: {{ $receiptData['printed_by'] }}</div>
            <div>Printed On: {{ $receiptData['printed_date'] }}</div>
        </div>
    </div>

    <!-- Print Button -->
    <button class="print-btn no-print" onclick="printReceipt()">
        ЁЯЦия╕П PRINT RECEIPT
    </button>

    <script>
        // Function to handle printing
        function printReceipt() {
            // Store original body content
            const originalBody = document.body.innerHTML;

            // Get receipt content only
            const receiptContent = document.querySelector('.receipt-container').outerHTML;

            // Replace body with receipt only for printing
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        @page {
                            size: 58mm auto;
                            margin: 0;
                            padding: 0;
                        }
                        body {
                            width: 58mm;
                            margin: 0;
                            padding: 2mm;
                            font-family: 'Courier New', monospace;
                            font-size: 9pt;
                            line-height: 1.1;
                        }
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        .receipt-container {
                            width: 54mm;
                            margin: 0 auto;
                        }
                        .row, .charge-item {
                            display: flex;
                            justify-content: space-between;
                            margin: 2px 0;
                        }
                        .divider {
                            border-top: 1px dashed #000;
                            margin: 4px 0;
                        }
                        .payment-info {
                            background: #f0f0f0;
                            padding: 4px;
                            margin: 5px 0;
                            text-align: center;
                            font-weight: bold;
                        }
                        .section-title {
                            text-align: center;
                            font-weight: bold;
                            text-transform: uppercase;
                            margin: 4px 0;
                        }
                        .grand-total {
                            border-top: 2px solid #000;
                            border-bottom: 2px solid #000;
                            margin: 4px 0;
                            padding: 3px 0;
                            font-weight: bold;
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                </body>
                </html>
            `;

            // Trigger print
            window.print();

            // Restore original content after printing
            setTimeout(() => {
                document.body.innerHTML = originalBody;
            }, 100);
        }

        // Auto-print after 1 second (optional)
        window.onload = function() {
            setTimeout(() => {
                printReceipt();
            }, 1000);
        };

        // Alternative auto-print with simple approach
        document.addEventListener('DOMContentLoaded', function() {
            // Simple print after delay
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
