@php
    $user = Auth::user();
    $roleMap = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Doctor',
        4 => 'Receptionist',
        5 => 'Accountant',
    ];
    $userRoleName = $roleMap[$user->role_id] ?? null;

    $menu = [
        [
            'label' => 'Dashboard',
            'route' => 'backend.dashboard',
            'icon' => 'dashboard',
            'roles' => ['Super Admin', 'Admin'],
        ],
        [
            'label' => 'Dashboard',
            'route' => 'backend.dashboard',
            'icon' => 'dashboard',
            'roles' => ['Doctor'],
        ],
        [
            'label' => 'Dashboard',
            'route' => 'backend.dashboard',
            'icon' => 'dashboard',
            'roles' => ['Receptionist'],
        ],
        [
            'label' => 'Dashboard',
            'route' => 'backend.dashboard',
            'icon' => 'dashboard',
            'roles' => ['Accountant'],
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
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Patient List',
                    'icon' => 'list',
                    'route' => 'backend.patients.index',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Edit Patient',
                    'icon' => 'B_Edit',
                    'route' => 'backend.patients.edit',
                    'params' => ['patient'], // Matches {patient} in route
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Patient Details',
                    'icon' => 'B_View',
                    'route' => 'backend.patients.show',
                    'params' => ['patient'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Create Family',
                    'icon' => 'B_Add',
                    'route' => 'backend.patient-families.create',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Family Management',
                    'icon' => 'Family',
                    'route' => 'backend.patient-families.index',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Edit Family',
                    'icon' => 'B_Edit',
                    'route' => 'backend.patient-families.edit',
                    'params' => ['patient_family'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'View Family Details',
                    'icon' => 'B_View',
                    'route' => 'backend.patient-families.show',
                    'params' => ['patient_family'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Referral Tracking',
                    'icon' => 'Referral',
                    'route' => 'backend.referrals.index',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'View Referral Details',
                    'icon' => 'B_View',
                    'route' => 'backend.referrals.show',
                    'params' => ['patient'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Referral Report',
                    'icon' => 'Report',
                    'route' => 'backend.referrals.report',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
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
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Appointment List',
                    'route' => 'backend.appointments.index',
                    'icon' => 'list',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Appointment Details',
                    'route' => 'backend.appointments.show',
                    'icon' => 'B_View',
                    'params' => ['appointment'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Todays Appointment',
                    'route' => 'backend.appointments.today',
                    'icon' => 'Today',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Calendar View',
                    'route' => 'backend.appointments.calendar',
                    'icon' => 'Calendar',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Appointment Reminders',
                    'route' => 'backend.reminders.index',
                    'icon' => 'Bell',
                    'roles' => ['Receptionist', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Queue Display (TV)',
                    'route' => 'backend.appointments.queue',
                    'icon' => 'TV',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
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
                    'roles' => ['Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Doctor List',
                    'icon' => 'list',
                    'route' => 'backend.doctors.index',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Edit Doctor Profile',
                    'icon' => 'B_Edit',
                    'route' => 'backend.doctors.edit',
                    'params' => ['doctor'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Doctor Details',
                    'icon' => 'B_View',
                    'route' => 'backend.doctors.show',
                    'params' => ['doctor'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Schedule Management',
                    'icon' => 'Calendar',
                    'route' => 'backend.doctors.schedule-management',
                    'params' => ['doctor'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Leave Requests',
                    'icon' => 'Leave',
                    'route' => 'backend.doctors.leave-requests',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'My Leaves',
                    'icon' => 'Vacation',
                    'route' => 'backend.doctors.my-leaves',
                    'roles' => ['Doctor'],
                ],
                [
                    'label' => 'My Calendar',
                    'icon' => 'Calendar',
                    'route' => 'backend.doctors.calendar',
                    'params' => ['doctor'],
                    'roles' => ['Doctor'],
                ],
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
                    'label' => 'Create Treatment Plan',
                    'icon' => 'B_Add',
                    'route' => 'backend.treatments.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Treatment Plan',
                    'icon' => 'Treatment_Plan',
                    'route' => 'backend.treatments.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Treatment Details',
                    'icon' => 'B_View',
                    'route' => 'backend.treatments.show',
                    'params' => ['treatment'],
                    'type' => 'current-page-indicator',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'New Sessions',
                    'icon' => 'B_Add',
                    'param' => ['sessions'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.treatment-sessions.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Treatment Sessions',
                    'icon' => 'Treatment_Session',
                    'route' => 'backend.treatment-sessions.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Todays Sessions',
                    'icon' => 'Treatment_Session',
                    'route' => 'backend.treatment-sessions.today',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'New Treatment',
                    'icon' => 'B_Add',
                    'param' => ['procedures'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.treatment-procedures.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Treatment Procedures',
                    'icon' => 'Treatment_Procedure',
                    'route' => 'backend.treatment-procedures.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Create Procedure',
                    'icon' => 'B_Add',
                    'param' => ['procedures-catalog'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.procedure-catalog.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Procedure Catalog',
                    'icon' => 'list',
                    'route' => 'backend.procedure-catalog.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Procedure Details',
                    'icon' => 'B_View',
                    'param' => ['procedures-catalog'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.procedure-catalog.show',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Edit Procedure',
                    'icon' => 'B_Edit',
                    'param' => ['procedures-catalog'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.procedure-catalog.edit',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Create Diagnosis Codes',
                    'icon' => 'B_Add',
                    'param' => ['diagnosis-codes'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.diagnosis-codes.create',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Diagnosis Codes',
                    'icon' => 'Diagnostic-Code',
                    'route' => 'backend.diagnosis-codes.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'View Diagnosis Codes',
                    'icon' => 'B_View',
                    'param' => ['diagnosis-codes'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.diagnosis-codes.show',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Edit Diagnosis Codes',
                    'icon' => 'B_Edit',
                    'param' => ['diagnosis-codes'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.diagnosis-codes.edit',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
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
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Dental Records',
                    'icon' => 'list',
                    'route' => 'backend.dental-charts.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Edit Dental Record',
                    'icon' => 'B_Edit',
                    'param' => ['dental_chart'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.dental-charts.edit',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'View Dental Record',
                    'icon' => 'B_View',
                    'param' => ['dental_chart'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.dental-charts.show',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Patient Dental Chart',
                    'icon' => 'B_View',
                    'param' => ['patient_chart'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.dental-charts.patient-chart',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
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
                // [
                //     'label' => 'Dashboard',
                //     'icon' => 'grid',
                //     'route' => 'backend.dental-chairs.dashboard',
                //     'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                // ],
                [
                    'label' => 'Add New Chair',
                    'icon' => 'Add-Circle',
                    'route' => 'backend.dental-chairs.create',
                    'roles' => ['Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Chair List',
                    'icon' => 'Bed',
                    'route' => 'backend.dental-chairs.index',
                    'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Edit Chair',
                    'icon' => 'B_Edit',
                    'param' => ['dental_chair'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.dental-chairs.edit',
                    'roles' => ['Admin', 'Super Admin'],
                ],
                [
                    'label' => 'View Chair',
                    'icon' => 'B_View',
                    'param' => ['dental_chair'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.dental-chairs.show',
                    'roles' => ['Admin', 'Super Admin'],
                ],
                // [
                //     'label' => 'Chair Schedule',
                //     'icon' => 'Schedule',
                //     'route' => 'backend.dental-chairs.schedule',
                //     'roles' => ['Receptionist', 'Doctor', 'Admin', 'Super Admin']
                // ],
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
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Prescription List',
                    'icon' => 'list',
                    'route' => 'backend.prescriptions.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'View Prescription',
                    'icon' => 'B_View',
                    'param' => ['prescription'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.prescriptions.show',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Edit Prescription',
                    'icon' => 'B_Edit',
                    'param' => ['prescription'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.prescriptions.edit',
                    'roles' => ['Super Admin', 'Admin', 'Doctor'],
                ],
                [
                    'label' => 'Create Medicine',
                    'icon' => 'Add-Circle',
                    'route' => 'backend.medicines.create',
                    'roles' => ['Super Admin', 'Admin', 'Accountant'],
                ],
                [
                    'label' => 'Medicine Catalog',
                    'icon' => 'medicine',
                    'route' => 'backend.medicines.index',
                    'roles' => ['Super Admin', 'Admin', 'Doctor', 'Accountant'],
                ],
                [
                    'label' => 'View Medicine Details',
                    'icon' => 'B_View',
                    'param' => ['medicine'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.medicines.show',
                    'roles' => ['Super Admin', 'Admin', 'Doctor', 'Accountant'],
                ],
                [
                    'label' => 'Edit Medicine',
                    'icon' => 'B_Edit',
                    'param' => ['medicine'],
                    'type' => 'current-page-indicator',
                    'route' => 'backend.medicines.edit',
                    'roles' => ['Super Admin', 'Admin', 'Accountant'],
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
                    'roles' => ['Accountant', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Session Payments',
                    'icon' => 'list',
                    'route' => 'backend.payments.procedure-payments',
                    'roles' => ['Accountant', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Procedure Payments',
                    'icon' => 'list',
                    'route' => 'backend.payments.session-payments',
                    'roles' => ['Accountant', 'Admin', 'Super Admin'],
                ],
                [
                    'label' => 'Refunds',
                    'route' => 'backend.payments.refunds',
                    'roles' => ['Accountant', 'Admin', 'Super Admin'],
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
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Roles & Permissions',
                    'icon' => 'key',
                    'route' => 'backend.roles.index',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'System Settings',
                    'icon' => 'cog',
                    'route' => 'backend.system-settings.index',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Backup & Restore',
                    'icon' => 'Backup',
                    'route' => 'backend.backup.index',
                    'roles' => ['Super Admin', 'Admin'],
                ],
                [
                    'label' => 'Audit Logs',
                    'icon' => 'list',
                    'route' => 'backend.audit.index',
                    'roles' => ['Super Admin', 'Admin'],
                ],
            ],
        ],
    ];
@endphp

@php
    $currentRoute = Route::currentRouteName();
    $currentParameters = Route::current()->parameters();
    $currentDoctorId = optional($user->doctor)->id;
    $buildRoute = function ($routeName, $paramConfig = []) use ($currentParameters, $currentDoctorId, $user) {
        if (!$routeName) {
            return '#';
        }

        $params = [];
        foreach ((array) $paramConfig as $key => $value) {
            if (is_int($key)) {
                $paramName = $value;
                $paramValue = $currentParameters[$paramName] ?? null;

                if ($paramName === 'doctor' && !$paramValue) {
                    $paramValue = $currentDoctorId;
                }

                if (!$paramValue) {
                    return '#';
                }

                $params[$paramName] = $paramValue;
                continue;
            }

            $paramValue = $value;
            if ($value === 'current_doctor') {
                $paramValue = $currentDoctorId;
            } elseif ($value === 'current_user') {
                $paramValue = $user->id;
            }

            if (!$paramValue) {
                return '#';
            }

            $params[$key] = $paramValue;
        }

        try {
            return route($routeName, $params);
        } catch (\Throwable $e) {
            return '#';
        }
    };
    $openGroupKey = null;

    foreach ($menu as $key => $item) {
        if (isset($item['roles']) && in_array($userRoleName, $item['roles']) && isset($item['items'])) {
            foreach ($item['items'] as $sub) {
                if (isset($sub['roles']) && in_array($userRoleName, $sub['roles'])) {
                    $subRoute = $sub['route'] ?? ($sub['link'] ?? '');

                    if (isset($sub['type']) && $sub['type'] === 'current-page-indicator') {
                        if ($subRoute === $currentRoute) {
                            $hasRequiredParam = true;
                            if (isset($sub['params'])) {
                                $paramNames = is_array($sub['params']) ? $sub['params'] : [$sub['params']];
                                foreach ($paramNames as $paramName) {
                                    if (!isset($currentParameters[$paramName])) {
                                        $hasRequiredParam = false;
                                        break;
                                    }
                                }
                            }
                            if ($hasRequiredParam) {
                                $openGroupKey = $key;
                                break 2;
                            }
                        }
                    } else {
                        if ($subRoute && $subRoute === $currentRoute) {
                            $openGroupKey = $key;
                            break 2;
                        }
                    }
                }
            }
        }
    }
@endphp

<div class="flex flex-col h-full" id="sidebarContainer">
    <!-- Brand -->
    <div class="flex items-center justify-center p-4">
        <img class="brand-full h-16 w-auto" src="{{ asset('assets/Website_Logo.png') }}" alt="Logo">
        <img class="brand-icon h-10 w-auto hidden" src="{{ asset('Teeth.png') }}" alt="Logo Icon">
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto overflow-x-visible scrollbar-none">
        @foreach ($menu as $key => $item)
            @php
                // Check if user has access to this menu item
                $hasAccess = isset($item['roles']) && in_array($userRoleName, $item['roles']);
                if (!$hasAccess) {
                    continue;
                }

                $hasChildren = isset($item['items']);
                $groupActive = false;
                if ($hasChildren) {
                    foreach ($item['items'] as $sub) {
                        $subAccess = isset($sub['roles']) && in_array($userRoleName, $sub['roles']);
                        if (!$subAccess) {
                            continue;
                        }

                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');

                        if (isset($sub['type']) && $sub['type'] === 'current-page-indicator') {
                            if ($subRoute === $currentRoute) {
                                $hasRequiredParam = true;
                                if (isset($sub['params'])) {
                                    $paramNames = is_array($sub['params']) ? $sub['params'] : [$sub['params']];
                                    foreach ($paramNames as $paramName) {
                                        if (!isset($currentParameters[$paramName])) {
                                            $hasRequiredParam = false;
                                            break;
                                        }
                                    }
                                }
                                if ($hasRequiredParam) {
                                    $groupActive = true;
                                    break;
                                }
                            }
                        } else {
                            if ($subRoute && $subRoute === $currentRoute) {
                                $groupActive = true;
                                break;
                            }
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
                    <div class="relative sidebar-group" data-group-key="{{ $key }}">
                        <!-- Group Button -->
                        <button onclick="handleGroupClick({{ $key }})"
                            class="tooltip-trigger group group-btn relative w-full flex items-center justify-between px-3 py-2 rounded transition-all duration-300 ease-in-out text-gray-700 hover:bg-gray-100 {{ $groupActive ? 'bg-blue-50 text-blue-600' : '' }}"
                            data-tooltip="{{ $item['title'] }}" data-group="{{ $key }}">
                            <div class="flex items-center gap-2 font-semibold">
                                @include('partials/sidebar-icon', [
                                    'name' => $item['icon'] ?? 'default',
                                    'class' => 'icon-only',
                                ])
                                <span class="sidebar-text">{{ $item['title'] }}</span>
                            </div>
                            <svg class="arrow w-4 h-4 transition-transform duration-300 sidebar-text {{ $groupActive ? 'rotate-180 text-blue-500' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>

                        </button>

                        <!-- Submenu (Expanded Mode) -->
                        <div id="group-{{ $key }}"
                            class="submenu overflow-hidden transition-all duration-300 ease-in-out"
                            style="max-height: {{ $groupActive ? '500px' : '0' }}; opacity: {{ $groupActive ? '1' : '0' }};">
                            <div class="mt-2 space-y-1 ml-1">
                                @foreach ($item['items'] as $sub)
                                    @php
                                        $subAccess = isset($sub['roles']) && in_array($userRoleName, $sub['roles']);
                                        if (!$subAccess) {
                                            continue;
                                        }

                                        $subRoute = $sub['route'] ?? ($sub['link'] ?? '');

                                        if (isset($sub['type']) && $sub['type'] === 'current-page-indicator') {
                                            $shouldShow = false;
                                            $isActive = false;

                                            if ($subRoute === $currentRoute) {
                                                $hasRequiredParam = true;
                                                if (isset($sub['params'])) {
                                                    $paramNames = is_array($sub['params'])
                                                        ? $sub['params']
                                                        : [$sub['params']];
                                                    foreach ($paramNames as $paramName) {
                                                        if (!isset($currentParameters[$paramName])) {
                                                            $hasRequiredParam = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                                if ($hasRequiredParam) {
                                                    $shouldShow = true;
                                                    $isActive = true;
                                                }
                                            }

                                            if (!$shouldShow) {
                                                continue;
                                            }

                                            $href = '#';
                                            $isDisabled = true;
                                        } else {
                                            $active = $subRoute && $subRoute === $currentRoute;
                                            $href = $buildRoute($subRoute, $sub['params'] ?? []);
                                            $isActive = $active;
                                            $isDisabled = false;
                                        }
                                    @endphp

                                    @if (isset($sub['type']) && $sub['type'] === 'current-page-indicator')
                                        <!-- Current Page Indicator (Non-clickable) -->
                                        <div
                                            class="flex items-center gap-3 px-3 py-2 rounded transition-all duration-200 bg-blue-50 text-blue-600 font-semibold cursor-default opacity-75">
                                            @include('partials/sidebar-icon', [
                                                'name' => $sub['icon'] ?? 'default',
                                                'class' => 'icon-only',
                                            ])
                                            <span class="sidebar-text">{{ $sub['label'] }}</span>
                                        </div>
                                    @else
                                        <!-- Regular Menu Item -->
                                        <a href="{{ $href }}"
                                            class="flex items-center gap-3 px-3 py-2 rounded transition-all duration-200 {{ $isActive ? 'bg-blue-100 font-semibold text-blue-600' : 'hover:bg-gray-100 text-gray-700' }}">
                                            @include('partials/sidebar-icon', [
                                                'name' => $sub['icon'] ?? 'default',
                                                'class' => 'icon-only',
                                            ])
                                            <span class="sidebar-text">{{ $sub['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Collapsed Mode Icons (Hidden by default) -->
                        <div class="collapsed-icons hidden flex-col items-center space-y-2 mt-2">
                            @foreach ($item['items'] as $sub)
                                @php
                                    $subAccess = isset($sub['roles']) && in_array($userRoleName, $sub['roles']);
                                    if (
                                        !$subAccess ||
                                        (isset($sub['type']) && $sub['type'] === 'current-page-indicator')
                                    ) {
                                        continue;
                                    }
                                    $subRoute = $sub['route'] ?? ($sub['link'] ?? '');
                                    $active = $subRoute && $subRoute === $currentRoute;
                                    $href = $buildRoute($subRoute, $sub['params'] ?? []);
                                @endphp
                                <a href="{{ $href }}"
                                    class="tooltip-trigger relative group flex items-center justify-center w-full p-2 rounded hover:bg-gray-100 {{ $active ? 'bg-blue-100 text-blue-600' : 'text-gray-700' }}"
                                    data-tooltip="{{ $sub['label'] }}">
                                    @include('partials/sidebar-icon', [
                                        'name' => $sub['icon'] ?? 'default',
                                        'class' => 'icon-only',
                                    ])
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                @php
                    $subRoute = $item['route'] ?? ($item['link'] ?? '');
                    $active = $subRoute && $subRoute === $currentRoute;
                    $href = $buildRoute($subRoute, $item['params'] ?? []);
                @endphp
                <a href="{{ $href }}"
                    class="tooltip-trigger relative group flex items-center gap-3 px-3 py-2 rounded transition-all duration-200 font-semibold {{ $active ? 'bg-gray-200 text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}"
                    data-tooltip="{{ $item['label'] }}">
                    @include('partials/sidebar-icon', [
                        'name' => $item['icon'] ?? 'default',
                        'class' => 'icon-only',
                    ])
                    <span class="sidebar-text">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>

<script>
    // Group management
    let openGroup = null;

    function handleGroupClick(key) {
        const sidebar = document.getElementById('sidebar');

        // Don't open dropdowns in collapsed mode
        if (sidebar.classList.contains('sidebar-collapsed')) {
            return;
        }

        toggleGroup(key);
    }

    function toggleGroup(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);

        if (!submenu || !button) return;

        const arrow = button.querySelector('.arrow');

        // Close currently open group
        if (openGroup !== null && openGroup !== key) {
            closeGroup(openGroup);
        }

        if (submenu.style.maxHeight && submenu.style.maxHeight !== '0px') {
            closeGroup(key);
            openGroup = null;
            localStorage.removeItem('activeSidebarGroup');
        } else {
            openGroupFn(key);
            openGroup = key;
            localStorage.setItem('activeSidebarGroup', key);
        }
    }

    function openGroupFn(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);

        if (!submenu || !button) return;

        const arrow = button.querySelector('.arrow');

        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        submenu.style.opacity = '1';

        if (arrow) {
            arrow.classList.add('rotate-180');
        }
    }

    function closeGroup(key) {
        const submenu = document.getElementById(`group-${key}`);
        const button = document.querySelector(`button[data-group='${key}']`);

        if (!submenu || !button) return;

        const arrow = button.querySelector('.arrow');

        submenu.style.maxHeight = '0px';
        submenu.style.opacity = '0';

        if (arrow) {
            arrow.classList.remove('rotate-180');
        }
    }

    function getOrCreateSidebarTooltip() {
        let tooltip = document.getElementById('sidebarFloatingTooltip');
        if (tooltip) return tooltip;

        tooltip = document.createElement('div');
        tooltip.id = 'sidebarFloatingTooltip';
        tooltip.className =
            'fixed px-2 py-1 bg-gray-900 text-white text-sm rounded pointer-events-none whitespace-nowrap z-[9999] opacity-0 transition-opacity duration-150';
        document.body.appendChild(tooltip);
        return tooltip;
    }

    function attachSidebarTooltipEvents() {
        const triggers = document.querySelectorAll('.tooltip-trigger[data-tooltip]');

        triggers.forEach(trigger => {
            if (trigger.dataset.tooltipBound === 'true') return;
            trigger.dataset.tooltipBound = 'true';

            const showTooltip = (event) => {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar || !sidebar.classList.contains('sidebar-collapsed')) return;

                const text = trigger.dataset.tooltip;
                if (!text) return;

                const tooltip = getOrCreateSidebarTooltip();
                tooltip.textContent = text;
                tooltip.style.opacity = '1';

                const rect = trigger.getBoundingClientRect();
                tooltip.style.left = `${rect.right + 10}px`;
                tooltip.style.top = `${rect.top + rect.height / 2}px`;
                tooltip.style.transform = 'translateY(-50%)';
            };

            const hideTooltip = () => {
                const tooltip = document.getElementById('sidebarFloatingTooltip');
                if (!tooltip) return;
                tooltip.style.opacity = '0';
            };

            trigger.addEventListener('mouseenter', showTooltip);
            trigger.addEventListener('focus', showTooltip);
            trigger.addEventListener('mouseleave', hideTooltip);
            trigger.addEventListener('blur', hideTooltip);
        });
    }

    // Auto open active group on load
    document.addEventListener('DOMContentLoaded', () => {
        @if ($openGroupKey !== null)
            setTimeout(() => {
                openGroupFn({{ $openGroupKey }});
                openGroup = {{ $openGroupKey }};
                localStorage.setItem('activeSidebarGroup', {{ $openGroupKey }});
            }, 100);
        @endif

        // Initialize sidebar state
        updateSidebarState();
        attachSidebarTooltipEvents();
    });

    // Update sidebar based on collapsed state
    function updateSidebarState() {
        const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        const floatingTooltip = document.getElementById('sidebarFloatingTooltip');
        const brandFull = document.querySelectorAll('.brand-full');
        const brandIcon = document.querySelectorAll('.brand-icon');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const arrows = document.querySelectorAll('.group-btn .arrow');
        const submenus = document.querySelectorAll('.submenu');
        const collapsedIconsContainers = document.querySelectorAll('.collapsed-icons');
        const groupButtons = document.querySelectorAll('.group-btn');

        if (!isCollapsed && floatingTooltip) {
            floatingTooltip.style.opacity = '0';
        }

        if (isCollapsed) {
            // Hide full logo, show icon
            brandFull.forEach(el => el.classList.add('hidden'));
            brandIcon.forEach(el => el.classList.remove('hidden'));

            // Hide text elements
            sidebarTexts.forEach(el => el.classList.add('hidden'));
            arrows.forEach(el => el.classList.add('hidden'));

            // Hide submenus
            submenus.forEach(el => {
                el.style.maxHeight = '0px';
                el.style.opacity = '0';
            });

            // Show collapsed icons
            collapsedIconsContainers.forEach(el => el.classList.remove('hidden'));

            // Center group buttons
            groupButtons.forEach(el => {
                el.classList.add('justify-center');
            });

            // Close all groups
            openGroup = null;
        } else {
            // Show full logo, hide icon
            brandFull.forEach(el => el.classList.remove('hidden'));
            brandIcon.forEach(el => el.classList.add('hidden'));

            // Show text elements
            sidebarTexts.forEach(el => el.classList.remove('hidden'));
            arrows.forEach(el => el.classList.remove('hidden'));

            // Hide collapsed icons
            collapsedIconsContainers.forEach(el => el.classList.add('hidden'));

            // Reset group button alignment
            groupButtons.forEach(el => {
                el.classList.remove('justify-center');
            });

            // Reopen active group
            const activeGroup = localStorage.getItem('activeSidebarGroup');
            if (activeGroup) {
                setTimeout(() => {
                    openGroupFn(activeGroup);
                    openGroup = activeGroup;
                }, 300);
            }
        }
    }

    // Listen for sidebar state changes
    const sidebarObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                updateSidebarState();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebarObserver.observe(sidebar, {
                attributes: true
            });
        }

        attachSidebarTooltipEvents();
    });
</script>

<style>
    /* Sidebar text visibility */
    .sidebar-collapsed .sidebar-text {
        display: none;
    }

    .sidebar-collapsed .group-btn {
        justify-content: center;
    }

    .sidebar-collapsed .brand-full {
        display: none;
    }

    .sidebar-collapsed .brand-icon {
        display: block;
    }

    .sidebar-collapsed .arrow {
        display: none;
    }

    .sidebar-collapsed .submenu {
        display: none;
    }

    .sidebar-collapsed .collapsed-icons {
        display: flex !important;
    }

    #sidebarFloatingTooltip {
        will-change: top, left, opacity;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Submenu animation */
    .submenu {
        transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out;
    }

    @media (max-width: 767px) {

        .brand-full,
        .brand-icon {
            display: none !important;
        }

        .brand-mobile-icon {
            display: flex !important;
        }
    }
</style>
