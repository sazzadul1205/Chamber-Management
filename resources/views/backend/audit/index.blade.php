@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Audit Logs</h2>
                <p class="text-gray-600 mt-1">Track system activities and user actions</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <!-- Export Button -->
                <a href="{{ route('backend.audit.export', request()->query()) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                <!-- Clear Logs Button -->
                <button type="button" 
                        data-modal-target="clearLogsModal"
                        class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Clear Old Logs</span>
                </button>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-green-800 font-medium">Success</p>
                    <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-red-800 font-medium">Error</p>
                    <p class="text-red-700 text-sm mt-1">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- STATISTICS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Logs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_logs']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Successful</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['success_logs']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Failed</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['failed_logs']) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Warnings</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['warning_logs']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.67 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="bg-white rounded-lg shadow p-5">
            <form method="GET" action="{{ route('backend.audit.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <!-- Quick Date Filters -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quick Filter</label>
                        <select name="time_period" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Select Period</option>
                            @foreach($filterData['time_periods'] as $value => $label)
                                <option value="{{ $value }}" {{ request('time_period') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">All Actions</option>
                            @foreach($filterData['actions'] as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">All Users</option>
                            @foreach($filterData['users'] as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">All Status</option>
                            @foreach($filterData['statuses'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Model Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <select name="subject_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">All Models</option>
                            @foreach($filterData['models'] as $model)
                                <option value="{{ $model }}" {{ request('subject_type') == $model ? 'selected' : '' }}>
                                    {{ $model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <!-- IP Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                        <input type="text" name="ip_address" value="{{ request('ip_address') }}"
                               placeholder="192.168.1.1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <!-- Description Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="description" value="{{ request('description') }}"
                               placeholder="Search description..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <div class="text-sm text-gray-500">
                        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('backend.audit.index') }}"
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Activity Logs</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Results per page:</span>
                        <select onchange="window.location.href = this.value" class="border border-gray-300 rounded text-sm px-2 py-1">
                            @foreach([10, 25, 50, 100] as $perPage)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $perPage]) }}"
                                        {{ $logs->perPage() == $perPage ? 'selected' : '' }}>
                                    {{ $perPage }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    #{{ $log->id }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900">{{ $log->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <span class="text-gray-500">{{ $log->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ substr($log->user->full_name ?? 'S', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $log->user->full_name ?? 'System' }}
                                            </span>
                                            <br>
                                            <span class="text-xs text-gray-500">
                                                ID: {{ $log->user_id ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $log->action == 'created' ? 'bg-green-100 text-green-800' :
                                           ($log->action == 'updated' ? 'bg-blue-100 text-blue-800' :
                                           ($log->action == 'deleted' ? 'bg-red-100 text-red-800' :
                                           ($log->action == 'viewed' ? 'bg-gray-100 text-gray-800' :
                                           'bg-purple-100 text-purple-800'))) }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <span class="font-medium">{{ $log->model_name }}</span>
                                        @if($log->subject_id)
                                            <br>
                                            <span class="text-xs text-gray-500">ID: {{ $log->subject_id }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 truncate" title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </p>
                                        @if($log->old_values || $log->new_values)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                Has Changes
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $log->status == 'success' ? 'bg-green-100 text-green-800' :
                                           ($log->status == 'failed' ? 'bg-red-100 text-red-800' :
                                           'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="text-sm font-mono text-gray-600">{{ $log->ip_address }}</span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('backend.audit.show', $log->id) }}"
                                           class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition"
                                           data-tooltip="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Audit Logs Found</h3>
                                        <p class="text-gray-500">Try changing your filters or perform some actions</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <!-- STATISTICS PANEL -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Users -->
            <div class="bg-white rounded-lg shadow p-5">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Most Active Users
                </h4>
                
                <div class="space-y-3">
                    @forelse($stats['top_users'] as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">
                                        {{ substr($user->user->full_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->user->full_name }}</p>
                                    <p class="text-xs text-gray-500">User ID: {{ $user->user_id }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded">
                                {{ $user->activity_count }} actions
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No user activity data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-5">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Recent Activity Summary
                </h4>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Last Hour</span>
                            <span class="text-sm font-bold text-blue-600">{{ $stats['recent_activity']['last_hour'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ min(($stats['recent_activity']['last_hour'] / max($stats['recent_activity']['last_7_days'], 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Last 24 Hours</span>
                            <span class="text-sm font-bold text-green-600">{{ $stats['recent_activity']['last_24_hours'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" 
                                 style="width: {{ min(($stats['recent_activity']['last_24_hours'] / max($stats['recent_activity']['last_7_days'], 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Last 7 Days</span>
                            <span class="text-sm font-bold text-purple-600">{{ $stats['recent_activity']['last_7_days'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" 
                                 style="width: {{ min(($stats['recent_activity']['last_7_days'] / max($stats['total_logs'], 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION DISTRIBUTION -->
        @if(!empty($stats['action_distribution']))
            <div class="bg-white rounded-lg shadow p-5">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Action Distribution
                </h4>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($stats['action_distribution'] as $action => $count)
                        <div class="border border-gray-200 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-gray-900">{{ $count }}</div>
                            <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $action) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- INFORMATION CARD -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">About Audit Logs</h4>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Audit logs track all user actions and system activities for security and compliance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Logs are automatically generated for create, update, delete, and view operations</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Export logs regularly for archiving and compliance purposes</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Regularly clear old logs to maintain system performance</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CLEAR LOGS MODAL -->
    <div id="clearLogsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clear Old Audit Logs
                </h3>
            </div>

            <form method="POST" action="{{ route('backend.audit.clear') }}" class="p-6 space-y-4">
                @csrf

                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-yellow-800 font-medium">Warning!</p>
                            <p class="text-yellow-700 text-sm mt-1">
                                This action cannot be undone. All logs older than the specified days will be permanently deleted.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delete logs older than (days)
                    </label>
                    <select name="days" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select days</option>
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days (Recommended)</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Logs older than this will be permanently deleted
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="confirm" required class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">
                            I understand this action cannot be undone
                        </span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="backup" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">
                            Export logs before clearing (recommended)
                        </span>
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" data-modal-hide="clearLogsModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SEARCH MODAL -->
    <div id="searchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search Audit Logs
                </h3>
            </div>

            <div class="p-6">
                <div class="relative">
                    <input type="text" id="searchInput"
                        placeholder="Search by description, user, action, IP..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-11 focus:ring-2 focus:ring-blue-400">
                    <div class="absolute left-3 top-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div id="searchResults" class="mt-4 max-h-64 overflow-y-auto hidden">
                    <!-- Results will be populated here -->
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="button" data-modal-hide="searchModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal management
            const modals = ['clearLogsModal', 'searchModal'];
            
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                // Close buttons
                modal.querySelectorAll('[data-modal-hide]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    });
                });

                // Close when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            });

            // Tooltip hover effect
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                const tooltipText = element.getAttribute('data-tooltip');
                
                // Create tooltip element
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute bottom-full mb-1 hidden bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50';
                tooltip.textContent = tooltipText;
                
                element.appendChild(tooltip);
                
                element.addEventListener('mouseenter', function() {
                    tooltip.classList.remove('hidden');
                });
                
                element.addEventListener('mouseleave', function() {
                    tooltip.classList.add('hidden');
                });
            });

            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (modal && !modal.classList.contains('hidden')) {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                }
            });

            // Quick date filter change
            const timePeriodSelect = document.querySelector('select[name="time_period"]');
            if (timePeriodSelect) {
                timePeriodSelect.addEventListener('change', function() {
                    if (this.value) {
                        const today = new Date();
                        const dateFrom = document.querySelector('input[name="date_from"]');
                        const dateTo = document.querySelector('input[name="date_to"]');
                        
                        switch(this.value) {
                            case 'today':
                                dateFrom.value = today.toISOString().split('T')[0];
                                dateTo.value = today.toISOString().split('T')[0];
                                break;
                            case 'yesterday':
                                const yesterday = new Date(today);
                                yesterday.setDate(yesterday.getDate() - 1);
                                dateFrom.value = yesterday.toISOString().split('T')[0];
                                dateTo.value = yesterday.toISOString().split('T')[0];
                                break;
                            case 'last_7_days':
                                const last7Days = new Date(today);
                                last7Days.setDate(last7Days.getDate() - 7);
                                dateFrom.value = last7Days.toISOString().split('T')[0];
                                dateTo.value = today.toISOString().split('T')[0];
                                break;
                            case 'last_30_days':
                                const last30Days = new Date(today);
                                last30Days.setDate(last30Days.getDate() - 30);
                                dateFrom.value = last30Days.toISOString().split('T')[0];
                                dateTo.value = today.toISOString().split('T')[0];
                                break;
                            case 'this_month':
                                dateFrom.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                                dateTo.value = today.toISOString().split('T')[0];
                                break;
                            case 'last_month':
                                const firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                                const lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                                dateFrom.value = firstDayLastMonth.toISOString().split('T')[0];
                                dateTo.value = lastDayLastMonth.toISOString().split('T')[0];
                                break;
                        }
                        
                        // Auto-submit form
                        this.form.submit();
                    }
                });
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const searchModal = document.getElementById('searchModal');
            
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    
                    if (this.value.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }
                    
                    searchTimeout = setTimeout(() => {
                        performSearch(this.value);
                    }, 500);
                });
                
                async function performSearch(query) {
                    try {
                        const response = await fetch('/audit-logs/search', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ query: query })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            if (data.results.length > 0) {
                                let html = '<div class="space-y-2">';
                                
                                data.results.forEach(result => {
                                    html += `
                                        <a href="${result.show_url}" 
                                           class="block p-3 border border-gray-200 rounded hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">${result.description}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-xs text-gray-600">${result.user}</span>
                                                        <span class="text-xs text-gray-500">•</span>
                                                        <span class="text-xs text-gray-600">${result.created_at}</span>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                    bg-${result.status_color}-100 text-${result.status_color}-800">
                                                    ${result.action}
                                                </span>
                                            </div>
                                        </a>
                                    `;
                                });
                                
                                html += '</div>';
                                searchResults.innerHTML = html;
                                searchResults.classList.remove('hidden');
                            } else {
                                searchResults.innerHTML = `
                                    <div class="text-center py-4">
                                        <p class="text-gray-500">No results found</p>
                                    </div>
                                `;
                                searchResults.classList.remove('hidden');
                            }
                        }
                    } catch (error) {
                        console.error('Search failed:', error);
                    }
                }
            }
        });
    </script>

    <style>
        select:focus, input:focus {
            outline: none;
            ring-width: 2px;
        }
        
        .max-w-xs {
            max-width: 20rem;
        }
    </style>
@endsection