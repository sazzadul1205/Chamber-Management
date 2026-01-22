@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Diagnosis Code Details</h2>
            <div class="flex space-x-2">
                <a href="{{ route('backend.diagnosis-codes.index') }}"
                    class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
                <a href="{{ route('backend.diagnosis-codes.edit', $diagnosisCode->id) }}"
                    class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5h2M12 5v14m0 0h-2m2 0h2" />
                    </svg>
                    Edit
                </a>
                <button type="button" data-id="{{ $diagnosisCode->id }}" data-code="{{ $diagnosisCode->code }}"
                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded flex items-center delete-code">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-1 12H6L5 7m5-4h4l1 4H9l1-4z" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded shadow p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 font-medium">Code</p>
                    <p class="text-gray-800 text-lg font-semibold">{{ $diagnosisCode->code }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-medium">Category</p>
                    <p class="text-gray-800 text-lg font-semibold">{{ $diagnosisCode->category_name }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 font-medium">Description</p>
                    <p class="text-gray-800 text-lg">{{ $diagnosisCode->description }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-medium">Status</p>
                    <p
                        class="text-lg font-semibold px-2 py-1 rounded bg-{{ $diagnosisCode->status == 'active' ? 'green-100 text-green-800' : 'red-100 text-red-800' }}">
                        {{ ucfirst($diagnosisCode->status) }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 font-medium">Created At</p>
                    <p class="text-gray-800">{{ $diagnosisCode->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-medium">Last Updated</p>
                    <p class="text-gray-800">{{ $diagnosisCode->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Delete Diagnosis Code</h3>
            <p class="mb-2">Are you sure you want to delete code "<span id="deleteCodeName" class="font-medium"></span>"?
            </p>
            <p class="text-red-600 text-sm mb-4">This action cannot be undone!</p>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteCodeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete modal
            document.querySelectorAll('.delete-code').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const code = this.dataset.code;
                    document.getElementById('deleteCodeName').textContent = code;
                    document.getElementById('deleteCodeForm').action = `/diagnosis-codes/${id}`;
                    document.getElementById('deleteCodeModal').classList.remove('hidden');
                });
            });
        });

        function closeDeleteModal() {
            document.getElementById('deleteCodeModal').classList.add('hidden');
        }
    </script>
@endsection