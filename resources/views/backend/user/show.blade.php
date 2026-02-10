@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                    <h2 class="text-3xl font-bold text-gray-800">User Details</h2>
                    <p class="text-gray-600 mt-1">Complete user information and activity overview</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                        <a href="{{ route('backend.user.edit', $user->id) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit User</span>
                        </a>
                    @endif

                    <a href="{{ route('backend.user.index') }}"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Back to Users</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: User Profile Card -->
                <div class="space-y-6">
                    <!-- User Profile Card -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <!-- Profile Header -->
                        <div class="relative bg-gradient-to-r from-blue-500 to-blue-600 h-20">
                            @if($user->deleted_at)
                                <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                    Deleted
                                </div>
                            @endif
                        </div>

                        <!-- Profile Body -->
                        <div class="px-6 pb-6 -mt-10">
                            <!-- Avatar -->
                            <div class="relative mb-4">
                                <div class="w-20 h-20 mx-auto rounded-full bg-white border-4 border-white shadow-lg overflow-hidden">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="{{ $user->full_name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                                            {{ substr($user->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                @if($user->is_online)
                                    <span class="absolute bottom-0 right-1/4 transform translate-x-1/2 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>

                            <!-- User Info -->
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h3>
                                <p class="text-gray-600">{{ $user->email ?? 'No email provided' }}</p>

                                <div class="flex items-center justify-center gap-2 mt-2">
                                    {!! $user->status_badge !!}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->role_id == 1 ? 'bg-purple-100 text-purple-800' :
        ($user->role_id == 2 ? 'bg-blue-100 text-blue-800' :
            ($user->role_id == 3 ? 'bg-green-100 text-green-800' :
                ($user->role_id == 4 ? 'bg-yellow-100 text-yellow-800' :
                    'bg-gray-100 text-gray-800'))) }}">
                                        {{ $user->getRoleName() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-blue-50 rounded-lg p-3 text-center">
                                    <div class="text-blue-600 text-sm font-medium">Status</div>
                                    <div class="text-lg font-bold text-blue-700">
                                        {{ $user->deleted_at ? 'Deleted' : ucfirst($user->status) }}
                                    </div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3 text-center">
                                    <div class="text-green-600 text-sm font-medium">Member Since</div>
                                    <div class="text-lg font-bold text-green-700">
                                        {{ $user->created_at->format('M Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-900 border-b pb-2">Personal Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ $user->phone }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium">{{ $user->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Blood Group:</span>
                                        <span class="font-medium">
                                            {!! $user->blood_group ? $user->blood_group_display : '<span class="text-gray-400">Not set</span>' !!}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">User ID:</span>
                                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $user->id }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Account Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Created At:</span>
                                <span class="font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Last Updated:</span>
                                <span class="font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Last Login:</span>
                                <span class="font-medium">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                        <span class="text-xs text-gray-500 block">({{ $user->last_login_human }})</span>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </span>
                            </div>
                            @if($user->last_login_device_id)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Last Device:</span>
                                    <span class="font-medium text-sm text-gray-600 truncate max-w-[150px]" title="{{ $user->last_login_device_id }}">
                                        {{ Str::limit($user->last_login_device_id, 25) }}
                                    </span>
                                </div>
                            @endif
                            @if($user->deleted_at)
                                <div class="flex justify-between items-center text-red-600">
                                    <span class="font-medium">Deleted At:</span>
                                    <span class="font-medium">{{ $user->deleted_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Activity & Timeline -->
                <div class="space-y-6">
                    <!-- Login Activity -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Login Activity</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Login Status</p>
                                        <p class="text-sm text-gray-600">
                                            @if($user->is_online)
                                                <span class="inline-flex items-center text-green-600">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>
                                                    Currently Online
                                                </span>
                                            @else
                                                <span class="text-gray-500">Currently Offline</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Last Login</p>
                                    <p class="font-semibold text-gray-900">
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->format('d M Y') }}
                                        @else
                                            <span class="text-gray-400">Never</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Time Active</p>
                                    <p class="font-semibold text-gray-900">
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Timeline -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">User Timeline</h4>
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
                                        'icon' => '🔑',
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
                                    <div class="relative">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full 
                                            {{ $event['completed'] ? "bg-{$event['color']}-100" : 'bg-gray-100' }} 
                                            flex items-center justify-center">
                                            <span class="text-lg">{{ $event['icon'] }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="absolute left-5 top-10 w-0.5 h-8 {{ $event['completed'] ? "bg-{$event['color']}-200" : 'bg-gray-200' }}"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 pt-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="font-medium {{ $event['completed'] ? 'text-gray-800' : 'text-gray-400' }}">
                                                    {{ $event['title'] }}
                                                </span>
                                                @if(!$event['completed'])
                                                    <p class="text-xs text-gray-400 mt-1">Never occurred</p>
                                                @endif
                                            </div>
                                            @if ($event['time'])
                                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $event['time']->format('d M, H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Doctor Profile Section (if applicable) -->
                    @if ($user->isDoctor())
                        <div class="bg-white rounded-xl shadow p-6">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Doctor Profile
                            </h4>

                            @if ($user->doctor)
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-green-600 font-bold text-lg">D</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Dr. {{ $user->full_name }}</p>
                                            <p class="text-sm text-green-600">{{ $user->doctor->specialization ?? 'General Practitioner' }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <p class="text-xs text-gray-600">License No</p>
                                            <p class="font-semibold text-gray-900">{{ $user->doctor->license_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <p class="text-xs text-gray-600">Department</p>
                                            <p class="font-semibold text-gray-900">{{ $user->doctor->department ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <a href="{{ route('backend.doctors.show', $user->doctor->id) }}"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        View Doctor Profile
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-gray-500 mb-4">No doctor profile created yet</p>
                                    @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                                        <a href="{{ route('backend.doctors.create') }}?user_id={{ $user->id }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Create Doctor Profile
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column: Quick Actions & System Info -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
                                <button data-modal-target="resetPasswordModal"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Reset Password
                                </button>
                            @endif

                            @if ($user->deleted_at)
                                @if (Auth::user()->role_id <= 2)
                                    <form action="{{ route('backend.user.restore', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Restore this user?')"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            Restore User
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if (Auth::user()->role_id <= 2 && Auth::id() != $user->id)
                                    <button data-modal-target="deleteUserModal"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete User
                                    </button>
                                @endif
                            @endif

                            <a href="{{ route('backend.user.index') }}"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Users
                            </a>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">System Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Current Session:</span>
                                <span class="font-medium">
                                    @if($user->current_session_id)
                                        <span class="text-green-600">Active</span>
                                    @else
                                        <span class="text-gray-400">No active session</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Email Verified:</span>
                                <span class="font-medium">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">{{ $user->email_verified_at->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-red-600">Not verified</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Account Type:</span>
                                <span class="font-medium">
                                    @if($user->isSuperAdmin())
                                        <span class="text-purple-600">Super Admin</span>
                                    @elseif($user->isAdmin())
                                        <span class="text-blue-600">Administrator</span>
                                    @elseif($user->isDoctor())
                                        <span class="text-green-600">Medical Staff</span>
                                    @else
                                        <span class="text-gray-600">Regular User</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Database ID:</span>
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $user->id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deletion Warning (if deleted) -->
                    @if ($user->deleted_at)
                        <div class="bg-red-50 border border-red-200 rounded-xl shadow p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.196 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-red-800">Account Deleted</h4>
                                    <p class="text-red-700 text-sm mt-1">This user account has been deleted from the system.</p>
                                    <p class="text-xs text-red-600 mt-2">
                                        Deleted on {{ $user->deleted_at->format('d F Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        @if (Auth::id() == $user->id || Auth::user()->role_id <= 2)
            <div id="resetPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            @if (Auth::id() == $user->id)
                                Change Your Password
                            @else
                                Reset Password for {{ $user->full_name }}
                            @endif
                        </h3>
                    </div>

                    <form action="{{ route('backend.user.reset-password', $user->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="p-6 space-y-4">
                            @if (Auth::id() == $user->id)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                                    <input type="password" name="current_password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                                        placeholder="Enter your current password">
                                    @error('current_password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                                <input type="password" name="new_password" required minlength="6"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                                    placeholder="Enter new password">
                                @error('new_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                                <input type="password" name="new_password_confirmation" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                                    placeholder="Confirm new password">
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t flex justify-end gap-2">
                            <button type="button" data-modal-hide="resetPasswordModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                    placeholder="Enter reason for deleting this user account..."></textarea>
                            </div>

                            <div class="pt-4 flex justify-end gap-2">
                                <button type="button" data-modal-hide="deleteUserModal"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                                <button type="submit" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition">
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

                    button.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });

                    modal.querySelectorAll('[data-modal-hide]').forEach(btn => {
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