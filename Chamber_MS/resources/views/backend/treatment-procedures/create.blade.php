@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Procedure</h1>
                <p class="text-gray-600 mt-1">Add a dental procedure to treatment</p>
            </div>
            @if($treatment)
                <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg">
                    <span class="font-medium">Patient:</span> {{ $treatment->patient->name }}
                    <span class="mx-2">|</span>
                    <span class="font-medium">Treatment #{{ $treatment->id }}</span>
                </div>
            @endif
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.treatment-procedures.store') }}" method="POST">
                @csrf

                <!-- Hidden treatment_id if preselected -->
                @if($treatment)
                    <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                @endif

                <div class="p-6 space-y-6">

                    <!-- Treatment Selection -->
                    @if(!$treatment)
                        <div>
                            <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Treatment *
                            </label>
                            <select id="treatment_id" name="treatment_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Treatment</option>
                                @foreach($treatments as $t)
                                    <option value="{{ $t->id }}" {{ old('treatment_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->patient->name ?? 'N/A' }} - Treatment #{{ $t->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('treatment_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Procedure Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Procedure Search & Quick Select -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Procedure</label>
                                <div class="space-y-2">

                                    <!-- Search Catalog -->
                                    <div class="relative">
                                        <input type="text" id="procedure-search"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Search procedure catalog...">
                                        <div id="catalog-results"
                                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                        </div>
                                    </div>

                                    <!-- Quick Select from Common Procedures -->
                                    @if(!empty($commonProcedures))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select:</label>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach($commonProcedures as $common)
                                                    <button type="button"
                                                        class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150"
                                                        onclick="selectCommonProcedure('{{ $common['procedure_code'] }}', '{{ $common['procedure_name'] }}', {{ $common['cost'] }}, {{ $common['duration'] }})">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <span
                                                                    class="font-medium text-gray-900">{{ $common['procedure_code'] }}</span>
                                                                <span
                                                                    class="text-gray-600 ml-2">{{ $common['procedure_name'] }}</span>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="font-medium">${{ number_format($common['cost'], 2) }}
                                                                </div>
                                                                <div class="text-sm text-gray-500">{{ $common['duration'] }} min
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Manual Entry -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Procedure Details</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="procedure_code" class="block text-sm font-medium text-gray-700 mb-1">
                                            Procedure Code *
                                        </label>
                                        <input type="text" id="procedure_code" name="procedure_code" required
                                            value="{{ old('procedure_code') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('procedure_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="procedure_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Procedure Name *
                                        </label>
                                        <input type="text" id="procedure_name" name="procedure_name" required
                                            value="{{ old('procedure_name') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('procedure_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">

                            <!-- Dental Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Dental Details</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="tooth_number" class="block text-sm font-medium text-gray-700 mb-1">Tooth
                                            Number</label>
                                        <input type="text" id="tooth_number" name="tooth_number"
                                            value="{{ old('tooth_number') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., 14">
                                        @error('tooth_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="surface"
                                            class="block text-sm font-medium text-gray-700 mb-1">Surface</label>
                                        <select id="surface" name="surface"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Surface</option>
                                            @foreach(['Occlusal', 'Mesial', 'Distal', 'Buccal', 'Lingual', 'Multiple'] as $surface)
                                                <option value="{{ $surface }}" {{ old('surface') == $surface ? 'selected' : '' }}>
                                                    {{ $surface }}</option>
                                            @endforeach
                                        </select>
                                        @error('surface')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial & Time -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial & Time</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">Cost ($)
                                            *</label>
                                        <input type="number" id="cost" name="cost" required step="0.01" min="0"
                                            value="{{ old('cost') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('cost')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration
                                            (min) *</label>
                                        <input type="number" id="duration" name="duration" required min="1" max="480"
                                            value="{{ old('duration') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('duration')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select id="status" name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(\App\Models\TreatmentProcedure::statuses() as $key => $val)
                                        <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end items-center px-6 py-4 bg-gray-50 border-t border-gray-200 space-x-3">
                    <a href="{{ route('backend.treatment-procedures.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        Add Procedure
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function selectCommonProcedure(code, name, cost, duration) {
                document.getElementById('procedure_code').value = code;
                document.getElementById('procedure_name').value = name;
                document.getElementById('cost').value = cost;
                document.getElementById('duration').value = duration;
            }

            // Catalog search functionality
            document.getElementById('procedure-search').addEventListener('input', function (e) {
                const search = e.target.value;
                const resultsDiv = document.getElementById('catalog-results');

                if (search.length < 2) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                fetch(`{{ route('backend.treatment-procedures.get-catalog-procedures') }}?search=${encodeURIComponent(search)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsDiv.innerHTML = '';
                        if (!data.length) {
                            resultsDiv.innerHTML = '<div class="p-4 text-gray-500 text-center">No procedures found</div>';
                            resultsDiv.classList.remove('hidden');
                            return;
                        }

                        data.forEach(proc => {
                            const div = document.createElement('div');
                            div.className = 'p-3 border-b border-gray-200 hover:bg-blue-50 cursor-pointer last:border-b-0';
                            div.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium">${proc.code} - ${proc.name}</div>
                                    <div class="text-sm text-gray-600">${proc.category}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">${proc.cost}</div>
                                    <div class="text-sm text-gray-600">${proc.duration} min</div>
                                </div>
                            </div>
                        `;
                            div.addEventListener('click', () => {
                                document.getElementById('procedure_code').value = proc.code;
                                document.getElementById('procedure_name').value = proc.name;
                                document.getElementById('cost').value = proc.cost;
                                document.getElementById('duration').value = proc.duration;
                                resultsDiv.classList.add('hidden');
                                e.target.value = '';
                            });
                            resultsDiv.appendChild(div);
                        });

                        resultsDiv.classList.remove('hidden');
                    });
            });

            // Close results when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#catalog-results') && !e.target.closest('#procedure-search')) {
                    document.getElementById('catalog-results').classList.add('hidden');
                }
            });
        </script>
    @endpush
@endsection