<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $ticketPurchase->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            position: relative;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 2px dashed #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .ticket-title {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .ticket-organizer {
            color: #6c757d;
            margin: 5px 0 0 0;
        }
        .ticket-section {
            margin-bottom: 20px;
        }
        .section-title {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .info-value {
            font-weight: bold;
            font-size: 14px;
        }
        .attendee-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .attendee-table th,
        .attendee-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .attendee-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .payment-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            text-align: right;
        }
        .qr-area {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            border: 1px dashed #6c757d;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #6c757d;
        }
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 36px;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Header -->
        <div class="ticket-header">
            <h1 class="ticket-title">{{ $ticketPurchase->event->title }}</h1>
            <p class="ticket-organizer">Organized by: {{ $ticketPurchase->event->organizer->name }}</p>
            <div class="barcode">*{{ $ticketPurchase->order_number }}*</div>
        </div>

        <!-- Event & Ticket Information -->
        <div class="info-grid">
            <div class="ticket-section">
                <h3 class="section-title">Event Information</h3>
                <div class="info-item">
                    <div class="info-label">Date & Time</div>
                    <div class="info-value">{{ $ticketPurchase->event->start_date->format('l, F d, Y \a\t h:i A') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $ticketPurchase->event->location }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Organizer</div>
                    <div class="info-value">{{ $ticketPurchase->event->organizer->name }}</div>
                </div>
            </div>

            <div class="ticket-section">
                <h3 class="section-title">Ticket Information</h3>
                <div class="info-item">
                    <div class="info-label">Order Number</div>
                    <div class="info-value">{{ $ticketPurchase->order_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ticket Type</div>
                    <div class="info-value">{{ $ticketPurchase->ticket->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quantity</div>
                    <div class="info-value">{{ $ticketPurchase->quantity }} ticket(s)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Purchase Date</div>
                    <div class="info-value">{{ $ticketPurchase->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Attendee Information -->
        @if($ticketPurchase->attendee_info && count($ticketPurchase->attendee_info) > 0)
        <div class="ticket-section">
            <h3 class="section-title">Attendee Information</h3>
            <table class="attendee-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketPurchase->attendee_info as $attendee)
                    <tr>
                        <td>{{ $attendee['name'] ?? 'N/A' }}</td>
                        <td>{{ $attendee['email'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Payment Summary -->
        <div class="payment-summary">
            <h3 class="section-title">Payment Summary</h3>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <div class="info-label">Subtotal</div>
                        <div class="info-value">KES {{ number_format($ticketPurchase->total_amount, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tax (16%)</div>
                        <div class="info-value">KES {{ number_format($ticketPurchase->tax_amount, 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Service Fee (2%)</div>
                        <div class="info-value">KES {{ number_format($ticketPurchase->fee_amount, 2) }}</div>
                    </div>
                </div>
                <div>
                    <div class="total-amount">
                        KES {{ number_format($ticketPurchase->final_amount, 2) }}
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Status</div>
                        <div class="info-value">{{ ucfirst($ticketPurchase->status) }}</div>
                    </div>
                    @if($ticketPurchase->paid_at)
                    <div class="info-item">
                        <div class="info-label">Paid On</div>
                        <div class="info-value">{{ $ticketPurchase->paid_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- QR Code Area -->
        <div class="qr-area">
            <h3>Present this ticket at the event</h3>
            <p>Scan the barcode above for entry</p>
            <p><strong>Order:</strong> {{ $ticketPurchase->order_number }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an electronic ticket. No need to print - you can show this on your mobile device.</p>
            <p>Generated on: {{ now()->format('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>
