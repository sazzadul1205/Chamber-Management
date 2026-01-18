 {{-- Sidebar --}}
 <aside class="app-sidebar bg-body-secondary shadow">

     {{-- Sidebar Brand --}}
     <div class="sidebar-brand">
         <a href="{{ route('backend.dashboard') }}" class="brand-link">
             <img src="{{ asset('login_logo.png') }}" alt="{{ config('app.name') }} Logo" class=" aside-bar-logo">
         </a>
     </div>

     {{-- Sidebar Menu --}}
     <div class="sidebar-wrapper">
         <nav class="mt-2">
             <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

                 {{-- Dashboard --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.dashboard') }}"
                         class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-speedometer"></i>
                         <p>Dashboard</p>
                     </a>
                 </li>

                 {{-- All Roles --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.roles.index') }}"
                         class="nav-link {{ request()->routeIs('backend.roles.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-person-badge"></i>
                         <p>Roles</p>
                     </a>
                 </li>

                 {{-- All Users --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.users.index') }}"
                         class="nav-link {{ request()->routeIs('backend.users.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-people"></i>
                         <p>Users</p>
                     </a>
                 </li>

                 {{-- All Patients --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.patients.index') }}"
                         class="nav-link {{ request()->routeIs('backend.patients.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-person-vcard"></i>
                         <p>Patients</p>
                     </a>
                 </li>

                 {{-- Sidebar: Patient Families --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.patient-families.index') }}"
                         class="nav-link {{ request()->routeIs('patient-families.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-people-fill"></i>
                         <span>Patient Families</span>
                     </a>
                 </li>

                 {{-- Sidebar: Doctors --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.doctors.index') }}"
                         class="nav-link {{ request()->routeIs('backend.doctors.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-person-badge-fill"></i>
                         <span>Doctors</span>
                     </a>
                 </li>

                 {{-- Sidebar: Dental Chairs --}}
                 <li class="nav-item">
                     <a href="{{ route('backend.dental-chairs.index') }}"
                         class="nav-link {{ request()->routeIs('backend.dental-chairs.*') ? 'active' : '' }}">
                         <i class="nav-icon bi bi-chair-fill"></i>
                         <span>Dental Chairs</span>
                     </a>
                 </li>

                 {{-- Patients --}}
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon bi bi-people-fill"></i>
                         <p>
                             Patients
                             <i class="nav-arrow bi bi-chevron-right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>All Patients</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>Add New Patient</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>Appointments</p>
                             </a>
                         </li>
                     </ul>
                 </li>

                 {{-- Doctors --}}
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon bi bi-heart-pulse-fill"></i>
                         <p>
                             Doctors
                             <i class="nav-arrow bi bi-chevron-right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>All Doctors</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>Schedules</p>
                             </a>
                         </li>
                     </ul>
                 </li>

                 {{-- Appointments --}}
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon bi bi-calendar-check-fill"></i>
                         <p>Appointments</p>
                     </a>
                 </li>

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
                                 <p>Medicines</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>Supplies</p>
                             </a>
                         </li>
                     </ul>
                 </li>

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
                                 <p>System Settings</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon bi bi-circle"></i>
                                 <p>User Management</p>
                             </a>
                         </li>
                     </ul>
                 </li>

             </ul>
         </nav>
     </div>
 </aside>
