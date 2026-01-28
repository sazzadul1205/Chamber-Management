@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- ==============================================
                                                HEADER SECTION
                                                Shows different titles based on context
                                            ============================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($treatment)
                        Add Procedure to {{ $treatment->treatment_code }}
                    @else
                        Add New Procedure
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">
                    @if($treatment)
                        Add a dental procedure to {{ $treatment->patient->full_name ?? 'patient' }}'s treatment
                    @else
                        Add a dental procedure to treatment
                    @endif
                </p>
            </div>
            @if($treatment)
                <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                    <span class="font-medium">Patient:</span> {{ $treatment->patient->full_name ?? 'N/A' }}
                    <span class="mx-2">|</span>
                    <span class="font-medium">Treatment:</span> {{ $treatment->treatment_code }}
                </div>
            @endif
        </div>

        <!-- ==============================================
                                                FORM CARD
                                                Main form for adding procedures
                                            ============================================== -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.treatment-procedures.store') }}" method="POST">
                @csrf

                <!-- Hidden treatment_id for pre-selected treatment -->
                @if($treatment)
                    <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                @endif

                <div class="p-6 space-y-6">
                    <!-- ==============================================
                                                            TREATMENT SELECTION SECTION
                                                            Three scenarios:
                                                            1. From treatment page: Show treatment info box
                                                            2. General page with multiple treatments: Show dropdown
                                                            3. General page with only one treatment: Show dropdown
                                                        ============================================== -->

                    <!-- SCENARIO 1: From treatment page - show info box -->
                    @if($treatment)
                        <div>

                        </div>

                        <!-- SCENARIO 2: General page with multiple treatments - show dropdown -->
                    @elseif($treatments->count() > 1)
                        <div>
                            <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Treatment *
                            </label>
                            <select id="treatment_id" name="treatment_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Treatment</option>
                                @foreach($treatments as $t)
                                    <option value="{{ $t->id }}" {{ old('treatment_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->patient->full_name ?? 'N/A' }} - {{ $t->treatment_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('treatment_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SCENARIO 3: General page with only one treatment - show dropdown -->
                    @else
                        <div>
                            <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Treatment *
                            </label>
                            <select id="treatment_id" name="treatment_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Treatment</option>
                                @foreach($treatments as $t)
                                    <option value="{{ $t->id }}" {{ old('treatment_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->patient->full_name ?? 'N/A' }} - {{ $t->treatment_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('treatment_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- ==============================================
                                                            PROCEDURE DETAILS SECTION
                                                            Two-column layout for form fields
                                                        ============================================== -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT COLUMN: Procedure selection and basic details -->
                        <div class="space-y-6">
                            <!-- PROCEDURE SEARCH & QUICK SELECT -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Procedure</label>
                                <div class="space-y-2">
                                    <!-- Search catalog -->
                                    <div class="flex gap-2">
                                        <div class="flex-1 relative">
                                            <input type="text" id="procedure-search"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Search by code or name...">
                                            <div id="catalog-results"
                                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                            </div>
                                        </div>
                                        <button type="button" id="search-btn"
                                            class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                                            Search
                                        </button>
                                    </div>
                                    <div id="search-error" class="mt-1 text-sm text-red-600 hidden"></div>

                                    <!-- Quick select from common procedures -->
                                    @if(!empty($commonProcedures))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select:</label>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach($commonProcedures as $common)
                                                    <button type="button"
                                                        class="text-left p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors duration-150 procedure-quick-select"
                                                        data-procedure-code="{{ $common['procedure_code'] }}"
                                                        data-procedure-name="{{ $common['procedure_name'] }}"
                                                        data-cost="{{ $common['cost'] }}" data-duration="{{ $common['duration'] }}">
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

                            <!-- MANUAL ENTRY -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Procedure Details</h3>
                                <div class="space-y-4">
                                    <!-- Procedure code -->
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

                                    <!-- Procedure name -->
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

                        <!-- RIGHT COLUMN: Dental details, financial info, and notes -->
                        <div class="space-y-6">
                            <!-- DENTAL DETAILS -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Dental Details</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Tooth number -->
                                    <div>
                                        <label for="tooth_number" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tooth Number
                                        </label>
                                        <input type="text" id="tooth_number" name="tooth_number"
                                            value="{{ old('tooth_number') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., 14">
                                        @error('tooth_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Surface -->
                                    <div>
                                        <label for="surface" class="block text-sm font-medium text-gray-700 mb-1">
                                            Surface
                                        </label>
                                        <select id="surface" name="surface"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Surface</option>
                                            @foreach(['Occlusal', 'Mesial', 'Distal', 'Buccal', 'Lingual', 'Multiple'] as $surface)
                                                <option value="{{ $surface }}" {{ old('surface') == $surface ? 'selected' : '' }}>
                                                    {{ $surface }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('surface')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL & TIME -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial & Time</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Cost -->
                                    <div>
                                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cost ($) *
                                        </label>
                                        <input type="number" id="cost" name="cost" required step="0.01" min="0"
                                            value="{{ old('cost') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('cost')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Duration -->
                                    <div>
                                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                                            Duration (min) *
                                        </label>
                                        <input type="number" id="duration" name="duration" required min="1" max="480"
                                            value="{{ old('duration') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('duration')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- STATUS -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select id="status" name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(\App\Models\TreatmentProcedure::statuses() as $key => $val)
                                        <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NOTES -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==============================================
                FORM ACTIONS
                Submit and cancel buttons
            ============================================== -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200 space-x-3">
                    @if($treatment)
                        <!-- If coming from treatment page, go back to treatment show page -->
                        <x-back-submit-buttons back-url="{{ route('backend.treatments.show', $treatment) }}"
                            submit-text="Add Procedure" />
                    @else
                        <!-- If coming from general procedures page, go back to procedures index -->
                        <x-back-submit-buttons back-url="{{ route('backend.treatment-procedures.index') }}"
                            submit-text="Add Procedure" />
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
        JAVASCRIPT SECTION
        Handles procedure selection, search, and form interactions
    ============================================== -->
    <script>
        // ==============================================
        // GLOBAL VARIABLES
        // ==============================================
        let currentSelectedProcedure = null;

        // ==============================================
        // QUICK SELECT FUNCTIONALITY
        // ==============================================
        document.querySelectorAll('.procedure-quick-select').forEach(button => {
            button.addEventListener('click', function () {
                const code = this.getAttribute('data-procedure-code');
                const name = this.getAttribute('data-procedure-name');
                const cost = this.getAttribute('data-cost');
                const duration = this.getAttribute('data-duration');

                // Store the current selection
                currentSelectedProcedure = { code, name, cost, duration };

                // Fill all fields (overwrite existing values)
                fillProcedureFields(code, name, cost, duration);

                // Visual feedback
                this.classList.add('bg-blue-100', 'border-blue-300');
                setTimeout(() => {
                    this.classList.remove('bg-blue-100', 'border-blue-300');
                }, 500);
            });
        });

        // ==============================================
        // SEARCH FUNCTIONALITY
        // ==============================================
        // Setup event listeners
        document.getElementById('search-btn').addEventListener('click', performSearch);
        document.getElementById('procedure-search').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Load popular procedures when search input is focused
        document.getElementById('procedure-search').addEventListener('focus', function () {
            const resultsDiv = document.getElementById('catalog-results');
            const searchInput = document.getElementById('procedure-search');

            // Only load popular if input is empty and results aren't already showing
            if (!searchInput.value.trim() && resultsDiv.classList.contains('hidden')) {
                loadPopularProcedures();
            }
        });

        // ==============================================
        // SEARCH FUNCTIONS
        // ==============================================
        /**
         * Load popular procedures (shows when search input is empty)
         */
        function loadPopularProcedures() {
            const resultsDiv = document.getElementById('catalog-results');
            const errorDiv = document.getElementById('search-error');

            // Clear previous error
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';

            // Show loading state
            showLoading(resultsDiv);

            // Fetch popular procedures
            fetch(`{{ route('backend.treatment-procedures.get-catalog-procedures') }}?search=`)
                .then(handleResponse)
                .then(data => {
                    if (!data || !data.length) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }
                    displayPopularResults(data, resultsDiv);
                })
                .catch(error => {
                    console.error('Error loading popular procedures:', error);
                    resultsDiv.classList.add('hidden');
                });
        }

        /**
         * Perform search based on user input
         */
        function performSearch() {
            const searchInput = document.getElementById('procedure-search');
            const search = searchInput.value.trim();
            const resultsDiv = document.getElementById('catalog-results');
            const errorDiv = document.getElementById('search-error');

            // Clear previous results and error
            resultsDiv.innerHTML = '';
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';

            // If empty search, show popular procedures
            if (!search) {
                loadPopularProcedures();
                return;
            }

            // Validate search input
            if (search.length < 2) {
                showError('Please enter at least 2 characters');
                return;
            }

            // Show loading state
            showLoading(resultsDiv);

            // Perform search
            fetch(`{{ route('backend.treatment-procedures.get-catalog-procedures') }}?search=${encodeURIComponent(search)}`)
                .then(handleResponse)
                .then(data => {
                    if (!data || !data.length) {
                        showNoResults(resultsDiv, search);
                        return;
                    }
                    displaySearchResults(data, resultsDiv, search);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showSearchError(resultsDiv);
                });

            // Helper function for showing errors
            function showError(message) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }
        }

        /**
         * Handle API response
         */
        function handleResponse(response) {
            if (!response.ok) {
                throw new Error('Search failed');
            }
            return response.json();
        }

        // ==============================================
        // UI HELPER FUNCTIONS
        // ==============================================
        function showLoading(container) {
            container.innerHTML = `
                                                    <div class="p-4 text-center">
                                                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                                        <p class="mt-2 text-gray-500">Searching...</p>
                                                    </div>
                                                `;
            container.classList.remove('hidden');
        }

        function showNoResults(container, searchTerm) {
            container.innerHTML = `
                                                    <div class="p-4 text-center">
                                                        <div class="text-gray-500 mb-2">No procedures found for "${searchTerm}"</div>
                                                        <div class="text-sm text-gray-400">Try searching with different keywords</div>
                                                    </div>
                                                `;
            container.classList.remove('hidden');
        }

        function showSearchError(container) {
            container.innerHTML = '<div class="p-4 text-red-500 text-center">Error searching. Please try again.</div>';
            container.classList.remove('hidden');
        }

        function displayPopularResults(data, container) {
            container.innerHTML = '';

            // Add header for popular procedures
            const header = document.createElement('div');
            header.className = 'p-3 bg-gray-50 border-b border-gray-200';
            header.innerHTML = `
                                                    <div class="text-sm font-medium text-gray-700">Popular Procedures</div>
                                                    ${currentSelectedProcedure ?
                    `<div class="text-xs text-gray-500 mt-1">Currently selected: ${currentSelectedProcedure.code} - ${currentSelectedProcedure.name}</div>` :
                    ''
                }
                                                `;
            container.appendChild(header);

            if (!data.length) {
                container.innerHTML += '<div class="p-4 text-gray-500 text-center">No popular procedures found</div>';
            } else {
                data.forEach(proc => {
                    createResultItem(proc, container, true);
                });
            }

            container.classList.remove('hidden');
        }

        function displaySearchResults(data, container, searchTerm) {
            container.innerHTML = '';

            // Add header with search info
            const header = document.createElement('div');
            header.className = 'p-3 bg-gray-50 border-b border-gray-200';
            header.innerHTML = `
                                                    <div class="text-sm font-medium text-gray-700">${data.length} result${data.length !== 1 ? 's' : ''} for "${searchTerm}"</div>
                                                    ${currentSelectedProcedure ?
                    `<div class="text-xs text-gray-500 mt-1">Click to replace: ${currentSelectedProcedure.code} - ${currentSelectedProcedure.name}</div>` :
                    ''
                }
                                                `;
            container.appendChild(header);

            data.forEach(proc => {
                createResultItem(proc, container, false);
            });

            container.classList.remove('hidden');
        }

        /**
         * Create a search result item
         */
        function createResultItem(proc, container, isPopular) {
            const div = document.createElement('div');
            div.className = 'p-3 border-b border-gray-200 hover:bg-blue-50 cursor-pointer last:border-b-0 procedure-search-result';
            div.setAttribute('data-procedure-code', proc.code);
            div.setAttribute('data-procedure-name', proc.name);
            div.setAttribute('data-cost', proc.cost);
            div.setAttribute('data-duration', proc.duration);

            // Highlight if this is the currently selected procedure
            const isSelected = currentSelectedProcedure &&
                currentSelectedProcedure.code === proc.code;

            div.innerHTML = `
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <div class="font-medium ${isSelected ? 'text-green-600' : 'text-blue-600'}">
                                                                ${proc.code}
                                                                ${isSelected ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded ml-2">Selected</span>' : ''}
                                                            </div>
                                                            <div class="text-gray-900">${proc.name}</div>
                                                            <div class="text-sm text-gray-600">${proc.category || ''}</div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-medium">$${proc.cost}</div>
                                                            <div class="text-sm text-gray-600">${proc.duration} min</div>
                                                        </div>
                                                    </div>
                                                `;
            container.appendChild(div);
        }

        // ==============================================
        // RESULT SELECTION HANDLING
        // ==============================================
        document.addEventListener('click', function (e) {
            // Search results selection
            if (e.target.closest('.procedure-search-result')) {
                const div = e.target.closest('.procedure-search-result');
                const code = div.getAttribute('data-procedure-code');
                const name = div.getAttribute('data-procedure-name');
                const cost = div.getAttribute('data-cost');
                const duration = div.getAttribute('data-duration');

                // Store the current selection
                currentSelectedProcedure = { code, name, cost, duration };

                // Fill all fields (overwrite existing values)
                fillProcedureFields(code, name, cost, duration);

                // Close results and clear search
                document.getElementById('catalog-results').classList.add('hidden');
                document.getElementById('procedure-search').value = '';

                // Show confirmation feedback
                showSelectionFeedback();
            }
        });

        // ==============================================
        // FORM FIELD MANIPULATION FUNCTIONS
        // ==============================================
        /**
         * Fill procedure fields (overwrites existing values)
         */
        function fillProcedureFields(code, name, cost, duration) {
            const codeField = document.getElementById('procedure_code');
            const nameField = document.getElementById('procedure_name');
            const costField = document.getElementById('cost');
            const durationField = document.getElementById('duration');

            // Fill all fields
            codeField.value = code;
            nameField.value = name;
            costField.value = cost;
            durationField.value = duration;

            // Trigger change events
            [codeField, nameField, costField, durationField].forEach(field => {
                field.dispatchEvent(new Event('change'));
            });
        }

        /**
         * Show visual feedback when a procedure is selected
         */
        function showSelectionFeedback() {
            const codeField = document.getElementById('procedure_code');
            const nameField = document.getElementById('procedure_name');

            // Highlight the filled fields temporarily
            [codeField, nameField].forEach(field => {
                field.classList.add('bg-green-50', 'border-green-300');
                setTimeout(() => {
                    field.classList.remove('bg-green-50', 'border-green-300');
                }, 1500);
            });

            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50';
            toast.innerHTML = `
                                                    <div class="flex items-center">
                                                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Procedure "${currentSelectedProcedure.code}" selected</span>
                                                    </div>
                                                `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        /**
         * Clear selection button functionality
         */
        function addClearSelectionButton() {
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'mt-2 text-sm text-red-600 hover:text-red-800 flex items-center gap-1';
            clearBtn.innerHTML = `
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Clear Selection
                                                `;
            clearBtn.addEventListener('click', clearProcedureSelection);

            // Insert after manual entry section
            const manualEntrySection = document.querySelector('.border-t.pt-6');
            if (manualEntrySection) {
                manualEntrySection.parentNode.insertBefore(clearBtn, manualEntrySection.nextSibling);
            }
        }

        function clearProcedureSelection() {
            currentSelectedProcedure = null;
            document.getElementById('procedure_code').value = '';
            document.getElementById('procedure_name').value = '';
            document.getElementById('cost').value = '';
            document.getElementById('duration').value = '';

            // Show feedback
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg shadow-lg z-50';
            toast.innerHTML = `
                                                    <div class="flex items-center">
                                                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Selection cleared</span>
                                                    </div>
                                                `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // ==============================================
        // UTILITY FUNCTIONS
        // ==============================================
        /**
         * Close results when clicking outside
         */
        document.addEventListener('click', function (e) {
            const resultsDiv = document.getElementById('catalog-results');
            const searchInput = document.getElementById('procedure-search');
            const searchBtn = document.getElementById('search-btn');

            if (!e.target.closest('#catalog-results') &&
                e.target !== searchInput &&
                e.target !== searchBtn &&
                !searchInput.contains(e.target) &&
                !searchBtn.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });

        // ==============================================
        // INITIALIZATION
        // ==============================================
        document.addEventListener('DOMContentLoaded', function () {
            addClearSelectionButton();
        });
    </script>
@endsection