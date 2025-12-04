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
Acc No: 483133#AccountNoo
{{ str_repeat('*', 42) }}
