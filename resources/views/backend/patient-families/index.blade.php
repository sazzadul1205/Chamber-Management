@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Patient Families Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.patient-families.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Create New Family</span>
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

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.patient-families.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by family code or family name"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="sort"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First
                    </option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Code (A-Z)</option>
                    <option value="code_desc" {{ request('sort') == 'code_desc' ? 'selected' : '' }}>Code (Z-A)</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Family Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Family Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Head of Family</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Members</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Head Phone</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Created Date</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($families as $family)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($families->currentPage() - 1) * $families->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $family->family_code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm font-medium">{{ $family->family_name }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($family->head)
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $family->head->full_name }}</span>
                                        <span class="text-xs text-gray-500">{{ $family->head->patient_code }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">No head assigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ $family->member_count }}</span>
                                    <span class="text-xs text-gray-500">members</span>
                                    @if ($family->members_count > 0)
                                        <button type="button" onclick="showMembers({{ $family->id }})"
                                            class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                            View
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($family->head)
                                    {{ $family->head->phone ?? 'N/A' }}
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                {{ $family->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">

                                    <!-- View -->
                                    <div class="relative group">
                                        <a href="{{ route('backend.patient-families.show', $family) }}"
                                            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs flex items-center">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_View',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>

                                        <span
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[11px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">
                                            View
                                        </span>
                                    </div>

                                    <!-- Edit -->
                                    <div class="relative group">
                                        <a href="{{ route('backend.patient-families.edit', $family) }}"
                                            class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs flex items-center">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Edit',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>

                                        <span
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[11px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">
                                            Edit
                                        </span>
                                    </div>

                                    <!-- Delete -->
                                    <div class="relative group">
                                        <button type="button" data-modal-target="deleteModal"
                                            data-route="{{ route('backend.patient-families.destroy', $family) }}"
                                            data-name="{{ $family->family_name }}"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs flex items-center">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Delete',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </button>

                                        <span
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[11px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">
                                            Delete
                                        </span>
                                    </div>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No families found. Create your first family by clicking "Create New Family".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$families" />
        </div>

    </div>

    <!-- Delete Modal -->
    <x-delete-modal id="deleteModal" title="Delete Family"
        message="Are you sure you want to delete this family? This action cannot be undone. All family member associations will be removed."
        :route="null" />

    <!-- Members Modal -->
    <div id="membersModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Family Members</h3>
                <button type="button" onclick="closeMembersModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto" id="membersContent">
                <!-- Content will be loaded via AJAX -->
                <div class="flex justify-center items-center h-32">
                    <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Members Modal Functions
        function showMembers(familyId) {
            document.getElementById('membersModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Load members via AJAX
            fetch(`/backend/patient-families/${familyId}/members`)
                .then(response => response.json())
                .then(data => {
                    let html = '';

                    if (data.members && data.members.length > 0) {
                        html = `
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-medium text-blue-800 mb-2">Head of Family</h4>
                                    ${data.members.filter(m => m.is_head).map(member => `
                                                                <div class="flex items-center justify-between p-2 bg-white rounded border border-blue-200">
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                            <span class="text-blue-600 font-bold">H</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="font-medium">${member.patient.full_name}</div>
                                                                            <div class="text-xs text-gray-600">${member.patient.patient_code} • ${member.patient.phone}</div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Head</span>
                                                                </div>
                                                            `).join('')}
                                </div>
                                
                                <div class="mt-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Family Members (${data.members.filter(m => !m.is_head).length})</h4>
                                    <div class="space-y-2">
                                        ${data.members.filter(m => !m.is_head).map(member => `
                                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200">
                                                                        <div class="flex items-center gap-3">
                                                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                                                <span class="text-gray-600 font-bold">${member.relationship.charAt(0).toUpperCase()}</span>
                                                                            </div>
                                                                            <div>
                                                                                <div class="font-medium">${member.patient.full_name}</div>
                                                                                <div class="text-xs text-gray-600">
                                                                                    ${member.patient.patient_code} • ${member.patient.phone}
                                                                                    <span class="ml-2 text-gray-500">(${member.relationship})</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-xs text-gray-500 capitalize">${member.relationship}</span>
                                                                    </div>
                                                                `).join('')}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        html = '<p class="text-center text-gray-500 py-4">No members found in this family.</p>';
                    }

                    document.getElementById('membersContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading members:', error);
                    document.getElementById('membersContent').innerHTML =
                        '<p class="text-center text-red-500 py-4">Error loading members. Please try again.</p>';
                });
        }

        function closeMembersModal() {
            document.getElementById('membersModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside
        document.getElementById('membersModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMembersModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('membersModal').classList.contains('hidden')) {
                closeMembersModal();
            }
        });

        // Apply sorting from controller
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.querySelector('select[name="sort"]');
            if (sortSelect) {
                const currentSort = "{{ request('sort', 'newest') }}";
                const urlParams = new URLSearchParams(window.location.search);

                sortSelect.addEventListener('change', function() {
                    urlParams.set('sort', this.value);
                    window.location.search = urlParams.toString();
                });
            }
        });
    </script>

    @if (request()->has('search') || request()->has('status') || request()->has('sort'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show active filters
                const activeFilters = [];

                @if (request('search'))
                    activeFilters.push(`Search: "{{ request('search') }}"`);
                @endif

                @if (request('status'))
                    activeFilters.push(`Status: {{ ucfirst(request('status')) }}`);
                @endif

                @if (request('sort'))
                    const sortText = {
                        'newest': 'Newest First',
                        'oldest': 'Oldest First',
                        'name_asc': 'Name (A-Z)',
                        'name_desc': 'Name (Z-A)',
                        'code_asc': 'Code (A-Z)',
                        'code_desc': 'Code (Z-A)'
                    } ["{{ request('sort') }}"] || "{{ request('sort') }}";
                    activeFilters.push(`Sort: ${sortText}`);
                @endif

                if (activeFilters.length > 0) {
                    const filtersHtml = `
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                                <a href="{{ route('backend.patient-families.index') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                    Clear all
                                </a>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                ${activeFilters.map(filter => `
                                                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-blue-700 border border-blue-300">
                                                                                        ${filter}
                                                                                        <a href="${removeFilter('${filter.split(':')[0].toLowerCase().trim()}')}"
                                                                                           class="ml-2 text-blue-500 hover:text-blue-700">
                                                                                            ×
                                                                                        </a>
                                                                                    </span>
                                                                                `).join('')}
                            </div>
                        </div>
                    `;

                    // Insert after filters form
                    const form = document.querySelector('form[method="GET"]');
                    if (form) {
                        form.insertAdjacentHTML('afterend', filtersHtml);
                    }
                }

                function removeFilter(filterType) {
                    const url = new URL(window.location.href);
                    const params = new URLSearchParams(url.search);

                    switch (filterType) {
                        case 'search':
                            params.delete('search');
                            break;
                        case 'status':
                            params.delete('status');
                            break;
                        case 'sort':
                            params.delete('sort');
                            break;
                    }

                    return `${url.pathname}?${params.toString()}`;
                }
            });
        </script>
    @endif
@endsection
