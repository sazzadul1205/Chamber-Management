<!-- Quick Actions Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            @include('partials.sidebar-icon', ['name' => 'B_Bolt', 'class' => 'w-5 h-5 text-red-600'])
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
                    @include('partials.sidebar-icon', ['name' => 'B_Play', 'class' => 'w-5 h-5'])
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
                    @include('partials.sidebar-icon', ['name' => 'B_Tick', 'class' => 'w-5 h-5'])
                    Complete Treatment
                </button>
            </form>

            <form action="{{ route('backend.treatments.hold', $treatment) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                    @include('partials.sidebar-icon', ['name' => 'B_Pause', 'class' => 'w-5 h-5'])
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
                    @include('partials.sidebar-icon', ['name' => 'B_Play', 'class' => 'w-5 h-5'])
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
                    @include('partials.sidebar-icon', ['name' => 'B_Cross', 'class' => 'w-5 h-5'])
                    Cancel Treatment
                </button>
            </form>
        @endif

        {{-- Add Session --}}
        @if ($treatment->canAddSession())
            <a href="{{ route('backend.treatments.sessions.create', $treatment) }}"
                class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-5 h-5'])
                Add Session
            </a>
        @endif

        {{-- Add Procedure --}}
        <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
            class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
            @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-5 h-5'])
            Add Procedure
        </a>

        {{-- Create Prescription --}}
        @if (!$treatment->prescriptions()->exists())
            <a href="{{ route('backend.prescriptions.create', ['treatment' => $treatment->id]) }}"
                class="w-full bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                @include('partials.sidebar-icon', ['name' => 'B_Plus', 'class' => 'w-5 h-5'])
                Create Prescription
            </a>
        @endif

    </div>
</div>
