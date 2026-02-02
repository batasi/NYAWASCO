@extends('layouts.app')
@props(['title' => 'Dashboard', 'subtitle' => 'Manage all activities in the system from a central position'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

<style>
/* Modern UI Color Scheme */
.professional-bg {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
    background-attachment: fixed;
}

.professional-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 1rem;
}

.professional-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(59, 130, 246, 0.1);
    transform: translateY(-4px) scale(1.02);
}

.accent-blue {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 50%, #bfdbfe 100%);
    border-left: 5px solid #3b82f6;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-green {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
    border-left: 5px solid #10b981;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-purple {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 50%, #e9d5ff 100%);
    border-left: 5px solid #8b5cf6;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-red {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 50%, #fecaca 100%);
    border-left: 5px solid #ef4444;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-yellow {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
    border-left: 5px solid #f59e0b;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-teal {
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #99f6e4 100%);
    border-left: 5px solid #14b8a6;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.accent-orange {
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 50%, #fdba74 100%);
    border-left: 5px solid #f97316;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.chart-container:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

.table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 2px solid rgba(226, 232, 240, 0.8);
    backdrop-filter: blur(5px);
}

.table-row {
    transition: all 0.2s ease;
}

.table-row:hover {
    background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(241, 245, 249, 0.6) 100%);
    transform: scale(1.01);
}

/* Modern scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Enhanced focus states */
.focus\:ring-blue-500:focus {
    --tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity));
}

/* Smooth animations */
* {
    scroll-behavior: smooth;
}
</style>

<div class="min-h-screen professional-bg">
    <!-- Modern Header -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 backdrop-blur-xl border-b border-white/20 shadow-2xl shadow-blue-900/20">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">{{ $title }}</h1>
                    <p class="text-base text-blue-100 font-medium leading-relaxed">{{ $subtitle }}</p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-xs text-blue-200 uppercase tracking-wide font-semibold">Welcome back</p>
                        <p class="text-lg font-semibold text-white">{{ Auth::user()->name ?? 'Admin' }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-xl shadow-indigo-500/25 ring-2 ring-white/50">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full px-6 sm:px-8 lg:px-12 py-12">

        <!-- Dashboard Overview -->
        <div class="mb-8">


            <!-- Customer Metrics -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V8H2v12h5m10 0a2 2 0 01-2 2H9a2 2 0 01-2-2m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4" />
                    </svg>
                    Customer Metrics
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @can('view customers')
                    <!-- Total Customers -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-blue flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5V8H2v12h5m10 0a2 2 0 01-2 2H9a2 2 0 01-2-2m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Total Customers</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $total_customers ?? ''}}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="#"
                                    class="font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200 flex items-center">
                                    View all customers
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Active Customers -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-green flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Active Customers</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $active_customers ?? '' }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="#"
                                    class="font-medium text-green-600 hover:text-green-700 transition-colors duration-200 flex items-center">
                                    Manage customers
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Suspended Customers -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-red flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Suspended Customers</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $inactive_customers ?? 0 }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="#"
                                    class="font-medium text-red-600 hover:text-red-700 transition-colors duration-200 flex items-center">
                                    Review suspended accounts
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Financial Metrics -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" />
                    </svg>
                    Financial Metrics
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @can('view bills')
                    <!-- Total Bills -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-yellow flex items-center justify-center">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4v9a2 2 0 01-2 2H9a2 2 0 01-2-2v-9m10 0H7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Total Bills</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $total_bills ?? ''}}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="{{ route('bills.index') }}"
                                    class="font-medium text-yellow-600 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                                    View all bills
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Unpaid Bills -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-orange flex items-center justify-center">
                                        <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Unpaid Bills</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $unpaid_bills ?? ''}}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="{{ route('bills.index') }}"
                                    class="font-medium text-orange-600 hover:text-orange-700 transition-colors duration-200 flex items-center">
                                    Review unpaid bills
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @can('view payments')
                    <!-- Total Payments -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-lg accent-purple flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <dt class="text-sm font-medium text-gray-600 truncate">Total Payments</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ $total_payments ?? '' }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                            <div class="text-sm">
                                <a href="{{ route('payments.index') }}"
                                    class="font-medium text-purple-600 hover:text-purple-700 transition-colors duration-200 flex items-center">
                                    View payment history
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Collected -->
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Total Revenue Collected</dt>
                                <dd class="text-2xl font-bold text-gray-900 mt-1">KES {{ number_format($total_revenue, 0) }}</dd>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg accent-teal flex items-center justify-center">
                                    <svg class="h-8 w-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v8m4-4H8m12 4V8a4 4 0 00-4-4H8a4 4 0 00-4 4v8a4 4 0 004 4h8a4 4 0 004-4z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- System Metrics -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    System Metrics
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4 text-center">
                            <div class="flex-shrink-0 mx-auto mb-4">
                                <div class="h-8 w-8 rounded-lg accent-blue flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <dt class="text-sm font-medium text-gray-600">System Users</dt>
                            <dd class="text-xl font-bold text-gray-900 mt-2">{{ $total_users }}</dd>
                        </div>
                    </div>
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4 text-center">
                            <div class="flex-shrink-0 mx-auto mb-4">
                                <div class="h-8 w-8 rounded-lg accent-purple flex items-center justify-center">
                                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <dt class="text-sm font-medium text-gray-600">Installed Meters</dt>
                            <dd class="text-xl font-bold text-gray-900 mt-2">{{ $installed_meters }}</dd>
                        </div>
                    </div>
                    <div class="professional-card rounded-lg overflow-hidden">
                        <div class="p-4 text-center">
                            <div class="flex-shrink-0 mx-auto mb-4">
                                <div class="h-8 w-8 rounded-lg accent-orange flex items-center justify-center">
                                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <dt class="text-sm font-medium text-gray-600">Pending Approvals</dt>
                            <dd class="text-xl font-bold text-gray-900 mt-2">{{ $pending_approvals }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-16">
            <!-- Monthly Billing vs Collections -->
            <div class="chart-container rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Monthly Billing vs Collections
                    </h3>
                    <button class="fullscreen-btn text-gray-500 hover:text-gray-700 p-1 transition-colors duration-200" data-chart="billingCollectionsChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="billingCollectionsChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Bill Status Distribution -->
            <div class="chart-container rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Bill Status Distribution
                    </h3>
                    <button class="fullscreen-btn text-gray-500 hover:text-gray-700 p-1 transition-colors duration-200" data-chart="billStatusChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-80 w-full flex items-center justify-center">
                    <canvas id="billStatusChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Additional Analytics Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-12">
            <!-- Customer Growth Trend -->
            <div class="watercolor-card rounded-xl p-6 chart-container transition-all duration-300 hover:shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        Customer Growth Trend
                    </h3>
                    <button class="fullscreen-btn text-gray-500 hover:text-gray-700 p-1 transition-colors duration-200" data-chart="customerGrowthChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="customerGrowthChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Revenue Trend -->
            <div class="watercolor-card rounded-xl p-6 chart-container transition-all duration-300 hover:shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        Revenue Trend
                    </h3>
                    <button class="fullscreen-btn text-gray-500 hover:text-gray-700 p-1 transition-colors duration-200" data-chart="revenueTrendChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="revenueTrendChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Third Row of Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-12">
            <!-- Connection Applications Status -->
            @can('view applications')
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Connection Applications
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="applicationsChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                @if(empty($application_status_counts))
                    <div class="text-center text-gray-500 py-8">
                        No application data available
                    </div>
                @else
                    <div class="relative h-64 w-full flex items-center justify-center">
                        <canvas id="applicationsChart" class="w-full h-full"></canvas>
                    </div>
                @endif
            </div>
            @endcan

            <!-- Bill Generation Trend -->
            @can('view bills')
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Bill Generation Trend
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="billGenerationChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="billGenerationChart" class="w-full h-full"></canvas>
                </div>
            </div>
            @endcan

        </div>

        <!-- Meter Status and Payment Status Row -->
        <div class="grid grid-cols-2 gap-10 mt-12">
            <!-- Meter Status Distribution -->
            @can('view meters')
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Meter Status Distribution
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="meterStatusChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="meterStatusChart" class="w-full h-full"></canvas>
                </div>
            </div>
            @endcan

            <!-- Payment Status Breakdown -->
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Payment Status Breakdown
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="paymentStatusChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="paymentStatusChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Fourth Row of Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-12">

            <!-- Payment Methods Chart -->
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Payment Methods
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="paymentMethodsChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="paymentMethodsChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Payment Timeliness -->
            @can('view bills')
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Payment Timeliness
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="paymentTimelinessChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="paymentTimelinessChart" class="w-full h-full"></canvas>
                </div>
            </div>
            @endcan
        </div>

        <!-- Meter Reading Trends -->
        <div class="grid grid-cols-1 gap-10 mt-12 place-items-center">
            @can('view meters')
            <div class="bg-white rounded-xl shadow p-6 chart-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Meter Reading Trends
                    </h3>
                    <button class="fullscreen-btn text-gray-400 hover:text-gray-600 p-1" data-chart="meterReadingChart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="meterReadingChart" class="w-full h-full"></canvas>
                </div>
            </div>
            @endcan
        </div>


        <!-- Users Table Section -->
        @can('view users')
        <div class="professional-card rounded-lg overflow-hidden mb-12 mt-12">
            <div class="px-8 py-6 table-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">System Users</h3>
                    <div class="flex space-x-3">
                        <div class="relative">
                            <input type="text" id="userSearch" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        @can('add users')
                        <button id="addUserBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add User
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        @forelse($recent_users as $user)
                        @php
                            $roleName = $user->roles->first()?->name ?? 'user';
                            $status = $user->email_verified_at ? 'Verified' : 'Pending';
                        @endphp
                        <tr class="table-row" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                        <span class="text-sm font-semibold text-blue-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $roleName === 'admin' ? 'bg-red-100 text-red-800' :
                                       ($roleName === 'organizer' ? 'bg-blue-100 text-blue-800' :
                                       ($roleName === 'vendor' ? 'bg-purple-100 text-purple-800' :
                                       ($roleName === 'manager' ? 'bg-purple-100 text-purple-800' :
                                       ($roleName === 'ict' ? 'bg-green-100 text-green-800' :
                                       ($roleName === 'ceo' ? 'bg-yellow-100 text-yellow-800' :
                                       ($roleName === 'chief' ? 'bg-indigo-100 text-indigo-800' :
                                       ($roleName === 'registrar' ? 'bg-pink-100 text-pink-800' :
                                       ($roleName === 'report' ? 'bg-orange-100 text-orange-800' :
                                       ($roleName === 'biller' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-800'))))))))) }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $status === 'Verified' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-start space-x-2">
                                    @can('edit users')
                                    <button class="edit-user-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $roleName }}"
                                            data-user-active="{{ $user->is_active ?? 1 }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    @endcan
                                    @can('edit permissions')
                                    <button class="permissions-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-role="{{ $roleName }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Permissions
                                    </button>
                                    @endcan
                                    @can('delete users')
                                    <button class="delete-user-btn inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($recent_users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ $recent_users->firstItem() }}</span>
                        to <span class="font-medium">{{ $recent_users->lastItem() }}</span>
                        of <span class="font-medium">{{ $recent_users->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        @if ($recent_users->onFirstPage())
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Previous
                        </span>
                        @else
                        <a href="{{ $recent_users->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Previous
                        </a>
                        @endif

                        @if ($recent_users->hasMorePages())
                        <a href="{{ $recent_users->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Next
                        </a>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Next
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endcan

        @can('view applications')
        <!-- Pending Approvals Table Section -->
        <div class="watercolor-card rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg mt-12">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-amber-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">Pending Approvals</h3>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                        {{ $pending_approvals }} items need approval
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-amber-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Submitted By</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <!-- Sample approval items - you would replace with actual data -->
                        @foreach ($pending_approval_items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->first_name }} {{ $item->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Plot: {{ $item->plot_number }}, House: {{ $item->house_number }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Water Application
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->email }}<br>
                                    <span class="text-gray-500 text-sm">{{ $item->phone }}</span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $item->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Pending Review
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Review
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-amber-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span class="font-medium">{{ $pending_approvals }}</span> pending items
                    </div>
                    <a href="" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200">
                        Review All Pending Items
                    </a>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>

<!-- Fullscreen Chart Modal -->
<div id="fullscreenModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="relative w-full max-w-6xl h-5/6 bg-white rounded-lg shadow-2xl flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 id="fullscreenTitle" class="text-xl font-semibold text-gray-800"></h3>
            <button id="closeFullscreen" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 p-4 min-h-0">
            <canvas id="fullscreenChart" width="800" height="600" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Add New User</h3>
                            <p class="text-blue-100 text-sm">Create a new system user account</p>
                        </div>
                    </div>
                    <button type="button" id="closeAddModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <form id="addUserForm">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="add_name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" name="name" id="add_name" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter full name">
                        </div>
                        <div>
                            <label for="add_username" class="block text-sm font-semibold text-gray-900 mb-2">
                                Username
                            </label>
                            <input
                                type="text"
                                name="username"
                                id="add_username"
                                required
                                autocomplete="off"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    focus:border-blue-500 sm:text-sm transition-colors duration-200"
                                placeholder="Enter unique username"
                            >
                        </div>

                        <div>
                            <label for="add_email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" name="email" id="add_email" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter email address">
                        </div>

                        <div>
                            <label for="add_password" class="block text-sm font-semibold text-gray-900 mb-2">Password</label>
                            <input type="password" name="password" id="add_password" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter password">
                        </div>

                        <div>
                            <label for="add_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <select name="role" id="add_role" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                <span class="text-sm text-gray-700">Active User</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" id="closeAddModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="createUserBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Create User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Edit User</h3>
                            <p class="text-blue-100 text-sm">Update user information</p>
                        </div>
                    </div>
                    <button type="button" id="closeEditModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="user_id">

                    <div class="space-y-5">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="edit_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="edit_user_name_preview" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="edit_user_email_preview" class="text-sm text-gray-600"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="edit_name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" name="name" id="edit_name" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                        </div>
                        <div>
                            <label for="edit_username" class="block text-sm font-semibold text-gray-900 mb-2">
                                Username
                            </label>
                            <input
                                type="text"
                                name="username"
                                id="edit_username"
                                required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    focus:border-blue-500 sm:text-sm transition-colors duration-200"
                            >
                        </div>

                        <div>
                            <label for="edit_email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" name="email" id="edit_email" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                        </div>

                        <div>
                            <label for="edit_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <select name="role" id="edit_role" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                <span class="text-sm text-gray-700">Active User</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" id="closeEditModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="updateUserBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Update User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Manage Permissions</h3>
                            <p class="text-purple-100 text-sm">Configure user permissions and access</p>
                        </div>
                    </div>
                    <button type="button" id="closePermissionsModal" class="text-white hover:text-purple-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6 max-h-96 overflow-y-auto">
                <form id="permissionsForm">
                    @csrf
                    <input type="hidden" id="perm_user_id" name="user_id">

                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="perm_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="perm_user_name" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="perm_user_email" class="text-sm text-gray-600"></p>
                                    <p class="text-xs text-gray-500">Role: <span id="perm_user_role" class="font-medium"></span></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Permissions</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($permissions_list as $permission)
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded transition-colors duration-200 permission-checkbox">
                                    <span class="text-sm font-medium text-gray-900 capitalize">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <span id="selectedPermissionsCount">0</span> permissions selected
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="closePermissionsModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" id="updatePermissionsBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-sm hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Update Permissions</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Complete User Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Modal Elements
    const addUserModal = document.getElementById('addUserModal');
    const editUserModal = document.getElementById('editUserModal');
    const permissionsModal = document.getElementById('permissionsModal');

    // Modal Management Functions
    function openModal(modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === addUserModal) closeModal(addUserModal);
        if (event.target === editUserModal) closeModal(editUserModal);
        if (event.target === permissionsModal) closeModal(permissionsModal);
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (!addUserModal.classList.contains('hidden')) closeModal(addUserModal);
            if (!editUserModal.classList.contains('hidden')) closeModal(editUserModal);
            if (!permissionsModal.classList.contains('hidden')) closeModal(permissionsModal);
        }
    });

    // Add User Modal Functionality
    const addUserBtn = document.getElementById('addUserBtn');
    const closeAddModal = document.getElementById('closeAddModal');
    const closeAddModalBtn = document.getElementById('closeAddModalBtn');
    const createUserBtn = document.getElementById('createUserBtn');

    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            openModal(addUserModal);
        });
    }

    if (closeAddModal) {
        closeAddModal.addEventListener('click', function() {
            closeModal(addUserModal);
        });
    }

    if (closeAddModalBtn) {
        closeAddModalBtn.addEventListener('click', function() {
            closeModal(addUserModal);
        });
    }

    if (createUserBtn) {
        createUserBtn.addEventListener('click', function() {
            createUser();
        });
    }

    // Edit User Modal Functionality
    const closeEditModal = document.getElementById('closeEditModal');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const updateUserBtn = document.getElementById('updateUserBtn');

    if (closeEditModal) {
        closeEditModal.addEventListener('click', function() {
            closeModal(editUserModal);
        });
    }

    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', function() {
            closeModal(editUserModal);
        });
    }

    if (updateUserBtn) {
        updateUserBtn.addEventListener('click', function() {
            updateUser();
        });
    }

    // Permissions Modal Functionality
    const closePermissionsModal = document.getElementById('closePermissionsModal');
    const closePermissionsModalBtn = document.getElementById('closePermissionsModalBtn');
    const updatePermissionsBtn = document.getElementById('updatePermissionsBtn');

    if (closePermissionsModal) {
        closePermissionsModal.addEventListener('click', function() {
            closeModal(permissionsModal);
        });
    }

    if (closePermissionsModalBtn) {
        closePermissionsModalBtn.addEventListener('click', function() {
            closeModal(permissionsModal);
        });
    }

    if (updatePermissionsBtn) {
        updatePermissionsBtn.addEventListener('click', function() {
            updatePermissions();
        });
    }

    // Delegated event listeners for dynamic content
    document.addEventListener('click', function(e) {
        // Edit User Button
        const editBtn = e.target.closest('.edit-user-btn');
        if (editBtn) {
            e.preventDefault();
            const userId = editBtn.dataset.userId;
            const userName = editBtn.dataset.userName;
            const userEmail = editBtn.dataset.userEmail;
            const userRole = editBtn.dataset.userRole;
            const userActive = editBtn.dataset.userActive === '1';

            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role').value = userRole;
            document.getElementById('edit_is_active').checked = userActive;
            document.getElementById('edit_user_name_preview').textContent = userName;
            document.getElementById('edit_user_email_preview').textContent = userEmail;

            const avatarContainer = document.getElementById('edit_user_avatar');
            const userAvatar = editBtn.closest('tr').querySelector('img')?.src || '';
            if (userAvatar) {
                avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
            } else {
                const initial = userName.charAt(0).toUpperCase();
                avatarContainer.innerHTML = `<span class="text-lg font-semibold text-blue-700">${initial}</span>`;
            }

            openModal(editUserModal);
        }

        // Permissions Button
        const permBtn = e.target.closest('.permissions-btn');
        if (permBtn) {
            e.preventDefault();
            const userId = permBtn.dataset.userId;
            const userName = permBtn.dataset.userName;
            const userRole = permBtn.dataset.userRole;
            const userEmail = permBtn.closest('tr').querySelector('td:first-child .text-gray-500')?.textContent || '';

            document.getElementById('perm_user_id').value = userId;
            document.getElementById('perm_user_name').textContent = userName;
            document.getElementById('perm_user_email').textContent = userEmail;
            document.getElementById('perm_user_role').textContent = userRole;

            const avatarContainer = document.getElementById('perm_user_avatar');
            const userAvatar = permBtn.closest('tr').querySelector('img')?.src || '';
            if (userAvatar) {
                avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
            } else {
                const initial = userName.charAt(0).toUpperCase();
                avatarContainer.innerHTML = `<span class="text-lg font-semibold text-purple-700">${initial}</span>`;
            }

            // Load user permissions
            loadUserPermissions(userId);
            openModal(permissionsModal);
        }

        // Delete User Button
        const deleteBtn = e.target.closest('.delete-user-btn');
        if (deleteBtn) {
            e.preventDefault();
            const userId = deleteBtn.dataset.userId;
            const userName = deleteBtn.dataset.userName;
            deleteUser(userId, userName);
        }
    });

    // Permission checkboxes count update
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-checkbox')) {
            updatePermissionsCount();
        }
    });

    // Search functionality
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');

            rows.forEach(row => {
                const userName = row.querySelector('td:first-child .text-gray-900').textContent.toLowerCase();
                const userEmail = row.querySelector('td:first-child .text-gray-500').textContent.toLowerCase();
                const userRole = row.querySelector('td:nth-child(2) span').textContent.toLowerCase();

                if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || userRole.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // API Functions
    function createUser() {
        const formData = new FormData(document.getElementById('addUserForm'));

        // Show loading state
        const originalText = createUserBtn.innerHTML;
        createUserBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Creating...</span>
        `;
        createUserBtn.disabled = true;

        fetch('{{ route("admin.users.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User created successfully', 'success');
                closeModal(addUserModal);
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Failed to create user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while creating the user', 'error');
        })
        .finally(() => {
            createUserBtn.innerHTML = originalText;
            createUserBtn.disabled = false;
        });
    }

    function updateUser() {
        const userId = document.getElementById('edit_user_id').value;
        const formData = new FormData(document.getElementById('editUserForm'));

        // Show loading state
        const originalText = updateUserBtn.innerHTML;
        updateUserBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Updating...</span>
        `;
        updateUserBtn.disabled = true;

        fetch(`/admin/users/${userId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User updated successfully', 'success');
                closeModal(editUserModal);
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Failed to update user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while updating the user', 'error');
        })
        .finally(() => {
            updateUserBtn.innerHTML = originalText;
            updateUserBtn.disabled = false;
        });
    }

    function deleteUser(userId, userName) {
        if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
            return;
        }

        showNotification(`Deleting user "${userName}"...`, 'info');

        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User deleted successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Failed to delete user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while deleting the user', 'error');
        });
    }

    function loadUserPermissions(userId) {
        fetch(`/admin/users/${userId}/permissions`)
            .then(response => response.json())
            .then(data => {
                // Clear all checkboxes first
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Check the user's permissions
                if (data.permissions) {
                    data.permissions.forEach(permission => {
                        const checkbox = document.querySelector(`.permission-checkbox[value="${permission}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }

                updatePermissionsCount();
            })
            .catch(error => {
                console.error('Error loading permissions:', error);
                showNotification('Failed to load permissions', 'error');
            });
    }

    function updatePermissions() {
        const userId = document.getElementById('perm_user_id').value;
        const formData = new FormData(document.getElementById('permissionsForm'));

        // Show loading state
        const originalText = updatePermissionsBtn.innerHTML;
        updatePermissionsBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Updating...</span>
        `;
        updatePermissionsBtn.disabled = true;

        fetch(`/admin/users/${userId}/permissions`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Permissions updated successfully', 'success');
                closeModal(permissionsModal);
            } else {
                throw new Error(data.message || 'Failed to update permissions');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while updating permissions', 'error');
        })
        .finally(() => {
            updatePermissionsBtn.innerHTML = originalText;
            updatePermissionsBtn.disabled = false;
        });
    }

    function updatePermissionsCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        const countElement = document.getElementById('selectedPermissionsCount');
        if (countElement) {
            countElement.textContent = count;
        }
    }

    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.custom-notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `custom-notification fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg text-white transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            type === 'warning' ? 'bg-yellow-500' :
            'bg-blue-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' :
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                        type === 'warning' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z' :
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('opacity-0', 'transform', 'scale-95');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
});

// Chart.js initialization for dashboard charts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts
    // Monthly Billing vs Collections Chart
    const billingCollectionsCtx = document.getElementById('billingCollectionsChart');
    if (billingCollectionsCtx) {
        const billingCollectionsChart = new Chart(billingCollectionsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Billed',
                    data: @json($monthly_billed ?? []),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Monthly Collected',
                    data: @json($monthly_collected ?? []),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Billing vs Collections'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Bill Status Distribution Chart
    const billStatusCtx = document.getElementById('billStatusChart');
    if (billStatusCtx) {
        const billStatusData = @json($bill_status_counts ?? []);
        const billStatusChart = new Chart(billStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Unpaid', 'Overdue'],
                datasets: [{
                    data: [billStatusData.paid || 0, billStatusData.unpaid || 0, billStatusData.overdue || 0],
                    backgroundColor: [
                        'rgb(16, 185, 129)', // Green for paid
                        'rgb(245, 158, 11)', // Yellow for unpaid
                        'rgb(239, 68, 68)'   // Red for overdue
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Bill Status Distribution'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Customer Growth Chart
    const customerGrowthCtx = document.getElementById('customerGrowthChart');
    if (customerGrowthCtx) {
        const customerGrowthChart = new Chart(customerGrowthCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 12}, (_, i) => {
                    const date = new Date();
                    date.setMonth(date.getMonth() - (11 - i));
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'New Customers',
                    data: @json($customer_growth ?? []),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Meter Status Distribution Chart
    const meterStatusCtx = document.getElementById('meterStatusChart');
    if (meterStatusCtx) {
        const meterStatusData = @json($meter_status_counts ?? []);
        const meterStatusChart = new Chart(meterStatusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(meterStatusData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                datasets: [{
                    data: Object.values(meterStatusData),
                    backgroundColor: [
                        'rgb(16, 185, 129)',   // Green
                        'rgb(59, 130, 246)',   // Blue
                        'rgb(245, 158, 11)',   // Yellow
                        'rgb(239, 68, 68)',    // Red
                        'rgb(139, 92, 246)'    // Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
    if (paymentMethodsCtx) {
        let paymentMethodsData = @json($payment_methods_breakdown ?? []);
        if (!paymentMethodsData || paymentMethodsData.length === 0) {
            paymentMethodsData = [
                {method: 'M-Pesa', transactions: 0, amount: 0},
                {method: 'Cash', transactions: 0, amount: 0},
                {method: 'Bank Transfer', transactions: 0, amount: 0}
            ];
        }
        const paymentMethodsChart = new Chart(paymentMethodsCtx, {
            type: 'bar',
            data: {
                labels: paymentMethodsData.map(item => item.method.charAt(0).toUpperCase() + item.method.slice(1)),
                datasets: [{
                    label: 'Transactions',
                    data: paymentMethodsData.map(item => item.transactions),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }, {
                    label: 'Revenue (KES)',
                    data: paymentMethodsData.map(item => item.amount),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index) {
                                if (index === 1) { // Revenue dataset
                                    return 'KES ' + value.toLocaleString();
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Applications Status Chart
    const applicationsCtx = document.getElementById('applicationsChart');
    if (applicationsCtx) {
        const applicationsData = @json($application_status_counts ?? []);
        const applicationsChart = new Chart(applicationsCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(applicationsData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                datasets: [{
                    data: Object.values(applicationsData),
                    backgroundColor: [
                        'rgb(245, 158, 11)',   // Yellow for pending
                        'rgb(16, 185, 129)',   // Green for approved
                        'rgb(239, 68, 68)',    // Red for rejected
                        'rgb(59, 130, 246)'    // Blue for others
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Revenue Trend Chart
    const revenueTrendCtx = document.getElementById('revenueTrendChart');
    if (revenueTrendCtx) {
        const revenueTrendChart = new Chart(revenueTrendCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 12}, (_, i) => {
                    const date = new Date();
                    date.setMonth(date.getMonth() - (11 - i));
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: @json($revenue_trend ?? []),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Bill Generation Trend Chart
    const billGenerationCtx = document.getElementById('billGenerationChart');
    if (billGenerationCtx) {
        const billGenerationChart = new Chart(billGenerationCtx, {
            type: 'bar',
            data: {
                labels: Array.from({length: 12}, (_, i) => {
                    const date = new Date();
                    date.setMonth(date.getMonth() - (11 - i));
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Bills Generated',
                    data: @json($bill_generation_trend ?? []),
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Payment Status Breakdown Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        let paymentStatusData = @json($payment_status_breakdown ?? []);
        if (!paymentStatusData || paymentStatusData.length === 0) {
            paymentStatusData = [
                {status: 'completed', count: 0, total: 0},
                {status: 'pending', count: 0, total: 0},
                {status: 'failed', count: 0, total: 0}
            ];
        }
        const paymentStatusChart = new Chart(paymentStatusCtx, {
            type: 'pie',
            data: {
                labels: paymentStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                datasets: [{
                    data: paymentStatusData.map(item => item.count),
                    backgroundColor: [
                        'rgb(16, 185, 129)',   // Green for completed
                        'rgb(245, 158, 11)',   // Yellow for pending
                        'rgb(239, 68, 68)',    // Red for failed
                        'rgb(59, 130, 246)'    // Blue for others
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Timeliness Chart
    const paymentTimelinessCtx = document.getElementById('paymentTimelinessChart');
    if (paymentTimelinessCtx) {
        const timelinessData = @json($payment_timeliness ?? []);
        const paymentTimelinessChart = new Chart(paymentTimelinessCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(timelinessData).map(status => status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')),
                datasets: [{
                    data: Object.values(timelinessData),
                    backgroundColor: [
                        'rgb(16, 185, 129)',   // Green for on_time
                        'rgb(245, 158, 11)',   // Yellow for pending
                        'rgb(239, 68, 68)',    // Red for overdue/late
                        'rgb(156, 163, 175)'   // Gray for unknown
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }


    // Meter Reading Trends Chart
    const meterReadingCtx = document.getElementById('meterReadingChart');
    if (meterReadingCtx) {
        const meterReadingData = @json($meter_reading_trend ?? []);
        const meterReadingChart = new Chart(meterReadingCtx, {
            type: 'line',
            data: {
                labels: meterReadingData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Number of Readings',
                    data: meterReadingData.map(item => item.readings),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    yAxisID: 'y',
                    tension: 0.4
                }, {
                    label: 'Average Consumption',
                    data: meterReadingData.map(item => item.avg_consumption),
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Readings'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Average Consumption'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    }

    // Fullscreen functionality
    let currentFullscreenChart = null;
    let fullscreenChartInstance = null;

    document.addEventListener('click', function(e) {
        if (e.target.closest('.fullscreen-btn')) {
            const button = e.target.closest('.fullscreen-btn');
            const chartId = button.dataset.chart;
            const chartTitle = button.closest('.chart-container').querySelector('h3').textContent;

            openFullscreenChart(chartId, chartTitle);
        }
    });

    document.getElementById('closeFullscreen').addEventListener('click', closeFullscreenChart);
    document.getElementById('fullscreenModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFullscreenChart();
        }
    });

    function openFullscreenChart(chartId, title) {
        const modal = document.getElementById('fullscreenModal');
        const titleElement = document.getElementById('fullscreenTitle');
        const canvas = document.getElementById('fullscreenChart');

        titleElement.textContent = title;
        modal.classList.remove('hidden');

        // Destroy any existing fullscreen chart
        if (fullscreenChartInstance) {
            fullscreenChartInstance.destroy();
            fullscreenChartInstance = null;
        }

        // Get the original chart instance
        const originalChart = Chart.getChart(chartId);
        if (originalChart) {
            currentFullscreenChart = originalChart;

            // Create fullscreen chart with same data but optimized options
            const fullscreenConfig = {
                type: originalChart.config.type,
                data: originalChart.config.data,
                options: {
                    ...originalChart.config.options,
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        ...originalChart.config.options.plugins,
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        legend: {
                            ...originalChart.config.options.plugins?.legend,
                            position: 'bottom'
                        }
                    },
                    scales: originalChart.config.options.scales
                }
            };

            fullscreenChartInstance = new Chart(canvas, fullscreenConfig);
        } else {
            console.error('Original chart not found for ID:', chartId);
        }
    }

    function closeFullscreenChart() {
        const modal = document.getElementById('fullscreenModal');
        modal.classList.add('hidden');

        if (fullscreenChartInstance) {
            fullscreenChartInstance.destroy();
            fullscreenChartInstance = null;
        }
        currentFullscreenChart = null;
    }

});
</script>

@endsection
