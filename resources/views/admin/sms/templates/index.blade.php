{{-- resources/views/admin/sms/templates/index.blade.php --}}

@extends('layouts.app')

@section('title', 'SMS Templates')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">SMS Templates</h1>
                <p class="text-sm text-gray-600 mt-1">Manage reusable message templates</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sms.templates.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Template
                </a>
                <a href="{{ route('admin.sms.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to SMS
                </a>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($template->category == 'bill') bg-blue-100 text-blue-800
                                @elseif($template->category == 'payment') bg-green-100 text-green-800
                                @elseif($template->category == 'reminder') bg-yellow-100 text-yellow-800
                                @elseif($template->category == 'reading') bg-purple-100 text-purple-800
                                @elseif($template->category == 'alert') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($template->category) }}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            @if(!$template->is_active)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $template->name }}</h3>

                    @if($template->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $template->description }}</p>
                    @endif

                    <div class="bg-gray-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-gray-800 line-clamp-3">{{ $template->message }}</p>
                    </div>

                    @if($template->placeholders)
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-2">Available placeholders:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($template->placeholders as $placeholder)
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full border border-blue-200">
                                        {{'{{' . $placeholder . '}}'}}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t">
                        <div class="text-xs text-gray-500">
                            Created by: {{ $template->creator->name ?? 'System' }}
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.sms.templates.edit', $template) }}"
                               class="text-yellow-600 hover:text-yellow-900"
                               title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <button onclick="previewTemplate({{ $template->id }})"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Preview">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>

                            <form action="{{ route('admin.sms.templates.destroy', $template) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this template?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No templates</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new template.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.sms.templates.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Template
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="preview-title">Template Preview</h3>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p id="preview-message" class="text-gray-800 whitespace-pre-wrap"></p>
            </div>
            <div class="mt-3 text-sm text-gray-500">
                <span id="preview-chars">0</span> characters |
                <span id="preview-parts">1</span> SMS part(s)
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="closePreview()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function previewTemplate(templateId) {
    // In a real implementation, you'd fetch the template via AJAX
    // For now, we'll use the data attributes from the clicked template
    const templateCard = event.target.closest('.bg-white');
    const messageEl = templateCard.querySelector('.bg-gray-50 p');

    if (messageEl) {
        const message = messageEl.textContent;
        document.getElementById('preview-message').textContent = message;
        document.getElementById('preview-chars').textContent = message.length;
        document.getElementById('preview-parts').textContent = Math.ceil(message.length / 160) || 1;
        document.getElementById('preview-modal').classList.remove('hidden');
    }
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}
</script>
@endsection
