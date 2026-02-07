@extends('backend.layout.structure')

@section('content')
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Reception Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Front desk management.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>
        </div>

        <!-- Reception Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Today's Appointments</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">24</h3>
                        <p class="text-xs text-gray-500 mt-1">8 confirmed, 16 pending</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.appointments.today') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    Manage Appointments
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Waiting Patients -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Waiting Patients</p>
                        <h3 class="text-2xl font-bold text-yellow-600 mt-1">5</h3>
                        <p class="text-xs text-gray-500 mt-1">In waiting room</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.appointments.queue') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    View Queue
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- New Patients Today -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">New Patients Today</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1">8</h3>
                        <p class="text-xs text-gray-500 mt-1">First-time visitors</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.patients.create') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    Register Patient
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Available Chairs -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Available Chairs</p>
                        <h3 class="text-2xl font-bold text-purple-600 mt-1">3</h3>
                        <p class="text-xs text-gray-500 mt-1">Out of 8 total</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.dental-chairs.dashboard') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    View Chairs
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Quick Reception Actions -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Front Desk Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- New Appointment -->
                <a href="{{ route('backend.appointments.create') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-blue-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg group-hover:bg-blue-100">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-blue-600">New Appointment</h3>
                            <p class="text-sm text-gray-500">Schedule patient visit</p>
                        </div>
                    </div>
                </a>

                <!-- Walk-in Patient -->
                <a href="{{ route('backend.appointments.walk-in') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-green-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-50 p-3 rounded-lg group-hover:bg-green-100">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-green-600">Walk-in Patient</h3>
                            <p class="text-sm text-gray-500">Register walk-in visit</p>
                        </div>
                    </div>
                </a>

                <!-- Patient Registration -->
                <a href="{{ route('backend.patients.create') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-purple-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-50 p-3 rounded-lg group-hover:bg-purple-100">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-purple-600">Register Patient</h3>
                            <p class="text-sm text-gray-500">New patient registration</p>
                        </div>
                    </div>
                </a>

                <!-- Check-in Patient -->
                <a href="{{ route('backend.appointments.today') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-orange-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-orange-50 p-3 rounded-lg group-hover:bg-orange-100">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-orange-600">Check-in Patient</h3>
                            <p class="text-sm text-gray-500">Mark patient arrival</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Appointments List -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h2 class="font-semibold text-gray-800">Today's Appointments</h2>
                        <span class="text-sm text-gray-500">24 Total</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @for($i = 1; $i <= 5; $i++)
                            <div
                                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">{{ 9 + $i }}:00</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Patient {{ $i }}</h4>
                                        <p class="text-sm text-gray-500">Dr. Smith • Check-up</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                                  @if($i == 1) bg-green-100 text-green-800 
                                                  @elseif($i == 2) bg-yellow-100 text-yellow-800 
                                                  @else bg-gray-100 text-gray-800 @endif">
                                        @if($i == 1) Checked-in
                                        @elseif($i == 2) Waiting
                                        @else Scheduled @endif
                                    </span>
                                    @if($i == 2)
                                        <button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                            Check-in
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('backend.appointments.calendar') }}"
                            class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                            View Calendar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Patient Search -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">Quick Patient Search</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <input type="text" placeholder="Search patient by name, phone, or ID..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-medium">P001</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">John Smith</h4>
                                <p class="text-sm text-gray-500">+1 234 567 8900</p>
                            </div>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-medium">P002</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Sarah Johnson</h4>
                                <p class="text-sm text-gray-500">+1 234 567 8901</p>
                            </div>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-medium">P003</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Michael Brown</h4>
                                <p class="text-sm text-gray-500">+1 234 567 8902</p>
                            </div>
                        </a>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('backend.patients.index') }}"
                            class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                            View All Patients
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dental Chair Status -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Dental Chair Status</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $chairs = [
                        ['id' => 1, 'name' => 'Chair 1', 'status' => 'occupied', 'color' => 'red'],
                        ['id' => 2, 'name' => 'Chair 2', 'status' => 'available', 'color' => 'green'],
                        ['id' => 3, 'name' => 'Chair 3', 'status' => 'cleaning', 'color' => 'yellow'],
                        ['id' => 4, 'name' => 'Chair 4', 'status' => 'occupied', 'color' => 'red'],
                    ];
                @endphp

                @foreach($chairs as $chair)
                    <a href="{{ route('backend.dental-chairs.show', $chair['id']) }}"
                        class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-800">{{ $chair['name'] }}</h3>
                                <span
                                    class="text-sm {{ $chair['color'] == 'green' ? 'text-green-600' : ($chair['color'] == 'yellow' ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ ucfirst($chair['status']) }}
                                </span>
                            </div>
                            <div
                                class="w-3 h-3 rounded-full 
                                          {{ $chair['color'] == 'green' ? 'bg-green-500' : ($chair['color'] == 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}">
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Reception Tools -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Reception Tools</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('backend.patient-families.index') }}"
                    class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="font-medium text-blue-700">Family Records</span>
                    </div>
                </a>

                <a href="{{ route('backend.appointments.queue') }}"
                    class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-green-50">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-medium text-green-700">Queue Display</span>
                    </div>
                </a>

                <a href="#"
                    class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-purple-50">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-purple-700">Print Forms</span>
                    </div>
                </a>

                <a href="#"
                    class="bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-orange-50">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-orange-700">Daily Report</span>
                    </div>
                </a>
            </div>
        </div>
    </main>
@endsection