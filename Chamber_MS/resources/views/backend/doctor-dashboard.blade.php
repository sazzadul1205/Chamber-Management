@extends('backend.layout.structure')

@section('content')
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Doctor Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back, Dr. {{ auth()->user()->name }}! Your schedule and patient
                    overview.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>
        </div>

        <!-- Doctor Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Today's Appointments</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">8</h3>
                        <p class="text-xs text-gray-500 mt-1">2 pending, 6 confirmed</p>
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
                    View Today's Schedule
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Active Treatments -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Treatments</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1">15</h3>
                        <p class="text-xs text-gray-500 mt-1">Under your care</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.treatments.index') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    View Treatments
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Pending Prescriptions -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Prescriptions</p>
                        <h3 class="text-2xl font-bold text-yellow-600 mt-1">3</h3>
                        <p class="text-xs text-gray-500 mt-1">To be reviewed</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.prescriptions.index') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    View Prescriptions
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
                        <h3 class="text-2xl font-bold text-purple-600 mt-1">2</h3>
                        <p class="text-xs text-gray-500 mt-1">In waiting room</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>

        <!-- Quick Doctor Actions -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Start Consultation -->
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
                            <p class="text-sm text-gray-500">Schedule consultation</p>
                        </div>
                    </div>
                </a>

                <!-- Create Treatment -->
                <a href="{{ route('backend.treatments.create') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-green-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-50 p-3 rounded-lg group-hover:bg-green-100">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-green-600">New Treatment</h3>
                            <p class="text-sm text-gray-500">Create treatment plan</p>
                        </div>
                    </div>
                </a>

                <!-- Write Prescription -->
                <a href="{{ route('backend.prescriptions.create') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-purple-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-50 p-3 rounded-lg group-hover:bg-purple-100">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-purple-600">Write Prescription</h3>
                            <p class="text-sm text-gray-500">Prescribe medication</p>
                        </div>
                    </div>
                </a>

                <!-- View Patient Records -->
                <a href="{{ route('backend.patients.index') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-orange-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-orange-50 p-3 rounded-lg group-hover:bg-orange-100">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-orange-600">Patient Records</h3>
                            <p class="text-sm text-gray-500">View patient history</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Schedule -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">Today's Schedule</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @for($i = 1; $i <= 4; $i++)
                            <div
                                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">{{ 9 + $i }}:00</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Patient {{ $i }}</h4>
                                        <p class="text-sm text-gray-500">Check-up • Room {{ $i + 1 }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                              @if($i == 1) bg-green-100 text-green-800 
                                              @elseif($i == 2) bg-yellow-100 text-yellow-800 
                                              @else bg-gray-100 text-gray-800 @endif">
                                    @if($i == 1) In Progress
                                    @elseif($i == 2) Waiting
                                    @else Scheduled @endif
                                </span>
                            </div>
                        @endfor
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('backend.appointments.calendar') }}"
                            class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                            View Full Calendar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Follow-ups -->
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">Upcoming Follow-ups</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @for($i = 1; $i <= 3; $i++)
                            <div
                                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div>
                                    <h4 class="font-medium text-gray-800">Follow-up Treatment {{ $i }}</h4>
                                    <p class="text-sm text-gray-500">Patient {{ $i }} • Tomorrow {{ 10 + $i }}:00 AM</p>
                                </div>
                                <a href="{{ route('backend.treatments.show', $i) }}"
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
                                    View
                                </a>
                            </div>
                        @endfor
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('backend.treatments.index') }}"
                            class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                            View All Treatments
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Tools -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Medical Tools</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('backend.dental-charts.index') }}"
                    class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-medium text-blue-700">Dental Chart</span>
                    </div>
                </a>

                <a href="{{ route('backend.procedure-catalog.index') }}"
                    class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-green-50">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="font-medium text-green-700">Procedure Catalog</span>
                    </div>
                </a>

                <a href="{{ route('backend.diagnosis-codes.index') }}"
                    class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-purple-50">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-purple-700">Diagnosis Codes</span>
                    </div>
                </a>

                <a href="{{ route('backend.medical-files.index') }}"
                    class="bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-4 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg group-hover:bg-orange-50">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-orange-700">Medical Files</span>
                    </div>
                </a>
            </div>
        </div>
    </main>
@endsection