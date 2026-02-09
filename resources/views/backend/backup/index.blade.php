@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Backup & Restore</h2>
                <p class="text-gray-600 mt-1">Manage system backups and restore points</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <!-- Quick Backup Button -->
                <button type="button" data-modal-target="createBackupModal"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Create Backup</span>
                </button>

                <!-- System Info Button -->
                <button type="button" onclick="loadSystemInfo()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>System Info</span>
                </button>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
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
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-red-800 font-medium">Error</p>
                    <p class="text-red-700 text-sm mt-1">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- STORAGE USAGE STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Storage</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $storageInfo['total_space'] }} MB</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Used Space</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $storageInfo['used_space'] }} MB</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full"
                            style="width: {{ ($storageInfo['used_space'] / $storageInfo['total_space']) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Available Space</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $storageInfo['free_space'] }} MB</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- BACKUP LIST -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Available Backups</h3>
                <p class="text-sm text-gray-600 mt-1">Total {{ count($backupFiles) }}
                    backup{{ count($backupFiles) !== 1 ? 's' : '' }} found</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Backup Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($backupFiles as $backup)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-100 rounded">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $backup['name'] }}</p>
                                            <p class="text-xs text-gray-500">by {{ $backup['created_by'] ?? 'System' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $backup['type'] === 'full' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($backup['type']) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="font-medium">{{ $backup['size'] }} MB</span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="text-sm">{{ $backup['created_at_formatted'] }}</span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="text-sm {{ $backup['age_days'] > 30 ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ $backup['age_days'] }} day{{ $backup['age_days'] !== 1 ? 's' : '' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-600">{{ $backup['description'] ?? 'No description' }}</p>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <!-- Download Button -->
                                        <a href="{{ route('backend.backup.download', $backup['name']) }}"
                                            class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition"
                                            data-tooltip="Download Backup">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>

                                        <!-- Restore Button -->
                                        <button type="button" data-modal-target="restoreModal"
                                            data-backup-name="{{ $backup['name'] }}" data-backup-type="{{ $backup['type'] }}"
                                            class="p-2 bg-green-100 text-green-600 rounded hover:bg-green-200 transition"
                                            data-tooltip="Restore Backup">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" data-modal-target="deleteBackupModal"
                                            data-backup-name="{{ $backup['name'] }}"
                                            class="p-2 bg-red-100 text-red-600 rounded hover:bg-red-200 transition"
                                            data-tooltip="Delete Backup">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Backups Found</h3>
                                        <p class="text-gray-500">Create your first backup to protect your data</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BACKUP SCHEDULE INFO -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mt-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">Backup Recommendations</h4>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Create weekly full backups for complete system protection</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Keep at least 3 months of backup history</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Store critical backups in external storage or cloud</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Test restore functionality regularly</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE BACKUP MODAL -->
    <div id="createBackupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Create New Backup
                </h3>
            </div>

            <form method="POST" action="{{ route('backend.backup.create') }}" class="p-6 space-y-4">
                @csrf

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Backup Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="relative">
                            <input type="radio" name="type" value="full" class="sr-only peer" checked>
                            <div
                                class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 rounded">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Full Backup</p>
                                        <p class="text-xs text-gray-500">Database + Files</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="type" value="database" class="sr-only peer">
                            <div
                                class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-green-100 rounded">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Database Only</p>
                                        <p class="text-xs text-gray-500">Tables & Records</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="type" value="files" class="sr-only peer">
                            <div
                                class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-purple-100 rounded">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Files Only</p>
                                        <p class="text-xs text-gray-500">Uploads & Storage</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description (Optional)
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
                        placeholder="Enter backup description, e.g., 'Before major update', 'Monthly backup'..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" data-modal-hide="createBackupModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Create Backup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RESTORE MODAL -->
    <div id="restoreModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Restore Backup
                </h3>
            </div>

            <form id="restoreForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('POST')

                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-yellow-800 font-medium">Warning!</p>
                            <p class="text-yellow-700 text-sm mt-1">
                                Restoring will overwrite existing data. The system will be put in maintenance mode during
                                restoration.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-gray-700 mb-2">
                        You are about to restore backup: <strong id="restoreBackupName"></strong>
                    </p>
                    <p class="text-sm text-gray-500">Backup Type: <span id="restoreBackupType"></span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Restore Type
                    </label>
                    <select name="restore_type" id="restoreType"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                        <option value="full">Full Restore (Database + Files)</option>
                        <option value="database">Database Only</option>
                        <option value="files">Files Only</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="confirm" required class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">
                            I understand this action cannot be undone
                        </span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="backup_current" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">
                            Create backup of current state before restoring
                        </span>
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" data-modal-hide="restoreModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Start Restore
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE BACKUP MODAL -->
    <div id="deleteBackupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Backup
                </h3>
            </div>

            <form id="deleteBackupForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('DELETE')

                <p class="text-gray-700">
                    Are you sure you want to delete backup <strong id="deleteBackupName"></strong>?
                    This action cannot be undone.
                </p>

                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" data-modal-hide="deleteBackupModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        Delete Backup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SYSTEM INFO MODAL -->
    <div id="systemInfoModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    System Information
                </h3>
            </div>

            <div class="p-6" id="systemInfoContent">
                <!-- Content will be loaded via JavaScript -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-gray-500 mt-2">Loading system information...</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t">
                <button type="button" data-modal-hide="systemInfoModal"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal management
            const modals = ['createBackupModal', 'restoreModal', 'deleteBackupModal', 'systemInfoModal'];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                // Close buttons
                modal.querySelectorAll('[data-modal-hide]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    });
                });

                // Close when clicking outside
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            });

            // Open modal buttons
            document.querySelectorAll('[data-modal-target]').forEach(button => {
                button.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-modal-target');
                    const modal = document.getElementById(modalId);

                    if (modal) {
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            // Restore modal setup
            document.querySelectorAll('[data-modal-target="restoreModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    const backupName = this.getAttribute('data-backup-name');
                    const backupType = this.getAttribute('data-backup-type');

                    document.getElementById('restoreBackupName').textContent = backupName;
                    document.getElementById('restoreBackupType').textContent = backupType;

                    const form = document.getElementById('restoreForm');
                    form.action = `/backup/restore/${backupName}`;

                    // Set restore type based on backup type
                    const restoreType = document.getElementById('restoreType');
                    if (backupType === 'database') {
                        restoreType.value = 'database';
                    } else if (backupType === 'files') {
                        restoreType.value = 'files';
                    } else {
                        restoreType.value = 'full';
                    }
                });
            });

            // Delete backup modal setup
            document.querySelectorAll('[data-modal-target="deleteBackupModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    const backupName = this.getAttribute('data-backup-name');

                    document.getElementById('deleteBackupName').textContent = backupName;

                    const form = document.getElementById('deleteBackupForm');
                    form.action = `/backup/delete/${backupName}`;
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

                element.addEventListener('mouseenter', function () {
                    tooltip.classList.remove('hidden');
                });

                element.addEventListener('mouseleave', function () {
                    tooltip.classList.add('hidden');
                });
            });

            // Close with Escape key
            document.addEventListener('keydown', function (e) {
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
        });

        // Load system information
        async function loadSystemInfo() {
            const modal = document.getElementById('systemInfoModal');
            const content = document.getElementById('systemInfoContent');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            try {
                const response = await fetch('/backup/system-info');
                const data = await response.json();

                let html = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                            </svg>
                                            Application Info
                                        </h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Laravel Version:</span>
                                                <span class="font-medium">${data.laravel_version}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">PHP Version:</span>
                                                <span class="font-medium">${data.php_version}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Environment:</span>
                                                <span class="font-medium">${data.environment}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Timezone:</span>
                                                <span class="font-medium">${data.timezone}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                                            </svg>
                                            Database
                                        </h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Type:</span>
                                                <span class="font-medium">${data.database}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                            Backup Status
                                        </h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Backups:</span>
                                                <span class="font-medium">${data.backup_count}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Last Backup:</span>
                                                <span class="font-medium">${data.last_backup}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                            </svg>
                                            Storage Status
                                        </h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Used Space:</span>
                                                <span class="font-medium">${data.storage_used || 'N/A'}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Available:</span>
                                                <span class="font-medium">${data.storage_available || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <strong>Note:</strong> System information is for diagnostic purposes. 
                                    Contact your system administrator for any issues.
                                </p>
                            </div>
                        `;

                content.innerHTML = html;
            } catch (error) {
                content.innerHTML = `
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-700">Failed to load system information: ${error.message}</p>
                            </div>
                        `;
            }
        }
    </script>

    <style>
        select:focus,
        input:focus,
        textarea:focus {
            outline: none;
            ring-width: 2px;
        }

        .peer:checked~div {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
@endsection