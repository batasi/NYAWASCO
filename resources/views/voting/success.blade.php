@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
        <p class="text-gray-600 mb-6">Your votes have been successfully cast. Thank you for participating!</p>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
            Back to Contest
        </a>
    </div>
</div>
@endsection
