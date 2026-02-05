@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Appointment Reminders</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.reminders.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>Schedule Reminder</span>
                </a>

                <a href="{{ route('backend.reminders.stats') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'Status',
                        'class' => 'w-4 h-4 text-white',
                    ])
                    <span>View Stats</span>
                </a>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif
        @if (session('warning'))
            <div class="p-3 bg-yellow-100 text-yellow-800 rounded mb-2">{{ session('warning') }}</div>
        @endif

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.reminders.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="type"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Types</option>
                    <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="push" {{ request('type') == 'push' ? 'selected' : '' }}>Push</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="From Date">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="To Date">
            </div>

            <div class="md:col-span-2">
                <input type="number" name="appointment_id" value="{{ request('appointment_id') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Appointment ID">
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
                <a href="{{ route('backend.reminders.index') }}"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md px-4 py-2 font-medium text-center">
                    Clear
                </a>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">
                            <input type="checkbox" id="selectAll" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Appointment</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Message</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Scheduled For</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Sent At</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reminders as $reminder)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <input type="checkbox" name="reminder_ids[]" value="{{ $reminder->id }}" class="rounded">
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                #{{ $reminder->id }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('backend.appointments.show', $reminder->appointment_id) }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    #{{ $reminder->appointment_id }}
                                </a>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ optional($reminder->appointment->patient)->name ?? 'N/A' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $reminder->reminder_type == 'sms' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $reminder->reminder_type == 'email' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $reminder->reminder_type == 'push' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ strtoupper($reminder->reminder_type) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="text-gray-600 max-w-xs truncate" title="{{ $reminder->message }}">
                                    {{ Str::limit($reminder->message, 50) }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium text-gray-900">
                                    {{ $reminder->scheduled_at->format('M d, Y') }}
                                </span>
                                <br>
                                <span class="text-xs text-gray-500">
                                    {{ $reminder->scheduled_at->format('h:i A') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($reminder->status == 'sent')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Sent
                                    </span>
                                @elseif($reminder->status == 'failed')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($reminder->sent_at)
                                    <span class="font-medium text-gray-900">
                                        {{ $reminder->sent_at->format('M d, Y') }}
                                    </span>
                                    <br>
                                    <span class="text-xs text-gray-500">
                                        {{ $reminder->sent_at->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    @if ($reminder->status == 'pending')
                                        <!-- Send Now -->
                                        <form method="POST" action="{{ route('backend.reminders.send-now', $reminder) }}"
                                            class="inline" id="sendForm-{{ $reminder->id }}">
                                            @csrf
                                            <button type="button" onclick="confirmSend({{ $reminder->id }})"
                                                class="{{ $btnBaseClasses }} bg-green-600 hover:bg-green-700"
                                                data-tooltip="Send Now">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Send',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Send Now
                                                </span>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($reminder->status != 'sent')
                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('backend.reminders.destroy', $reminder) }}"
                                            class="inline" id="deleteForm-{{ $reminder->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $reminder->id }})"
                                                class="{{ $btnBaseClasses }} bg-red-600 hover:bg-red-700"
                                                data-tooltip="Delete">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Delete',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Delete
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No reminders found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- BULK ACTIONS & PAGINATION -->
        @if ($reminders->count() > 0)
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 gap-3">
                <!-- Bulk Actions -->
                <form action="{{ route('backend.reminders.bulk-send') }}" method="POST" id="bulkSendForm"
                    class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="reminder_ids" id="bulkReminderIds">
                    <button type="button" onclick="submitBulkSend()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                        Send Selected Reminders
                    </button>
                </form>

                <!-- Pagination -->
                <div>
                    {{ $reminders->links() }}
                </div>
            </div>
        @endif

    </div>

    <script>
        // Select All Checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('input[name="reminder_ids[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Bulk Send
        function submitBulkSend() {
            let checkboxes = document.querySelectorAll('input[name="reminder_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one reminder');
                return;
            }

            if (!confirm(`Send ${checkboxes.length} reminder(s) now?`)) {
                return;
            }

            let ids = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkReminderIds').value = JSON.stringify(ids);
            document.getElementById('bulkSendForm').submit();
        }

        // Individual Send Confirmation
        function confirmSend(reminderId) {
            if (confirm('Send this reminder now?')) {
                document.getElementById(`sendForm-${reminderId}`).submit();
            }
        }

        // Individual Delete Confirmation
        function confirmDelete(reminderId) {
            if (confirm('Delete this reminder?')) {
                document.getElementById(`deleteForm-${reminderId}`).submit();
            }
        }

        // Tooltip functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    const tooltip = this.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.classList.remove('hidden');
                    }
                });

                element.addEventListener('mouseleave', function() {
                    const tooltip = this.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection
