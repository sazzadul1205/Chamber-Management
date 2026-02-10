@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Users Management</h2>
                <p class="text-gray-600 mt-1">Manage all system users and their permissions</p>
            </div>

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
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('backend.user.index') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Name, phone, email"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
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

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                    <select name="blood_group"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Groups</option>
                        <option value="A+" {{ request('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ request('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ request('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ request('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ request('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ request('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ request('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ request('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        <option value="last_login" {{ request('sort') == 'last_login' ? 'selected' : '' }}>Last Login</option>
                    </select>
                </div>

                <div class="md:col-span-1 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium"
                        title="Apply filters">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </button>
                    <a href="{{ route('backend.user.index') }}"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md px-4 py-2 text-center font-medium"
                        title="Reset filters">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Users</p>
                        <p class="text-2xl font-bold text-green-600">{{ $activeUsers }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Online Now</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $onlineUsers }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Last 24 Hours</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $recentLogins }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Blood Group</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Last Login</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $user->id }}
                            </td>

                            <!-- User Column -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border-2 border-white">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                     alt="{{ $user->full_name }}" 
                                                     class="w-full h-full object-cover rounded-full">
                                            @else
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ substr($user->full_name, 0, 1) }}
                                                </span>
                                            @endif
                                            @if($user->is_online)
                                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('backend.user.show', $user->id) }}"
                                            class="text-gray-900 hover:text-blue-600 hover:underline font-medium block">
                                            {{ $user->full_name }}
                                        </a>
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $user->created_at->format('d/m/Y') }}
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
                                        <span class="font-medium">{{ $user->phone }}</span>
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

                            <!-- Blood Group Column -->
                            <td class="px-4 py-3">
                                @if($user->blood_group)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                        {!! $user->blood_group_display !!}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            <!-- Role Column -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $user->role_id == 1 ? 'bg-purple-100 text-purple-800' :
                                    ($user->role_id == 2 ? 'bg-blue-100 text-blue-800' :
                                    ($user->role_id == 3 ? 'bg-green-100 text-green-800' :
                                    ($user->role_id == 4 ? 'bg-yellow-100 text-yellow-800' :
                                    ($user->role_id == 5 ? 'bg-indigo-100 text-indigo-800' :
                                    'bg-gray-100 text-gray-800')))) }}">
                                    {{ $user->getRoleName() }}
                                </span>
                            </td>

                            <!-- Status Column with Toggle -->
                            <td class="px-4 py-3">
                                @if(Auth::user()->role_id <= 2 && $user->id != Auth::id() && $user->role_id != 1)
                                    <div class="flex items-center gap-2">
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
                                        <span class="text-xs font-medium 
                                            {{ $user->status == 'active' ? 'text-green-600' :
                                            ($user->status == 'inactive' ? 'text-gray-600' : 'text-red-600') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                @else
                                    {!! $user->status_badge !!}
                                @endif
                            </td>

                            <!-- Last Login Column -->
                            <td class="px-4 py-3 text-sm">
                                @if($user->last_login_at)
                                    <div class="space-y-1">
                                        <span class="text-gray-600">{{ $user->last_login_at->format('d/m/Y') }}</span>
                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $user->last_login_at->format('h:i A') }}
                                        </div>
                                        @if($user->last_login_device_id)
                                        <div class="text-xs text-gray-400 truncate max-w-[120px]" title="{{ $user->last_login_device_id }}">
                                            {{ Str::limit($user->last_login_device_id, 20) }}
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Never logged in</span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    @php
                                        $btnBaseClasses = 'relative flex items-center justify-center p-2 rounded text-white hover:shadow transition group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.user.show', $user->id) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600"
                                        data-tooltip="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                                        <a href="{{ route('backend.user.edit', $user->id) }}"
                                            class="{{ $btnBaseClasses }} bg-yellow-500 hover:bg-yellow-600"
                                            data-tooltip="Edit User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    <!-- Delete -->
                                    @php
                                        $disableDelete = false;
                                        if ($user->role_id == 1) {
                                            $disableDelete = true;
                                        } elseif ($user->role_id == 2 && auth()->user()->role_id != 1) {
                                            $disableDelete = true;
                                        } elseif ($user->id === auth()->id()) {
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
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button"
                                            class="{{ $btnBaseClasses }} bg-gray-400 cursor-not-allowed"
                                            disabled
                                            data-tooltip="Cannot delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.67 3.623a9 9 0 01-13.74 0"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No users found</p>
                                    <p class="text-sm text-gray-400 mt-1 max-w-md">
                                        Try adjusting your search filters or 
                                        <a href="{{ route('backend.user.create') }}" class="text-blue-600 hover:underline">create a new user</a>.
                                    </p>
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
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
                let tooltip = null;
                
                element.addEventListener('mouseenter', function() {
                    const text = this.getAttribute('data-tooltip');
                    if (!text) return;

                    // Create tooltip
                    tooltip = document.createElement('div');
                    tooltip.className = 'absolute z-50 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded shadow-sm whitespace-nowrap bottom-full left-1/2 transform -translate-x-1/2 mb-1';
                    tooltip.textContent = text;
                    
                    // Position
                    this.style.position = 'relative';
                    this.appendChild(tooltip);
                });

                element.addEventListener('mouseleave', function() {
                    if (tooltip) {
                        tooltip.remove();
                        tooltip = null;
                    }
                });
            });

            // Make toggleStatus function globally available
            window.toggleStatus = toggleStatus;
        });
    </script>
@endsection