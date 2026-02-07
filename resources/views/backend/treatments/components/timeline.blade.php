    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-gray-600"></i>
                Treatment Timeline
            </h3>
        </div>
        <div class="p-4">
            <div class="relative pl-6 space-y-6">
                <!-- Created -->
                <div class="relative">
                    <div class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow">
                    </div>
                    <div class="ml-2">
                        <h6 class="font-semibold text-gray-800">Treatment Created</h6>
                        <p class="text-sm text-gray-500">{{ $treatment->created_at->format('d F, Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-400">By: {{ $treatment->creator->name ?? 'System' }}</p>
                    </div>
                </div>

                <!-- Started -->
                @if ($treatment->start_date)
                    <div class="relative">
                        <div
                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-green-600 border-4 border-white shadow">
                        </div>
                        <div class="ml-2">
                            <h6 class="font-semibold text-gray-800">Treatment Started</h6>
                            <p class="text-sm text-gray-500">{{ $treatment->start_date->format('d F, Y') }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Expected End -->
                @if ($treatment->expected_end_date)
                    <div class="relative">
                        <div
                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-yellow-500 border-4 border-white shadow">
                        </div>
                        <div class="ml-2">
                            <h6 class="font-semibold text-gray-800">Expected Completion</h6>
                            <p class="text-sm text-gray-500">
                                {{ $treatment->expected_end_date->format('d F, Y') }}
                            </p>
                            @if ($treatment->expected_end_date->isPast() && $treatment->status != 'completed')
                                <span
                                    class="inline-block mt-1 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                    Overdue
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actual End -->
                @if ($treatment->actual_end_date)
                    <div class="relative">
                        <div
                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-purple-600 border-4 border-white shadow">
                        </div>
                        <div class="ml-2">
                            <h6 class="font-semibold text-gray-800">Treatment Completed</h6>
                            <p class="text-sm text-gray-500">
                                {{ $treatment->actual_end_date->format('d F, Y') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Updated -->
                @if ($treatment->updated_at != $treatment->created_at)
                    <div class="relative">
                        <div
                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-gray-400 border-4 border-white shadow">
                        </div>
                        <div class="ml-2">
                            <h6 class="font-semibold text-gray-800">Last Updated</h6>
                            <p class="text-sm text-gray-500">
                                {{ $treatment->updated_at->format('d F, Y H:i') }}</p>
                            <p class="text-xs text-gray-400">By: {{ $treatment->updater->name ?? 'System' }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
