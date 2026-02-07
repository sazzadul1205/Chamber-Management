@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Referral Performance Report</h2>
                <p class="text-gray-600 mt-1 text-sm">Detailed analysis of referral performance</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.referrals.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                    <span>Back to Tracking</span>
                </a>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.referrals.report') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

            <div class="md:col-span-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Generate Report
                </button>
            </div>

            <div class="md:col-span-2">
                <a href="{{ route('backend.referrals.report') }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium transition">
                    <span>Reset Filters</span>
                </a>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Referral Performance Summary</h3>
                <button onclick="window.print()" class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Referrer</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patients Referred</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Active Patients</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Completed</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Total Revenue</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Avg. Revenue/Patient</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Conversion Rate</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($report as $item)
                        @php
                            $referrer = $item->referrer;
                            $avgRevenue = $item->total_referred > 0 ? $item->total_revenue / $item->total_referred : 0;
                            $conversionRate = $item->total_referred > 0 ? ($item->completed_patients / $item->total_referred) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $referrer->full_name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-xs">
                                                {{ $referrer->patient_code ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="font-bold text-blue-600">{{ $item->total_referred }}</span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                    {{ $item->active_patients }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">
                                    {{ $item->completed_patients }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="font-bold text-green-600">
                                    ৳ {{ number_format($item->total_revenue, 2) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="font-medium text-gray-700">
                                    ৳ {{ number_format($avgRevenue, 2) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <span class="font-medium {{ $conversionRate >= 50 ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ number_format($conversionRate, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fas fa-chart-bar text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No referral data available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection