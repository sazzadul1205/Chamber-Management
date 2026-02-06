@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Leave Requests</h1>
                <p class="text-gray-600 mt-1">
                    Manage your leave applications and view status
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full {{ $doctor->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $doctor->specialization }}</span>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="button" onclick="showApplyLeaveModal()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply for Leave
                </button>
                <a href="{{ route('backend.doctors.show', $doctor) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to Profile
                </a>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">
                            {{ $leaves->where('status', 'pending')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $leaves->where('status', 'approved')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ $leaves->where('status', 'rejected')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $leaves->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEAVES TABLE -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">My Leave History</h3>
            </div>

            @if ($leaves->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No leave requests</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        You haven't applied for any leave yet. Click "Apply for Leave" to get started.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Leave Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type & Reason
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Applied On
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($leaves as $leave)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $leave->formatted_date }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $leave->formatted_type }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $leave->reason }}</div>
                                        @if ($leave->notes)
                                            <div class="text-xs text-gray-500 mt-1">{{ $leave->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leave->status_color }}">
                                            {{ $leave->status_text }}
                                        </span>
                                        @if ($leave->rejection_reason)
                                            <div class="text-xs text-red-600 mt-1">
                                                Reason: {{ $leave->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $leave->created_at->format('d M, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $leave->created_at->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if (in_array($leave->status, ['pending', 'approved']))
                                            <form action="{{ route('backend.doctors.cancel-leave', $leave) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to cancel this leave?')"
                                                    class="text-red-600 hover:text-red-900">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-500">No action</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div id="applyLeaveModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Apply for Leave</h3>
                    <button type="button" onclick="hideApplyLeaveModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('backend.doctors.apply-leave') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Leave Date -->
                        <div>
                            <label for="leave_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Leave Date *
                            </label>
                            <input type="date" name="leave_date" id="leave_date" required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Select the date you want to take leave
                            </p>
                        </div>

                        <!-- Leave Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Leave Type *
                            </label>
                            <select name="type" id="type" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="full_day">Full Day</option>
                                <option value="half_day">Half Day</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                                Reason *
                            </label>
                            <input type="text" name="reason" id="reason" required maxlength="255"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Brief reason for leave">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Additional Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Any additional information (optional)"></textarea>
                        </div>

                        <!-- Next Working Day Info -->
                        <div id="nextWorkingDayInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-3 hidden">
                            <p class="text-sm text-blue-800">
                                <span class="font-medium">Note:</span> You are not scheduled to work on this day.
                                Only emergency leaves are allowed for non-working days.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button" onclick="hideApplyLeaveModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md text-sm font-medium text-white">
                            Apply for Leave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showApplyLeaveModal() {
            const modal = document.getElementById('applyLeaveModal');
            modal.classList.remove('hidden');

            // Reset form
            document.getElementById('leave_date').value = '';
            document.getElementById('type').value = '';
            document.getElementById('reason').value = '';
            document.getElementById('notes').value = '';
            document.getElementById('nextWorkingDayInfo').classList.add('hidden');
        }

        function hideApplyLeaveModal() {
            document.getElementById('applyLeaveModal').classList.add('hidden');
        }

        // Check if selected date is a working day
        document.getElementById('leave_date').addEventListener('change', function() {
            const selectedDate = this.value;
            const dayOfWeek = new Date(selectedDate).toLocaleString('en-US', {
                weekday: 'long'
            }).toLowerCase();
            const infoDiv = document.getElementById('nextWorkingDayInfo');

            // This is a simplified check. In production, you'd want to check against the doctor's actual schedule
            const workingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            if (!workingDays.includes(dayOfWeek)) {
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        });

        // Close modal on outside click
        document.getElementById('applyLeaveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideApplyLeaveModal();
            }
        });
    </script>
@endsection
