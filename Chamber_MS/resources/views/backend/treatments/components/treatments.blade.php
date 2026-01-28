 <!-- Procedures Section -->
 @if ($treatment->procedures->count())
     <div class="bg-white rounded-xl shadow-lg overflow-hidden">
         <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b">
             <div class="flex justify-between items-center">
                 <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                     <i class="fas fa-teeth text-purple-600"></i>
                     Procedures ({{ $treatment->procedures->count() }})
                 </h3>
                 <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                     class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                     <i class="fas fa-plus"></i> Add Procedure
                 </a>
             </div>
         </div>
         <div class="overflow-x-auto">
             <table class="min-w-full divide-y divide-gray-200">
                 <thead class="bg-gray-50">
                     <tr>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Procedure
                         </th>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Tooth
                         </th>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Cost
                         </th>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Status
                         </th>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Date
                         </th>
                         <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                             Actions
                         </th>
                     </tr>
                 </thead>
                 <tbody class="bg-white divide-y divide-gray-200">
                     @foreach ($treatment->procedures as $procedure)
                         <tr class="hover:bg-gray-50 transition-colors">
                             <td class="px-4 py-3">
                                 <div class="font-medium text-gray-900">{{ $procedure->procedure_name }}</div>
                                 <div class="text-sm text-gray-500">{{ $procedure->procedure_code }}</div>
                                 @if ($procedure->notes)
                                     <div class="text-sm text-gray-500 mt-1">
                                         {{ Str::limit($procedure->notes, 50) }}
                                     </div>
                                 @endif
                             </td>
                             <td class="px-4 py-3">
                                 @if ($procedure->tooth_number)
                                     <span
                                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                         Tooth #{{ $procedure->tooth_number }}
                                     </span>
                                     @if ($procedure->surface)
                                         <div class="text-xs text-gray-500 mt-1">{{ $procedure->surface }}</div>
                                     @endif
                                 @else
                                     <span class="text-gray-400">-</span>
                                 @endif
                             </td>
                             <td class="px-4 py-3 font-medium">৳ {{ number_format($procedure->cost, 2) }}</td>
                             <td class="px-4 py-3">
                                 @php
                                     $statusColors = [
                                         'planned' => 'bg-yellow-100 text-yellow-800',
                                         'in_progress' => 'bg-blue-100 text-blue-800',
                                         'completed' => 'bg-green-100 text-green-800',
                                         'cancelled' => 'bg-red-100 text-red-800',
                                     ];
                                 @endphp
                                 <span
                                     class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$procedure->status] ?? 'bg-gray-100 text-gray-800' }}">
                                     {{ ucfirst(str_replace('_', ' ', $procedure->status)) }}
                                 </span>
                             </td>
                             <td class="px-4 py-3 text-sm text-gray-500">
                                 {{ $procedure->completed_at?->format('d/m/Y') ?? 'Pending' }}
                             </td>
                             <td class="px-4 py-3">
                                 <div class="flex items-center space-x-2">
                                     <!-- View Button -->
                                     <a href="{{ route('backend.treatment-procedures.show', $procedure) }}"
                                         class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors"
                                         title="View Details">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                 d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                 d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                         </svg>
                                     </a>

                                     <!-- Edit Button -->
                                     <a href="{{ route('backend.treatment-procedures.edit', $procedure) }}"
                                         class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded hover:bg-yellow-100 transition-colors"
                                         title="Edit">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                 d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                     </a>

                                     <!-- Status Action Buttons -->
                                     @if ($procedure->status == 'planned')
                                         <form action="{{ route('backend.treatment-procedures.start', $procedure) }}"
                                             method="POST" class="inline">
                                             @csrf
                                             <button type="submit"
                                                 class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                                                 title="Start Procedure">
                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round"
                                                         stroke-width="2"
                                                         d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                     <path stroke-linecap="round" stroke-linejoin="round"
                                                         stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                 </svg>
                                             </button>
                                         </form>
                                     @endif

                                     @if ($procedure->status == 'in_progress')
                                         <form
                                             action="{{ route('backend.treatment-procedures.complete', $procedure) }}"
                                             method="POST" class="inline">
                                             @csrf
                                             <button type="submit"
                                                 class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                                                 title="Mark Complete">
                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round"
                                                         stroke-width="2" d="M5 13l4 4L19 7" />
                                                 </svg>
                                             </button>
                                         </form>
                                     @endif
                                 </div>
                             </td>
                         </tr>
                     @endforeach
                 </tbody>
                 @if ($treatment->procedures->sum('cost') > 0)
                     <tfoot class="bg-gray-50">
                         <tr>
                             <td colspan="3" class="px-4 py-3 text-right font-semibold">Total Procedures Cost:</td>
                             <td class="px-4 py-3 font-bold text-lg">৳
                                 {{ number_format($treatment->procedures->sum('cost'), 2) }}</td>
                             <td colspan="2"></td>
                         </tr>
                     </tfoot>
                 @endif
             </table>
         </div>
     </div>
 @else
     <!-- Empty State -->
     <div class="bg-white rounded-xl shadow-lg overflow-hidden">
         <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b">
             <div class="flex justify-between items-center">
                 <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                     <i class="fas fa-teeth text-purple-600"></i>
                     Procedures
                 </h3>
                 <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                     class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                     <i class="fas fa-plus"></i> Add Procedure
                 </a>
             </div>
         </div>
         <div class="p-12 text-center">
             <div class="mx-auto w-24 h-24 bg-purple-50 rounded-full flex items-center justify-center mb-4">
                 <i class="fas fa-teeth text-purple-400 text-3xl"></i>
             </div>
             <h3 class="text-lg font-medium text-gray-900 mb-2">No Procedures Yet</h3>
             <p class="text-gray-500 mb-6">Add procedures to track dental work for this treatment</p>
             <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                 class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                 <i class="fas fa-plus mr-2"></i>
                 Add Your First Procedure
             </a>
         </div>
     </div>
 @endif
