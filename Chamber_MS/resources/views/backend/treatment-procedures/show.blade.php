@extends('backend.layout.structure')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Procedure Details</h1>
            <p class="text-gray-600 mt-1">View procedure information</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('backend.treatment-procedures.edit', $treatmentProcedure) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            
            <!-- Status Actions -->
            <div class="flex items-center space-x-2">
                @if($treatmentProcedure->status == 'planned')
                    <form action="{{ route('backend.treatment-procedures.start', $treatmentProcedure) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Start Procedure
                        </button>
                    </form>
                @endif
                
                @if($treatmentProcedure->status == 'in_progress')
                    <form action="{{ route('backend.treatment-procedures.complete', $treatmentProcedure) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            Mark Complete
                        </button>
                    </form>
                @endif
                
                @if(in_array($treatmentProcedure->status, ['planned', 'in_progress']))
                    <form action="{{ route('backend.treatment-procedures.cancel', $treatmentProcedure) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to cancel this procedure?')">
                            Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Procedure Information Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Procedure Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Procedure Code</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $treatmentProcedure->procedure_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Procedure Name</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $treatmentProcedure->procedure_name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tooth Number</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-900">
                                {{ $treatmentProcedure->tooth_number ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Surface</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-900">
                                {{ $treatmentProcedure->surface ?? '-' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cost</dt>
                            <dd class="mt-1 text-lg font-semibold text-green-600">
                                ${{ number_format($treatmentProcedure->cost, 2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">
                                {{ $treatmentProcedure->duration }} minutes
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'planned' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$treatmentProcedure->status] }}">
                                    {{ ucfirst(str_replace('_', ' ', $treatmentProcedure->status)) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created On</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $treatmentProcedure->created_at->format('M d, Y h:i A') }}
                            </dd>
                        </div>
                        
                        @if($treatmentProcedure->status == 'completed' && $treatmentProcedure->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completed On</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $treatmentProcedure->completed_at->format('M d, Y h:i A') }}
                                </dd>
                            </div>
                        @endif
                        
                        @if($treatmentProcedure->status == 'completed' && $treatmentProcedure->completer)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completed By</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $treatmentProcedure->completer->name }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Notes Card -->
            @if($treatmentProcedure->notes)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Notes</h2>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        {{ $treatmentProcedure->notes }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Treatment Information Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Treatment Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Patient</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                {{ $treatmentProcedure->treatment->patient->name ?? 'N/A' }}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Treatment ID</h3>
                            <p class="mt-1">
                                <a href="{{ route('backend.treatments.show', $treatmentProcedure->treatment_id) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    #{{ $treatmentProcedure->treatment_id }}
                                </a>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Treatment Status</h3>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $treatmentProcedure->treatment->status == 'active' ? 'bg-green-100 text-green-800' : 
                                       ($treatmentProcedure->treatment->status == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($treatmentProcedure->treatment->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Treatment Cost</h3>
                            <p class="mt-1 text-xl font-bold text-green-600">
                                ${{ number_format($treatmentProcedure->treatment->actual_cost ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('backend.treatments.show', $treatmentProcedure->treatment_id) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                        View Treatment
                    </a>
                    
                    <form action="{{ route('backend.treatment-procedures.destroy', $treatmentProcedure) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this procedure? This action cannot be undone.')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Procedure
                        </button>
                    </form>
                    
                    <a href="{{ route('backend.treatment-procedures.index') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to All Procedures
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection