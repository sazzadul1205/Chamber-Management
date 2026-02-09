@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
                <p class="text-gray-600 mt-1">
                    Add a new user account with appropriate role and permissions
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
                <form action="{{ route('backend.user.store') }}" method="POST" class="space-y-6" id="userForm">
                    @csrf

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
                                    <input type="text" name="full_name" value="{{ old('full_name') }}"
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
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
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
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        maxlength="100" placeholder="user@example.com">
                                    <p class="text-xs text-gray-500 mt-1">Optional - For notifications and login</p>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Role *
                                    </label>
                                    <select name="role_id" required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select User Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- SECURITY & STATUS -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Security & Status</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Password -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Password *
                                        </label>
                                        <button type="button" id="generatePassword"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Generate Random
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <input type="password" name="password" id="passwordField"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                            required minlength="6" placeholder="Enter password">
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

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Status *
                                    </label>
                                    <select name="status" required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
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

                    </div>

                  <!-- FORM ACTIONS -->
                    <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                        <x-back-submit-buttons back-url="{{ route('backend.user.index') }}"
                            submit-text="Make New User" />
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const phoneInput = document.getElementById('phone');
                const passwordField = document.getElementById('passwordField');
                const generatePasswordBtn = document.getElementById('generatePassword');
                const togglePasswordBtn = document.getElementById('togglePassword');
                const eyeIcon = document.getElementById('eyeIcon');
                const eyeOffIcon = document.getElementById('eyeOffIcon');
                const strengthBar = document.getElementById('strengthBar');
                const strengthText = document.getElementById('strengthText');
                const form = document.getElementById('userForm');

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

                // Generate random password
                generatePasswordBtn?.addEventListener('click', () => {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
                    let password = '';

                    // Ensure password has at least 8 characters
                    for (let i = 0; i < 10; i++) {
                        password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }

                    passwordField.value = password;
                    passwordField.type = 'text'; // Show the generated password

                    // Update icons
                    eyeIcon.classList.add('hidden');
                    eyeOffIcon.classList.remove('hidden');

                    // Check password strength
                    checkPasswordStrength(password);

                    // Show success message
                    showToast('Password generated successfully!');
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

                // Check password strength in real-time
                passwordField?.addEventListener('input', () => {
                    checkPasswordStrength(passwordField.value);
                });

                // Check password strength function
                function checkPasswordStrength(password) {
                    let strength = 0;

                    if (password.length >= 6) strength++;
                    if (password.length >= 8) strength++;
                    if (/[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^A-Za-z0-9]/.test(password)) strength++;

                    const width = strength * 20;
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

                    strengthBar.className = `h-full w-${width} transition-all duration-300 ${color}`;
                    strengthText.textContent = text;
                    strengthText.className = `text-xs font-medium ${color.replace('bg-', 'text-')}`;
                }

                // Show toast notification
                function showToast(message) {
                    // Remove existing toast
                    const existingToast = document.getElementById('passwordToast');
                    if (existingToast) {
                        existingToast.remove();
                    }

                    // Create toast element
                    const toast = document.createElement('div');
                    toast.id = 'passwordToast';
                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full';
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
                        alert('Please enter a valid Bangladeshi phone number (e.g., +8801712345678)');
                        phoneInput.focus();
                        return;
                    }

                    // Validate password length
                    const password = passwordField?.value;
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('Password must be at least 6 characters long.');
                        passwordField.focus();
                    }
                });

                // Initial password strength check
                if (passwordField?.value) {
                    checkPasswordStrength(passwordField.value);
                }
            });
        </script>
@endsection