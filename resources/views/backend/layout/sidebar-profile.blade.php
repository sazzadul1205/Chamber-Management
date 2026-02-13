@php
    $user = Auth::user();
    $roleMap = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Doctor',
        4 => 'Receptionist',
        5 => 'Accountant'
    ];
    $userRoleName = $roleMap[$user->role_id] ?? 'User';
@endphp

<!-- User Profile -->
<div class="border-gray-200">
    <a href="#"
        class="tooltip-trigger sidebar-profile-item flex items-center gap-3 rounded hover:bg-gray-100 transition-all duration-200 p-2"
        data-tooltip="{{ $user->full_name }} ({{ $userRoleName }})">
        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/Default_User.png') }}"
            class="sidebar-profile-avatar w-10 h-10 rounded-full border border-gray-300 transition-all duration-200"
            alt="{{ $user->name }}">
        <div class="sidebar-text flex flex-col text-sm truncate min-w-0">
            <span class="font-semibold text-gray-800 truncate">{{ $user->full_name }}</span>
            <span class="text-gray-500 truncate">{{ $userRoleName }}</span>
        </div>
    </a>

    <!-- Logout Button -->
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit"
            class="tooltip-trigger sidebar-profile-item w-full flex items-center gap-2 px-3 py-2 rounded hover:bg-red-100 transition-all duration-200 text-red-600 font-medium"
            data-tooltip="Logout">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5" />
            </svg>
            <span class="sidebar-text">Logout</span>
        </button>
    </form>
</div>

<style>
    .sidebar-collapsed .sidebar-profile-item {
        justify-content: center;
    }

    .sidebar-collapsed .sidebar-profile-avatar {
        width: 2rem;
        height: 2rem;
    }
</style>
