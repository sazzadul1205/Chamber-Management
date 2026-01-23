@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Doctors Management</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.doctors.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Add Doctor</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.doctors.index') }}" class="grid grid-cols-1 md:grid-cols-8 gap-3 mt-4">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name, or specialization"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="specialization" class="w-full border rounded px-3 py-2">
                    <option value="">All Specializations</option>
                    @foreach ($specializations as $spec)
                        <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                            {{ $spec }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">#</th>
                        <th class="px-3 py-2 text-left text-sm">Doctor Code</th>
                        <th class="px-3 py-2 text-left text-sm">Name</th>
                        <th class="px-3 py-2 text-left text-sm">Specialization</th>
                        <th class="px-3 py-2 text-left text-sm">Qualification</th>
                        <th class="px-3 py-2 text-left text-sm">Fee</th>
                        <th class="px-3 py-2 text-left text-sm">Commission</th>
                        <th class="px-3 py-2 text-center text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $doctor->doctor_code }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $doctor->user->full_name ?? 'Demo Doctor' }}</td>
                            <td class="px-3 py-2">{{ $doctor->specialization ?? '-' }}</td>
                            <td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($doctor->qualification, 30) ?? '-' }}
                            </td>
                            <td class="px-3 py-2">৳ {{ number_format($doctor->consultation_fee ?? 0, 2) }}</td>
                            <td class="px-3 py-2">{{ $doctor->commission_percent ?? 0 }}%</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusColors = ['active' => 'green', 'inactive' => 'gray', 'on_leave' => 'yellow'];
                                @endphp
                                <span
                                    class="px-2 py-1 rounded text-xs bg-{{ $statusColors[$doctor->status] ?? 'gray' }}-500 text-white">
                                    {{ ucfirst(str_replace('_', ' ', $doctor->status ?? 'N/A')) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.doctors.show', $doctor->id) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <a href="{{ route('backend.doctors.edit', $doctor->id) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.doctors.destroy', $doctor->id) }}"
                                        data-name="{{ $doctor->user->full_name ?? 'Demo Doctor' }}"
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-gray-500">No doctors found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$doctors" class="mt-3" />

    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal id="deleteModal" title="Delete Doctor" message="Are you sure?" :route="null" />
@endsection
