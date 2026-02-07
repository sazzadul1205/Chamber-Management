@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Patient Families</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.patient-families.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Create Family</span>
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

        <!-- Search -->
        <form method="GET" action="{{ route('backend.patient-families.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or name"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-4">
            @forelse($families as $family)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            {{ $family->family_code }}
                        </span>

                        <h3 class="mt-2 font-semibold text-lg">{{ $family->family_name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Head: {{ $family->head_name }}</p>
                        <p class="text-sm text-gray-600">Members: {{ $family->member_count }}</p>
                    </div>

                    <div class="mt-4 flex justify-between gap-1">
                        <!-- View Family -->
                        <a href="{{ route('backend.patient-families.show', $family) }}"
                            class="flex-1 px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs flex items-center justify-center gap-1">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_View',
                                'class' => 'w-4 h-4',
                            ])
                            <span>View</span>
                        </a>

                        <!-- Edit Family -->
                        <a href="{{ route('backend.patient-families.edit', $family) }}"
                            class="flex-1 px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs flex items-center justify-center gap-1">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Edit',
                                'class' => 'w-4 h-4',
                            ])
                            <span>Edit</span>
                        </a>

                        <!-- Delete Family -->
                        <button type="button" data-modal-target="deleteModal"
                            data-route="{{ route('backend.patient-families.destroy', $family) }}"
                            data-name="{{ $family->family_name }}"
                            class="flex-1 px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs flex items-center justify-center gap-1">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Delete',
                                'class' => 'w-4 h-4',
                            ])
                            <span>Delete</span>
                        </button>
                    </div>

                </div>
            @empty
                <p class="text-center col-span-full text-gray-500">No families found</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$families" class="mt-3" />

    </div>

    <!-- Delete Modal -->
    <x-delete-modal id="deleteModal" title="Delete Family" message="Are you sure?" :route="null" />
@endsection
