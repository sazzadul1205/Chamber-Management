<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    // Show settings index page (grouped by category)
    public function index()
    {
        $settings = SystemSetting::orderBy('category')
            ->orderBy('setting_key')
            ->get()
            ->groupBy('category');

        $categories = $settings->keys();

        return view('backend.system_settings.index', compact('settings', 'categories'));
    }

    // Show create form
    public function create()
    {
        $settingTypes = ['string', 'number', 'boolean', 'json', 'date'];
        $categories = ['general', 'appointment', 'billing', 'notification', 'inventory', 'patient', 'doctor', 'email', 'sms', 'other'];

        return view('backend.system_settings.create', compact('settingTypes', 'categories'));
    }

    // Store new setting
    public function store(Request $request)
    {
        $request->validate([
            'setting_key' => 'required|string|max:100|unique:system_settings',
            'setting_value' => 'required',
            'setting_type' => 'required|in:string,number,boolean,json,date',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        SystemSetting::create([
            'setting_key' => $request->setting_key,
            'setting_value' => $this->formatValue($request->setting_type, $request->setting_value),
            'setting_type' => $request->setting_type,
            'category' => $request->category,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('system-settings.index')
            ->with('success', 'System setting created successfully.');
    }

    // Show edit form
    public function edit(SystemSetting $systemSetting)
    {
        $settingTypes = ['string', 'number', 'boolean', 'json', 'date'];
        $categories = ['general', 'appointment', 'billing', 'notification', 'inventory', 'patient', 'doctor', 'email', 'sms', 'other'];

        return view('backend.system_settings.edit', compact('systemSetting', 'settingTypes', 'categories'));
    }

    // Update setting
    public function update(Request $request, SystemSetting $systemSetting)
    {
        $request->validate([
            'setting_value' => 'required',
            'setting_type' => 'required|in:string,number,boolean,json,date',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $systemSetting->update([
            'setting_value' => $this->formatValue($request->setting_type, $request->setting_value),
            'setting_type' => $request->setting_type,
            'category' => $request->category,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('system-settings.index')
            ->with('success', 'System setting updated successfully.');
    }

    // Delete setting
    public function destroy(SystemSetting $systemSetting)
    {
        // Don't allow deletion of critical settings
        $criticalSettings = [
            'clinic_name',
            'clinic_address',
            'clinic_phone',
            'currency',
            'timezone'
        ];

        if (in_array($systemSetting->setting_key, $criticalSettings)) {
            return redirect()->route('system-settings.index')
                ->with('error', 'Cannot delete critical system setting.');
        }

        $systemSetting->delete();
        return redirect()->route('system-settings.index')
            ->with('success', 'System setting deleted successfully.');
    }

    // API endpoint to get setting value
    public function getValue($key)
    {
        $value = SystemSetting::getValue($key);
        return response()->json(['value' => $value]);
    }

    // Format value based on type
    private function formatValue($type, $value)
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'json':
                return is_array($value) ? json_encode($value) : $value;
            case 'number':
                return is_numeric($value) ? $value : 0;
            default:
                return $value;
        }
    }

    // Bulk update settings from array
    public function bulkUpdate(Request $request)
    {
        $settings = $request->input('settings', []); // <-- get the nested array

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('setting_key', $key)->first();
            if ($setting) {
                $setting->update([
                    'setting_value' => $this->formatValue($setting->setting_type, $value),
                    'updated_by' => auth()->id()
                ]);
            }
        }

        return redirect()->route('backend.system-settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
