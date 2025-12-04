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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print, .print-actions {
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
            .print-actions {
                text-align: center;
                margin-top: 20px;
                padding: 10px;
                background: #f5f5f5;
            }
        }

        .receipt-content {
            white-space: pre;
            line-height: 1.2;
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
        
        /* Print status message */
        .print-status {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body>
    <!-- Print status message -->
    <div class="print-status" id="printStatus">
        <i class="fas fa-print mr-2"></i>Printing in progress...
    </div>

    <div class="receipt-content">
        @include('bills.receipts.thermal')
    </div>

    <div class="print-actions no-print">
        <button class="btn btn-print" onclick="printReceipt()">
            <i class="fas fa-print"></i> Print Now
        </button>
        <button class="btn btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Close Window
        </button>
        <p style="font-size: 12px; margin-top: 10px; color: #666;">
            If printing doesn't start automatically, click "Print Now"
        </p>
    </div>

    <script>
        // Show print status
        function showPrintStatus() {
            const statusEl = document.getElementById('printStatus');
            statusEl.style.display = 'block';
        }

        // Hide print status
        function hidePrintStatus() {
            const statusEl = document.getElementById('printStatus');
            statusEl.style.display = 'none';
        }

        // Main print function
        function printReceipt() {
            showPrintStatus();
            
            // Small delay to ensure status is visible
            setTimeout(() => {
                window.print();
            }, 100);
            
            // Hide status after a delay
            setTimeout(hidePrintStatus, 2000);
        }

        // Auto-print immediately when page loads
        window.onload = function() {
            // Show printing status
            showPrintStatus();
            
            // Print immediately
            setTimeout(() => {
                window.print();
            }, 300);
            
            // Auto-close after printing (optional - can be enabled if needed)
            window.onafterprint = function() {
                // Option 1: Auto-close after printing
                // setTimeout(function() {
                //     window.close();
                // }, 1000);
                
                // Option 2: Just hide status
                hidePrintStatus();
            };
        };

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printReceipt();
            }
            // Escape to close
            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Handle print dialog cancel
        let beforePrint = function() {
            showPrintStatus();
        };

        let afterPrint = function() {
            hidePrintStatus();
        };

        // Support for both Chrome and Firefox
        if (window.matchMedia) {
            let mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }

        // Fallback for older browsers
        window.onbeforeprint = beforePrint;
        window.onafterprint = afterPrint;
    </script>
</body>
</html>