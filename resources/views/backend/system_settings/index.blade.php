@extends('backend.layout.structure')

@section('content')
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">System Settings</h1>
        </div>

        <!-- Success / Error Messages -->
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

        <!-- Settings Form -->
        <form action="{{ route('backend.system-settings.bulk-update') }}" method="POST">
            @csrf
            @method('POST')

            @foreach ($settings as $category => $categorySettings)
                <div class="mb-6 bg-white shadow rounded p-4">
                    <h2 class="text-lg font-semibold mb-4">{{ ucfirst($category) }} Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($categorySettings as $setting)
                            <div>
                                <label for="setting_{{ $setting->id }}" class="block text-gray-700 font-medium mb-1">
                                    {{ str_replace('_', ' ', ucwords($setting->setting_key)) }}
                                    @if ($setting->description)
                                        <span class="block text-sm text-gray-500">{{ $setting->description }}</span>
                                    @endif
                                </label>

                                @if ($setting->setting_type === 'boolean')
                                    <select name="settings[{{ $setting->setting_key }}]" id="setting_{{ $setting->id }}"
                                        class="w-full border border-gray-300 rounded p-2">
                                        <option value="true" {{ $setting->value ? 'selected' : '' }}>Yes</option>
                                        <option value="false" {{ !$setting->value ? 'selected' : '' }}>No</option>
                                    </select>
                                @elseif($setting->setting_type === 'json')
                                    <textarea name="settings[{{ $setting->setting_key }}]" id="setting_{{ $setting->id }}"
                                        class="w-full border border-gray-300 rounded p-2" rows="3">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                                @else
                                    <input type="{{ $setting->setting_type === 'number' ? 'number' : 'text' }}"
                                        name="settings[{{ $setting->setting_key }}]" id="setting_{{ $setting->id }}"
                                        class="w-full border border-gray-300 rounded p-2" value="{{ $setting->value }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Save All Changes
                </button>
            </div>
        </form>
    </main>
@endsection
