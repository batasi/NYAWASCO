@extends('layouts.app')

@section('title', 'Vendor Details - ' . $vendor->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vendor Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($vendor->avatar)
                        <img src="{{ asset($vendor->avatar) }}" alt="Avatar" class="img-circle img-fluid" style="width: 100px; height: 100px;">
                        @else
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <span class="text-white font-weight-bold" style="font-size: 2rem;">{{ strtoupper(substr($vendor->name, 0, 1)) }}</span>
                        </div>
                        @endif
                    </div>

                    <h4 class="text-center">{{ $vendor->business_name ?? $vendor->name }}</h4>
                    <p class="text-muted text-center">{{ $vendor->services_offered ?? 'No services specified' }}</p>

                    <hr>

                    <p><strong>Contact Person:</strong> {{ $vendor->name }}</p>
                    <p><strong>Email:</strong> {{ $vendor->email }}</p>
                    <p><strong>Phone:</strong> {{ $vendor->contact_number ?? $vendor->phone ?? 'N/A' }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                    <p><strong>Verified:</strong>
                        <span class="badge {{ $vendor->is_verified ? 'bg-success' : 'bg-warning' }}">
                            {{ $vendor->is_verified ? 'Verified' : 'Unverified' }}
                        </span>
                    </p>
                    <p><strong>Member Since:</strong> {{ $vendor->created_at->format('M j, Y') }}</p>

                    @if($vendor->about)
                    <hr>
                    <p><strong>About:</strong></p>
                    <p>{{ $vendor->about }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Business Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Business Name:</strong> {{ $vendor->business_name ?? 'N/A' }}</p>
                            <p><strong>Contact Number:</strong> {{ $vendor->contact_number ?? 'N/A' }}</p>
                            <p><strong>Services Offered:</strong> {{ $vendor->services_offered ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> {{ $vendor->address ?? 'N/A' }}</p>
                            <p><strong>City:</strong> {{ $vendor->city ?? 'N/A' }}</p>
                            <p><strong>Country:</strong> {{ $vendor->country ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($vendor->website)
                    <p><strong>Website:</strong> <a href="{{ $vendor->website }}" target="_blank">{{ $vendor->website }}</a></p>
                    @endif
                </div>
            </div>

            <!-- Additional vendor-specific content can go here -->
        </div>
    </div>
</div>
@endsection