@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add New Doctor</h2>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('backend.doctors.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Select User -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select User *</label>
                    <select name="user_id"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('user_id') border-red-500 @enderror"
                        required>
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->full_name }} ({{ $user->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor Code *</label>
                    <div class="flex gap-2">
                        <input type="text" name="doctor_code" id="doctor_code" value="{{ old('doctor_code') }}"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('doctor_code') border-red-500 @enderror"
                            required maxlength="20">
                        <button type="button" id="generateCode"
                            class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded flex items-center gap-1 text-sm">
                            <i class="fas fa-sync"></i> Generate
                        </button>
                    </div>
                </div>

                <!-- Specialization -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('specialization') border-red-500 @enderror">
                </div>

                <!-- Qualification -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('qualification') border-red-500 @enderror">
                </div>

                <!-- Consultation Fee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Fee (BDT) *</label>
                    <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('consultation_fee') border-red-500 @enderror"
                        required>
                </div>

                <!-- Commission -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commission (%) *</label>
                    <input type="number" step="0.01" name="commission_percent"
                        value="{{ old('commission_percent', 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('commission_percent') border-red-500 @enderror"
                        required min="0" max="100">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror"
                        required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.doctors.index') }}" submit-text="Save Doctor" />

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('generateCode')?.addEventListener('click', () => {
                fetch("{{ route('backend.doctors.generate-code') }}")
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('doctor_code').value = data.code;
                    })
                    .catch(() => alert('Error generating code'));
            });
        });
    </script>
@endsection
