@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Roles Management</h2>

            <!-- Role information badge -->
            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-700">
                    System-defined roles cannot be modified
                </p>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Role Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Description</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Users Count</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Created</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                                <tr class="hover:bg-gray-50 even:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- Role Name Column -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                        {{ $role->id == 1 ? 'bg-purple-100' :
                        ($role->id == 2 ? 'bg-blue-100' :
                            ($role->id == 3 ? 'bg-green-100' :
                                ($role->id == 4 ? 'bg-yellow-100' :
                                    ($role->id == 5 ? 'bg-red-100' : 'bg-gray-100')))) }}">
                                                <span class="font-semibold text-sm
                                                            {{ $role->id == 1 ? 'text-purple-600' :
                        ($role->id == 2 ? 'text-blue-600' :
                            ($role->id == 3 ? 'text-green-600' :
                                ($role->id == 4 ? 'text-yellow-600' :
                                    ($role->id == 5 ? 'text-red-600' : 'text-gray-600')))) }}">
                                                    {{ substr($role->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-900 font-medium block">
                                                    {{ $role->name }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    ID: {{ $role->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Description Column -->
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @php
                                            $descriptions = [
                                                1 => 'Full system access and control',
                                                2 => 'Administrative access with limited system control',
                                                3 => 'Medical and treatment management',
                                                4 => 'Patient registration and appointment management',
                                                5 => 'Financial and billing management'
                                            ];
                                        @endphp
                                        {{ $descriptions[$role->id] ?? 'Custom user role' }}
                                    </td>

                                    <!-- Users Count Column -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                                        {{ ($role->users_count ?? 0) > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $role->users_count ?? 0 }} {{ ($role->users_count ?? 0) == 1 ? 'user' : 'users' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Created Date Column -->
                                    <td class="px-4 py-3 text-sm">
                                        <span class="text-gray-600">{{ $role->created_at->format('d/m/Y') }}</span>
                                    </td>

                                    <!-- Status Column -->
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                                    {{ in_array($role->name, ['Super Admin', 'Admin', 'Doctor', 'Receptionist', 'Accountant'])
                        ? 'bg-blue-100 text-blue-800'
                        : 'bg-gray-100 text-gray-800' }}">
                                            {{ in_array($role->name, ['Super Admin', 'Admin', 'Doctor', 'Receptionist', 'Accountant'])
                        ? 'System Role'
                        : 'Custom Role' }}
                                        </span>
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-gray-500">No roles found</p>
                                    <p class="text-sm text-gray-400 mt-1">System roles are automatically loaded</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- INFO CARD -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">About System Roles</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        These are predefined system roles with specific permissions. Each role has different access levels
                        and capabilities within the system. The role structure is fixed to maintain system integrity and
                        security. User assignments can be managed through the Users Management section.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tooltip hover effect
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                element.addEventListener('mouseenter', function () {
                    const tooltip = this.querySelector('.tooltip, span');
                    if (tooltip) {
                        tooltip.classList.remove('hidden');
                    }
                });

                element.addEventListener('mouseleave', function () {
                    const tooltip = this.querySelector('.tooltip, span');
                    if (tooltip) {
                        tooltip.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection