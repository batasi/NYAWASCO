@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <!-- Receipt Preview -->
        <div class="border-2 border-gray-300 p-4 font-mono text-sm">
            @foreach(explode("\n", view('bills.receipts.thermal', compact('receiptData'))->render()) as $line)
                <div class="whitespace-pre">{{ $line }}</div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'thermal']) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-print mr-2"></i> Print Thermal
            </a>
            <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'pdf']) }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </div>
</div>
@endsection
