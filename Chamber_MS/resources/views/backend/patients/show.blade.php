@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Patient Details: {{ $patient->full_name }}
            </h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.patients.edit', $patient) }}"
                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center gap-2 shadow">
                    <i class="fas fa-edit"></i> Edit
                </a>

                <a href="{{ route('backend.patients.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg flex items-center gap-2 shadow">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Patient Info Card -->
            <div class="bg-white border rounded-xl shadow p-6 text-center">
                <div class="mb-4">
                    <i class="fas fa-user-circle fa-4x text-blue-500"></i>
                </div>

                <h3 class="text-xl font-semibold mb-1">{{ $patient->full_name }}</h3>
                <p class="text-gray-500 mb-2">{{ $patient->patient_code }}</p>

                <div class="mb-4">
                    {!! $patient->status_badge !!}
                </div>

                <table class="w-full text-sm text-gray-700">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Phone:</th>
                            <td>{{ $patient->phone }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Email:</th>
                            <td>{{ $patient->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Gender:</th>
                            <td>{{ $patient->gender_text }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Age:</th>
                            <td>{{ $patient->age_text }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Date of Birth:</th>
                            <td>
                                {{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Address:</th>
                            <td>{{ $patient->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Emergency Contact:</th>
                            <td>{{ $patient->emergency_contact ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Referred By:</th>
                            <td>{{ $patient->referrer->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Registered On:</th>
                            <td>{{ $patient->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Stats & Activity -->
            <div class="md:col-span-2 space-y-6">

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-blue-500 text-white rounded-xl shadow p-6">
                        <h6 class="text-sm font-medium">Total Visits</h6>
                        <h3 class="text-2xl font-bold">{{ $stats['total_visits'] }}</h3>
                    </div>

                    <div class="bg-green-500 text-white rounded-xl shadow p-6">
                        <h6 class="text-sm font-medium">Treatments</h6>
                        <h3 class="text-2xl font-bold">{{ $stats['total_treatments'] }}</h3>
                    </div>

                    <div class="bg-indigo-500 text-white rounded-xl shadow p-6">
                        <h6 class="text-sm font-medium">Invoices</h6>
                        <h3 class="text-2xl font-bold">{{ $stats['total_invoices'] }}</h3>
                    </div>

                    <div class="bg-yellow-500 text-white rounded-xl shadow p-6">
                        <h6 class="text-sm font-medium">Pending Amount</h6>
                        <h3 class="text-2xl font-bold">
                            ৳ {{ number_format($stats['pending_amount'], 2) }}
                        </h3>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Recent Appointments</h4>

                    @if ($patient->appointments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Date</th>
                                        <th class="py-2 px-3 text-left">Doctor</th>
                                        <th class="py-2 px-3 text-left">Type</th>
                                        <th class="py-2 px-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($patient->appointments->take(5) as $appointment)
                                        <tr>
                                            <td class="py-2 px-3">
                                                {{ $appointment->appointment_date->format('d/m/Y') }}
                                            </td>
                                            <td class="py-2 px-3">
                                                {{ $appointment->doctor->user->full_name ?? '-' }}
                                            </td>
                                            <td class="py-2 px-3">
                                                {{ ucfirst($appointment->appointment_type) }}
                                            </td>
                                            <td class="py-2 px-3">
                                                <span
                                                    class="px-2 py-1 rounded text-white
                                                    {{ $appointment->status == 'completed' ? 'bg-green-500' : 'bg-blue-500' }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center">No appointments found.</p>
                    @endif
                </div>

                <!-- Recent Treatments -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Recent Treatments</h4>

                    @if ($patient->treatments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Code</th>
                                        <th class="py-2 px-3 text-left">Doctor</th>
                                        <th class="py-2 px-3 text-left">Diagnosis</th>
                                        <th class="py-2 px-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($patient->treatments->take(5) as $treatment)
                                        <tr>
                                            <td class="py-2 px-3">{{ $treatment->treatment_code }}</td>
                                            <td class="py-2 px-3">
                                                {{ $treatment->doctor->user->full_name ?? '-' }}
                                            </td>
                                            <td class="py-2 px-3">
                                                {{ \Illuminate\Support\Str::limit($treatment->diagnosis, 30) }}
                                            </td>
                                            <td class="py-2 px-3">
                                                <span
                                                    class="px-2 py-1 rounded text-white
                                                    {{ $treatment->status == 'completed' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                                    {{ ucfirst($treatment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center">No treatments found.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
