@extends('backend.layout.structure')

@section('content')
    <main class="flex-1 overflow-y-auto p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Doctor Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back, Dr. {{ $user->full_name ?? $user->name }}! Clinical overview and daily workflow.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Appointments</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($doctorAppointmentCount ?? 0) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($doctorAppointmentToday ?? 0) }} scheduled today</p>
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
                    View Today's Appointments
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Treatments</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1">{{ number_format($doctorActiveTreatmentCount ?? 0) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($doctorPatientCount ?? 0) }} patients under care</p>
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

            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Today's Prescriptions</p>
                        <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($doctorPrescriptionToday ?? 0) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Issued today</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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

            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Waiting / In Progress</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format(($doctorWaitingCount ?? 0) + ($doctorInProgressCount ?? 0)) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($doctorWaitingCount ?? 0) }} waiting, {{ number_format($doctorInProgressCount ?? 0) }} in progress</p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('backend.appointments.queue') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-4">
                    Open Queue
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Doctor Management</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ $doctorId ? route('backend.doctors.calendar', $doctorId) : '#' }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-blue-300 transition-all group {{ $doctorId ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg group-hover:bg-blue-100">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-blue-600">My Calendar</h3>
                            <p class="text-sm text-gray-500">Manage your schedule</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('backend.doctors.my-leaves') }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-green-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-50 p-3 rounded-lg group-hover:bg-green-100">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-green-600">My Leaves</h3>
                            <p class="text-sm text-gray-500">View leave status</p>
                        </div>
                    </div>
                </a>

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
                            <p class="text-sm text-gray-500">Add medication plan</p>
                        </div>
                    </div>
                </a>

                <a href="{{ $doctorId ? route('backend.doctors.calendar', $doctorId) : '#' }}"
                    class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-indigo-300 transition-all group {{ $doctorId ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-50 p-3 rounded-lg group-hover:bg-indigo-100">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 group-hover:text-indigo-600">My Calendar</h3>
                            <p class="text-sm text-gray-500">Manage doctor schedule</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 opacity-90">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">Today Snapshot</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-700">Appointments Today</span>
                        <span class="font-semibold text-blue-700">{{ number_format($doctorAppointmentToday ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm text-gray-700">Waiting Patients</span>
                        <span class="font-semibold text-yellow-700">{{ number_format($doctorWaitingCount ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-700">In Progress</span>
                        <span class="font-semibold text-green-700">{{ number_format($doctorInProgressCount ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-700">Prescriptions Today</span>
                        <span class="font-semibold text-purple-700">{{ number_format($doctorPrescriptionToday ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">Quick Links</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('backend.patients.index') }}"
                        class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                        <span class="font-medium text-gray-700">Patient Management</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('backend.appointments.index') }}"
                        class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                        <span class="font-medium text-gray-700">Appointment Management</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('backend.treatments.index') }}"
                        class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                        <span class="font-medium text-gray-700">Treatment Plans</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                <a href="{{ route('backend.prescriptions.index') }}"
                    class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                    <span class="font-medium text-gray-700">Prescription Management</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ $doctorId ? route('backend.doctors.calendar', $doctorId) : '#' }}"
                    class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group {{ $doctorId ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                    <span class="font-medium text-gray-700">My Calendar</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('backend.doctors.my-leaves') }}"
                    class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                    <span class="font-medium text-gray-700">My Leaves</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        </div>
    </main>
@endsection
