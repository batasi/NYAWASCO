{{-- resources/views/admin/sms/templates/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit SMS Template')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Template</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $template->name }}</p>
            </div>
            <a href="{{ route('admin.sms.templates.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Templates
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.sms.templates.update', $template) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Template Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $template->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="slug"
                           id="slug"
                           value="{{ old('slug', $template->slug) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Unique identifier, use lowercase and underscores</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category"
                            id="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category', $template->category) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center h-10">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $template->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div class="md:col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message Template <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message"
                              id="message"
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              onkeyup="updateCharCount()"
                              required>{{ old('message', $template->message) }}</textarea>
                    <div class="mt-2 flex justify-between items-center">
                        <div>
                            <span id="char-count">{{ strlen(old('message', $template->message)) }}</span> characters |
                            <span id="sms-count">{{ ceil(strlen(old('message', $template->message)) / 160) ?: 1 }}</span> SMS part(s)
                        </div>
                        <div class="text-xs text-gray-500">
                           Use @{{variable_name}} for placeholders
                        </div>
                    </div>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Placeholders Info -->
                <div class="md:col-span-2">
                    <div id="placeholders-info" class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Detected Placeholders</h4>
                        <div id="placeholders-list" class="flex flex-wrap gap-2"></div>
                        <p class="text-xs text-blue-600 mt-2">
                            These placeholders will be replaced with actual data when sending SMS.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <button type="button"
                        onclick="previewTemplate()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Preview
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Template Preview</h3>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p id="preview-text" class="text-gray-800 whitespace-pre-wrap"></p>
            </div>
            <div class="mt-2 text-sm text-gray-500">
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
function updateCharCount() {
    const message = document.getElementById('message').value;
    const count = message.length;
    const smsCount = Math.ceil(count / 160) || 1;

    document.getElementById('char-count').textContent = count;
    document.getElementById('sms-count').textContent = smsCount;

    // Extract placeholders
    const matches = message.match(/\{\{([^}]+)\}\}/g) || [];
    const placeholders = matches.map(m => m.replace(/\{|\}/g, ''));
    const uniquePlaceholders = [...new Set(placeholders)];

    const list = document.getElementById('placeholders-list');
    list.innerHTML = '';

    if (uniquePlaceholders.length > 0) {
        uniquePlaceholders.forEach(placeholder => {
            const badge = document.createElement('span');
            badge.className = 'px-2 py-1 bg-white text-blue-600 text-xs rounded-full border border-blue-200';
            badge.textContent = '{{' + placeholder + '}}';
            list.appendChild(badge);
        });
        document.getElementById('placeholders-info').classList.remove('hidden');
    } else {
        document.getElementById('placeholders-info').classList.add('hidden');
    }
}

function previewTemplate() {
    const message = document.getElementById('message').value;
    if (!message.trim()) {
        alert('Please enter a message to preview');
        return;
    }

    document.getElementById('preview-text').textContent = message;
    document.getElementById('preview-chars').textContent = message.length;
    document.getElementById('preview-parts').textContent = Math.ceil(message.length / 160) || 1;

    document.getElementById('preview-modal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Initialize
updateCharCount();
</script>
@endsection
