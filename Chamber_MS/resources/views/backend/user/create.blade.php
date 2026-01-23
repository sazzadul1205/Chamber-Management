@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Create New User</h2>
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
        <form action="{{ route('backend.user.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('full_name') border-red-500 @enderror"
                        required maxlength="100">
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role_id" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('role_id') border-red-500 @enderror">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('phone') border-red-500 @enderror"
                        required maxlength="20">
                    <p class="text-gray-500 text-xs mt-1">Format: +8801XXXXXXXXX</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror"
                        maxlength="100">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror"
                        required minlength="6">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required minlength="6">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

            </div>

            <!-- Submit Buttons -->
            <x-back-submit-buttons back-url="{{ route('backend.user.index') }}" submit-text="Create User" />

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phoneInput = document.getElementById('phone');
            phoneInput?.addEventListener('input', () => {
                let phone = phoneInput.value.replace(/\D/g, '');
                if (phone.startsWith('880')) {
                    phone = '+' + phone;
                } else if (phone.startsWith('01') && phone.length >= 10) {
                    phone = '+880' + phone.substring(1);
                } else if (phone.length >= 11) {
                    phone = '+' + phone;
                }
                phoneInput.value = phone;
            });
        });
    </script>
@endsection
