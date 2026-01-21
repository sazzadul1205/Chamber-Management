<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'category',
        'description',
        'is_public',
        'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'setting_value' => 'array' // For JSON type
    ];

    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper methods
    public function getValueAttribute()
    {
        return $this->castValue($this->setting_value);
    }

    protected function castValue($value)
    {
        switch ($this->setting_type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    // Static helper to get setting
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // Static helper to set setting
    public static function setValue($key, $value, $type = 'string', $category = 'general', $description = null, $isPublic = false)
    {
        $setting = self::firstOrNew(['setting_key' => $key]);

        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        }

        $setting->fill([
            'setting_value' => $value,
            'setting_type' => $type,
            'category' => $category,
            'description' => $description,
            'is_public' => $isPublic,
            'updated_by' => auth()->id()
        ]);

        return $setting->save();
    }
}
