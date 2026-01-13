
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-2 sm:px-2 lg:px-4 py-8">
            <div class="text-center">
                <div class="flex justify-center mb-7">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-white/95 to-white/85 backdrop-blur-2xl rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-105 transition-all duration-500">
                            <svg class="w-20 h-20 text-indigo-600 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight animate-fade-in-up">
                    System Backups
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-orange-300 to-pink-300 animate-pulse">
                        & Management
                    </span>
                </h1>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Search & Filters -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                               placeholder="Search backups and logs..."
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500 text-lg shadow-sm">
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        Active Tab: <span class="font-semibold text-indigo-600 capitalize">{{ str_replace('_', ' ', $activeTab) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <button wire:click="switchTab('configurations')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'configurations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Backup Configurations ({{ $systemBackups->total() }})
                    </button>
                    <button wire:click="switchTab('logs')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'logs' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Backup Logs ({{ $backupLogs->total() }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- Configurations Tab -->
        @if($activeTab === 'configurations')
        <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-50 to-orange-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Backup Configurations</h3>
                        <p class="text-gray-600 mt-1">Configure automated system backups</p>
                    </div>
                    <button wire:click="openModal"
                            class="bg-gradient-to-r from-red-500 to-orange-600 text-white px-6 py-3 rounded-xl hover:from-red-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Backup Config
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-red-50 to-orange-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Configuration</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Schedule</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Components</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Run</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($systemBackups as $backup)
                        <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-orange-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ $backup->name }}</div>
                                        @if($backup->type === 'remote')
                                        <div class="text-sm text-gray-500">{{ $backup->remote_host }}</div>
                                        @else
                                        <div class="text-sm text-gray-500">{{ $backup->local_path ?? 'Default path' }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $backup->type === 'local' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($backup->type) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($backup->schedule_enabled)
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">{{ ucfirst($backup->schedule_frequency) }}</div>
                                    <div class="text-gray-500">{{ $backup->schedule_time }}</div>
                                    @if($backup->next_run_at)
                                    <div class="text-xs text-blue-600">Next: {{ $backup->next_run_at->format('M d, H:i') }}</div>
                                    @endif
                                </div>
                                @else
                                <span class="text-sm text-gray-500">Manual only</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($backup->database_backup)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Database
                                    </span>
                                    @endif
                                    @if($backup->reports_backup)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Reports
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($backup->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    Inactive
                                </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($backup->last_run_at)
                                <div class="text-sm text-gray-900">{{ $backup->last_run_at->format('M d, Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $backup->last_run_message ?? 'Success' }}</div>
                                @else
                                <span class="text-sm text-gray-500">Never</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="runBackup({{ $backup->id }})"
                                            class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        Run Now
                                    </button>
                                    <button wire:click="openModal({{ $backup->id }})"
                                            class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-orange-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $backup->id }})"
                                            class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-4 py-2 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Backup Configurations Found</h3>
                                <p class="text-xl text-gray-600 mb-6">Start by creating your first backup configuration</p>
                                <button wire:click="openModal"
                                        class="bg-gradient-to-r from-red-600 via-orange-600 to-yellow-600 text-white px-8 py-4 rounded-2xl hover:from-red-700 hover:via-orange-700 hover:to-yellow-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold text-lg flex items-center mx-auto">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create First Backup Config
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($systemBackups->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                {{ $systemBackups->links() }}
            </div>
            @endif
        </div>
        @endif

        <!-- Logs Tab -->
        @if($activeTab === 'logs')
        <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Backup Logs</h3>
                    <p class="text-gray-600 mt-1">View backup execution history and status</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Backup Name</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Components</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Execution Time</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Started At</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($backupLogs as $log)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900">{{ $log->systemBackup->name }}</div>
                                <div class="text-sm text-gray-500">{{ $log->systemBackup->type === 'remote' ? $log->systemBackup->remote_host : 'Local' }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $log->type === 'manual' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($log->status === 'success')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Success
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    Failed
                                </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($log->database_backup)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Database
                                    </span>
                                    @endif
                                    @if($log->reports_backup)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Reports
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($log->execution_time)
                                <div class="text-sm text-gray-900">{{ $log->execution_time }}s</div>
                                @if($log->file_size)
                                <div class="text-xs text-gray-500">{{ number_format($log->file_size / 1024 / 1024, 2) }} MB</div>
                                @endif
                                @else
                                <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $log->started_at->format('M d, Y H:i') }}</div>
                                @if($log->completed_at)
                                <div class="text-xs text-gray-500">to {{ $log->completed_at->format('H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($log->error_message)
                                <div class="text-sm text-red-600 max-w-xs truncate" title="{{ $log->error_message }}">
                                    {{ Str::limit($log->error_message, 50) }}
                                </div>
                                @elseif($log->file_path)
                                <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $log->file_path }}">
                                    {{ basename($log->file_path) }}
                                </div>
                                @else
                                <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                @if($log->status === 'success' && $log->file_path && file_exists($log->file_path))
                                <button wire:click="downloadBackup({{ $log->id }})"
                                        class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download
                                </button>
                                @else
                                <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Backup Logs Found</h3>
                                <p class="text-xl text-gray-600 mb-6">Backup logs will appear here after executions</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($backupLogs->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                {{ $backupLogs->links() }}
            </div>
            @endif
        </div>
        @endif

        <!-- Modal -->
        @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">{{ $editingId ? 'Edit' : 'Create New' }} Backup Configuration</h3>
                                    <p class="text-indigo-100">{{ $editingId ? 'Update the details below' : 'Fill in the details below' }}</p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="save" class="px-8 py-8">
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Name *</label>
                                <input type="text" wire:model="form.name"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.name') border-red-300 @enderror"
                                       placeholder="Enter backup name">
                                @error('form.name')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Backup Type *</label>
                                <select wire:model="form.type"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.type') border-red-300 @enderror">
                                    <option value="local">Local Backup</option>
                                    <option value="remote">Remote Backup (SSH)</option>
                                </select>
                                @error('form.type')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(($form['type'] ?? 'local') === 'local')
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Local Path</label>
                                <input type="text" wire:model="form.local_path"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.local_path') border-red-300 @enderror"
                                       placeholder="e.g., /var/backups or storage/backups">
                                @error('form.local_path')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif

                            @if(($form['type'] ?? 'local') === 'remote')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-700">Remote Host *</label>
                                    <input type="text" wire:model="form.remote_host"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.remote_host') border-red-300 @enderror"
                                           placeholder="e.g., 192.168.1.100">
                                    @error('form.remote_host')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-700">Remote User *</label>
                                    <input type="text" wire:model="form.remote_user"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.remote_user') border-red-300 @enderror"
                                           placeholder="e.g., backupuser">
                                    @error('form.remote_user')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Remote Path *</label>
                                <input type="text" wire:model="form.remote_path"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.remote_path') border-red-300 @enderror"
                                       placeholder="e.g., /home/backupuser/backups">
                                @error('form.remote_path')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">SSH Key (Private Key Content)</label>
                                <textarea wire:model="form.ssh_key"
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                          rows="4" placeholder="Paste your private SSH key here..."></textarea>
                            </div>
                            @endif

                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-700">Backup Components</label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="form.database_backup" id="database_backup" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="database_backup" class="ml-2 text-sm text-gray-700">Database</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="form.reports_backup" id="reports_backup" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="reports_backup" class="ml-2 text-sm text-gray-700">Reports</label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-700">Scheduling</label>
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" wire:model="form.schedule_enabled" id="schedule_enabled" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="schedule_enabled" class="ml-2 text-sm text-gray-700">Enable automated scheduling</label>
                                </div>

                                @if($form['schedule_enabled'] ?? false)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <label class="block text-sm font-semibold text-gray-700">Frequency *</label>
                                        <select wire:model="form.schedule_frequency"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.schedule_frequency') border-red-300 @enderror">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                        @error('form.schedule_frequency')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-sm font-semibold text-gray-700">Time *</label>
                                        <input type="time" wire:model="form.schedule_time"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.schedule_time') border-red-300 @enderror">
                                        @error('form.schedule_time')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Status *</label>
                                <select wire:model="form.status"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('form.status') border-red-300 @enderror">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('form.status')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                            <button type="button" wire:click="closeModal"
                                    class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 font-semibold">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-xl hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $editingId ? 'Update' : 'Create' }} Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center animate-fade-in-up">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ session('message') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center animate-fade-in-up">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
        @endif

        <style>
        @keyframes tilt {
            0%, 50%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(1deg);
            }
            75% {
                transform: rotate(-1deg);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-tilt {
            animation: tilt 10s infinite linear;
        }

        .animate-fade-in-up {
            animation: fade-in-up 1s ease-out forwards;
            opacity: 0;
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
        }

        .animation-delay-400 {
            animation-delay: 0.4s;
        }
        </style>

        <script>
        document.addEventListener('livewire:loaded', () => {
            // Auto-hide flash messages
            setTimeout(() => {
                const messages = document.querySelectorAll('[role="alert"]');
                messages.forEach(message => {
                    message.style.transition = 'all 0.5s cubic-bezier(0.4, 0.2, 1, 1)';
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-20px) scale(0.95)';
                    setTimeout(() => message.remove(), 500);
                });
            }, 3000);
        });
        </script>
    </div>
</div>