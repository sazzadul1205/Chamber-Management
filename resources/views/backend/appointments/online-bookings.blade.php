<div class="bg-white rounded-lg shadow p-5 mt-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Online Booking Requests</h3>
            <p class="text-sm text-gray-500">Requests submitted from the website booking form</p>
        </div>
    </div>

    <form method="GET" action="{{ route('backend.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="hidden" name="doctor_id" value="{{ request('doctor_id') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        <input type="hidden" name="type" value="{{ request('type') }}">

        <div class="md:col-span-5">
            <input
                type="text"
                name="online_search"
                value="{{ request('online_search') }}"
                placeholder="Search name / email / phone"
                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
        </div>

        <div class="md:col-span-4">
            <select name="online_status"
                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                <option value="">All Status</option>
                <option value="pending" {{ request('online_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('online_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('online_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="converted" {{ request('online_status') === 'converted' ? 'selected' : '' }}>Converted</option>
            </select>
        </div>

        <div class="md:col-span-3">
            <button type="submit"
                class="w-full bg-slate-700 hover:bg-slate-800 text-white rounded-md px-4 py-2 font-medium">
                Filter Online
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-900 text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Date & Time</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Service</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Requested At</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($onlineBookings as $onlineBooking)
                    <tr class="hover:bg-gray-50 even:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            {{ ($onlineBookings->currentPage() - 1) * $onlineBookings->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $onlineBooking->full_name }}
                            @if ($onlineBooking->message)
                                <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($onlineBooking->message, 80) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div>{{ $onlineBooking->email }}</div>
                            <div class="text-gray-500">{{ $onlineBooking->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="font-medium">
                                {{ optional($onlineBooking->preferred_date)->format('d/m/Y') ?? '-' }}
                            </span>
                            <br>
                            <span class="text-xs text-gray-500">{{ $onlineBooking->preferred_time }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $onlineBooking->service }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'converted' => 'bg-blue-100 text-blue-800',
                                ];
                                $statusClass = $statusStyles[$onlineBooking->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($onlineBooking->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ optional($onlineBooking->created_at)->format('d/m/Y h:i A') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                            No online bookings found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <x-pagination :paginator="$onlineBookings" />
    </div>
</div>
