<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receiptData['bill_number'] }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            width: 80mm;
            margin: 0;
            padding: 0;
        }

        .receipt {
            white-space: pre;
            line-height: 1.1;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="receipt">
@include('bills.receipts.thermal')
    </div>
</body>
</html>
