@extends('layouts.app')

@section('title', $title ?? 'Create Voting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Voting </h1>
            <p class="mt-2 text-lg text-gray-600">
                Create a <strong>voting</strong> or <strong>ticketing</strong> event quickly using the form below.
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
      
        </div>
    </div>
</div>

<!-- OpenStreetMap (Leaflet.js) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


@endsection
