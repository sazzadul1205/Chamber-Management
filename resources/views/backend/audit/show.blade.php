@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Audit Log Details</h2>
                <p class="text-gray-600 mt-1">View detailed information about this activity</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.audit.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Logs</span>
                </a>
            </div>
        </div>

        <!-- LOG DETAILS CARD -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Log Information</h3>
                <p class="text-sm text-gray-600 mt-1">Log ID: #{{ $log->id }}</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Basic Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Action:</span>
                                <span class="font-medium capitalize">{{ $log->action }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $log->status == 'success' ? 'bg-green-100 text-green-800' :
        ($log->status == 'failed' ? 'bg-red-100 text-red-800' :
            'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Model:</span>
                                <span class="font-medium">{{ $log->model_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Model ID:</span>
                                <span class="font-mono">{{ $log->subject_id ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Timestamps</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time Ago:</span>
                                <span class="font-medium">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium">{{ $log->updated_at->format('M d, Y H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- USER INFO -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">User Information</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-lg">
                                    {{ substr($log->user->full_name ?? 'S', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $log->user->full_name ?? 'System' }}</p>
                                <p class="text-sm text-gray-600">User ID: {{ $log->user_id ?? 'N/A' }}</p>
                                @if($log->user)
                                    <p class="text-sm text-gray-600">{{ $log->user->email ?? '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Description</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-800">{{ $log->description }}</p>
                    </div>
                </div>

                <!-- NETWORK INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Network Information</h4>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-600 block text-sm mb-1">IP Address:</span>
                                <span class="font-mono bg-gray-100 px-3 py-1 rounded">{{ $log->ip_address ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 block text-sm mb-1">User Agent:</span>
                                <div class="bg-gray-100 px-3 py-2 rounded text-sm font-mono">
                                    {{ $log->user_agent ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Request Information</h4>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-600 block text-sm mb-1">HTTP Method:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium 
                                        {{ $log->method == 'GET' ? 'bg-blue-100 text-blue-800' :
        ($log->method == 'POST' ? 'bg-green-100 text-green-800' :
            ($log->method == 'PUT' ? 'bg-yellow-100 text-yellow-800' :
                ($log->method == 'DELETE' ? 'bg-red-100 text-red-800' :
                    'bg-gray-100 text-gray-800'))) }}">
                                    {{ $log->method ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 block text-sm mb-1">URL:</span>
                                <div class="bg-gray-100 px-3 py-2 rounded text-sm truncate" title="{{ $log->url }}">
                                    {{ $log->url ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DATA CHANGES -->
                @if($oldValues || $newValues)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Data Changes</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($oldValues)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Old Values</h5>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <pre
                                            class="text-sm text-red-800 whitespace-pre-wrap">{{ json_encode($oldValues, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif

                            @if($newValues)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">New Values</h5>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <pre
                                            class="text-sm text-green-800 whitespace-pre-wrap">{{ json_encode($newValues, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- RAW DATA -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Raw Data</h4>
                    <div class="bg-gray-900 rounded-lg p-4">
                        <pre
                            class="text-sm text-gray-300 whitespace-pre-wrap">{{ json_encode($log->toArray(), JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <div class="flex justify-between items-center pt-4">
            <div class="text-sm text-gray-600">
                <p>Use this information for debugging and security investigations</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('backend.audit.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Back to List
                </a>
            </div>
        </div>
    </div>
@endsection