<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureCatalog extends Model
{
    use HasFactory;

    protected $table = 'procedure_catalog';

    protected $fillable = [
        'procedure_code',
        'procedure_name',
        'category',
        'standard_duration',
        'standard_cost',
        'description',
        'status'
    ];

    protected $casts = [
        'standard_duration' => 'integer',
        'standard_cost' => 'decimal:2'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Relationships
    public function treatmentProcedures()
    {
        if (class_exists(\App\Models\TreatmentProcedure::class)) {
            return $this->hasMany(\App\Models\TreatmentProcedure::class, 'procedure_code', 'procedure_code');
        }

        // Return an empty relation to avoid breaking queries
        return $this->hasManyDummy();
    }

    // Helper methods
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->standard_duration / 60);
        $minutes = $this->standard_duration % 60;

        if ($hours > 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' . $minutes . ' minutes';
        }
        return $minutes . ' minutes';
    }

    public function getFormattedCostAttribute()
    {
        return '₹' . number_format($this->standard_cost, 2);
    }

    public static function categories()
    {
        return [
            'diagnostic' => 'Diagnostic',
            'preventive' => 'Preventive',
            'restorative' => 'Restorative',
            'endodontic' => 'Endodontic',
            'prosthodontic' => 'Prosthodontic',
            'periodontic' => 'Periodontic',
            'oral_surgery' => 'Oral Surgery',
            'orthodontic' => 'Orthodontic',
            'pediatric' => 'Pediatric',
            'other' => 'Other'
        ];
    }

    public function getCategoryNameAttribute()
    {
        $categories = self::categories();
        return $categories[$this->category] ?? ucfirst($this->category);
    }

    // Helper method to return an empty relation
    protected function hasManyDummy()
    {
        return $this->hasMany(static::class)->whereRaw('1 = 0');
    }
}
