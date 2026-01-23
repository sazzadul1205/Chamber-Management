@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Doctor Details: {{ $doctor->user->full_name ?? 'Demo Doctor' }}</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ $doctor->id ? route('backend.doctors.edit', $doctor->id) : '#' }}"
                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center gap-2 shadow {{ $doctor->id ? '' : 'opacity-50 cursor-not-allowed' }}">
                    <i class="fas fa-edit"></i> Edit
                </a>

                <a href="{{ route('backend.doctors.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg flex items-center gap-2 shadow">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Doctor Info Card -->
            <div class="bg-white border rounded-xl shadow p-6 text-center">
                <div class="mb-4">
                    <i class="fas fa-user-md fa-4x text-blue-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-1">{{ $doctor->user->full_name ?? 'Demo Doctor' }}</h3>
                <p class="text-gray-500 mb-4">{{ $doctor->doctor_code ?? 'N/A' }}</p>

                <table class="w-full text-sm text-gray-700">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Phone:</th>
                            <td>{{ $doctor->user->phone ?? '0000000000' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Email:</th>
                            <td>{{ $doctor->user->email ?? 'demo@example.com' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Specialization:</th>
                            <td>{{ $doctor->specialization ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Qualification:</th>
                            <td>{{ $doctor->qualification ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Consultation Fee:</th>
                            <td class="font-semibold">৳ {{ number_format($doctor->consultation_fee ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Commission:</th>
                            <td class="font-semibold">{{ $doctor->commission_percent ?? 0 }}%</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Status:</th>
                            <td>
                                <span
                                    class="px-2 py-1 rounded text-white 
                                {{ $doctor->status == 'active' ? 'bg-green-500' : ($doctor->status == 'inactive' ? 'bg-gray-500' : 'bg-yellow-500') }}">
                                    {{ ucfirst(str_replace('_', ' ', $doctor->status ?? 'inactive')) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Statistics Cards -->
            @php
                // Total patients safely
                $totalPatients = 0;
                if ($doctor->treatments instanceof \Illuminate\Support\Collection) {
                    $totalPatients = $doctor->treatments->pluck('patient_id')->unique()->count();
                } elseif (method_exists($doctor, 'treatments')) {
                    $totalPatients = $doctor->treatments()->distinct('patient_id')->count();
                }

                // Total appointments safely
                $totalAppointments = 0;
                if ($doctor->appointments instanceof \Illuminate\Support\Collection) {
                    $totalAppointments = $doctor->appointments->count();
                } elseif (method_exists($doctor, 'appointments')) {
                    $totalAppointments = $doctor->appointments()->count();
                }
            @endphp

            <div class="md:col-span-2 grid grid-cols-2 gap-6">
                <div class="bg-blue-500 text-white rounded-xl shadow p-6">
                    <h6 class="text-sm font-medium">Total Patients</h6>
                    <h3 class="text-2xl font-bold">{{ $totalPatients }}</h3>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
                <div class="bg-green-500 text-white rounded-xl shadow p-6">
                    <h6 class="text-sm font-medium">Total Appointments</h6>
                    <h3 class="text-2xl font-bold">{{ $totalAppointments }}</h3>
                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                </div>

                <!-- Recent Appointments -->
                <div class="md:col-span-2 bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Recent Appointments</h4>

                    @php
                        $appointments =
                            $doctor->appointments instanceof \Illuminate\Support\Collection
                                ? $doctor->appointments
                                : ($doctor->appointments()->exists()
                                    ? $doctor->appointments()->orderByDesc('appointment_date')->get()
                                    : collect());
                    @endphp

                    @if ($appointments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-700 font-medium">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Date</th>
                                        <th class="py-2 px-3 text-left">Patient</th>
                                        <th class="py-2 px-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($appointments->take(5) as $appointment)
                                        <tr>
                                            <td class="py-2 px-3">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="py-2 px-3">{{ $appointment->patient->full_name ?? 'Demo Patient' }}
                                            </td>
                                            <td class="py-2 px-3">
                                                <span
                                                    class="px-2 py-1 rounded text-white 
                                                {{ $appointment->status == 'completed' ? 'bg-green-500' : 'bg-blue-500' }}">
                                                    {{ ucfirst($appointment->status ?? 'scheduled') }}
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
            </div>
        </div>
    </div>
@endsection
