<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8 relative">
        <!-- Top Bar with Navigation Buttons - Stack on mobile -->
        <div class="absolute top-4 left-4 right-4 flex flex-col sm:flex-row justify-between items-center gap-2">
            <!-- Back to Home Button -->
            <a href="{{ url('/') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md group">
                <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>

            <!-- Go to Page Builder Button -->
            <a href="{{ url('/page-builder') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                    </path>
                </svg>
                Go to Page Builder
            </a>
        </div>

        <div class="max-w-md w-full space-y-8 mt-24 sm:mt-16">
            <!-- Logo / Brand -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto" src="{{ asset('assets/Website_Logo.png') }}" alt="My App Logo">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Sign in to your account
                </h2>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form Card -->
            <form method="POST" action="{{ route('login') }}"
                class="mt-8 bg-white shadow-lg rounded-lg p-8 space-y-6 border border-gray-200">
                @csrf

                <!-- Email -->
                <div class="space-y-1">
                    <x-input-label for="email" :value="__('Email')" class="font-medium text-gray-700" />
                    <x-text-input id="email"
                        class="block mt-1 w-full border border-gray-300 bg-white text-gray-900 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />

                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <x-input-label for="password" :value="__('Password')" class="font-medium text-gray-700" />
                    <x-text-input id="password"
                        class="block mt-1 w-full border border-gray-300 bg-white text-gray-900 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center text-gray-600">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            name="remember">
                        <span class="ml-2 text-sm">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <x-primary-button class="w-full py-3 text-lg font-semibold">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Quick Login Boxes -->
            <div class="mt-8">
                <p class="text-center text-sm text-gray-600 mb-4 font-medium">
                    Quick Login (Testing Only)
                </p>

                <div class="grid grid-cols-1 gap-3">
                    <button type="button" onclick="quickLogin('Reception@gmail.com')"
                        class="w-full py-2 rounded-md bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-semibold transition">
                        Login as Receptionist
                    </button>

                    <button type="button" onclick="quickLogin('Doctor@gmail.com')"
                        class="w-full py-2 rounded-md bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 font-semibold transition">
                        Login as Doctor
                    </button>

                    <button type="button" onclick="quickLogin('admin@gmail.com')"
                        class="w-full py-2 rounded-md bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 font-semibold transition">
                        Login as Admin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function quickLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'Admin1205';
        }
    </script>

</x-guest-layout>