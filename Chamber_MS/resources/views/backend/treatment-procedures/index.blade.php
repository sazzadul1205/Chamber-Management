@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
                <h2 class="text-2xl font-semibold mb-3 md:mb-0">Treatment Procedures</h2>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('backend.treatment-procedures.create') }}"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                        @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                        New Procedure
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('backend.treatment-procedures.index') }}" 
                  class="grid grid-cols-1 md:grid-cols-12 gap-3">

                <!-- Search -->
                <div class="md:col-span-3">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search code / name"
                           class="w-full border rounded px-3 py-2">
                </div>

                <!-- Treatment -->
                <div class="md:col-span-2">
                    <select name="treatment_id" class="w-full border rounded px-3 py-2">
                        <option value="">All Treatments</option>
                        @foreach($treatments as $treatment)
                            <option value="{{ $treatment->id }}" {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                                {{ $treatment->treatment_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <select name="status" class="w-full border rounded px-3 py-2">
                        <option value="">All Status</option>
                        @foreach(\App\Models\TreatmentProcedure::statuses() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tooth Number -->
                <div class="md:col-span-2">
                    <input type="text" name="tooth_number" value="{{ request('tooth_number') }}" 
                           placeholder="Tooth number"
                           class="w-full border rounded px-3 py-2">
                </div>

                <!-- Filter Button -->
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                        Filter
                    </button>
                </div>

                <!-- Clear Filters (if any filters applied) -->
                @if(request()->anyFilled(['search', 'treatment_id', 'status', 'tooth_number']))
                    <div class="md:col-span-1">
                        <a href="{{ route('backend.treatment-procedures.index') }}"
                           class="w-full inline-block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 rounded px-3 py-2">
                            Clear
                        </a>
                    </div>
                @endif
            </form>

            <!-- Table -->
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left text-sm">Code</th>
                            <th class="px-3 py-2 text-left text-sm">Procedure</th>
                            <th class="px-3 py-2 text-left text-sm">Patient</th>
                            <th class="px-3 py-2 text-left text-sm">Tooth</th>
                            <th class="px-3 py-2 text-left text-sm">Cost</th>
                            <th class="px-3 py-2 text-left text-sm">Duration</th>
                            <th class="px-3 py-2 text-left text-sm">Status</th>
                            <th class="px-3 py-2 text-center text-sm">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($procedures as $procedure)
                            <tr class="hover:bg-gray-50">
                                <!-- Code -->
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-medium">
                                        {{ $procedure->procedure_code }}
                                    </span>
                                </td>

                                <!-- Procedure Name -->
                                <td class="px-3 py-2">
                                    <div class="font-medium text-gray-900">{{ $procedure->procedure_name }}</div>
                                    @if($procedure->notes)
                                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                            {{ Str::limit($procedure->notes, 50) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Patient -->
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.patients.show', $procedure->treatment->patient_id) }}"
                                               class="text-blue-600 hover:underline font-medium">
                                                {{ $procedure->treatment->patient->full_name ?? 'Unknown' }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                {{ $procedure->treatment->treatment_code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tooth -->
                                <td class="px-3 py-2">
                                    @if($procedure->tooth_number)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-800 font-medium">
                                                Tooth #{{ $procedure->tooth_number }}
                                            </span>
                                            @if($procedure->surface)
                                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                    {{ $procedure->surface }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Cost -->
                                <td class="px-3 py-2">
                                    <span class="font-medium text-green-600">
                                        ${{ number_format($procedure->cost, 2) }}
                                    </span>
                                </td>

                                <!-- Duration -->
                                <td class="px-3 py-2">
                                    <div class="text-sm text-gray-700">
                                        {{ $procedure->duration }} min
                                    </div>
                                    @if($procedure->created_at)
                                        <div class="text-xs text-gray-500">
                                            {{ $procedure->created_at->format('M d') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-3 py-2">
                                    @php
                                        $statusColors = [
                                            'planned' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded {{ $statusColors[$procedure->status] }}">
                                        {{ ucfirst(str_replace('_', ' ', $procedure->status)) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-3 py-2">
                                    <div class="flex justify-center gap-1">
                                        <!-- View -->
                                        <a href="{{ route('backend.treatment-procedures.show', $procedure) }}"
                                            class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                            title="View Details">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_View',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('backend.treatment-procedures.edit', $procedure) }}"
                                            class="w-8 h-8 flex items-center justify-center bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                            title="Edit">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Edit',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>

                                        <!-- Start -->
                                        @if($procedure->status == 'planned')
                                            <form method="POST" action="{{ route('backend.treatment-procedures.start', $procedure) }}"
                                                  class="inline" onsubmit="return confirm('Start this procedure?')">
                                                @csrf
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                        title="Start Procedure">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M14.752 11.168l-6.518-3.759A1 1 0 007 8.215v7.57a1 1 0 001.234.97l6.518-1.887a1 1 0 00.752-.97v-3.72a1 1 0 00-.752-.97z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Complete -->
                                        @if($procedure->status == 'in_progress')
                                            <form method="POST" action="{{ route('backend.treatment-procedures.complete', $procedure) }}"
                                                  class="inline" onsubmit="return confirm('Mark as complete?')">
                                                @csrf
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                        title="Mark Complete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancel -->
                                        @if(in_array($procedure->status, ['planned', 'in_progress']))
                                            <form method="POST" action="{{ route('backend.treatment-procedures.cancel', $procedure) }}"
                                                  class="inline" onsubmit="return confirm('Cancel this procedure?')">
                                                @csrf
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                        title="Cancel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('backend.treatment-procedures.destroy', $procedure) }}"
                                              class="inline" onsubmit="return confirm('Delete this procedure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                    title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    No procedures found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <x-pagination :paginator="$procedures" />
        </div>
@endsection