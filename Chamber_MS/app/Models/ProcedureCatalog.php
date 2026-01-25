<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureCatalog extends Model
{
    use HasFactory;

    // =======================
    // Table Configuration
    // =======================
    protected $table = 'procedure_catalog';

    // =======================
    // Mass Assignable Fields
    // =======================
    protected $fillable = [
        'procedure_code',
        'procedure_name',
        'category',
        'standard_duration', // minutes
        'standard_cost',
        'description',
        'status',
    ];

    // =======================
    // Attribute Casting
    // =======================
    protected $casts = [
        'standard_duration' => 'integer',
        'standard_cost'     => 'decimal:2',
    ];

    // =======================
    // Query Scopes
    // =======================

    /**
     * Only active procedures
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // =======================
    // Relationships
    // =======================

    /**
     * Procedures used in treatments
     */
    public function treatmentProcedures()
    {
        return $this->hasMany(
            TreatmentProcedure::class,
            'procedure_code',
            'procedure_code'
        );
    }

    // =======================
    // Accessors
    // =======================

    /**
     * Human-readable duration (e.g. "1 hour 30 minutes")
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->standard_duration;

        if ($minutes < 60) {
            return $minutes . ' minutes';
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        return trim(
            $hours . ' hour' . ($hours > 1 ? 's' : '') .
                ($remaining ? ' ' . $remaining . ' minutes' : '')
        );
    }

    /**
     * Formatted cost (currency symbol handled here)
     */
    public function getFormattedCostAttribute(): string
    {
        return '₹' . number_format((float) $this->standard_cost, 2);
    }

    /**
     * Human-readable category name
     */
    public function getCategoryNameAttribute(): string
    {
        return self::categories()[$this->category]
            ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    // =======================
    // Static Helpers
    // =======================

    /**
     * Available procedure categories
     */
    public static function categories(): array
    {
        return [
            'diagnostic'      => 'Diagnostic',
            'preventive'      => 'Preventive',
            'restorative'     => 'Restorative',
            'endodontic'      => 'Endodontic',
            'prosthodontic'   => 'Prosthodontic',
            'periodontic'     => 'Periodontic',
            'oral_surgery'    => 'Oral Surgery',
            'orthodontic'     => 'Orthodontic',
            'pediatric'       => 'Pediatric',
            'other'           => 'Other',
        ];
    }
}
