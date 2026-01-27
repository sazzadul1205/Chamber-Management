@extends('backend.layout.structure')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Treatment Procedures</h1>
            <p class="text-gray-600 mt-1">Manage all treatment procedures</p>
        </div>
        <a href="{{ route('backend.treatment-procedures.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Procedure
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('backend.treatment-procedures.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">Treatment</label>
                <select id="treatment_id" name="treatment_id" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Treatments</option>
                    @foreach($treatments as $treatment)
                        <option value="{{ $treatment->id }}" {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                            {{ $treatment->patient->name ?? 'N/A' }} - #{{ $treatment->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="tooth_number" class="block text-sm font-medium text-gray-700 mb-1">Tooth Number</label>
                <input type="text" id="tooth_number" name="tooth_number" 
                       value="{{ request('tooth_number') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., 14">
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Filter
                </button>
                @if(request()->anyFilled(['treatment_id', 'status', 'tooth_number']))
                    <a href="{{ route('backend.treatment-procedures.index') }}" 
                       class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Procedures Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Procedure
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient/Treatment
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tooth/Surface
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cost/Duration
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($procedures as $procedure)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $procedure->procedure_code }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $procedure->procedure_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $procedure->treatment->patient->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Treatment #{{ $procedure->treatment_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $procedure->tooth_number ?? '-' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $procedure->surface ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    ${{ number_format($procedure->cost, 2) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $procedure->duration }} min
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'planned' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$procedure->status] }}">
                                    {{ ucfirst(str_replace('_', ' ', $procedure->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('backend.treatment-procedures.show', $procedure) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                    <a href="{{ route('backend.treatment-procedures.edit', $procedure) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('backend.treatment-procedures.destroy', $procedure) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this procedure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No procedures found</p>
                                    <p class="mt-1">Try adjusting your filters or add a new procedure</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($procedures->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $procedures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection