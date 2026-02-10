<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

            <!-- Logo / Brand -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto" src="{{ asset('assets/Website_Logo.png') }}" alt="My App Logo">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Account Deactivated
                </h2>
            </div>

            <!-- Deactivation Message Card -->
            <div class="mt-8 bg-white shadow-lg rounded-lg p-8 space-y-6 border border-red-200">
                <!-- Warning Icon -->
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.196 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>

                <!-- Deactivation Message -->
                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-center text-gray-900">
                        Account Access Restricted
                    </h3>

                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <p class="text-red-700 text-center font-medium">
                            Your account has been deactivated.
                        </p>
                    </div>

                    <div class="text-gray-600 text-center">
                        <p class="mb-2">
                            You are unable to access your account at this time.
                        </p>
                        <p class="font-medium">
                            Please contact your superior or administrator to reactivate your account.
                        </p>
                    </div>

                    <!-- Contact Information (Optional) -->
                    @if(config('app.support_email'))
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-500 text-center">
                                For assistance, please contact:
                            </p>
                            <p class="text-sm font-medium text-center text-gray-700 mt-1">
                                {{ config('app.support_email') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 space-y-4">
                    <a href="/"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Return to Homepage
                    </a>

                    <!-- Optional: Logout button if needed -->
                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            Sign out from all devices
                        </button>
                    </form>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="text-center text-sm text-gray-500">
                <p>
                    If you believe this is a mistake, please reach out to your system administrator immediately.
                </p>
            </div>

        </div>
    </div>

    <!-- Optional: Auto-redirect after some time -->
    @if(config('app.auto_redirect_time'))
        <script>
            setTimeout(function () {
                window.location.href = '/';
            }, {{ config('app.auto_redirect_time') }}); // e.g., 10000 = 10 seconds
        </script>
    @endif

</x-guest-layout>