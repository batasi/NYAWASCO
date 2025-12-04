<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt - {{ $receiptData['bill_number'] }}</title>
    <style>
        @media print {
            @page {
                size: 80mm auto; /* Thermal printer size */
                margin: 0;
            }
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                width: 80mm;
                margin: 0;
                padding: 5mm;
            }
            .no-print {
                display: none !important;
            }
        }

        @media screen {
            body {
                font-family: 'Courier New', monospace;
                font-size: 14px;
                width: 80mm;
                margin: 20px auto;
                border: 1px dashed #ccc;
                padding: 10mm;
                background: white;
            }
        }

        .receipt-content {
            white-space: pre;
            line-height: 1.2;
        }

        .print-actions {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background: #f5f5f5;
        }

        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print {
            background: #4CAF50;
            color: white;
        }

        .btn-close {
            background: #f44336;
            color: white;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-content">
@include('bills.receipts.thermal')
    </div>

    <div class="print-actions no-print">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Again
        </button>
        <button class="btn btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Close
        </button>
        <p style="font-size: 12px; margin-top: 10px; color: #666;">
            If print dialog doesn't open automatically, click "Print Again"
        </p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            // Small delay to ensure page is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);

            // Close window after printing (optional)
            window.onafterprint = function() {
                Uncomment if you want to auto-close after printing
                setTimeout(function() {
                    window.close();
                }, 1000);
            };
        };

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            // Escape to close
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>
