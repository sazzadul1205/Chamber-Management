<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    // =======================
    // CONSTANTS
    // =======================

    private const SETTING_TYPES = ['string', 'number', 'boolean', 'json', 'date'];

    private const CATEGORIES = [
        'general',
        'appointment',
        'billing',
        'notification',
        'inventory',
        'patient',
        'doctor',
        'email',
        'sms',
        'other',
    ];

    private const CRITICAL_SETTINGS = [
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'currency',
        'timezone',
    ];

    // =======================
    // LIST SETTINGS
    // =======================
    public function index()
    {
        $settings = SystemSetting::orderBy('category')
            ->orderBy('setting_key')
            ->get()
            ->groupBy('category');

        return view('backend.system_settings.index', [
            'settings'   => $settings,
            'categories' => $settings->keys(),
        ]);
    }

    // =======================
    // CREATE SETTING
    // =======================
    public function create()
    {
        return view('backend.system_settings.create', [
            'settingTypes' => self::SETTING_TYPES,
            'categories'   => self::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'setting_key'   => 'required|string|max:100|unique:system_settings,setting_key',
            'setting_value' => 'required',
            'setting_type'  => 'required|in:' . implode(',', self::SETTING_TYPES),
            'category'      => 'required|string|max:50',
            'description'   => 'nullable|string',
            'is_public'     => 'boolean',
        ]);

        SystemSetting::create([
            'setting_key'   => $validated['setting_key'],
            'setting_value' => $this->formatValue($validated['setting_type'], $validated['setting_value']),
            'setting_type'  => $validated['setting_type'],
            'category'      => $validated['category'],
            'description'   => $validated['description'] ?? null,
            'is_public'     => $request->boolean('is_public'),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()
            ->route('backend.system-settings.index')
            ->with('success', 'System setting created successfully.');
    }

    // =======================
    // EDIT SETTING
    // =======================
    public function edit(SystemSetting $systemSetting)
    {
        return view('backend.system_settings.edit', [
            'systemSetting' => $systemSetting,
            'settingTypes'  => self::SETTING_TYPES,
            'categories'    => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, SystemSetting $systemSetting)
    {
        $validated = $request->validate([
            'setting_value' => 'required',
            'setting_type'  => 'required|in:' . implode(',', self::SETTING_TYPES),
            'category'      => 'required|string|max:50',
            'description'   => 'nullable|string',
            'is_public'     => 'boolean',
        ]);

        $systemSetting->update([
            'setting_value' => $this->formatValue($validated['setting_type'], $validated['setting_value']),
            'setting_type'  => $validated['setting_type'],
            'category'      => $validated['category'],
            'description'   => $validated['description'] ?? null,
            'is_public'     => $request->boolean('is_public'),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()
            ->route('backend.system-settings.index')
            ->with('success', 'System setting updated successfully.');
    }

    // =======================
    // DELETE SETTING
    // =======================
    public function destroy(SystemSetting $systemSetting)
    {
        if (in_array($systemSetting->setting_key, self::CRITICAL_SETTINGS, true)) {
            return redirect()
                ->route('backend.system-settings.index')
                ->with('error', 'Cannot delete critical system setting.');
        }

        $systemSetting->delete();

        return redirect()
            ->route('backend.system-settings.index')
            ->with('success', 'System setting deleted successfully.');
    }

    // =======================
    // API: GET VALUE
    // =======================
    public function getValue(string $key)
    {
        return response()->json([
            'value' => SystemSetting::getValue($key),
        ]);
    }

    // =======================
    // BULK UPDATE
    // =======================
    public function bulkUpdate(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('setting_key', $key)->first();

            if (!$setting) {
                continue;
            }

            $setting->update([
                'setting_value' => $this->formatValue($setting->setting_type, $value),
                'updated_by'    => auth()->id(),
            ]);
        }

        return redirect()
            ->route('backend.system-settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    // =======================
    // VALUE FORMATTER
    // =======================
    private function formatValue(string $type, $value): string
    {
        return match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json'    => json_encode($value),
            'number'  => is_numeric($value) ? (string) $value : '0',
            default   => (string) $value,
        };
    }
}
