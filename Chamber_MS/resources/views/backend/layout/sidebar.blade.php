@php
    $menu = [
        // ===============================
        // AUTH & DASHBOARD
        // ===============================

        [
            'label' => 'Main Dashboard',
            'icon' => 'dashboard',
            'route' => 'backend.dashboard',
        ],
        ['label' => 'Doctor Dashboard', 'icon' => 'dashboard'],
        ['label' => 'Reception Dashboard', 'icon' => 'dashboard'],
        ['label' => 'Admin Dashboard', 'icon' => 'dashboard'],

        // ===============================
        // PATIENT MANAGEMENT
        // ===============================
        [
            'title' => 'Patients',
            'icon' => 'Patient',
            'items' => [
                [
                    'label' => 'Patient Registration',
                    'icon' => 'User-Plus',
                    'route' => 'backend.patients.create',
                ],
                [
                    'label' => 'Patient List',
                    'icon' => 'list',
                    'route' => 'backend.patients.index',
                ],
                [
                    'label' => 'Family Management',
                    'icon' => 'Family',
                    'route' => 'backend.patient-families.index',
                ],
                [
                    'label' => 'Referral Tracking',
                    'icon' => 'share-alt',
                    'route' => 'backend.patients.search', // assuming search route
                ],
            ],
        ],

        // ===============================
        // APPOINTMENTS
        // ===============================
        [
            'title' => 'Appointments',
            'icon' => 'Appointment',
            'items' => [
                [
                    'label' => 'Calendar View',
                    'route' => 'backend.appointments.calendar',
                    'icon' => 'Calendar',
                ],
                [
                    'label' => 'Schedule Appointment',
                    'route' => 'backend.appointments.create',
                    'icon' => 'Add-Circle',
                ],
                [
                    'label' => 'Appointment List',
                    'route' => 'backend.appointments.index',
                    'icon' => 'list',
                ],
                [
                    'label' => 'Appointment Reminders',
                    'route' => '', // TODO: Add route
                    'icon' => 'bell',
                ],
                [
                    'label' => 'Queue Display (TV)',
                    'route' => 'backend.appointments.queue', // Add route in web.php
                    'icon' => 'TV',
                ],
            ],
        ],

        // ===============================
        // DOCTORS
        // ===============================
        [
            'title' => 'Doctors',
            'icon' => 'Doctor',
            'items' => [
                [
                    'label' => 'Doctor Registration',
                    'icon' => 'User-Plus',
                    'route' => 'backend.doctors.create',
                ],
                [
                    'label' => 'Doctor List',
                    'icon' => 'list',
                    'route' => 'backend.doctors.index',
                ],
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
            'icon' => 'Treatment',
            'items' => [
                [
                    'label' => 'Treatment Plan',
                    'icon' => 'Treatment_Plan',
                    'route' => 'backend.treatments.index',
                ],
                [
                    'label' => 'Treatment Sessions',
                    'icon' => 'Treatment_Session',
                    'route' => 'backend.treatment-sessions.index',
                ],
                [
                    'label' => 'Treatment Procedures',
                    'icon' => 'Treatment_Procedure',
                    'route' => 'backend.treatment-procedures.index',
                ],
                [
                    'label' => 'Procedure Catalog',
                    'icon' => 'list',
                    'route' => 'backend.procedure-catalog.index',
                ],
                [
                    'label' => 'Diagnosis Codes',
                    'icon' => 'Diagnostic-Code',
                    'route' => 'backend.diagnosis-codes.index',
                ],
            ],
        ],

        // ===============================
        // DENTAL CHARTING
        // ===============================
        [
            'title' => 'Dental Charting',
            'icon' => 'Tooth',
            'items' => [
                [
                    'label' => 'Add Dental Record',
                    'icon' => 'Add_Tooth',
                    'route' => 'backend.dental-charts.create',
                ],
                [
                    'label' => 'Dental Records',
                    'icon' => 'list',
                    'route' => 'backend.dental-charts.index',
                ],
                ['label' => 'Tooth Conditions'],
                ['label' => 'Chart History'],
                ['label' => 'X-Ray Viewer'],
                ['label' => 'Print Chart'],
            ],
        ],

        // ===============================
        // DENTAL CHAIRS
        // ===============================
        [
            'title' => 'Dental Chairs',
            'icon' => 'Bed',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'grid',
                    'route' => 'backend.dental-chairs.dashboard',
                ],
                [
                    'label' => 'Chair List',
                    'icon' => 'Bed',
                    'route' => 'backend.dental-chairs.index',
                ],
                [
                    'label' => 'Add New Chair',
                    'icon' => 'Add-Circle',
                    'route' => 'backend.dental-chairs.create',
                ],
                [
                    'label' => 'Chair Schedule',
                    'icon' => 'Schedule',
                    'route' => 'backend.dental-chairs.schedule',
                ],
            ],
        ],

        // ===============================
        // PRESCRIPTIONS
        // ===============================
        [
            'title' => 'Prescriptions',
            'icon' => 'Prescription',
            'items' => [
                ['label' => 'Create Prescription', 'icon' => 'Add-Circle', 'route' => 'backend.prescriptions.create'],
                ['label' => 'Prescription Templates'],
                [
                    'label' => 'Prescription History',
                    'icon' => 'list',
                    'route' => 'backend.prescriptions.index',
                ],
                [
                    'label' => 'Medicine Catalog',
                    'icon' => 'medicine',
                    'route' => 'backend.medicines.index',
                ],
            ],
        ],

        // ===============================
        // INVENTORY
        // ===============================
        [
            'title' => 'Inventory',
            'icon' => 'boxes',
            'items' => [
                [
                    'label' => 'Inventory Dashboard',
                    'route' => 'backend.inventory-items.index',
                    'icon' => 'grid',
                ],
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
            'icon' => 'Settings',
            'items' => [
                [
                    'label' => 'User Management',
                    'icon' => 'User',
                    'route' => 'backend.user.index',
                ],
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

<div class="flex flex-col h-full">

    <!-- Brand -->
    <img class="mx-auto h-16 w-auto p-4" src="{{ asset('assets/Website_Logo.png') }}" alt="Logo">

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto scrollbar-none">
        @foreach ($menu as $item)
            @php
                $hasChildren = isset($item['items']);
                $groupActive = false;
                if ($hasChildren) {
                    foreach ($item['items'] as $sub) {
                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                        if ($subRoute && $subRoute === $currentRoute) {
                            $groupActive = true;
                        }
                    }
                }
            @endphp

            @if ($hasChildren)
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-gray-100">
                        <div class="flex items-center gap-2 font-semibold">
                            @include('partials.sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                            <span>{{ $item['title'] }}</span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            @else
                @php
                    $subRoute = $item['route'] ?? ($item['link'] ?? '');
                    $active = $subRoute && $subRoute === $currentRoute;
                    $href = $subRoute ? route($subRoute) : '#';
                @endphp
                <a href="{{ $href }}"
                    class="flex items-center gap-3 px-3 py-2 rounded transition font-semibold
                                                                                                                                                                  {{ $active ? 'bg-gray-200 text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}">
                    @include('partials.sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>
