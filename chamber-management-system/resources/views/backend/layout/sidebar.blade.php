{{-- Sidebar --}}
<aside class="app-sidebar bg-body-secondary shadow">

    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('backend.dashboard') }}" class="brand-link">
            <img src="{{ asset('login_logo.png') }}" alt="{{ config('app.name') }} Logo" class="aside-bar-logo">
        </a>
    </div>

    {{-- Sidebar Menu --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('backend.dashboard') }}"
                        class="nav-link {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- SYSTEM MANAGEMENT --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">System Management</li>

                {{-- Roles --}}
                <li class="nav-item">
                    <a href="{{ route('backend.roles.index') }}"
                        class="nav-link {{ request()->routeIs('backend.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Roles</p>
                    </a>
                </li>

                {{-- Users --}}
                <li class="nav-item">
                    <a href="{{ route('backend.users.index') }}"
                        class="nav-link {{ request()->routeIs('backend.users.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Users</p>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- PATIENT MANAGEMENT --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Patient Management</li>

                {{-- All Patients --}}
                <li class="nav-item">
                    <a href="{{ route('backend.patients.index') }}"
                        class="nav-link {{ request()->routeIs('backend.patients.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-vcard"></i>
                        <p>Patients</p>
                    </a>
                </li>

                {{-- Patient Families --}}
                <li class="nav-item">
                    <a href="{{ route('backend.patient-families.index') }}"
                        class="nav-link {{ request()->routeIs('backend.patient-families.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <span>Patient Families</span>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- CLINIC MANAGEMENT --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Clinic Management</li>

                {{-- Doctors --}}
                <li class="nav-item">
                    <a href="{{ route('backend.doctors.index') }}"
                        class="nav-link {{ request()->routeIs('backend.doctors.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge-fill"></i>
                        <span>Doctors</span>
                    </a>
                </li>

                {{-- Dental Chairs --}}
                <li class="nav-item">
                    <a href="{{ route('backend.dental-chairs.index') }}"
                        class="nav-link {{ request()->routeIs('backend.dental-chairs.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-hospital"></i>
                        <span>Dental Chairs</span>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- MEDICAL RECORDS --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Medical Records</li>

                {{-- Dental Charts --}}
                <li class="nav-item">
                    <a href="{{ route('backend.dental-charts.index') }}"
                        class="nav-link {{ request()->routeIs('backend.dental-charts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clipboard2-data-fill"></i>
                        <span>Dental Charts</span>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- APPOINTMENTS --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Appointments</li>

                {{-- Appointments --}}
                <li class="nav-item">
                    <a href="{{ route('backend.appointments.index') }}"
                        class="nav-link {{ request()->routeIs('backend.appointments.index') || request()->routeIs('backend.appointments.show') || request()->routeIs('backend.appointments.edit') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>All Appointments</p>
                    </a>
                </li>

                {{-- Appointment Calendar --}}
                <li class="nav-item">
                    <a href="{{ route('backend.appointments.calendar') }}"
                        class="nav-link {{ request()->routeIs('backend.appointments.calendar') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-week"></i>
                        <p>Calendar View</p>
                    </a>
                </li>

                {{-- Appointment Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('backend.appointments.dashboard') }}"
                        class="nav-link {{ request()->routeIs('backend.appointments.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Appointment Dashboard</p>
                    </a>
                </li>

                {{-- New Appointment --}}
                <li class="nav-item">
                    <a href="{{ route('backend.appointments.create') }}"
                        class="nav-link {{ request()->routeIs('backend.appointments.create') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-plus"></i>
                        <p>New Appointment</p>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- TREATMENTS (Upcoming) --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Treatments</li>

                {{-- Treatments --}}
                <li class="nav-item">
                    <a href="{{ route('backend.treatments.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>All Treatments</p>
                    </a>
                </li>

                {{-- Treatment Procedures --}}
                <li class="nav-item">
                    {{-- <a href="{{ route('backend.treatment-procedures.index') }}" class="nav-link"> --}}
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Treatment Procedures</p>
                    </a>
                </li>
                {{-- Prescriptions --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-prescription2"></i>
                        <p>
                            Prescriptions
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>All Prescriptions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Medicines</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ========================= --}}
                {{-- BILLING & INVENTORY (Upcoming) --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Billing & Inventory</li>

                {{-- Billing --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-cash-stack"></i>
                        <p>
                            Billing
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Invoices</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Payments</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Inventory --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-seam-fill"></i>
                        <p>
                            Inventory
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Inventory Items</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Stock Logs</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ========================= --}}
                {{-- REPORTS & SETTINGS --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Reports & Settings</li>

                {{-- Reports --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-bar-chart-fill"></i>
                        <p>
                            Reports
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Appointment Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Financial Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Patient Reports</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Medical Files --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-folder-fill"></i>
                        <p>
                            Medical Files
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>X-Rays</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Images</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Audit Logs --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-journal-text"></i>
                        <p>
                            Audit Logs
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>System Logs</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Settings --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-gear-fill"></i>
                        <p>
                            Settings
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Clinic Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>User Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Backup & Restore</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ========================= --}}
                {{-- QUICK LINKS --}}
                {{-- ========================= --}}
                <li class="nav-header text-uppercase small text-muted mt-3 mb-1">Quick Links</li>

                {{-- Today's Appointments --}}
                <li class="nav-item">
                    <a href="{{ route('backend.appointments.index', ['date' => today()->format('Y-m-d')]) }}"
                        class="nav-link">
                        <i class="nav-icon bi bi-calendar-day"></i>
                        <p>Today's Appointments</p>
                    </a>
                </li>

                {{-- New Patient Quick --}}
                <li class="nav-item">
                    <a href="{{ route('backend.patients.create') }}" class="nav-link">
                        <i class="nav-icon bi bi-person-add"></i>
                        <p>Quick Add Patient</p>
                    </a>
                </li>

                {{-- Chair Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('backend.dental-chairs.dashboard') }}"
                        class="nav-link {{ request()->routeIs('backend.dental-chairs.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-grid-3x3-gap"></i>
                        <p>Chair Dashboard</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer mt-auto p-3 border-top">
        <div class="small text-muted">
            <div>Clinic Hours:</div>
            <div>9:00 AM - 5:00 PM</div>
        </div>
        <div class="mt-2 small text-muted">
            <i class="bi bi-telephone me-1"></i> Emergency: 123-456-7890
        </div>
    </div>
</aside>

<style>
    .aside-bar-logo {
        height: 40px;
        width: auto;
        object-fit: contain;
    }

    .nav-header {
        padding: 0.5rem 1rem;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .sidebar-footer {
        font-size: 0.8rem;
    }

    /* Active link styling */
    .nav-link.active {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-left: 3px solid var(--bs-primary);
        font-weight: 500;
    }

    /* Treeview arrow rotation */
    .nav-item>.nav-link>.nav-arrow {
        transition: transform 0.3s;
        float: right;
    }

    .nav-item.menu-open>.nav-link>.nav-arrow {
        transform: rotate(90deg);
    }
</style>

<script>
    // Initialize treeview functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add menu-open class to parent when child is active
        document.querySelectorAll('.nav-link.active').forEach(activeLink => {
            let parentItem = activeLink.closest('.nav-treeview')?.parentElement;
            while (parentItem && parentItem.classList.contains('nav-item')) {
                parentItem.classList.add('menu-open');
                parentItem = parentItem.parentElement.closest('.nav-item');
            }
        });

        // Toggle treeview on click
        document.querySelectorAll('.nav-link[href="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const parentItem = this.closest('.nav-item');
                parentItem.classList.toggle('menu-open');

                // Close other open menus at same level
                const siblingItems = parentItem.parentElement.querySelectorAll(
                    '.nav-item.menu-open');
                siblingItems.forEach(item => {
                    if (item !== parentItem) {
                        item.classList.remove('menu-open');
                    }
                });
            });
        });
    });
</script>
