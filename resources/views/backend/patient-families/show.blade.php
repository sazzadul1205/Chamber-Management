@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    @include('partials.sidebar-icon', [
                        'name' => 'Family',
                        'class' => 'w-8 h-8 text-blue-500',
                    ])
                    <h1 class="text-3xl font-bold text-gray-900">Family Details</h1>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        {{ $patientFamily->family_code }}
                    </span>
                    <span class="text-gray-400">•</span>
                    <span class="text-sm">{{ $patientFamily->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('backend.patient-families.edit', $patientFamily) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Edit',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Edit Family</span>
                </a>
                <a href="{{ route('backend.patient-families.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg shadow transition-colors duration-200">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Back to List</span>
                </a>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Sidebar - Family Info -->
            <div class="space-y-6">
                <!-- Family Overview Card -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">{{ $patientFamily->family_name }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-6">
                            <div class="relative">
                                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'Family',
                                        'class' => 'w-10 h-10 text-blue-500',
                                    ])
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-2 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $patientFamily->members->count() }} members
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'User',
                                        'class' => 'w-5 h-5 text-blue-500',
                                    ])
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Head of Family</p>
                                    <p class="font-medium text-gray-900">
                                        @if ($patientFamily->head)
                                            {{ $patientFamily->head->full_name }}
                                            <span
                                                class="text-xs text-gray-500 block">{{ $patientFamily->head->patient_code }}</span>
                                        @else
                                            <span class="text-gray-400">No head assigned</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">Total Members</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $patientFamily->members->count() }}</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">Active Members</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ $patientFamily->members->where('patient.status', 'active')->count() }}
                                    </p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Created</span>
                                    <span
                                        class="font-medium">{{ $patientFamily->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                    <div class="space-y-3">
                        <button onclick="openAddMemberModal()"
                            class="w-full flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors duration-200">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Add',
                                'class' => 'w-5 h-5 text-blue-500',
                            ])
                            <span class="font-medium">Add New Member</span>
                        </button>
                        <a href="#"
                            class="flex items-center gap-3 p-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors duration-200">
                            @include('partials.sidebar-icon', [
                                'name' => 'Invoice',
                                'class' => 'w-5 h-5 text-green-500',
                            ])
                            <span class="font-medium">View Family Invoices</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors duration-200">
                            @include('partials.sidebar-icon', [
                                'name' => 'Report',
                                'class' => 'w-5 h-5 text-purple-500',
                            ])
                            <span class="font-medium">Family Reports</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Content - Members List -->
            <div class="lg:col-span-2">
                <!-- Members Card -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Family Members</h3>
                            <p class="text-sm text-gray-500 mt-1">Manage family members and their relationships</p>
                        </div>
                        <button onclick="openAddMemberModal()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Add',
                                'class' => 'w-4 h-4',
                            ])
                            Add Member
                        </button>
                    </div>

                    @if ($patientFamily->members->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Patient
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Relationship
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Contact
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($patientFamily->members as $member)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full {{ $member->is_head ? 'bg-yellow-100' : 'bg-blue-100' }} flex items-center justify-center">
                                                            @if ($member->is_head)
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'Crown',
                                                                    'class' => 'w-5 h-5 text-yellow-500',
                                                                ])
                                                            @else
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'User',
                                                                    'class' => 'w-5 h-5 text-blue-500',
                                                                ])
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $member->patient->full_name }}
                                                            @if ($member->is_head)
                                                                <span
                                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    @include('partials.sidebar-icon', [
                                                                        'name' => 'Crown',
                                                                        'class' => 'w-3 h-3 mr-1',
                                                                    ])
                                                                    Head
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $member->patient->patient_code }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $relationshipColors = [
                                                        'spouse' => 'bg-pink-100 text-pink-700',
                                                        'child' => 'bg-blue-100 text-blue-700',
                                                        'parent' => 'bg-purple-100 text-purple-700',
                                                        'sibling' => 'bg-green-100 text-green-700',
                                                        'relative' => 'bg-yellow-100 text-yellow-800',
                                                        'self' => 'bg-gray-100 text-gray-700',
                                                        'other' => 'bg-gray-100 text-gray-700',
                                                    ];
                                                    $colorClass =
                                                        $relationshipColors[$member->relationship] ??
                                                        'bg-gray-100 text-gray-700';
                                                @endphp

                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                                    {{ ucfirst($member->relationship) }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $member->patient->phone ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $member->patient->status_badge ?? '<span class="text-gray-400">N/A</span>' !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2">
                                                    @if (!$member->is_head)
                                                        <form
                                                            action="{{ route('backend.patient-families.set-head', [$patientFamily, $member->patient]) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200"
                                                                onclick="return confirm('Set this member as family head?')">
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'Crown',
                                                                    'class' => 'w-3 h-3',
                                                                ])
                                                                Make Head
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('backend.patient-families.members.remove', [$patientFamily, $member->patient]) }}"
                                                            method="POST" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                                                onclick="return confirm('Remove this member from family?')">
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'B_Delete',
                                                                    'class' => 'w-3 h-3',
                                                                ])
                                                                Remove
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('backend.patients.show', $member->patient) }}"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                                        @include('partials.sidebar-icon', [
                                                            'name' => 'B_View',
                                                            'class' => 'w-3 h-3',
                                                        ])
                                                        View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_UserGroup',
                                    'class' => 'w-10 h-10 text-gray-400',
                                ])
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Family Members Yet</h4>
                            <p class="text-gray-500 mb-6">Add members to this family to get started</p>
                            <button onclick="openAddMemberModal()"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-md transition-colors duration-200">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_AddUser',
                                    'class' => 'w-4 h-4',
                                ])
                                Add First Member
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95"
            id="modalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Add Family Member</h3>
                    <p class="text-sm text-gray-500">Search and select a patient to add to this family</p>
                </div>
                <button onclick="closeAddMemberModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Close',
                        'class' => 'w-5 h-5',
                    ])
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('backend.patient-families.members.add', $patientFamily) }}" method="POST"
                class="px-6 py-4" id="addMemberForm">
                @csrf

                <div class="space-y-4">
                    <!-- Patient Search -->
                    <div class="relative">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700">
                                Select Patient *
                            </label>
                        </div>

                        <div class="flex gap-2">
                            <input type="text" id="member_search"
                                placeholder="Search patient by name, code, or phone..."
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                autocomplete="off">

                            <button type="button" id="clear-member"
                                class="px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hidden">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_Clear',
                                    'class' => 'w-4 h-4',
                                ])
                            </button>
                        </div>

                        <!-- Dropdown -->
                        <ul id="member_results"
                            class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                        </ul>

                        <input type="hidden" name="patient_id" id="member_id" value="">

                        <!-- Selected Patient Info -->
                        <div id="selected-member-info" class="mt-2 hidden">
                            <div
                                class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'User',
                                                'class' => 'w-4 h-4 text-blue-500',
                                            ])
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-blue-700" id="selected-member-name">
                                            </div>
                                            <div class="text-xs text-blue-600 flex items-center gap-2">
                                                <span id="selected-member-code"></span>
                                                <span>•</span>
                                                <span id="selected-member-phone"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="remove-member"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium ml-2">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Close',
                                        'class' => 'w-4 h-4',
                                    ])
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Relationship -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship *
                        </label>
                        <select name="relationship" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Select relationship...</option>
                            <option value="spouse">Spouse</option>
                            <option value="child">Child</option>
                            <option value="parent">Parent</option>
                            <option value="sibling">Sibling</option>
                            <option value="relative">Relative</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Info Note -->
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start gap-3">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Info',
                                'class' => 'w-5 h-5 text-blue-500 mt-0.5',
                            ])
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Note:</p>
                                <p>The patient will be linked to this family and can be managed here.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeAddMemberModal()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Cross',
                            'class' => 'w-4 h-4',
                        ])
                        Cancel
                    </button>
                    <button type="submit" id="submitMemberBtn" disabled
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md transition-all duration-200 opacity-50 cursor-not-allowed">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Add',
                            'class' => 'w-4 h-4',
                        ])
                        Add to Family
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Member Modal Elements
            const addMemberModal = document.getElementById('addMemberModal');
            const modalContent = document.getElementById('modalContent');

            // Patient Search Elements
            const memberInput = document.getElementById('member_search');
            const memberResults = document.getElementById('member_results');
            const memberHidden = document.getElementById('member_id');
            const clearMemberBtn = document.getElementById('clear-member');
            const selectedMemberInfo = document.getElementById('selected-member-info');
            const selectedMemberName = document.getElementById('selected-member-name');
            const selectedMemberCode = document.getElementById('selected-member-code');
            const selectedMemberPhone = document.getElementById('selected-member-phone');
            const removeMemberBtn = document.getElementById('remove-member');
            const submitMemberBtn = document.getElementById('submitMemberBtn');
            let memberTimeout = null;

            // Initialize patient search
            initializeMemberSearch();

            // Clear member button
            clearMemberBtn.addEventListener('click', clearMemberSelection);

            // Remove member button
            removeMemberBtn.addEventListener('click', clearMemberSelection);

            // Form submission validation
            document.getElementById('addMemberForm').addEventListener('submit', function(e) {
                if (!memberHidden.value) {
                    e.preventDefault();
                    alert('Please select a patient from the search results');
                    memberInput.focus();
                }
            });

            function initializeMemberSearch() {
                function searchPatients(query) {
                    // Filter out already added patients
                    const existingPatientIds = [
                        {{ $patientFamily->head_patient_id }},
                        @foreach ($patientFamily->members as $member)
                            {{ $member->patient_id }},
                        @endforeach
                    ].filter(id => id);

                    fetch(`/api/patients?search=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            memberResults.innerHTML = '';

                            // Filter out patients already in family
                            const filteredData = data.filter(patient =>
                                !existingPatientIds.includes(patient.id)
                            );

                            if (filteredData.length === 0) {
                                memberResults.innerHTML = `
                                    <li class="px-3 py-2 text-gray-500 cursor-default">
                                        No available patients found. 
                                        ${data.length > 0 ? 
                                            'All matching patients are already in this family.' : 
                                            'Try a different search term.'
                                        }
                                    </li>`;
                            } else {
                                filteredData.forEach(patient => {
                                    const li = document.createElement('li');
                                    li.className =
                                        "px-3 py-2 cursor-pointer hover:bg-blue-50 border-b last:border-b-0 transition-colors duration-150";
                                    li.innerHTML = `
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'User',
                                                    'class' => 'w-4 h-4 text-blue-500',
                                                ])
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">${patient.full_name}</div>
                                                <div class="text-xs text-gray-600">
                                                    ${patient.patient_code} | ${patient.phone || 'No phone'} | ${patient.gender || 'N/A'}
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    li.addEventListener('click', () => {
                                        selectMember(patient);
                                    });
                                    memberResults.appendChild(li);
                                });
                            }

                            memberResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error(`Error searching patients:`, error);
                            memberResults.innerHTML =
                                `<li class="px-3 py-2 text-red-500 cursor-default">Error loading patients</li>`;
                            memberResults.classList.remove('hidden');
                        });
                }

                function showPlaceholder() {
                    memberResults.innerHTML =
                        `<li class="px-3 py-2 text-gray-400 italic cursor-default">Type at least 2 characters to search...</li>`;
                    memberResults.classList.remove('hidden');
                    memberHidden.value = '';

                    clearMemberBtn.classList.add('hidden');
                    selectedMemberInfo.classList.add('hidden');
                    submitMemberBtn.disabled = true;
                    submitMemberBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                memberInput.addEventListener('input', function() {
                    clearTimeout(memberTimeout);
                    const query = memberInput.value.trim();

                    // Clear hidden input if search is cleared
                    if (query.length === 0) {
                        memberHidden.value = '';
                        showPlaceholder();
                        return;
                    }

                    memberTimeout = setTimeout(() => {
                        searchPatients(query);
                    }, 300);
                });

                memberInput.addEventListener('focus', function() {
                    if (memberResults.innerHTML === '' && !memberHidden.value) {
                        showPlaceholder();
                    } else {
                        memberResults.classList.remove('hidden');
                    }
                });

                // Hide dropdown if click outside
                document.addEventListener('click', function(e) {
                    if (!memberResults.contains(e.target) && e.target !== memberInput && e.target !==
                        clearMemberBtn) {
                        memberResults.classList.add('hidden');
                    }
                });

                // Initial state
                showPlaceholder();
            }

            function selectMember(patient) {
                memberHidden.value = patient.id;
                memberInput.value = `${patient.patient_code} - ${patient.full_name}`;

                // Update selected patient info
                selectedMemberName.textContent = patient.full_name;
                selectedMemberCode.textContent = patient.patient_code;
                selectedMemberPhone.textContent = patient.phone || 'No phone';

                selectedMemberInfo.classList.remove('hidden');
                clearMemberBtn.classList.remove('hidden');
                memberResults.classList.add('hidden');

                // Disable input to prevent changes
                memberInput.disabled = true;
                memberInput.classList.add('bg-gray-100', 'cursor-not-allowed');

                // Enable submit button
                submitMemberBtn.disabled = false;
                submitMemberBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            function clearMemberSelection() {
                memberInput.value = '';
                memberHidden.value = '';
                memberInput.disabled = false;
                memberInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                memberResults.innerHTML = '';
                memberResults.classList.add('hidden');
                clearMemberBtn.classList.add('hidden');
                selectedMemberInfo.classList.add('hidden');
                submitMemberBtn.disabled = true;
                submitMemberBtn.classList.add('opacity-50', 'cursor-not-allowed');
                memberInput.focus();
            }
        });

        function openAddMemberModal() {
            const modal = document.getElementById('addMemberModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Clear any previous selection
            const memberInput = document.getElementById('member_search');
            const memberHidden = document.getElementById('member_id');
            const selectedInfo = document.getElementById('selected-member-info');
            const clearBtn = document.getElementById('clear-member');
            const submitBtn = document.getElementById('submitMemberBtn');

            if (memberInput) {
                memberInput.value = '';
                memberHidden.value = '';
                memberInput.disabled = false;
                memberInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                selectedInfo.classList.add('hidden');
                clearBtn.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                memberInput.focus();
            }

            // Animation
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeAddMemberModal() {
            const modal = document.getElementById('addMemberModal');
            const content = document.getElementById('modalContent');

            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 200);
        }

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !document.getElementById('addMemberModal').classList.contains('hidden')) {
                closeAddMemberModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('addMemberModal').addEventListener('click', (e) => {
            if (e.target.id === 'addMemberModal') closeAddMemberModal();
        });
    </script>

@endsection
