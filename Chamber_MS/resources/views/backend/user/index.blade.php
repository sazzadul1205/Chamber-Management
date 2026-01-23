@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Users Management</h2>
            <div class="flex flex-wrap gap-2">

                <a href="{{ route('backend.user.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Add New User</span>
                </a>

            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.user.index') }}" class="grid grid-cols-1 md:grid-cols-8 gap-3 mb-4">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, phone or email"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="role_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="md:col-span-1 flex gap-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">Filter</button>
                <a href="{{ route('backend.user.index') }}"
                    class="w-full bg-gray-300 hover:bg-gray-200 text-gray-800 rounded px-3 py-2 text-center">Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">#</th>
                        <th class="px-3 py-2 text-left text-sm">Full Name</th>
                        <th class="px-3 py-2 text-left text-sm">Phone</th>
                        <th class="px-3 py-2 text-left text-sm">Email</th>
                        <th class="px-3 py-2 text-left text-sm">Role</th>
                        <th class="px-3 py-2 text-center text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Created At</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2">{{ $user->full_name }}</td>
                            <td class="px-3 py-2">{{ $user->phone }}</td>
                            <td class="px-3 py-2">{{ $user->email ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $user->getRoleName() }}</td>
                            <td class="px-3 py-2 text-center">{!! $user->getStatusBadge() !!}</td>
                            <td class="px-3 py-2 text-center">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">

                                    <a href="{{ route('backend.user.show', $user->id) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>



                                    <a href="{{ route('backend.user.edit', $user->id) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>




                                    @php
                                        $disableDelete = false;

                                        if ($user->role_id == 1) {
                                            // Super Admin can never be deleted
                                            $disableDelete = true;
                                        } elseif ($user->role_id == 2 && auth()->user()->role_id != 1) {
                                            // Admin can only be deleted by Super Admin
                                            $disableDelete = true;
                                        }
                                    @endphp

                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.user.destroy', $user->id) }}"
                                        data-name="{{ $user->full_name }}"
                                        class="px-2 py-1 rounded text-xs text-white {{ $disableDelete ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700' }}"
                                        {{ $disableDelete ? 'disabled' : '' }}>
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-gray-500">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$users" class="mt-3" />

    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal id="deleteModal" title="Delete User" message="Are you sure?" :route="null" />
@endsection
