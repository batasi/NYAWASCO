@extends('layouts.app')

@section('title', 'Attendee Details')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Attendee: {{ $attendee->name }}</h1>

    <div class="bg-white shadow rounded-lg p-4">
        <p><strong>Email:</strong> {{ $attendee->email }}</p>
        <p><strong>Phone:</strong> {{ $attendee->phone ?? 'N/A' }}</p>
        <p><strong>Role:</strong> {{ ucfirst($attendee->role) }}</p>
        <p><strong>Status:</strong>
            <span class="badge {{ $attendee->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $attendee->is_active ? 'Active' : 'Inactive' }}
            </span>
        </p>

        @if($attendee->ticketPurchases->count())
        <h2 class="mt-4 font-semibold">Recent Ticket Purchases</h2>
        <table class="table-auto w-full mt-2 border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2">Event</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendee->ticketPurchases as $ticket)
                <tr>
                    <td class="border px-4 py-2">{{ $ticket->event->title ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">${{ $ticket->total_amount }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($ticket->status) }}</td>
                    <td class="border px-4 py-2">{{ $ticket->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="mt-2">No ticket purchases found for this attendee.</p>
        @endif
    </div>

    <a href="{{ route('admin.attendees.index') }}" class="btn btn-secondary mt-4">Back to Attendees</a>
</div>
@endsection