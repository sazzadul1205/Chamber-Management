@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Users Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.user.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Add',
                        'class' => 'w-4 h-4 text-white',
                    ])
                        <span>Add New User</span>
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
            <form method="GET" action="{{ route('backend.user.index') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by name, phone or email"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                </div>

                <div class="md:col-span-3">
                    <select name="role_id"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <select name="status"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                        Filter
                    </button>
                    <a href="{{ route('backend.user.index') }}"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md px-4 py-2 text-center font-medium">
                        Reset
                    </a>
                </div>
            </form>

            <!-- TABLE -->
            <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Role</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Last Login</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Created</th>
                            <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                                            <tr class="hover:bg-gray-50 even:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                                </td>

                                                <!-- User Column -->
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            @if($user->avatar)
                                                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                                     alt="{{ $user->full_name }}" 
                                                                     class="w-full h-full object-cover rounded-full">
                                                            @else
                                                                <span class="text-blue-600 font-semibold text-sm">
                                                                    {{ substr($user->full_name, 0, 1) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('backend.user.show', $user->id) }}"
                                                                class="text-gray-900 hover:text-blue-600 hover:underline font-medium block">
                                                                {{ $user->full_name }}
                                                            </a>
                                                            <span class="text-xs text-gray-500">
                                                                ID: {{ $user->id }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Contact Column -->
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-1">
                                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                                            </svg>
                                                            <span>{{ $user->phone }}</span>
                                                        </div>
                                                        @if($user->email)
                                                            <div class="flex items-center gap-1">
                                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                                                </svg>
                                                                <span class="text-xs truncate max-w-[150px]">{{ $user->email }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Role Column -->
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $user->role_id == 1 ? 'bg-purple-100 text-purple-800' :
                            ($user->role_id == 2 ? 'bg-blue-100 text-blue-800' :
                                ($user->role_id == 3 ? 'bg-green-100 text-green-800' :
                                    ($user->role_id == 4 ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-gray-100 text-gray-800'))) }}">
                                                        {{ $user->getRoleName() }}
                                                    </span>
                                                </td>

                                                <!-- Status Column with Toggle -->
                                                <td class="px-4 py-3">
                                                    @if(Auth::user()->role_id <= 2 && $user->id != Auth::id() && $user->role_id != 1)
                                                                                <form action="{{ route('backend.user.toggle-status', $user->id) }}" method="POST" class="inline-block" id="statusForm{{ $user->id }}">
                                                                                    @csrf
                                                                                    @method('PATCH')
                                                                                    <input type="hidden" name="current_status" value="{{ $user->status }}">

                                                                                    <button type="button" 
                                                                                        onclick="toggleStatus({{ $user->id }})"
                                                                                        class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                                                                        {{ $user->status == 'active' ? 'bg-green-500 hover:bg-green-600' :
                                                        ($user->status == 'inactive' ? 'bg-gray-400 hover:bg-gray-500' : 'bg-red-500 hover:bg-red-600') }}"
                                                                                        data-tooltip="{{ $user->status == 'active' ? 'Click to deactivate' : 'Click to activate' }}"
                                                                                        data-user-id="{{ $user->id }}"
                                                                                        data-user-name="{{ $user->full_name }}"
                                                                                        data-current-status="{{ $user->status }}">
                                                                                        <span class="sr-only">Toggle status</span>
                                                                                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300 ease-in-out
                                                                                            {{ $user->status == 'active' ? 'translate-x-6' : 'translate-x-1' }}">
                                                                                        </span>
                                                                                    </button>
                                                                                </form>
                                                                                <span class="ml-2 text-xs font-medium 
                                                                                    {{ $user->status == 'active' ? 'text-green-600' :
                                                        ($user->status == 'inactive' ? 'text-gray-600' : 'text-red-600') }}">
                                                                                    {{ ucfirst($user->status) }}
                                                                                </span>
                                                    @else
                                                        {!! $user->getStatusBadge() !!}
                                                    @endif
                                                </td>

                                                <!-- Last Login Column -->
                                                <td class="px-4 py-3 text-sm">
                                                    @if($user->last_login_at)
                                                        <span class="text-gray-600">{{ $user->last_login_at->format('d/m/Y') }}</span>
                                                        <br>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $user->last_login_at->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-xs">Never</span>
                                                    @endif
                                                </td>

                                                <!-- Created Column -->
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="text-gray-600">{{ $user->created_at->format('d/m/Y') }}</span>
                                                </td>

                                                <!-- Actions Column -->
                                                <td class="px-4 py-3 text-center text-sm">
                                                    <div class="flex justify-center gap-1">
                                                        @php
                                                            $btnBaseClasses = 'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                                        @endphp

                                                        <!-- View -->
                                                        <a href="{{ route('backend.user.show', $user->id) }}"
                                                            class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600"
                                                            data-tooltip="View Details">
                                                            @include('partials.sidebar-icon', [
                                                                'name' => 'B_View',
                                                                'class' => 'w-4 h-4',
                                                            ])
                                                            <span class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                                                View Details
                                                            </span>
                                                        </a>

                                                        <!-- Edit -->
                                                        @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                                                            <a href="{{ route('backend.user.edit', $user->id) }}"
                                                                class="{{ $btnBaseClasses }} bg-yellow-500 hover:bg-yellow-600"
                                                                data-tooltip="Edit User">
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'B_Edit',
                                                                    'class' => 'w-4 h-4',
                                                                ])
                                                                <span class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                                                    Edit User
                                                                </span>
                                                            </a>
                                                        @endif

                                                        <!-- Delete -->
                                                        @php
                                                            $disableDelete = false;
                                                            if ($user->role_id == 1) {
                                                                // Super Admin can never be deleted
                                                                $disableDelete = true;
                                                            } elseif ($user->role_id == 2 && auth()->user()->role_id != 1) {
                                                                // Admin can only be deleted by Super Admin
                                                                $disableDelete = true;
                                                            } elseif ($user->id === auth()->id()) {
                                                                // Cannot delete own account
                                                                $disableDelete = true;
                                                            }
                                                        @endphp

                                                        @if (!$disableDelete && Auth::user()->role_id <= 2)
                                                            <button type="button" 
                                                                data-modal-target="deleteModal"
                                                                data-route="{{ route('backend.user.destroy', $user->id) }}"
                                                                data-name="{{ $user->full_name }}"
                                                                data-user-id="{{ $user->id }}"
                                                                class="{{ $btnBaseClasses }} bg-red-600 hover:bg-red-700"
                                                                data-tooltip="Delete User">
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'B_Delete',
                                                                    'class' => 'w-4 h-4',
                                                                ])
                                                                <span class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                                                    Delete User
                                                                </span>
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="{{ $btnBaseClasses }} bg-gray-400 cursor-not-allowed"
                                                                disabled
                                                                data-tooltip="Cannot delete">
                                                                @include('partials.sidebar-icon', [
                                                                    'name' => 'B_Delete',
                                                                    'class' => 'w-4 h-4',
                                                                ])
                                                                <span class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                                                    Cannot delete
                                                                </span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.67 3.623a9 9 0 01-13.74 0" />
                                        </svg>
                                        <p class="text-gray-500">No users found</p>
                                        <p class="text-sm text-gray-400 mt-1">Try changing your filters or create a new user</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-4">
                <x-pagination :paginator="$users" />
            </div>
        </div>

        <!-- Status Toggle Confirmation Modal -->
        <div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold flex items-center gap-2" id="statusModalTitle">
                        @include('partials.icons', [
                            'name' => 'Info',
                            'class' => 'w-5 h-5',
                        ])
                        <span id="modalTitleText"></span>
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 mb-4" id="statusModalMessage"></p>

                    <form id="statusToggleForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="current_status" id="currentStatusInput">

                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" data-modal-hide="statusModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-4 py-2" 
                                id="statusSubmitBtn">
                                Confirm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold flex items-center gap-2 text-red-600">
                        @include('partials.icons', [
                            'name' => 'Delete',
                            'class' => 'w-5 h-5',
                        ])
                        Delete User Account
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 mb-4">
                        Are you sure you want to delete <strong id="deleteUserName"></strong>? 
                        This action cannot be undone.
                    </p>

                    <form id="deleteForm" method="POST" class="space-y-4">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for deletion (optional)
                            </label>
                            <textarea name="deletion_reason" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-red-400"
                                placeholder="Enter reason for deleting this user account..."></textarea>
                        </div>

                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" data-modal-hide="deleteModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                Delete User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Status toggle functionality
                function toggleStatus(userId) {
                    const button = document.querySelector(`button[data-user-id="${userId}"]`);
                    if (!button) return;

                    const userName = button.getAttribute('data-user-name');
                    const currentStatus = button.getAttribute('data-current-status');
                    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                    const action = newStatus === 'active' ? 'activate' : 'deactivate';

                    // Update modal content
                    document.getElementById('modalTitleText').textContent = 
                        `${action === 'activate' ? 'Activate' : 'Deactivate'} User`;

                    document.getElementById('statusModalMessage').textContent = 
                        `Are you sure you want to ${action} ${userName}?`;

                    // Update form action
                    const form = document.getElementById('statusToggleForm');
                    form.action = `/backend/users/${userId}/toggle-status`;

                    // Update current status input
                    document.getElementById('currentStatusInput').value = currentStatus;

                    // Update submit button style based on action
                    const submitBtn = document.getElementById('statusSubmitBtn');
                    if (action === 'activate') {
                        submitBtn.textContent = 'Activate User';
                        submitBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm';
                    } else {
                        submitBtn.textContent = 'Deactivate User';
                        submitBtn.className = 'px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm';
                    }

                    // Show modal
                    const modal = document.getElementById('statusModal');
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                // Delete modal functionality
                const deleteModal = document.getElementById('deleteModal');
                const deleteForm = document.getElementById('deleteForm');
                const deleteUserName = document.getElementById('deleteUserName');

                const statusModal = document.getElementById('statusModal');
                const statusForm = document.getElementById('statusToggleForm');

                // Handle delete button clicks
                document.querySelectorAll('[data-modal-target="deleteModal"]').forEach(button => {
                    button.addEventListener('click', function() {
                        const route = this.getAttribute('data-route');
                        const name = this.getAttribute('data-name');
                        const userId = this.getAttribute('data-user-id');

                        // Update modal content
                        deleteUserName.textContent = name;
                        deleteForm.action = route;

                        // Show modal
                        deleteModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });
                });

                // Close modal buttons for both modals
                function setupModalClose(modalId) {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;

                    // Close buttons
                    modal.querySelectorAll('[data-modal-hide]').forEach(btn => {
                        btn.addEventListener('click', function() {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        });
                    });

                    // Close when clicking outside
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                }

                // Setup close functionality for both modals
                setupModalClose('deleteModal');
                setupModalClose('statusModal');

                // Close with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.querySelectorAll('.hidden').forEach(modal => {
                            if (modal.id === 'deleteModal' || modal.id === 'statusModal') {
                                modal.classList.add('hidden');
                                document.body.style.overflow = '';
                            }
                        });
                    }
                });

                // Tooltip hover effect
                document.querySelectorAll('[data-tooltip]').forEach(element => {
                    element.addEventListener('mouseenter', function() {
                        const tooltip = this.querySelector('.tooltip, span');
                        if (tooltip) {
                            tooltip.classList.remove('hidden');
                        }
                    });

                    element.addEventListener('mouseleave', function() {
                        const tooltip = this.querySelector('.tooltip, span');
                        if (tooltip) {
                            tooltip.classList.add('hidden');
                        }
                    });
                });

                // Make toggleStatus function globally available
                window.toggleStatus = toggleStatus;
            });
        </script>
@endsection