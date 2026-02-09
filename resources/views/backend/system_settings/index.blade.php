@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">System Settings</h2>

            <!-- Information Badge -->
            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span>Changes take effect immediately</span>
                </p>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-green-800 font-medium">Success</p>
                    <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-red-800 font-medium">Error</p>
                    <p class="text-red-700 text-sm mt-1">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- SETTINGS FORM -->
        <form action="{{ route('backend.system-settings.bulk-update') }}" method="POST" class="space-y-6">
            @csrf
            @method('POST')

            @foreach ($settings as $category => $categorySettings)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Category Header -->
                    <div class="px-6 py-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            @php
                                $categoryIcons = [
                                    'general' => 'Settings',
                                    'email' => 'Mail',
                                    'appointment' => 'Calendar',
                                    'billing' => 'CreditCard',
                                    'security' => 'Shield',
                                    'notification' => 'Bell',
                                    'display' => 'Monitor'
                                ];
                            @endphp

                            @if(isset($categoryIcons[strtolower($category)]))
                                @include('partials.icons', [
                                    'name' => $categoryIcons[strtolower($category)],
                                    'class' => 'w-5 h-5 text-blue-600',
                                ])
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @endif
                            <span>{{ ucfirst($category) }} Settings</span>
                        </h3>
                    </div>

                    <!-- Settings Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach ($categorySettings as $setting)
                                <div class="space-y-2">
                                    <label for="setting_{{ $setting->id }}" class="block">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ str_replace('_', ' ', ucwords($setting->setting_key)) }}
                                        </span>
                                        @if ($setting->description)
                                            <span class="block text-xs text-gray-500 mt-1">{{ $setting->description }}</span>
                                        @endif
                                    </label>

                                    @if ($setting->setting_type === 'boolean')
                                        <div class="relative">
                                            <select name="settings[{{ $setting->setting_key }}]" 
                                                    id="setting_{{ $setting->id }}"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                                <option value="true" {{ $setting->value ? 'selected' : '' }}>Enabled</option>
                                                <option value="false" {{ !$setting->value ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>

                                    @elseif($setting->setting_type === 'json')
                                        <textarea name="settings[{{ $setting->setting_key }}]" 
                                                  id="setting_{{ $setting->id }}"
                                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                  rows="3"
                                                  placeholder="Enter JSON data">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>

                                    @elseif($setting->setting_type === 'textarea')
                                        <textarea name="settings[{{ $setting->setting_key }}]" 
                                                  id="setting_{{ $setting->id }}"
                                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                  rows="3">{{ $setting->value }}</textarea>

                                    @elseif($setting->setting_type === 'number')
                                        <input type="number" 
                                               name="settings[{{ $setting->setting_key }}]" 
                                               id="setting_{{ $setting->id }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               value="{{ $setting->value }}">

                                    @elseif($setting->setting_type === 'email')
                                        <input type="email" 
                                               name="settings[{{ $setting->setting_key }}]" 
                                               id="setting_{{ $setting->id }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               value="{{ $setting->value }}">

                                    @elseif($setting->setting_type === 'password')
                                        <input type="password" 
                                               name="settings[{{ $setting->setting_key }}]" 
                                               id="setting_{{ $setting->id }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter password" 
                                               value="{{ $setting->value }}">

                                    @else
                                        <input type="text" 
                                               name="settings[{{ $setting->setting_key }}]" 
                                               id="setting_{{ $setting->id }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               value="{{ $setting->value }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- FORM ACTIONS -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Review all changes before saving</span>
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ url()->current() }}" 
                           class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            Reset Form
                        </a>
                        <button type="submit" 
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- IMPORTANT NOTES -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="font-medium text-yellow-900">Important Notes</h4>
                    <ul class="mt-2 text-sm text-yellow-800 space-y-1">
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Some settings require a system restart to take effect</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Changing email settings may affect notification delivery</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-block w-1.5 h-1.5 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <span>Backup your settings before making major changes</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        select:focus {
            outline: none;
            ring-width: 2px;
        }

        input:focus, textarea:focus {
            outline: none;
            ring-width: 2px;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .bg-yellow-50 {
            background-color: #fffbeb;
        }

        .border-blue-200 {
            border-color: #bfdbfe;
        }

        .border-yellow-200 {
            border-color: #fde68a;
        }
    </style>
@endsection