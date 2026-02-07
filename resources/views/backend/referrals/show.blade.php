@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Referrer Details</h2>
                <div class="flex items-center gap-2 mt-1 text-sm">
                    <a href="{{ route('backend.referrals.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Referral Tracking
                    </a>
                    <span class="text-gray-400">•</span>
                    <a href="{{ route('backend.patients.show', $patient) }}" class="text-gray-600 hover:text-gray-800">
                        View Patient Profile
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Print', 'class' => 'w-4 h-4'])
                    <span>Print Report</span>
                </button>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- REFERRER INFO -->
        <div class="overflow-x-auto bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium">{{ $patient->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Patient Code</p>
                            <p class="font-medium">{{ $patient->patient_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="font-medium">{{ $patient->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            {!! $patient->status_badge !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-500 text-white rounded-lg shadow p-4">
                <div class="text-center">
                    <h6 class="text-sm font-medium opacity-90">Total Referred</h6>
                    <h3 class="text-2xl font-bold mt-1">{{ $stats['total_referred'] }}</h3>
                    <p class="text-xs opacity-80 mt-1">Patients</p>
                </div>
            </div>

            <div class="bg-green-500 text-white rounded-lg shadow p-4">
                <div class="text-center">
                    <h6 class="text-sm font-medium opacity-90">Completed Visits</h6>
                    <h3 class="text-2xl font-bold mt-1">{{ $stats['completed_visits'] }}</h3>
                    <p class="text-xs opacity-80 mt-1">Appointments</p>
                </div>
            </div>

            <div class="bg-purple-500 text-white rounded-lg shadow p-4">
                <div class="text-center">
                    <h6 class="text-sm font-medium opacity-90">Active Patients</h6>
                    <h3 class="text-2xl font-bold mt-1">{{ $stats['active_patients'] }}</h3>
                    <p class="text-xs opacity-80 mt-1">Currently Active</p>
                </div>
            </div>

            <div class="bg-yellow-500 text-white rounded-lg shadow p-4">
                <div class="text-center">
                    <h6 class="text-sm font-medium opacity-90">Total Value</h6>
                    <h3 class="text-2xl font-bold mt-1">৳ {{ number_format($stats['total_value'], 2) }}</h3>
                    <p class="text-xs opacity-80 mt-1">Revenue Generated</p>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.referrals.show', $patient) }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

            <div class="md:col-span-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search referred patients..."
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patient</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Appointments</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Treatments</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Total Value</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Referred On</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($referredPatients as $referred)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($referredPatients->currentPage() - 1) * $referredPatients->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('backend.patients.show', $referred) }}"
                                                class="hover:text-blue-600">
                                                {{ $referred->full_name }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-xs">
                                                {{ $referred->patient_code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="text-gray-900">{{ $referred->phone }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $referred->email ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                {!! $referred->status_badge !!}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                    {{ $referred->appointments_count }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                    {{ $referred->treatments_count }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-green-600">
                                    ৳ {{ number_format($referred->invoices_sum_total_amount ?? 0, 2) }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $referred->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fas fa-user-plus text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No patients referred yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$referredPatients" />
        </div>

    </div>

    <!-- Chart Script (if needed) -->
    @if (isset($monthlyData))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('referralChart')?.getContext('2d');
                if (!ctx) return;

                const monthlyData = @json($monthlyData ?? []);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [{
                            label: 'Patients Referred',
                            data: monthlyData.map(item => item.count),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
