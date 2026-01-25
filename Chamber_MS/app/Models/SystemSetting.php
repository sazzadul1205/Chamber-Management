<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    // =======================
    // Mass Assignable Fields
    // =======================
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'category',
        'description',
        'is_public',
        'updated_by',
    ];

    // =======================
    // Attribute Casting
    // =======================
    protected $casts = [
        'is_public' => 'boolean',
    ];

    // =======================
    // Relationships
    // =======================

    /**
     * User who last updated the setting
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =======================
    // Accessors
    // =======================

    /**
     * Always return the setting value
     * casted based on setting_type
     */
    public function getValueAttribute()
    {
        return $this->castValue($this->setting_value);
    }

    // =======================
    // Internal Helpers
    // =======================

    /**
     * Cast value based on type
     */
    protected function castValue($value)
    {
        if ($value === null) {
            return null;
        }

        return match ($this->setting_type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number'  => str_contains($value, '.')
                ? (float) $value
                : (int) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    // =======================
    // Static Helpers
    // =======================

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        return optional(
            self::where('setting_key', $key)->first()
        )->value ?? $default;
    }

    /**
     * Create or update a setting
     */
    public static function setValue(
        string $key,
        $value,
        string $type = 'string',
        string $category = 'general',
        ?string $description = null,
        bool $isPublic = false
    ): bool {
        $setting = self::firstOrNew(['setting_key' => $key]);

        // Normalize value before saving
        $normalizedValue = match ($type) {
            'json'    => json_encode($value),
            'boolean' => $value ? 'true' : 'false',
            default   => (string) $value,
        };

        $setting->fill([
            'setting_value' => $normalizedValue,
            'setting_type'  => $type,
            'category'      => $category,
            'description'   => $description,
            'is_public'     => $isPublic,
            'updated_by'    => auth()->id(),
        ]);

        return $setting->save();
    }
}
