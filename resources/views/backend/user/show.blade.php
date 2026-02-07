@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-3">
            <h2 class="text-2xl font-semibold">User Details</h2>

            <div class="flex gap-2">
                @can('edit-users')
                    <a href="{{ route('backend.user.edit', $user->id) }}"
                        class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">
                        @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                        Edit
                    </a>
                @endcan

                <a href="{{ route('backend.user.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-800 rounded text-sm">
                    @include('partials.icons', ['name' => 'ArrowLeft', 'class' => 'w-4 h-4'])
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- User Information -->
            <div class="lg:col-span-2 bg-white rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 w-1/3">Full Name</th>
                            <td class="px-4 py-3">{{ $user->full_name }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Phone Number</th>
                            <td class="px-4 py-3">{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                            <td class="px-4 py-3">{{ $user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Role</th>
                            <td class="px-4 py-3">{{ $user->getRoleName() }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                            <td class="px-4 py-3">{!! $user->getStatusBadge() !!}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Created At</th>
                            <td class="px-4 py-3">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Last Updated</th>
                            <td class="px-4 py-3">{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if ($user->deleted_at)
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-red-600">Deleted At</th>
                                <td class="px-4 py-3 text-red-600">{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded shadow p-4 space-y-3">

                <h3 class="text-lg font-semibold mb-2">Quick Actions</h3>

                @if ($user->isDoctor())
                    @if ($user->doctor)
                        <a href="{{ route('doctors.show', $user->doctor->id) }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm">
                            @include('partials.icons', ['name' => 'Doctor', 'class' => 'w-4 h-4'])
                            View Doctor Profile
                        </a>
                    @else
                        <a href="#"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                            @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                            Create Doctor Profile
                        </a>
                    @endif
                @endif

                @can('reset-password')
                    <button data-modal-target="resetPasswordModal"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">
                        @include('partials.icons', ['name' => 'Key', 'class' => 'w-4 h-4'])
                        Reset Password
                    </button>
                @endcan

                @if ($user->deleted_at)
                    @can('restore-users')
                        <form action="{{ route('backend.user.restore', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Restore this user?')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                @include('partials.icons', ['name' => 'Restore', 'class' => 'w-4 h-4'])
                                Restore User
                            </button>
                        </form>
                    @endcan
                @endif

            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    @can('reset-password')
        <x-modal id="resetPasswordModal" title="Reset Password">
            <form action="{{ route('backend.user.reset-password', $user->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <input type="password" name="new_password_confirmation" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" data-modal-close class="px-4 py-2 bg-gray-300 hover:bg-gray-200 rounded text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                        Reset Password
                    </button>
                </div>
            </form>
        </x-modal>
    @endcan
@endsection
