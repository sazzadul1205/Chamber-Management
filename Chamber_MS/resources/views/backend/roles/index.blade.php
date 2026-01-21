@extends('backend.layout.structure')

@section('content')
    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Roles Management</h1>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                onclick="document.getElementById('createRoleModal').classList.remove('hidden')">
                + Add New Role
            </button>
        </div>

        <!-- Success / Error -->
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Role Name</th>
                        <th class="p-3 text-left">Created</th>
                        <th class="p-3 text-left">Updated</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3 font-medium">{{ $role->name }}</td>
                            <td class="p-3">{{ $role->created_at->format('d M Y') }}</td>
                            <td class="p-3">{{ $role->updated_at->format('d M Y') }}</td>
                            <td class="p-3 text-center space-x-2">
                                <button class="px-3 py-1 bg-yellow-500 text-white rounded"
                                    onclick="openEditModal({{ $role->id }}, '{{ $role->name }}')">
                                    Edit
                                </button>

                                <button class="px-3 py-1 bg-red-600 text-white rounded"
                                    onclick="openDeleteModal({{ $role->id }}, '{{ $role->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No roles found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <!-- CREATE MODAL -->
    <div id="createRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-md rounded p-6">
            <h2 class="text-lg font-semibold mb-4">Create Role</h2>

            <form method="POST" action="{{ route('backend.roles.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Role name" class="w-full border rounded p-2 mb-4"
                    required>

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded"
                        onclick="this.closest('#createRoleModal').classList.add('hidden')">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-md rounded p-6">
            <h2 class="text-lg font-semibold mb-4">Edit Role</h2>

            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')

                <input type="text" id="editRoleName" name="name" class="w-full border rounded p-2 mb-4" required>

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded" onclick="closeModal('editRoleModal')">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-md rounded p-6">
            <h2 class="text-lg font-semibold mb-3">Delete Role</h2>

            <p class="mb-4">
                Are you sure you want to delete
                <strong id="deleteRoleName"></strong>?
            </p>

            <form id="deleteRoleForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded" onclick="closeModal('deleteRoleModal')">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SIMPLE JS -->
    <script>
        function openEditModal(id, name) {
            document.getElementById('editRoleForm').action = `/roles/${id}`;
            document.getElementById('editRoleName').value = name;
            document.getElementById('editRoleModal').classList.remove('hidden');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteRoleForm').action = `/roles/${id}`;
            document.getElementById('deleteRoleName').innerText = name;
            document.getElementById('deleteRoleModal').classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
@endsection
