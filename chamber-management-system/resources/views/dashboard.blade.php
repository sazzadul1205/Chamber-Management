<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                    <!-- Session Data -->
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                        <h3 class="font-bold mb-2">Session Data:</h3>
                        <p><strong>Role ID:</strong> {{ session('role_id') ?? 'Not set' }}</p>
                        <p><strong>User Role:</strong> {{ session('user_role') ?? 'Not set' }}</p>
                        <p><strong>Session ID:</strong> {{ session()->getId() }}</p>
                    </div>

                    <!-- Auth User Data -->
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                        <h3 class="font-bold mb-2">Auth User Data:</h3>
                        @auth
                            <p><strong>User ID:</strong> {{ Auth::id() }}</p>
                            <p><strong>Full Name:</strong> {{ Auth::user()->full_name }}</p>
                            <p><strong>Phone:</strong> {{ Auth::user()->phone }}</p>
                            <p><strong>Email:</strong> {{ Auth::user()->email ?? 'Not set' }}</p>
                            <p><strong>Role ID (from DB):</strong> {{ Auth::user()->role_id }}</p>
                            <p><strong>Status:</strong> {{ Auth::user()->status }}</p>

                            <!-- Access Role relationship -->
                            @if (Auth::user()->role)
                                <p><strong>Role Name (from relationship):</strong> {{ Auth::user()->role->name }}</p>
                            @endif
                        @endauth
                    </div>

                    <!-- All Session Data (Debug) -->
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                        <h3 class="font-bold mb-2">All Session Data (Debug):</h3>
                        <pre class="text-xs overflow-auto">{{ print_r(session()->all(), true) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
