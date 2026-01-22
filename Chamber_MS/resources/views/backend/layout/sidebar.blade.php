@php
    $menu = [
        // ===============================
        // AUTH & DASHBOARD
        // ===============================
        [
            'title' => 'Dashboard & Auth',
            'icon' => 'dashboard',
            'items' => [
                [
                    'label' => 'Main Dashboard',
                    'icon' => 'dashboard',
                    'route' => 'backend.dashboard',
                ],
                ['label' => 'Doctor Dashboard', 'icon' => 'dashboard'],
                ['label' => 'Reception Dashboard', 'icon' => 'dashboard'],
                ['label' => 'Admin Dashboard', 'icon' => 'dashboard'],
            ],
        ],

        // ===============================
        // PATIENT MANAGEMENT
        // ===============================
        [
            'title' => 'Patients',
            'icon' => 'users',
            'items' => [
                ['label' => 'Patient Registration'],
                ['label' => 'Patient List'],
                ['label' => 'Patient Profile'],
                ['label' => 'Edit Patient'],
                ['label' => 'Patient Search'],
                ['label' => 'Patient History'],
                ['label' => 'Family Management'],
                ['label' => 'Referral Tracking'],
            ],
        ],

        // ===============================
        // APPOINTMENTS
        // ===============================
        [
            'title' => 'Appointments',
            'icon' => 'calendar',
            'items' => [
                ['label' => 'Calendar View'],
                ['label' => 'Schedule Appointment'],
                ['label' => 'Appointment List'],
                ['label' => 'Reschedule Appointment'],
                ['label' => 'Cancel Appointment'],
                ['label' => 'Walk-in Appointment'],
                ['label' => 'Appointment Reminders'],
                ['label' => 'Queue Display (TV)'],
            ],
        ],

        // ===============================
        // DOCTORS
        // ===============================
        [
            'title' => 'Doctors',
            'icon' => 'stethoscope',
            'items' => [
                ['label' => 'Doctor Registration'],
                ['label' => 'Doctor List'],
                ['label' => 'Doctor Profile'],
                ['label' => 'Doctor Schedule'],
                ['label' => 'Leave Management'],
                ['label' => 'Commission Reports'],
            ],
        ],

        // ===============================
        // TREATMENTS
        // ===============================
        [
            'title' => 'Treatments',
            'icon' => 'activity',
            'items' => [
                ['label' => 'Treatment Plan'],
                ['label' => 'Treatment Sessions'],
                ['label' => 'Multi-Visit Tracking'],
                ['label' => 'Treatment Progress'],
                ['label' => 'Treatment Completion'],
                [
                    'label' => 'Procedure Catalog',
                    'icon' => 'list',
                    'route' => 'backend.procedure-catalog.index',
                ],
                [
                    'label' => 'Diagnosis Codes',
                    'icon' => 'file-text',
                    'route' => 'backend.diagnosis-codes.index',
                ],
                ['label' => 'Treatment History'],
            ],
        ],

        // ===============================
        // DENTAL CHARTING
        // ===============================
        [
            'title' => 'Dental Charting',
            'icon' => 'tooth',
            'items' => [
                ['label' => 'Dental Chart'],
                ['label' => 'Tooth Conditions'],
                ['label' => 'Chart History'],
                ['label' => 'X-Ray Viewer'],
                ['label' => 'Print Chart'],
            ],
        ],

        // ===============================
        // PRESCRIPTIONS
        // ===============================
        [
            'title' => 'Prescriptions',
            'icon' => 'file-text',
            'items' => [
                ['label' => 'Create Prescription'],
                ['label' => 'Prescription Templates'],
                ['label' => 'Prescription History'],
                ['label' => 'Medicine Catalog'],
                ['label' => 'Print Prescription'],
            ],
        ],

        // ===============================
        // INVENTORY
        // ===============================
        [
            'title' => 'Inventory',
            'icon' => 'boxes',
            'items' => [
                ['label' => 'Inventory Dashboard'],
                ['label' => 'Stock List'],
                ['label' => 'Add / Update Stock'],
                ['label' => 'Low Stock Alerts'],
                ['label' => 'Purchase Orders'],
                ['label' => 'Usage Tracking'],
                ['label' => 'Suppliers'],
                ['label' => 'Expiry Tracking'],
            ],
        ],

        // ===============================
        // BILLING & PAYMENTS
        // ===============================
        [
            'title' => 'Billing & Payments',
            'icon' => 'credit-card',
            'items' => [
                ['label' => 'Invoice Generation'],
                ['label' => 'Invoice List'],
                ['label' => 'Payment Collection'],
                ['label' => 'Installments'],
                ['label' => 'Partial Payments'],
                ['label' => 'Receipts'],
                ['label' => 'Outstanding Payments'],
                ['label' => 'Payment History'],
                ['label' => 'Advance Payments'],
                ['label' => 'Refunds'],
            ],
        ],

        // ===============================
        // REPORTS
        // ===============================
        [
            'title' => 'Reports & Analytics',
            'icon' => 'bar-chart',
            'items' => [
                ['label' => 'Daily / Monthly Reports'],
                ['label' => 'Financial Reports'],
                ['label' => 'Patient Statistics'],
                ['label' => 'Doctor Performance'],
                ['label' => 'Treatment Reports'],
                ['label' => 'Inventory Reports'],
                ['label' => 'Appointment Analytics'],
                ['label' => 'Revenue Reports'],
            ],
        ],

        // ===============================
        // SYSTEM SETTINGS
        // ===============================
        [
            'title' => 'System',
            'icon' => 'settings',
            'items' => [
                ['label' => 'User Management'],
                [
                    'label' => 'Roles & Permissions',
                    'icon' => 'key',
                    'route' => 'backend.roles.index',
                ],
                [
                    'label' => 'System Settings',
                    'icon' => 'cog',
                    'route' => 'backend.system-settings.index',
                ],
                ['label' => 'Backup & Restore'],
                ['label' => 'Audit Logs'],
                ['label' => 'Notifications'],
                ['label' => 'Holiday Management'],
                ['label' => 'Clinic Configuration'],
            ],
        ],
    ];
@endphp

@php
    $currentRoute = Route::currentRouteName();
@endphp

<aside class="bg-white w-64 border-r flex flex-col transition-all duration-300"
    :class="sidebarOpen ? 'block' : 'hidden md:block'">

    <!-- Brand -->
    <img class="mx-auto h-16 w-auto p-4" src="{{ asset('assets/Website_Logo.png') }}" alt="My App Logo">

    <!-- Menu Links -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        @foreach ($menu as $item)
            @php
                $hasChildren = isset($item['items']);
                // Check if any sub-item is active
                $groupActive = false;
                if ($hasChildren) {
                    foreach ($item['items'] as $sub) {
                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                        if ($subRoute && $subRoute === $currentRoute) {
                            $groupActive = true;
                            break;
                        }
                    }
                }
            @endphp

            {{-- Group with items --}}
            @if ($hasChildren)
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-gray-100">
                        <div class="flex items-center gap-2 font-semibold">
                            @include('partials.sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                            <span>{{ $item['title'] }}</span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" class="mt-2 space-y-1 ml-1 overflow-hidden whitespace-nowrap">
                        @foreach ($item['items'] as $sub)
                            @php
                                $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                                $active = $subRoute && $subRoute === $currentRoute;
                                $href = $subRoute ? route($subRoute) : '#';
                            @endphp
                            <a href="{{ $href }}"
                                class="flex items-center gap-3 px-3 py-2 rounded transition
                                                            {{ $active ? 'bg-blue-100 font-semibold text-blue-600' : 'hover:bg-gray-100 text-gray-700' }}">
                                @include('partials.sidebar-icon', ['name' => $sub['icon'] ?? 'default'])
                                <span>{{ $sub['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Independent single item --}}
            @else
                @php
                    $subRoute = $item['route'] ?? ($item['link'] ?? '');
                    $active = $subRoute && $subRoute === $currentRoute;
                    $href = $subRoute ? route($subRoute) : '#';
                @endphp
                <a href="{{ $href }}" class="flex items-center gap-3 px-3 py-2 rounded transition font-semibold
                                        {{ $active ? 'bg-gray-200 text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}">
                    @include('partials.sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- Profile & Logout -->
    <div class="border-t p-4 flex flex-col gap-2">
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100">
            <img src="{{ asset('assets/Default_User.png') }}" class="w-10 h-10 rounded-full border" alt="User">
            <div class="flex flex-col text-sm">
                <span class="font-semibold text-gray-800">John Doe</span>
                <span class="text-gray-500">Administrator</span>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded
                           hover:bg-red-100 text-red-600 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>