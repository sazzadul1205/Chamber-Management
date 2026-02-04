@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Reminder Statistics</h2>
            <a href="{{ route('backend.reminders.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium transition">
                @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                <span>Back to Reminders</span>
            </a>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Reminders Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Reminders</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="bg-blue-400/20 p-3 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Stats',
                            'class' => 'w-6 h-6 text-blue-200',
                        ])
                    </div>
                </div>
                <div class="mt-4 text-blue-100 text-sm">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                clip-rule="evenodd" />
                        </svg>
                        All time statistics
                    </span>
                </div>
            </div>

            <!-- Sent Reminders Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Sent Successfully</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $stats['sent'] }}</h3>
                        @if ($stats['total'] > 0)
                            <p class="text-green-200 text-sm mt-1">
                                {{ number_format(($stats['sent'] / $stats['total']) * 100, 1) }}% success rate
                            </p>
                        @endif
                    </div>
                    <div class="bg-green-400/20 p-3 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Send',
                            'class' => 'w-6 h-6 text-green-200',
                        ])
                    </div>
                </div>
            </div>

            <!-- Pending Reminders Card -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">Pending</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $stats['pending'] }}</h3>
                        @if ($stats['total'] > 0)
                            <p class="text-amber-200 text-sm mt-1">
                                {{ number_format(($stats['pending'] / $stats['total']) * 100, 1) }}% pending
                            </p>
                        @endif
                    </div>
                    <div class="bg-amber-400/20 p-3 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Clock',
                            'class' => 'w-6 h-6 text-amber-200',
                        ])
                    </div>
                </div>
            </div>

            <!-- Failed Reminders Card -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Failed</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $stats['failed'] }}</h3>
                        @if ($stats['total'] > 0)
                            <p class="text-red-200 text-sm mt-1">
                                {{ number_format(($stats['failed'] / $stats['total']) * 100, 1) }}% failure rate
                            </p>
                        @endif
                    </div>
                    <div class="bg-red-400/20 p-3 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Warning',
                            'class' => 'w-6 h-6 text-red-200',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Line Chart - Last 7 Days -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Reminders Sent (Last 7 Days)</h3>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                <span class="text-sm text-gray-600">Sent</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                <span class="text-sm text-gray-600">Failed</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart - By Type -->
            <div>
                <div class="bg-white rounded-lg shadow p-6 h-full">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">By Reminder Type</h3>
                    <div class="h-64">
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ($typeStats as $stat)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="w-3 h-3 rounded-full mr-2 
                                        {{ $stat->reminder_type == 'sms' ? 'bg-blue-500' : '' }}
                                        {{ $stat->reminder_type == 'email' ? 'bg-green-500' : '' }}
                                        {{ $stat->reminder_type == 'push' ? 'bg-purple-500' : '' }}
                                        {{ $stat->reminder_type == 'in_app' ? 'bg-amber-500' : '' }}">
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ strtoupper($stat->reminder_type) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-900">{{ $stat->sent }}</span>
                                    <span class="text-xs text-gray-500 ml-1">
                                        ({{ $stat->total > 0 ? number_format(($stat->sent / $stat->total) * 100, 0) : 0 }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILED STATISTICS TABLE -->
        <div class="bg-white rounded-lg shadow mt-6 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Daily Statistics</h3>
                <p class="text-sm text-gray-600 mt-1">Detailed breakdown of reminder performance</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Sent
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Failed
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Success Rate
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($dailyStats as $day)
                            @php
                                $rate = $day->total > 0 ? ($day->sent / $day->total) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($day->date)->format('D, M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $day->total }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $day->sent }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $day->failed }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mr-3">
                                            <div class="h-2.5 rounded-full 
                                                {{ $rate >= 90 ? 'bg-green-500' : '' }}
                                                {{ $rate >= 70 && $rate < 90 ? 'bg-yellow-500' : '' }}
                                                {{ $rate < 70 ? 'bg-red-500' : '' }}"
                                                style="width: {{ min($rate, 100) }}%"></div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($rate, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($rate >= 90)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Excellent
                                        </span>
                                    @elseif($rate >= 70)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Good
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Needs Attention
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Performance Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Summary</h3>
                <div class="space-y-4">
                    @php
                        $avgSuccess = $dailyStats->avg(function ($day) {
                            return $day->total > 0 ? ($day->sent / $day->total) * 100 : 0;
                        });
                        $totalSent = $dailyStats->sum('sent');
                        $totalFailed = $dailyStats->sum('failed');
                    @endphp
                    <div>
                        <p class="text-sm text-gray-600">Average Success Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($avgSuccess, 1) }}%</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Sent</p>
                            <p class="text-xl font-bold text-green-600">{{ $totalSent }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Failed</p>
                            <p class="text-xl font-bold text-red-600">{{ $totalFailed }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Effective Type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Effective Type</h3>
                @php
                    $mostEffective = $typeStats
                        ->sortByDesc(function ($stat) {
                            return $stat->total > 0 ? ($stat->sent / $stat->total) * 100 : 0;
                        })
                        ->first();
                @endphp
                @if ($mostEffective)
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Highest Success Rate</span>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $mostEffective->total > 0 ? number_format(($mostEffective->sent / $mostEffective->total) * 100, 1) : 0 }}%
                            </p>
                            <p class="text-sm text-gray-500 mt-1">{{ strtoupper($mostEffective->reminder_type) }}
                                reminders</p>
                        </div>
                        <div class="text-3xl">
                            @if ($mostEffective->reminder_type == 'email')
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_Email',
                                    'class' => 'w-12 h-12 text-green-500',
                                ])
                            @elseif($mostEffective->reminder_type == 'sms')
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_SMS',
                                    'class' => 'w-12 h-12 text-blue-500',
                                ])
                            @else
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_Bell',
                                    'class' => 'w-12 h-12 text-purple-500',
                                ])
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recommendations -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">Recommendations</h3>
                <ul class="space-y-2">
                    @if ($stats['failed'] > 0)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Review {{ $stats['failed'] }} failed reminders for improvement</span>
                        </li>
                    @endif
                    @if ($stats['pending'] > 0)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ $stats['pending'] }} reminders scheduled for future delivery</span>
                        </li>
                    @endif
                    @if ($avgSuccess < 80)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Consider adjusting reminder timing for better response rates</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Daily Line Chart
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json(
                    $dailyStats->pluck('date')->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('D, M d');
                    })),
                datasets: [{
                    label: 'Sent',
                    data: @json($dailyStats->pluck('sent')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Failed',
                    data: @json($dailyStats->pluck('failed')),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Type Doughnut Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: @json(
                    $typeStats->pluck('reminder_type')->map(function ($type) {
                        return strtoupper($type);
                    })),
                datasets: [{
                    data: @json($typeStats->pluck('sent')),
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#10b981', // green
                        '#8b5cf6', // purple
                        '#f59e0b' // amber
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection
