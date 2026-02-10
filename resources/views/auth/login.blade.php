<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

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