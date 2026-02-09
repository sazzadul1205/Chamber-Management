@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                User Details: {{ $user->full_name }}
            </h2>

            <div class="flex flex-wrap gap-2">
                @if (Auth::id() == $user->id || Auth::user()->role_id <= 2) {{-- Allow if same user or admin/superadmin --}}
                    <a href="{{ route('backend.user.edit', $user->id) }}"
                        class="flex justify-center items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition px-4 py-2 w-40">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Edit',
                            'class' => 'w-4 h-4',
                        ])
                        <span class="text-center flex-1">Edit User</span>
                    </a>
                @endif

                <a href="{{ route('backend.user.index') }}"
                    class="flex justify-center items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Back to List</span>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- User Info Card -->
            <div class="bg-white border rounded-xl shadow p-6 space-y-4">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-3 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->full_name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($user->full_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <h3 class="text-xl font-semibold">{{ $user->full_name }}</h3>
                    <p class="text-gray-600">{{ $user->email ?? 'No email' }}</p>

                    <div class="flex items-center justify-center gap-2 mt-2">
                        {!! $user->getStatusBadge() !!}
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $user->getRoleName() }}
                        </span>
                    </div>
                </div>

                <table class="w-full text-sm text-gray-700 mt-4">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Phone Number:</th>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Email:</th>
                            <td>{{ $user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Role:</th>
                            <td>{{ $user->getRoleName() }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Created At:</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Last Updated:</th>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if ($user->deleted_at)
                            <tr>
                                <th class="py-2 text-left font-medium text-red-600">Deleted At:</th>
                                <td class="text-red-600">{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-6">
                <!-- User Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Status</h6>
                        <h3 class="text-xl font-bold">
                            {{ $user->deleted_at ? 'Deleted' : ucfirst($user->status) }}
                        </h3>
                    </div>
                    <div class="bg-green-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Role</h6>
                        <h3 class="text-xl font-bold">{{ $user->getRoleName() }}</h3>
                    </div>
                </div>

                <!-- Doctor Profile (if applicable) -->
                @if ($user->isDoctor())
                    <div class="bg-white border rounded-xl shadow p-6">
                        <h4 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Doctor',
                                'class' => 'w-5 h-5 text-green-600',
                            ])
                            Doctor Profile
                        </h4>

                        @if ($user->doctor)
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-green-600 font-semibold">
                                        D
                                    </span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Dr. {{ $user->doctor->user->full_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $user->doctor->specialization ?? 'General Dentist' }}
                                    </p>
                                </div>
                            </div>

                            <table class="w-full text-sm text-gray-700">
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <th class="py-1 text-left font-medium w-1/2">License No:</th>
                                        <td>{{ $user->doctor->license_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="py-1 text-left font-medium">Department:</th>
                                        <td>{{ $user->doctor->department ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <a href="{{ route('doctors.show', $user->doctor->id) }}"
                                class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm">
                                @include('partials.icons', ['name' => 'Doctor', 'class' => 'w-4 h-4'])
                                View Doctor Profile
                            </a>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-400 mb-4">No doctor profile created</p>
                                @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                                    <a href="#"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Add',
                                            'class' => 'w-4 h-4',
                                        ])
                                        Create Doctor Profile
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Quick Actions & Timeline -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Quick Actions</h4>

                    <div class="space-y-3">
                        @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                            <button data-modal-target="resetPasswordModal"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">
                                @include('partials.icons', ['name' => 'Key', 'class' => 'w-4 h-4'])
                                Reset Password
                            </button>
                        @endif

                        @if ($user->deleted_at)
                            @if (Auth::user()->role_id <= 2) {{-- Only admin/superadmin can restore --}}
                                <form action="{{ route('backend.user.restore', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Restore this user?')"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                        @include('partials.icons', ['name' => 'Restore', 'class' => 'w-4 h-4'])
                                        Restore User
                                    </button>
                                </form>
                            @endif
                        @else
                            @if (Auth::user()->role_id <= 2 && Auth::id() != $user->id) {{-- Admin can delete others, not themselves --}}
                                <button data-modal-target="deleteUserModal"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                    @include('partials.icons', ['name' => 'Delete', 'class' => 'w-4 h-4'])
                                    Delete User
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- User Timeline -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">User Timeline</h4>

                    <div class="space-y-4">
                        @php
                            $timelineEvents = [
                                [
                                    'icon' => '👤',
                                    'title' => 'Account Created',
                                    'time' => $user->created_at,
                                    'color' => 'blue',
                                    'completed' => true,
                                ],
                                [
                                    'icon' => '📅',
                                    'title' => 'Last Login',
                                    'time' => $user->last_login_at,
                                    'color' => 'green',
                                    'completed' => !is_null($user->last_login_at),
                                ],
                                [
                                    'icon' => '🔄',
                                    'title' => 'Last Updated',
                                    'time' => $user->updated_at,
                                    'color' => 'yellow',
                                    'completed' => true,
                                ],
                            ];
                            
                            if ($user->deleted_at) {
                                $timelineEvents[] = [
                                    'icon' => '🗑️',
                                    'title' => 'Account Deleted',
                                    'time' => $user->deleted_at,
                                    'color' => 'red',
                                    'completed' => true,
                                ];
                            }
                        @endphp

                        @foreach ($timelineEvents as $event)
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full 
                                    {{ $event['completed'] ? "bg-{$event['color']}-100" : 'bg-gray-100' }} 
                                    flex items-center justify-center">
                                    {{ $event['icon'] }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="font-medium {{ $event['completed'] ? 'text-gray-800' : 'text-gray-400' }}">
                                            {{ $event['title'] }}
                                        </span>
                                        @if ($event['time'])
                                            <span class="text-xs text-gray-500">
                                                {{ $event['time']->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if (!$event['completed'])
                                        <p class="text-xs text-gray-400 mt-1">Never logged in</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Deletion Warning -->
        @if ($user->deleted_at)
            <div class="bg-red-50 border border-red-200 rounded-xl shadow p-6">
                <h4 class="text-lg font-semibold text-red-800 mb-2">Account Deleted</h4>
                <p class="text-red-700">This user account has been deleted from the system.</p>
                <p class="text-sm text-red-600 mt-2">
                    Deleted on {{ $user->deleted_at->format('d/m/Y H:i') }}
                </p>
            </div>
        @endif
    </div>

    <!-- Reset Password Modal -->
@if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
    <div id="resetPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    @include('partials.icons', [
                        'name' => 'Key',
                        'class' => 'w-5 h-5 text-yellow-500',
                    ])
                    @if (Auth::id() == $user->id)
                        Change Password
                    @else
                        Reset Password for {{ $user->full_name }}
                    @endif
                </h3>
            </div>

            <form action="{{ route('backend.user.reset-password', $user->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="p-6 space-y-4">
                    @if (Auth::id() == $user->id)
                        <!-- Current password field for user changing their own password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                            <input type="password" name="current_password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-400"
                                placeholder="Enter your current password">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-400"
                            placeholder="Enter new password">
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-400"
                            placeholder="Confirm new password">
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-2">
                    <button type="button" data-modal-hide="resetPasswordModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                        @if (Auth::id() == $user->id)
                            Change Password
                        @else
                            Reset Password
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

    <!-- Delete User Modal -->
    @if (Auth::user()->role_id <= 2 && Auth::id() != $user->id)
        <div id="deleteUserModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
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
                        Are you sure you want to delete <strong>{{ $user->full_name }}</strong>? 
                        This action cannot be undone.
                    </p>
                    
                    <form action="{{ route('backend.user.destroy', $user->id) }}" method="POST" class="space-y-4">
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
                            <button type="button" data-modal-hide="deleteUserModal"
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
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset Password Modal
            const resetPasswordBtn = document.querySelector('[data-modal-target="resetPasswordModal"]');
            const resetPasswordModal = document.getElementById('resetPasswordModal');
            
            // Delete User Modal
            const deleteUserBtn = document.querySelector('[data-modal-target="deleteUserModal"]');
            const deleteUserModal = document.getElementById('deleteUserModal');

            function setupModal(button, modal) {
                if (!button || !modal) return;
                
                const closeBtns = modal.querySelectorAll('[data-modal-hide]');
                
                button.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });

                closeBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    });
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            }

            setupModal(resetPasswordBtn, resetPasswordModal);
            setupModal(deleteUserBtn, deleteUserModal);
        });
    </script>
@endsection