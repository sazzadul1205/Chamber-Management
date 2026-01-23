@extends('backend.layout.structure')

@section('content')
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <i class="fas fa-tooth"></i>
                    Dental Clinic Chairs Dashboard
                </h1>
                <p class="text-sm opacity-90">Real-time chair status monitoring</p>
            </div>

            <div class="mt-4 md:mt-0 text-right">
                <div id="currentTime" class="text-xl font-bold">
                    {{ now()->format('h:i A') }}
                </div>
                <div class="text-sm opacity-90">
                    {{ now()->format('l, F j, Y') }}
                </div>
                <div class="text-xs opacity-75 mt-1">
                    Auto-refresh in <span id="refreshTimer">30</span>s
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-green-500 text-white rounded-xl p-4 text-center shadow">
                <div class="text-2xl font-bold">{{ $chairs->where('status', 'available')->count() }}</div>
                <div class="text-sm">Available</div>
            </div>

            <div class="bg-yellow-400 text-gray-900 rounded-xl p-4 text-center shadow">
                <div class="text-2xl font-bold">{{ $chairs->where('status', 'occupied')->count() }}</div>
                <div class="text-sm">Occupied</div>
            </div>

            <div class="bg-red-500 text-white rounded-xl p-4 text-center shadow">
                <div class="text-2xl font-bold">{{ $chairs->where('status', 'maintenance')->count() }}</div>
                <div class="text-sm">Maintenance</div>
            </div>

            <div class="bg-blue-500 text-white rounded-xl p-4 text-center shadow">
                <div class="text-2xl font-bold">{{ $chairs->count() }}</div>
                <div class="text-sm">Total Chairs</div>
            </div>
        </div>

        {{-- Chairs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($chairs as $chair)
                <div class="bg-white rounded-2xl shadow hover:shadow-lg transition overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b">
                        <div>
                            <h5 class="font-semibold flex items-center gap-2">
                                <i class="fas fa-chair text-gray-500"></i>
                                {{ $chair->name }}
                            </h5>
                            <p class="text-xs text-gray-500">{{ $chair->chair_code }}</p>
                        </div>

                        <span
                            class="px-3 py-1 text-xs font-semibold rounded-full
                    @if ($chair->status === 'available') bg-green-100 text-green-700
                    @elseif($chair->status === 'occupied') bg-yellow-100 text-yellow-700
                    @elseif($chair->status === 'maintenance') bg-red-100 text-red-700
                    @else bg-blue-100 text-blue-700 @endif">
                            {{ $chair->status_name }}
                        </span>
                    </div>

                    <div class="p-5 text-sm text-gray-700">
                        <div class="flex items-center gap-2 mb-3 text-gray-500">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $chair->location ?? 'Not specified' }}
                        </div>

                        @if ($chair->currentAppointment)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h6 class="font-semibold mb-2 flex items-center gap-2">
                                    <i class="fas fa-user-injured"></i>
                                    Current Patient
                                </h6>

                                <div class="mb-1"><strong>Name:</strong>
                                    {{ $chair->currentAppointment->patient->full_name ?? 'N/A' }}
                                </div>
                                <div class="mb-1"><strong>Doctor:</strong>
                                    {{ $chair->currentAppointment->doctor->user->full_name ?? 'N/A' }}
                                </div>
                                <div class="mb-1"><strong>Appointment:</strong>
                                    {{ $chair->currentAppointment->appointment_time }}
                                </div>

                                <span
                                    class="inline-block mt-2 px-3 py-1 text-xs rounded-full
                            {{ $chair->currentAppointment->status === 'in_progress'
                                ? 'bg-yellow-100 text-yellow-700'
                                : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($chair->currentAppointment->status) }}
                                </span>
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-400">
                                <i class="fas fa-chair text-4xl mb-3"></i>
                                <p>No current appointment</p>
                                @if ($chair->last_used)
                                    <p class="text-xs mt-1">
                                        Last used: {{ $chair->formatted_last_used }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if ($chair->notes)
                            <div class="border-t mt-4 pt-3 text-xs text-gray-500 flex gap-2">
                                <i class="fas fa-sticky-note"></i>
                                {{ $chair->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="text-center text-xs text-gray-500 mt-8">
            <i class="fas fa-sync-alt mr-1"></i>
            Auto-refresh every 30 seconds |
            Last updated: {{ now()->format('h:i:s A') }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent =
                now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

            document.querySelector('.date-display')?.textContent =
                now.toLocaleDateString([], {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
        }

        let refreshCounter = 30;

        setInterval(() => {
            refreshCounter--;
            document.getElementById('refreshTimer').textContent = refreshCounter;
            if (refreshCounter <= 0) location.reload();
        }, 1000);

        setInterval(updateTime, 1000);
    </script>
@endsection
