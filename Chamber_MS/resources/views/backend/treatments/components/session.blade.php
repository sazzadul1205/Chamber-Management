<!-- Sessions Section -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clock text-orange-600"></i>
                Treatment Sessions
                <span class="text-sm font-normal text-gray-600">
                    ({{ $treatment->sessions->count() }}/{{ $treatment->estimated_sessions }})
                </span>
            </h3>
            <div class="flex gap-2">
                @if ($treatment->canAddSession())
                    <a href="{{ route('backend.treatments.sessions.create', $treatment) }}"
                        class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                        <i class="fas fa-plus"></i> Add Session
                    </a>
                @endif
                <a href="{{ route('backend.treatments.sessions.index', $treatment) }}"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                    <i class="fas fa-list"></i> Manage Sessions
                </a>
            </div>
        </div>
    </div>

    @if ($treatment->sessions->count())
        <div class="overflow-x-auto">
            <!-- In your session component file -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Session #
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Cost
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Payment Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($sessionCosts as $session)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold">
                                    {{ $session['session_number'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($session['scheduled_date'])->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $session['title'] }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-green-700">
                                    ৳ {{ number_format($session['cost'], 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $paymentStatus =
                                        $session['balance'] == 0
                                            ? 'paid'
                                            : ($session['paid'] > 0
                                                ? 'partial'
                                                : 'unpaid');
                                    $statusColor = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'unpaid' => 'bg-red-100 text-red-800',
                                    ][$paymentStatus];
                                    $statusText = [
                                        'paid' => 'Paid',
                                        'partial' => 'Partial',
                                        'unpaid' => 'Unpaid',
                                    ][$paymentStatus];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                                @if ($session['paid'] > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Paid: ৳ {{ number_format($session['paid'], 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <!-- View Session Details -->
                                    <a href="{{ route('backend.treatment-sessions.show', $session['id'] ?? null) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-8 text-center">
            <div class="mx-auto w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clock text-orange-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Sessions Yet</h3>
            <p class="text-gray-500 mb-4">Add sessions to schedule dental appointments for this treatment
            </p>
            @if ($treatment->canAddSession())
                <a href="{{ route('backend.treatments.sessions.create', $treatment) }}"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create First Session
                </a>
            @endif
        </div>
    @endif
</div>
