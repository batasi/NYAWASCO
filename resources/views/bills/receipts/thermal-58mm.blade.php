<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { 
                width: 58mm; 
                margin: 0; 
                padding: 2mm; 
                font-family: 'Courier New', monospace; 
                font-size: 12px !important; /* INCREASED FONT */
                font-weight: bold; 
                text-align: center; /* CENTER EVERYTHING */
            }
            .no-print { display: none; }
        }
        
        @media screen {
            body { 
                width: 58mm; 
                margin: 20px auto; 
                padding: 10px; 
                font-family: 'Courier New', monospace; 
                font-size: 12px !important; /* INCREASED FONT */
                font-weight: bold; 
                text-align: center; /* CENTER EVERYTHING */
                border: 1px solid #000;
            }
        }
        
        /* CENTER LOGO */
        .logo { 
            text-align: center; 
            margin: 0 auto 5px; 
        }
        .logo img { 
            max-width: 35mm !important; /* SMALLER LOGO */
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        /* CENTER RECEIPT CONTENT */
        .receipt-content {
            text-align: center;
            width: 100%;
        }
        
        /* CENTER PRINT BUTTON */
        .print-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- CENTERED LOGO -->
    <div class="logo">
        @if(file_exists(public_path('img/logo.png')))
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
        @endif
    </div>
    
    <!-- CENTERED RECEIPT CONTENT -->
    <div class="receipt-content">
        {{ str_repeat('=', 42) }}
{{ str_pad('NYAWASCO WATER COMPANY', 42, ' ', STR_PAD_BOTH) }}
{{ str_pad('P.O Box 255-40500 - NYAMIRA', 42, ' ', STR_PAD_BOTH) }}
{{ str_pad('Tel: 0787080455', 42, ' ', STR_PAD_BOTH) }}
{{ str_repeat('-', 42) }}
{{ str_pad('OFFICIAL RECEIPT', 42, ' ', STR_PAD_BOTH) }}
{{ str_repeat('-', 42) }}
Date: {{ $receiptData['date'] }}
Receipt: {{ $receiptData['receipt_number'] }}
Bill: {{ $receiptData['bill_number'] }}
{{ str_repeat('-', 42) }}
CUSTOMER INFORMATION:
{{ str_repeat('-', 42) }}
Name: {{ $receiptData['customer_name'] }}
Acct: {{ $receiptData['customer_number'] }}
Phone: {{ $receiptData['customer_phone'] }}
Meter: {{ $receiptData['meter_number'] }}
{{ str_repeat('-', 42) }}
BILLING DETAILS:
{{ str_repeat('-', 42) }}
Period: {{ $receiptData['billing_period'] }}
Consumption: {{ $receiptData['consumption'] }}
Rate: {{ $receiptData['rate'] }}
{{ str_repeat('-', 42) }}
AMOUNT BREAKDOWN:
{{ str_repeat('-', 42) }}
Subtotal: {{ $receiptData['subtotal'] }}
@if(floatval(str_replace(['KSh ', ','], '', $receiptData['vat'])) > 0)
VAT: {{ $receiptData['vat'] }}
@endif
{{ str_repeat('-', 42) }}
TOTAL DUE: {{ $receiptData['total_amount'] }}
Amount Paid: {{ $receiptData['amount_paid'] }}
BALANCE: {{ $receiptData['balance'] }}
{{ str_repeat('-', 42) }}
STATUS: {{ $receiptData['payment_status'] }}
Due Date: {{ $receiptData['due_date'] }}
{{ str_repeat('-', 42) }}
{{ $receiptData['footer_message'] }}
{{ str_repeat('=', 42) }}
Printed: {{ $receiptData['printed_date'] }}
{{ str_repeat('*', 42) }}
M-PESA Paybill: 247 247
Acc No: 483133#{{ $receiptData['customer_number'] }}
{{ str_repeat('*', 42) }}
    </div>
    
    <!-- CENTERED PRINT BUTTON -->
    <button class="print-btn no-print" onclick="window.print()">
        PRINT RECEIPT
    </button>
    
    <script>
        // Optional: Auto-print after 1 second
        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
</body>
</html>