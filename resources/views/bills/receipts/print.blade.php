<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt - {{ $receiptData['bill_number'] }}</title>
    <style>
        @media screen {
            body {
                background: #f0f0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            
            .receipt-container {
                background: white;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                border-radius: 8px;
                padding: 10px;
                max-width: 58mm;
            }
            
            .print-actions {
                text-align: center;
                margin-top: 20px;
                padding: 15px;
                background: #f5f5f5;
                border-radius: 5px;
            }
            
            .btn {
                padding: 10px 20px;
                margin: 0 5px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                font-weight: bold;
            }
            
            .btn-print {
                background: #4CAF50;
                color: white;
            }
            
            .btn-close {
                background: #f44336;
                color: white;
            }
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 58mm !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .receipt-container {
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Include the thermal receipt -->
        @include('bills.receipts.thermal')
        
        <!-- Print actions (only visible on screen) -->
        <div class="print-actions no-print">
            <button class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print Now
            </button>
            <button class="btn btn-close" onclick="window.close()">
                <i class="fas fa-times"></i> Close
            </button>
            <p style="font-size: 12px; margin-top: 10px; color: #666;">
                Note: This receipt is formatted for 58mm thermal printers.
            </p>
        </div>
    </div>
    
    <script>
        // Auto-print and close
        window.onload = function() {
            // Wait a bit for content to load
            setTimeout(function() {
                window.print();
                
                // Close after printing
                setTimeout(function() {
                    window.close();
                }, 1000);
            }, 300);
        };
        
        // Fallback for browsers that block auto-print
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 1000);
            }, 1000);
        });
        
        // Handle after print event
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 500);
        };
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>