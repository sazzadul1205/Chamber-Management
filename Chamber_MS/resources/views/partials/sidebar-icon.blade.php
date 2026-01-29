@php
    $icon = $name ?? 'default';
@endphp

@switch($icon)
    {{-- ==============================
         DASHBOARD ICONS
    ============================== --}}
    @case('dashboard')
        @include('components.icons.dashboard')
    @break

    {{-- ==============================
         SYSTEM / SETTINGS ICONS
    ============================== --}}
    @case('key')
        @include('components.icons.key')
    @break

    @case('cog')
        @include('components.icons.cog')
    @break

    @case('Settings')
        @include('components.icons.Settings')
    @break

    @case('grid')
        @include('components.icons.grid')
    @break

    {{-- ==============================
         LIST & MANAGEMENT ICONS
    ============================== --}}
    @case('list')
        @include('components.icons.list')
    @break

    @case('folder')
        @include('components.icons.folder')
    @break

    {{-- ==============================
         USER / DOCTOR / PATIENT ICONS
    ============================== --}}
    @case('User')
        @include('components.icons.User')
    @break

    @case('User-Plus')
        @include('components.icons.User-Plus')
    @break

    @case('Doctor')
        @include('components.icons.Doctor')
    @break

    @case('Patient')
        @include('components.icons.Patient')
    @break

    @case('Family')
        @include('components.icons.Family')
    @break

    {{-- ==============================
         DENTAL / TREATMENT ICONS
    ============================== --}}
    @case('Tooth')
        @include('components.icons.Tooth')
    @break

    @case('Add_Tooth')
        @include('components.icons.Add_Tooth')
    @break

    @case('Treatment')
        @include('components.icons.Treatment')
    @break

    @case('Diagnostic-Code')
        @include('components.icons.Diagnostic-Code')
    @break

    {{-- ==============================
         APPOINTMENT ICONS
    ============================== --}}
    @case('Appointment')
        @include('components.icons.Appointment')
    @break

    @case('Calendar')
        @include('components.icons.Calendar')
    @break

    @case('Add-Appointment')
        @include('components.icons.Add-Appointment')
    @break

    @case('Today')
        @include('components.icons.Today')
    @break

    @case('Queue')
        @include('components.icons.Queue')
    @break

    @case('TV')
        @include('components.icons.TV')
    @break

    @case('Treatment_Plan')
        @include('components.icons.Treatment_Plan')
    @break

    @case('Treatment_Session')
        @include('components.icons.Treatment_Session')
    @break

    @case('Treatment_Procedure')
        @include('components.icons.Treatment_Procedure')
    @break

    {{-- ==============================
         PRESCRIPTION ICONS
    ============================== --}}
    @case('Prescription')
        @include('components.icons.Prescription')
    @break

    @case('medicine')
        @include('components.icons.medicine')
    @break

    {{-- ==============================
         BUTTON / ACTION ICONS
    ============================== --}}
    @case('B_View')
        @include('components.icons.Buttons.B_View')
    @break

    @case('B_Edit')
        @include('components.icons.Buttons.B_Edit')
    @break

    @case('B_Delete')
        @include('components.icons.Buttons.B_Delete')
    @break

    @case('B_Add')
        @include('components.icons.Buttons.B_Add')
    @break

    @case('B_Export')
        @include('components.icons.Buttons.B_Export')
    @break

    @case('B_Reschedule')
        @include('components.icons.Buttons.B_Reschedule')
    @break

    @case('B_Tick')
        @include('components.icons.Buttons.B_Tick')
    @break

    @case('B_Cross')
        @include('components.icons.Buttons.B_Cross')
    @break

    @case('B_Play')
        @include('components.icons.Buttons.B_Play')
    @break

    @case('B_Print')
        @include('components.icons.Buttons.B_Print')
    @break

    @case('B_Back')
        @include('components.icons.Buttons.B_Back')
    @break

    @case('B_Pause')
        @include('components.icons.Buttons.B_Pause')
    @break

    {{-- ==============================
         MISC ICONS
    ============================== --}}
    @case('Add-Circle')
        @include('components.icons.Add-Circle')
    @break

    @case('Schedule')
        @include('components.icons.Schedule')
    @break

    @case('Bed')
        @include('components.icons.Bed')
    @break

    {{-- ==============================
         DEFAULT ICON
    ============================== --}}

    @default
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <circle cx="10" cy="10" r="3" />
        </svg>
@endswitch
