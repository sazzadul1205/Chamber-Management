<!-- Quick Actions Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <!-- Bolt Icon -->
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Quick Actions
        </h3>
    </div>

    <div class="p-4 space-y-3">
        {{-- Start Treatment --}}
        @if ($treatment->status === 'planned')
            <form action="{{ route('backend.treatments.start', $treatment) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <!-- Play Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-6.518-3.759A1 1 0 007 8.279v7.442a1 1 0 001.234.97l6.518-1.88a1 1 0 00.752-.97V12.14a1 1 0 00-.752-.97z" />
                    </svg>
                    Start Treatment
                </button>
            </form>
        @endif

        {{-- In Progress Actions --}}
        @if ($treatment->status === 'in_progress')
            <form action="{{ route('backend.treatments.complete', $treatment) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <!-- Check Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Complete Treatment
                </button>
            </form>

            <form action="{{ route('backend.treatments.hold', $treatment) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <!-- Pause Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6" />
                    </svg>
                    Put on Hold
                </button>
            </form>
        @endif

        {{-- Resume --}}
        @if ($treatment->status === 'on_hold')
            <form action="{{ route('backend.treatments.resume', $treatment) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <!-- Play Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-6.518-3.759A1 1 0 007 8.279v7.442a1 1 0 001.234.97l6.518-1.88a1 1 0 00.752-.97V12.14a1 1 0 00-.752-.97z" />
                    </svg>
                    Resume Treatment
                </button>
            </form>
        @endif

        {{-- Cancel --}}
        @if (in_array($treatment->status, ['planned', 'in_progress', 'on_hold']))
            <form action="{{ route('backend.treatments.cancel', $treatment) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to cancel this treatment? This action cannot be undone.')">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <!-- X Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel Treatment
                </button>
            </form>
        @endif

        {{-- Add Session --}}
        @if ($treatment->canAddSession())
            <a href="{{ route('backend.treatments.sessions.create', $treatment) }}"
                class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                <!-- Plus Circle Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Session
            </a>
        @endif


        {{-- Add Procedure --}}
        <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
            class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
            <!-- Tooth Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4c-3.314 0-6 2.239-6 5 0 2.5 1.5 3.5 1.5 6.5S9 20 10.5 20c1.5 0 1.5-2 1.5-2s0 2 1.5 2 3-1.5 3-4.5S18 11.5 18 9c0-2.761-2.686-5-6-5z" />
            </svg>
            Add Procedure
        </a>

        {{-- Create Prescription --}}
        <a href="{{ route('backend.prescriptions.create', ['treatment' => $treatment->id]) }}"
            class="w-full bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
            <!-- Plus Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
            Create Prescription
        </a>
    </div>
</div>
