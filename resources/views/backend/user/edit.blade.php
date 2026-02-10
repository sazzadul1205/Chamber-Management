@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit User: {{ $user->full_name }}</h1>
                <p class="text-gray-600 mt-1">
                    Update user information and account settings
                </p>
            </div>

            <a href="{{ route('backend.user.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Users
            </a>
        </div>

        <!-- VALIDATION ERRORS -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.user.update', $user->id) }}" method="POST" class="space-y-6" id="userEditForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- USER BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name *
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="100" placeholder="Enter full name">
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Phone Number *
                                    </label>
                                    <span class="text-xs text-gray-500">
                                        Format: +8801XXXXXXXXX
                                    </span>
                                </div>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="20" placeholder="e.g., +8801712345678">
                            </div>

                        </div>
                    </div>

                    <!-- ACCOUNT DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Email Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Address
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    maxlength="100" placeholder="user@example.com">
                                <p class="text-xs text-gray-500 mt-1">Optional - For notifications and login</p>
                            </div>

                            <!-- Blood Group -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Blood Group
                                </label>
                                <select name="blood_group"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ old('blood_group', $user->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group', $user->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group', $user->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group', $user->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_group', $user->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group', $user->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_group', $user->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group', $user->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Optional - Medical information</p>
                            </div>

                        </div>
                    </div>

                    <!-- ROLE & STATUS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Role & Status</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Role *
                                </label>
                                <select name="role_id" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select User Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Determines user permissions and access</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>
                                        Suspended
                                    </option>
                                </select>
                                <div class="mt-2 space-y-1 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span>Active: Can login and use system</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                        <span>Inactive: Cannot login temporarily</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span>Suspended: Account suspended</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ADMIN PASSWORD RESET (Only for Admin/Super Admin) -->
                    @if (Auth::user()->role_id <= 2 && Auth::id() != $user->id)
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Password Reset</h3>
                                @if($user->current_session_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Currently Logged In
                                </span>
                                @endif
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-blue-800 font-medium">
                                            You can reset this user's password (Admin privilege)
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            Leave blank to keep current password
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- New Password -->
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-sm font-medium text-gray-700">
                                                    New Password
                                                </label>
                                                <button type="button" id="generatePassword"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    Generate Random
                                                </button>
                                            </div>
                                            <div class="relative">
                                                <input type="password" name="password" id="passwordField"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                    minlength="6" placeholder="Enter new password">
                                                <button type="button" id="togglePassword"
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        id="eyeIcon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        id="eyeOffIcon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="mt-2 flex items-center gap-2">
                                                <div id="passwordStrength" class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div id="strengthBar" class="h-full w-0 transition-all duration-300"></div>
                                                </div>
                                                <span id="strengthText" class="text-xs font-medium text-gray-500">Weak</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters. Click "Generate Random" for a secure password.</p>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Confirm Password
                                            </label>
                                            <div class="relative">
                                                <input type="password" name="password_confirmation" id="confirmPasswordField"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                    minlength="6" placeholder="Confirm new password">
                                                <button type="button" id="toggleConfirmPassword"
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        id="confirmEyeIcon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        id="confirmEyeOffIcon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div id="passwordMatch" class="mt-2 hidden">
                                                <p class="text-xs font-medium text-green-600 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Passwords match
                                                </p>
                                            </div>
                                            <div id="passwordMismatch" class="mt-2 hidden">
                                                <p class="text-xs font-medium text-red-600 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Passwords do not match
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Strength Info -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Password Requirements</h4>
                                        <ul class="text-xs text-gray-600 space-y-1">
                                            <li class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Minimum 6 characters
                                            </li>
                                            <li class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Stronger passwords include mixed case
                                            </li>
                                            <li class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Include numbers and special characters
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- USER INFO & ACTIVITY -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Information & Activity</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- User Info Card -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Current User Info</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Current Role:</span>
                                        <span class="font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $user->getRoleName() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Blood Group:</span>
                                        <span class="font-medium">
                                            {!! $user->blood_group ? $user->blood_group_display : '<span class="text-gray-400">Not set</span>' !!}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span>{!! $user->status_badge !!}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Account Created:</span>
                                        <span class="font-medium">{{ $user->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Card -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Activity</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Last Login:</span>
                                        <span class="font-medium">
                                            @if($user->last_login_at)
                                                {{ $user->last_login_at->format('d M Y H:i') }}
                                                <span class="text-xs text-gray-500 block">({{ $user->last_login_human }})</span>
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Login Status:</span>
                                        <span class="font-medium">
                                            @if($user->is_online)
                                                <span class="inline-flex items-center text-green-600">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>
                                                    Online
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-gray-500">
                                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1.5"></span>
                                                    Offline
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    @if($user->last_login_device_id)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Last Device:</span>
                                        <span class="font-medium text-xs text-gray-600 truncate max-w-[150px]" title="{{ $user->last_login_device_id }}">
                                            {{ Str::limit($user->last_login_device_id, 30) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- FORM ACTIONS -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons 
                        back-url="{{ route('backend.user.index') }}"
                        submit-text="Update User" />
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phoneInput = document.getElementById('phone');
            const passwordField = document.getElementById('passwordField');
            const confirmPasswordField = document.getElementById('confirmPasswordField');
            const generatePasswordBtn = document.getElementById('generatePassword');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            const confirmEyeIcon = document.getElementById('confirmEyeIcon');
            const confirmEyeOffIcon = document.getElementById('confirmEyeOffIcon');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const passwordMatch = document.getElementById('passwordMatch');
            const passwordMismatch = document.getElementById('passwordMismatch');
            const form = document.getElementById('userEditForm');

            // Phone number formatting
            phoneInput?.addEventListener('input', () => {
                let phone = phoneInput.value.replace(/\D/g, '');
                
                // Format Bangladeshi phone numbers
                if (phone.startsWith('880')) {
                    phone = '+' + phone;
                } else if (phone.startsWith('01') && phone.length >= 10) {
                    phone = '+880' + phone.substring(1);
                } else if (phone.length >= 11) {
                    phone = '+' + phone;
                }
                
                phoneInput.value = phone;
            });

            // Format on page load if there's existing value
            if (phoneInput?.value) {
                phoneInput.dispatchEvent(new Event('input'));
            }

            // Generate random password (only if admin and password field exists)
            generatePasswordBtn?.addEventListener('click', () => {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
                let password = '';
                
                // Generate a strong password
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                passwordField.value = password;
                confirmPasswordField.value = password;
                
                // Show both passwords
                passwordField.type = 'text';
                confirmPasswordField.type = 'text';
                
                // Update icons
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
                confirmEyeIcon.classList.add('hidden');
                confirmEyeOffIcon.classList.remove('hidden');
                
                // Check password strength
                checkPasswordStrength(password);
                checkPasswordMatch();
                
                // Show success message
                showToast('Strong password generated! Both fields have been filled.', 'success');
            });

            // Toggle password visibility
            togglePasswordBtn?.addEventListener('click', () => {
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.classList.add('hidden');
                    eyeOffIcon.classList.remove('hidden');
                } else {
                    passwordField.type = 'password';
                    eyeIcon.classList.remove('hidden');
                    eyeOffIcon.classList.add('hidden');
                }
            });

            // Toggle confirm password visibility
            toggleConfirmPasswordBtn?.addEventListener('click', () => {
                if (confirmPasswordField.type === 'password') {
                    confirmPasswordField.type = 'text';
                    confirmEyeIcon.classList.add('hidden');
                    confirmEyeOffIcon.classList.remove('hidden');
                } else {
                    confirmPasswordField.type = 'password';
                    confirmEyeIcon.classList.remove('hidden');
                    confirmEyeOffIcon.classList.add('hidden');
                }
            });

            // Check password strength in real-time
            passwordField?.addEventListener('input', () => {
                checkPasswordStrength(passwordField.value);
                checkPasswordMatch();
            });

            // Check password match in real-time
            confirmPasswordField?.addEventListener('input', checkPasswordMatch);

            // Check password strength function
            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 6) strength++;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                const width = Math.min(strength * 20, 100);
                strengthBar.style.width = `${width}%`;
                
                let color, text;
                switch(strength) {
                    case 0:
                    case 1:
                        color = 'bg-red-500';
                        text = 'Very Weak';
                        break;
                    case 2:
                        color = 'bg-red-400';
                        text = 'Weak';
                        break;
                    case 3:
                        color = 'bg-yellow-500';
                        text = 'Fair';
                        break;
                    case 4:
                        color = 'bg-green-400';
                        text = 'Good';
                        break;
                    case 5:
                        color = 'bg-green-600';
                        text = 'Strong';
                        break;
                    default:
                        color = 'bg-gray-300';
                        text = 'Weak';
                }
                
                strengthBar.className = `h-full transition-all duration-300 ${color}`;
                strengthText.textContent = text;
                strengthText.className = `text-xs font-medium ${color.replace('bg-', 'text-')}`;
            }

            // Check if passwords match
            function checkPasswordMatch() {
                const password = passwordField.value;
                const confirmPassword = confirmPasswordField.value;

                if (!confirmPassword) {
                    passwordMatch.classList.add('hidden');
                    passwordMismatch.classList.add('hidden');
                    return;
                }

                if (password === confirmPassword) {
                    passwordMatch.classList.remove('hidden');
                    passwordMismatch.classList.add('hidden');
                } else {
                    passwordMatch.classList.add('hidden');
                    passwordMismatch.classList.remove('hidden');
                }
            }

            // Show toast notification
            function showToast(message, type = 'success') {
                // Remove existing toast
                const existingToast = document.getElementById('passwordToast');
                if (existingToast) {
                    existingToast.remove();
                }
                
                // Determine color based on type
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };
                
                // Create toast element
                const toast = document.createElement('div');
                toast.id = 'passwordToast';
                toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full z-50`;
                toast.textContent = message;
                
                // Add to DOM
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                    toast.classList.add('translate-x-0');
                }, 10);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }

            // Form validation
            form?.addEventListener('submit', function(e) {
                const phone = phoneInput?.value.trim();
                
                // Validate phone format for Bangladesh
                if (phone && !/^(\+88)?01[3-9]\d{8}$/.test(phone.replace('+', ''))) {
                    e.preventDefault();
                    showToast('Please enter a valid Bangladeshi phone number (e.g., +8801712345678)', 'error');
                    phoneInput.focus();
                    return;
                }

                // Validate password if provided
                const password = passwordField?.value;
                if (password && password.length < 6) {
                    e.preventDefault();
                    showToast('Password must be at least 6 characters long.', 'error');
                    passwordField.focus();
                    return;
                }

                // Check if passwords match when provided
                const confirmPassword = confirmPasswordField?.value;
                if (password && password !== confirmPassword) {
                    e.preventDefault();
                    showToast('Passwords do not match. Please check and try again.', 'error');
                    confirmPasswordField.focus();
                    return;
                }

                // Check password strength warning (but allow submission)
                if (password) {
                    const passwordStrength = strengthText.textContent.toLowerCase();
                    if (passwordStrength === 'very weak' || passwordStrength === 'weak') {
                        if (!confirm('Your password is weak. Are you sure you want to update the user with this password?')) {
                            e.preventDefault();
                            return;
                        }
                    }
                }
            });

            // Initial checks
            if (passwordField?.value) {
                checkPasswordStrength(passwordField.value);
                checkPasswordMatch();
            }
        });
    </script>
@endsection