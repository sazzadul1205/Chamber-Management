@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Referral Tracking</h2>
                <p class="text-gray-600 mt-1 text-sm">Track patient referrals and referrer performance</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.referrals.report') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Report', 'class' => 'w-4 h-4'])
                    <span>Generate Report</span>
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

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-500 text-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h6 class="text-sm font-medium opacity-90">Total Referrers</h6>
                        <h3 class="text-2xl font-bold mt-1">{{ $stats['total_referrers'] }}</h3>
                    </div>
                    <i class="fas fa-user-friends text-2xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-green-500 text-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h6 class="text-sm font-medium opacity-90">Patients Referred</h6>
                        <h3 class="text-2xl font-bold mt-1">{{ $stats['total_referred'] }}</h3>
                    </div>
                    <i class="fas fa-users text-2xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-purple-500 text-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h6 class="text-sm font-medium opacity-90">Active Referred</h6>
                        <h3 class="text-2xl font-bold mt-1">{{ $stats['active_referred'] }}</h3>
                    </div>
                    <i class="fas fa-user-check text-2xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-yellow-500 text-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h6 class="text-sm font-medium opacity-90">Total Revenue</h6>
                        <h3 class="text-2xl font-bold mt-1">৳ {{ number_format($stats['total_revenue'], 2) }}</h3>
                    </div>
                    <i class="fas fa-money-bill-wave text-2xl opacity-80"></i>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.referrals.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, code, or phone"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="to_date" value="{{ request('to_date') }}"
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
                        <th class="px-4 py-3 text-left text-sm font-medium">Referrer</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patients Referred</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Active Patients</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Total Revenue</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($referrers as $referrer)
                        @php
                            $referrerStats = $referrer->referral_stats;
                        @endphp
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($referrers->currentPage() - 1) * $referrers->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $referrer->full_name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-xs">
                                                {{ $referrer->patient_code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="text-gray-900">{{ $referrer->phone }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $referrer->email ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-blue-600">
                                    {{ $referrer->total_referred }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $referrer->active_patients }} Active
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-green-600">
                                    ৳ {{ number_format($referrerStats['total_value'], 2) }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.referrals.show', $referrer) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View Details">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.patients.show', $referrer) }}"
                                        class="px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs"
                                        title="Patient Profile">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'User',
                                            'class' => 'w-4 h-4 text-white',
                                        ])
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fas fa-users text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No referrers found</p>
                                    <p class="text-xs text-gray-400 mt-1">Patients who refer other patients will appear here
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$referrers" />
        </div>

    </div>
@endsection
