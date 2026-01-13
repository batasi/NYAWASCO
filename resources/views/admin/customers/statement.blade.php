@extends('layouts.app')

@section('title', 'Customer Statement - NYAWASCO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Statement Header -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Statement</h1>
                <div class="flex items-center space-x-4 mt-2">
                    <div>
                        <p class="text-sm text-gray-600">Customer:</p>
                        <p class="font-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Account Number:</p>
                        <p class="font-semibold">{{ $customer->customer_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Statement Period:</p>
                        <p class="font-semibold">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Generated:</p>
                <p class="font-semibold">{{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Opening Balance</p>
                <p class="text-xl font-bold {{ $openingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    KSh {{ number_format(abs($openingBalance), 2) }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ $openingBalance >= 0 ? 'Customer Credit' : 'Customer Owed' }}
                </p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-red-600">Total Debits</p>
                <p class="text-xl font-bold text-red-600">
                    KSh {{ number_format($totalDebits, 2) }}
                </p>
                <p class="text-xs text-red-500">{{ $bills->count() }} bills</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600">Total Credits</p>
                <p class="text-xl font-bold text-green-600">
                    KSh {{ number_format($totalCredits, 2) }}
                </p>
                <p class="text-xs text-green-500">{{ $payments->count() }} payments</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-600">Closing Balance</p>
                <p class="text-xl font-bold {{ $closingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    KSh {{ number_format(abs($closingBalance), 2) }}
                </p>
                <p class="text-xs text-blue-500">
                    {{ $closingBalance >= 0 ? 'Customer Credit' : 'Customer Owed' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Opening Balance Row -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $startDate->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                Opening
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            —
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Opening Balance
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($openingBalance < 0)
                                <span class="text-red-600">KSh {{ number_format(abs($openingBalance), 2) }}</span>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($openingBalance > 0)
                                <span class="text-green-600">KSh {{ number_format($openingBalance, 2) }}</span>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $openingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            KSh {{ number_format(abs($openingBalance), 2) }}
                        </td>
                    </tr>

                    @php
                        $runningBalance = $openingBalance;
                    @endphp

                    <!-- Bills (Debits) -->
                    @foreach($bills as $bill)
                    @php
                        $runningBalance += $bill->total_amount;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $bill->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                Bill
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            {{ $bill->bill_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Water Bill - {{ $bill->billing_period_start?->format('M Y') ?? 'N/A' }}
                            <div class="text-xs text-gray-500">
                                Consumption: {{ number_format($bill->consumption, 2) }} m³
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            KSh {{ number_format($bill->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            —
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $runningBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            KSh {{ number_format(abs($runningBalance), 2) }}
                        </td>
                    </tr>
                    @endforeach

                    <!-- Payments (Credits) -->
                    @foreach($payments as $payment)
                    @php
                        $runningBalance -= $payment->amount;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->payment_date?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                Payment
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            {{ $payment->payment_no ?? $payment->receipt_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Payment {{ $payment->payment_method ? 'via ' . $payment->payment_method : '' }}
                            @if($payment->bill)
                                <div class="text-xs text-gray-500">
                                    For bill: {{ $payment->bill->bill_number }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            —
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                            KSh {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $runningBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            KSh {{ number_format(abs($runningBalance), 2) }}
                        </td>
                    </tr>
                    @endforeach

                    <!-- Meter Readings -->
                    @foreach($meterReadings as $reading)
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reading->reading_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Reading
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            {{ $reading->meter->meter_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Meter Reading
                            <div class="text-xs text-gray-500">
                                {{ number_format($reading->previous_reading, 2) }} → {{ number_format($reading->current_reading, 2) }} m³
                                (Usage: {{ number_format($reading->consumption, 2) }} m³)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            —
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            —
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-500">
                            —
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-between items-center no-print">
        <a href="{{ route('admin.customers.show', $customer) }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Profile
        </a>
        <div class="space-x-3">
            <a href="{{ route('admin.customers.statement.pdf', [
                'customer' => $customer,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]) }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <button onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-print mr-2"></i> Print Statement
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .container {
            padding: 0;
        }
        body {
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
    }
</style>
@endsection
