@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Patients Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.patients.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Add Patient</span>
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
        <form method="GET" action="{{ route('backend.patients.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 mt-4">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name, or phone"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="deceased" {{ request('status') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="gender" class="w-full border rounded px-3 py-2">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
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
                        <th class="px-3 py-2 text-left text-sm">Patient Code</th>
                        <th class="px-3 py-2 text-left text-sm">Name</th>
                        <th class="px-3 py-2 text-left text-sm">Phone</th>
                        <th class="px-3 py-2 text-left text-sm">Age</th>
                        <th class="px-3 py-2 text-left text-sm">Gender</th>
                        <th class="px-3 py-2 text-center text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($patients->currentPage() - 1) * $patients->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-3 py-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $patient->patient_code }}
                                </span>
                            </td>

                            <td class="px-3 py-2">{{ $patient->full_name }}</td>
                            <td class="px-3 py-2">{{ $patient->phone }}</td>
                            <td class="px-3 py-2">{{ $patient->age_text }}</td>
                            <td class="px-3 py-2">{{ $patient->gender_text }}</td>

                            <td class="px-3 py-2 text-center">
                                {!! $patient->status_badge !!}
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.patients.show', $patient) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.patients.edit', $patient) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.patients.destroy', $patient) }}"
                                        data-name="{{ $patient->full_name }}"
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
                            <td colspan="8" class="px-3 py-6 text-center text-gray-500">
                                No patients found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$patients" class="mt-3" />

    </div>

    <!-- Delete Modal -->
    <x-delete-modal id="deleteModal" title="Delete Patient" message="Are you sure?" :route="null" />
@endsection
