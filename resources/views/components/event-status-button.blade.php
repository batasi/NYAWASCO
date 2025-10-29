@props(['event', 'color' => 'gray', 'nextStatus' => null, 'label' => '', 'confirm' => null])

@php
    $status = $event->status ?? 'unknown';
    $colors = [
        'draft' => 'bg-gray-200 text-gray-800',
        'pending_approval' => 'bg-yellow-200 text-yellow-800',
        'approved' => 'bg-green-200 text-green-800',
        'cancelled' => 'bg-red-200 text-red-800',
    ];

    $label = ucfirst(str_replace('_', ' ', $status));
    $color = $colors[$status] ?? 'bg-gray-100 text-gray-700';
@endphp

<span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
    {{ $label }}
</span>
