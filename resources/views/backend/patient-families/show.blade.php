@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-users text-3xl text-blue-500"></i>
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
                    <i class="fas fa-edit"></i>
                    Edit Family
                </a>
                <a href="{{ route('backend.patient-families.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg shadow transition-colors duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Back to List
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
                                    <i class="fas fa-users text-4xl text-blue-500"></i>
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-2 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $patientFamily->member_count }} members
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-tie text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Head of Family</p>
                                    <p class="font-medium text-gray-900">{{ $patientFamily->head_name }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">Total Members</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $patientFamily->member_count }}</p>
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
                            <i class="fas fa-user-plus text-blue-500"></i>
                            <span class="font-medium">Add New Member</span>
                        </button>
                        <a href="#"
                            class="flex items-center gap-3 p-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-file-invoice text-green-500"></i>
                            <span class="font-medium">View Family Invoices</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-chart-bar text-purple-500"></i>
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
                            <i class="fas fa-plus"></i>
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
                                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            @if ($member->is_head)
                                                                <i class="fas fa-crown text-yellow-500"></i>
                                                            @else
                                                                <i class="fas fa-user text-blue-500"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $member->patient->full_name }}
                                                            @if ($member->is_head)
                                                                <span
                                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    <i class="fas fa-crown mr-1"></i> Head
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $member->patient->patient_code }}</div>
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
                                                {!! $member->patient->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2">
                                                    @if (!$member->is_head)
                                                        <form
                                                            action="{{ route('backend.patient-families.set-head', [$patientFamily, $member->patient]) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                                                                onclick="return confirm('Set this member as family head?')">
                                                                <i class="fas fa-crown mr-1"></i> Make Head
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('backend.patient-families.members.remove', [$patientFamily, $member->patient]) }}"
                                                            method="POST" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                                onclick="return confirm('Remove this member from family?')">
                                                                <i class="fas fa-user-times mr-1"></i> Remove
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('backend.patients.show', $member->patient) }}"
                                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-eye mr-1"></i> View
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
                                <i class="fas fa-user-friends text-3xl text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Family Members Yet</h4>
                            <p class="text-gray-500 mb-6">Add members to this family to get started</p>
                            <button onclick="openAddMemberModal()"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-md">
                                <i class="fas fa-user-plus"></i>
                                Add First Member
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95"
            id="modalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Add Family Member</h3>
                    <p class="text-sm text-gray-500">Select a patient to add to this family</p>
                </div>
                <button onclick="closeAddMemberModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('backend.patient-families.members.add', $patientFamily) }}" method="POST"
                class="px-6 py-4">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Patient *
                        </label>
                        <select name="patient_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white">
                            <option value="">Choose a patient...</option>
                            @foreach ($availablePatients as $patient)
                                <option value="{{ $patient->id }}">
                                    {{ $patient->patient_code }} - {{ $patient->full_name }}
                                    @if ($patient->phone)
                                        ({{ $patient->phone }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship *
                        </label>
                        <select name="relationship" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Select relationship...</option>
                            <option value="spouse" class="flex items-center">
                                <i class="fas fa-heart mr-2 text-pink-500"></i> Spouse
                            </option>
                            <option value="child" class="flex items-center">
                                <i class="fas fa-child mr-2 text-blue-500"></i> Child
                            </option>
                            <option value="parent" class="flex items-center">
                                <i class="fas fa-user-friends mr-2 text-purple-500"></i> Parent
                            </option>
                            <option value="sibling" class="flex items-center">
                                <i class="fas fa-users mr-2 text-green-500"></i> Sibling
                            </option>
                            <option value="relative" class="flex items-center">
                                <i class="fas fa-user-tag mr-2 text-yellow-500"></i> Relative
                            </option>
                            <option value="other" class="flex items-center">
                                <i class="fas fa-user mr-2 text-gray-500"></i> Other
                            </option>
                        </select>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
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
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md transition-all duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add to Family
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddMemberModal() {
            const modal = document.getElementById('addMemberModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

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
            }, 200);
        }

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAddMemberModal();
        });

        // Close modal when clicking outside
        document.getElementById('addMemberModal').addEventListener('click', (e) => {
            if (e.target.id === 'addMemberModal') closeAddMemberModal();
        });
    </script>

@endsection
