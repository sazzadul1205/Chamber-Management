@php
    $user = Auth::user();
    $roleMap = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Doctor',
        4 => 'Receptionist',
        5 => 'Accountant'
    ];
    $userRoleName = $roleMap[$user->role_id] ?? null;

    $menu = [
        // ===============================
        // AUTH & DASHBOARD
        // ===============================
        [
            'label' => 'Main Dashboard',
            'icon' => 'dashboard',
            'route' => 'backend.dashboard',
            'roles' => ['Super Admin', 'Admin',]
        ],
        [
            'label' => 'Doctor Dashboard',
            'icon' => 'dashboard',
            'roles' => ['Doctor']
        ],
        [
            'label' => 'Reception Dashboard',
            'icon' => 'dashboard',
            'roles' => ['Receptionist']
        ],
        [
            'label' => 'Admin Dashboard',
            'icon' => 'dashboard',
            'roles' => ['Admin', 'Super Admin']
        ],

        // ===============================
        // PATIENT MANAGEMENT
        // ===============================
        [
            'title' => 'Patients',
            'icon' => 'Patient',
            'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
            'items' => [
                [
                    'label' => 'Patient Registration',
                    'icon' => 'User-Plus',
                    'route' => 'backend.patients.create',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin', 'Doctor']
                ],
                [
                    'label' => 'Patient List',
                    'icon' => 'list',
                    'route' => 'backend.patients.index',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Family Management',
                    'icon' => 'Family',
                    'route' => 'backend.patient-families.index',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Referral Tracking',
                    'icon' => 'share-alt',
                    'route' => 'backend.patients.search',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
            ],
        ],

        // ===============================
        // APPOINTMENTS
        // ===============================
        [
            'title' => 'Appointments',
            'icon' => 'Appointment',
            'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
            'items' => [
                [
                    'label' => 'Schedule Appointment',
                    'route' => 'backend.appointments.create',
                    'icon' => 'Add-Circle',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin', 'Doctor']
                ],
                [
                    'label' => 'Appointment List',
                    'route' => 'backend.appointments.index',
                    'icon' => 'list',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Calendar View',
                    'route' => 'backend.appointments.calendar',
                    'icon' => 'Calendar',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Appointment Reminders',
                    'route' => '',
                    'icon' => 'bell',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Queue Display (TV)',
                    'route' => 'backend.appointments.queue',
                    'icon' => 'TV',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
            ],
        ],

        // ===============================
        // DOCTORS
        // ===============================
        [
            'title' => 'Doctors',
            'icon' => 'Doctor',
            'roles' => ['Super Admin', 'Admin', 'Doctor'],
            'items' => [
                [
                    'label' => 'Doctor Registration',
                    'icon' => 'User-Plus',
                    'route' => 'backend.doctors.create',
                    'roles' => ['Admin', 'Super Admin']
                ],
                [
                    'label' => 'Doctor List',
                    'icon' => 'list',
                    'route' => 'backend.doctors.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                // [
                //     'label' => 'Doctor Schedule',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
                // [
                //     'label' => 'Leave Management',
                //     'roles' => ['Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Commission Reports',
                //     'roles' => ['Admin', 'Super Admin', 'Accountant']
                // ],
            ],
        ],

        // ===============================
        // TREATMENTS
        // ===============================
        [
            'title' => 'Treatments',
            'icon' => 'Treatment',
            'roles' => ['Super Admin', 'Admin', 'Doctor'],
            'items' => [
                [
                    'label' => 'Treatment Plan',
                    'icon' => 'Treatment_Plan',
                    'route' => 'backend.treatments.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Treatment Sessions',
                    'icon' => 'Treatment_Session',
                    'route' => 'backend.treatment-sessions.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Treatment Procedures',
                    'icon' => 'Treatment_Procedure',
                    'route' => 'backend.treatment-procedures.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Procedure Catalog',
                    'icon' => 'list',
                    'route' => 'backend.procedure-catalog.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Diagnosis Codes',
                    'icon' => 'Diagnostic-Code',
                    'route' => 'backend.diagnosis-codes.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
            ],
        ],

        // ===============================
        // DENTAL CHARTING
        // ===============================
        [
            'title' => 'Dental Charting',
            'icon' => 'Tooth',
            'roles' => ['Super Admin', 'Admin', 'Doctor'],
            'items' => [
                [
                    'label' => 'Add Dental Record',
                    'icon' => 'Add_Tooth',
                    'route' => 'backend.dental-charts.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Dental Records',
                    'icon' => 'list',
                    'route' => 'backend.dental-charts.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                // [
                //     'label' => 'Tooth Conditions',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
                // [
                //     'label' => 'Chart History',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
                // [
                //     'label' => 'X-Ray Viewer',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
                // [
                //     'label' => 'Print Chart',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
            ],
        ],

        // ===============================
        // DENTAL CHAIRS
        // ===============================
        [
            'title' => 'Dental Chairs',
            'icon' => 'Bed',
            'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'grid',
                    'route' => 'backend.dental-chairs.dashboard',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Chair List',
                    'icon' => 'Bed',
                    'route' => 'backend.dental-chairs.index',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Add New Chair',
                    'icon' => 'Add-Circle',
                    'route' => 'backend.dental-chairs.create',
                    'roles' => ['Admin', 'Super Admin']
                ],
                [
                    'label' => 'Chair Schedule',
                    'icon' => 'Schedule',
                    'route' => 'backend.dental-chairs.schedule',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                ],
            ],
        ],

        // ===============================
        // PRESCRIPTIONS
        // ===============================
        [
            'title' => 'Prescriptions',
            'icon' => 'Prescription',
            'roles' => ['Super Admin', 'Admin', 'Doctor'],
            'items' => [
                [
                    'label' => 'Create Prescription',
                    'icon' => 'Add-Circle',
                    'route' => 'backend.prescriptions.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                // [
                //     'label' => 'Prescription Templates',
                //     'roles' => ['Super Admin', 'Admin', 'Doctor']
                // ],
                [
                    'label' => 'Prescription History',
                    'icon' => 'list',
                    'route' => 'backend.prescriptions.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor']
                ],
                [
                    'label' => 'Medicine Catalog',
                    'icon' => 'medicine',
                    'route' => 'backend.medicines.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor', 'Accountant']
                ],
            ],
        ],

        // ===============================
        // INVENTORY
        // ===============================
        // [
        //     'title' => 'Inventory',
        //     'icon' => 'boxes',
        //     'roles' => ['Super Admin', 'Admin', 'Accountant', 'Doctor'],
        //     'items' => [
        //         [
        //             'label' => 'Inventory Dashboard',
        //             'route' => 'backend.inventory-items.index',
        //             'icon' => 'grid',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant', 'Doctor']
        //         ],
        //         [
        //             'label' => 'Stock List',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant', 'Doctor']
        //         ],
        //         [
        //             'label' => 'Add / Update Stock',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Low Stock Alerts',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant', 'Doctor']
        //         ],
        //         [
        //             'label' => 'Purchase Orders',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Usage Tracking',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant', 'Doctor']
        //         ],
        //         [
        //             'label' => 'Suppliers',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Expiry Tracking',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //     ],
        // ],

        // ===============================
        // BILLING & PAYMENTS
        // ===============================
        [
            'title' => 'Billing & Payments',
            'icon' => 'Payment',
            'roles' => ['Accountant', 'Admin', 'Super Admin'],
            'items' => [
                // [
                //     'label' => 'Invoice Generation',
                //     'route' => 'backend.invoices.create',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Invoice List',
                //     'route' => 'backend.invoices.index',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Payment Collection',
                //     'route' => 'backend.payments.create',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Installments',
                //     'route' => 'payment-installments.index',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Partial Payments',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Receipts',
                //     'route' => 'receipts.index',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                // [
                //     'label' => 'Outstanding Payments',
                //     'roles' => ['Accountant', 'Admin', 'Super Admin']
                // ],
                [
                    'label' => 'Payment History',
                    'route' => 'backend.payments.index',
                    'icon' => 'list',
                    'roles' => ['Accountant', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Advance Payments',
                    'roles' => ['Accountant', 'Admin', 'Super Admin']
                ],
                [
                    'label' => 'Refunds',
                    'roles' => ['Accountant', 'Admin', 'Super Admin']
                ],
            ],
        ],

        // ===============================
        // REPORTS
        // ===============================
        // [
        //     'title' => 'Reports & Analytics',
        //     'icon' => 'bar-chart',
        //     'roles' => ['Super Admin', 'Admin', 'Accountant'],
        //     'items' => [
        //         [
        //             'label' => 'Daily / Monthly Reports',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Financial Reports',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Patient Statistics',
        //             'roles' => ['Super Admin', 'Admin']
        //         ],
        //         [
        //             'label' => 'Doctor Performance',
        //             'roles' => ['Super Admin', 'Admin']
        //         ],
        //         [
        //             'label' => 'Treatment Reports',
        //             'roles' => ['Super Admin', 'Admin', 'Doctor']
        //         ],
        //         [
        //             'label' => 'Inventory Reports',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //         [
        //             'label' => 'Appointment Analytics',
        //             'roles' => ['Super Admin', 'Admin']
        //         ],
        //         [
        //             'label' => 'Revenue Reports',
        //             'roles' => ['Super Admin', 'Admin', 'Accountant']
        //         ],
        //     ],
        // ],

        // ===============================
        // SYSTEM SETTINGS
        // ===============================
        [
            'title' => 'System',
            'icon' => 'Settings',
            'roles' => ['Super Admin', 'Admin'],
            'items' => [
                [
                    'label' => 'User Management',
                    'icon' => 'User',
                    'route' => 'backend.user.index',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Roles & Permissions',
                    'icon' => 'key',
                    'route' => 'backend.roles.index',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'System Settings',
                    'icon' => 'cog',
                    'route' => 'backend.system-settings.index',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Backup & Restore',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Audit Logs',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Notifications',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Holiday Management',
                    'roles' => ['Super Admin', 'Admin']
                ],
                [
                    'label' => 'Clinic Configuration',
                    'roles' => ['Super Admin', 'Admin']
                ],
            ],
        ],
    ];
@endphp

@php
    $currentRoute = Route::currentRouteName();
@endphp


@php
    $currentRoute = Route::currentRouteName();
    $openGroupKey = null;

    foreach ($menu as $key => $item) {
        if (isset($item['roles']) && in_array($userRoleName, $item['roles']) && isset($item['items'])) {
            foreach ($item['items'] as $sub) {
                if (isset($sub['roles']) && in_array($userRoleName, $sub['roles'])) {
                    $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                    if ($subRoute && $subRoute === $currentRoute) {
                        $openGroupKey = $key;
                        break 2;
                    }
                }
            }
        }
    }
@endphp


<div class="flex flex-col h-full">
    <!-- Brand -->
    <img class="mx-auto h-16 w-auto p-4" src="{{ asset('assets/Website_Logo.png') }}" alt="Logo">

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto scrollbar-none">
        @foreach ($menu as $key => $item)
            @php
                // Check if user has access to this menu item
                $hasAccess = isset($item['roles']) && in_array($userRoleName, $item['roles']);
                if (!$hasAccess)
                    continue;

                $hasChildren = isset($item['items']);
                $groupActive = false;
                if ($hasChildren) {
                    foreach ($item['items'] as $sub) {
                        $subAccess = isset($sub['roles']) && in_array($userRoleName, $sub['roles']);
                        if (!$subAccess)
                            continue;

                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                        if ($subRoute && $subRoute === $currentRoute) {
                            $groupActive = true;
                        }
                    }
                }
            @endphp

            @if ($hasChildren)
                @php
                    $hasVisibleChildren = false;
                    foreach ($item['items'] as $sub) {
                        if (isset($sub['roles']) && in_array($userRoleName, $sub['roles'])) {
                            $hasVisibleChildren = true;
                            break;
                        }
                    }
                @endphp

                @if ($hasVisibleChildren)
                    <div class="relative">
                        <!-- Group Button -->
                        <button
                            class="group-btn w-full flex items-center justify-between px-3 py-2 rounded transition-all duration-300 ease-in-out text-gray-700 hover:bg-gray-100 {{ $groupActive ? 'bg-blue-50 text-blue-600' : '' }}"
                            data-group="{{ $key }}">
                            <div class="flex items-center gap-2 font-semibold">
                                @include('partials/sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                                <span>{{ $item['title'] }}</span>
                            </div>
                            <svg class="arrow w-4 h-4 transition-transform duration-300 {{ $groupActive ? 'rotate-180 text-blue-500' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Submenu -->
                        <div id="group-{{ $key }}"
                            class="submenu overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-in-out {{ $groupActive ? 'open' : '' }}"
                            style="{{ $groupActive ? 'max-height: 500px; opacity: 1;' : '' }}">
                            <div class="mt-2 space-y-1 ml-1">
                                @foreach ($item['items'] as $sub)
                                    @php
                                        $subAccess = isset($sub['roles']) && in_array($userRoleName, $sub['roles']);
                                        if (!$subAccess)
                                            continue;

                                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                                        $active = $subRoute && $subRoute === $currentRoute;
                                        $href = $subRoute ? route($subRoute) : '#';
                                    @endphp
                                    <a href="{{ $href }}"
                                        class="flex items-center gap-3 px-3 py-2 rounded transition-all duration-200 {{ $active ? 'bg-blue-100 font-semibold text-blue-600' : 'hover:bg-gray-100 text-gray-700' }}">
                                        @include('partials/sidebar-icon', ['name' => $sub['icon'] ?? 'default'])
                                        <span>{{ $sub['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @else
                @php
                    $subRoute = $item['route'] ?? ($item['link'] ?? '');
                    $active = $subRoute && $subRoute === $currentRoute;
                    $href = $subRoute ? route($subRoute) : '#';
                @endphp
                <a href="{{ $href }}"
                    class="flex items-center gap-3 px-3 py-2 rounded transition-all duration-200 font-semibold {{ $active ? 'bg-gray-200 text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}">
                    @include('partials/sidebar-icon', ['name' => $item['icon'] ?? 'default'])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>

<!-- JS -->
<script>
    let openGroup = null;

    function toggleGroup(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);
        const arrow = button.querySelector('.arrow');

        // Close currently open group
        if (openGroup !== null && openGroup !== key) {
            closeGroup(openGroup);
        }

        if (submenu.classList.contains('open')) {
            closeGroup(key);
            openGroup = null;
        } else {
            openGroupFn(key);
            openGroup = key;
        }
    }

    function openGroupFn(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);
        const arrow = button.querySelector('.arrow');

        submenu.classList.add('open');
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        submenu.classList.remove('opacity-0');
        submenu.classList.add('opacity-100');

        arrow.classList.add('rotate-180');
    }

    function closeGroup(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);
        const arrow = button.querySelector('.arrow');

        submenu.style.maxHeight = '0px';
        submenu.classList.remove('opacity-100');
        submenu.classList.add('opacity-0');
        submenu.classList.remove('open');

        arrow.classList.remove('rotate-180');
    }

    // Auto open active group on load
    document.addEventListener('DOMContentLoaded', () => {
        @if($openGroupKey !== null)
            openGroupFn({{ $openGroupKey }});
            openGroup = {{ $openGroupKey }};
        @endif
    });

    // Attach click listeners
    document.querySelectorAll('.group-btn').forEach(btn => {
        btn.addEventListener('click', () => toggleGroup(btn.dataset.group));
    });
</script>