{{-- resources/views/admin/sms/show.blade.php --}}

@extends('layouts.app')

@section('title', 'SMS Details')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">SMS Details</h1>
                <p class="text-sm text-gray-600 mt-1">View complete information about this message</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sms.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>

                @if($smsLog->status == 'failed')
                    <form action="{{ route('admin.sms.retry', $smsLog) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                                onclick="return confirm('Retry sending this SMS?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Retry
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.sms.create', ['customer' => $smsLog->customer_id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Again
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Message Information</h2>
                </div>

                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="mb-6">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if($smsLog->status == 'sent') bg-green-100 text-green-800
                            @elseif($smsLog->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($smsLog->status == 'failed') bg-red-100 text-red-800
                            @elseif($smsLog->status == 'delivered') bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($smsLog->status) }}
                        </span>
                        @if($smsLog->status == 'failed' && $smsLog->error_message)
                            <span class="ml-2 text-sm text-red-600">({{ $smsLog->error_message }})</span>
                        @endif
                    </div>

                    <!-- Message Content -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Message Content</h3>
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $smsLog->message }}</p>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            {{ strlen($smsLog->message) }} characters |
                            {{ ceil(strlen($smsLog->message) / 160) }} SMS part(s)
                        </div>
                    </div>

                    <!-- Recipient Details -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Recipient</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <p class="text-sm font-medium">{{ $smsLog->recipient_phone }}</p>
                            </div>
                            @if($smsLog->customer)
                                <div>
                                    <p class="text-xs text-gray-500">Customer</p>
                                    <p class="text-sm font-medium">
                                        <a href="{{ route('admin.customers.show', $smsLog->customer_id) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $smsLog->customer->full_name }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $smsLog->customer->customer_number }}</p>
                                </div>
                            @endif
                            @if($smsLog->meter)
                                <div>
                                    <p class="text-xs text-gray-500">Meter</p>
                                    <p class="text-sm font-medium">
                                        <a href="{{ route('admin.meters.show', $smsLog->meter_id) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $smsLog->meter->meter_number }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Message Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Message Type</p>
                                <p class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $smsLog->message_type)) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Sender ID</p>
                                <p class="text-sm font-medium">{{ $smsLog->sender_id ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Cost</p>
                                <p class="text-sm font-medium">KSh {{ number_format($smsLog->cost ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Retry Count</p>
                                <p class="text-sm font-medium">{{ $smsLog->retry_count }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Timeline</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium">{{ $smsLog->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            @if($smsLog->sent_at)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Sent:</span>
                                    <span class="font-medium">{{ $smsLog->sent_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($smsLog->delivered_at)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Delivered:</span>
                                    <span class="font-medium">{{ $smsLog->delivered_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- API Response -->
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">API Response</h3>
                </div>
                <div class="p-4">
                    @if($smsLog->api_response)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <pre class="text-xs overflow-x-auto">{{ json_encode($smsLog->api_response, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-2">No API response data</p>
                    @endif

                    @if($smsLog->api_response_code)
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-600">Response Code:</span>
                            <span class="font-medium">{{ $smsLog->api_response_code }}</span>
                        </div>
                    @endif

                    @if($smsLog->api_response_message)
                        <div class="mt-1 flex justify-between text-sm">
                            <span class="text-gray-600">Message:</span>
                            <span class="font-medium">{{ $smsLog->api_response_message }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sent By -->
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">Sent By</h3>
                </div>
                <div class="p-4">
                    @if($smsLog->sender)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-medium">
                                    {{ substr($smsLog->sender->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">{{ $smsLog->sender->name }}</p>
                                <p class="text-xs text-gray-500">{{ $smsLog->sender->email }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">System Generated</p>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            @if($smsLog->metadata)
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold">Additional Metadata</h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <pre class="text-xs overflow-x-auto">{{ json_encode($smsLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
