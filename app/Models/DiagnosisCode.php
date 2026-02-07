<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisCode extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'code',
        'description',
        'category',
        'status',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'status' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Only active diagnosis codes
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

    /**
     * Search by code, description, or category
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Treatments associated with this diagnosis code
     *
     * NOTE:
     * If this relationship breaks, it SHOULD fail.
     * Silent failures hide real bugs.
     */
    public function treatments()
    {
        return $this->hasMany(Treatment::class, 'diagnosis_code', 'code');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Available diagnosis categories
     */
    public static function categories(): array
    {
        return [
            'dental_caries'        => 'Dental Caries',
            'pulp_diseases'        => 'Pulp Diseases',
            'periodontal_diseases' => 'Periodontal Diseases',
            'gingivitis'           => 'Gingivitis',
            'periodontitis'        => 'Periodontitis',
            'gingival_disorders'   => 'Gingival Disorders',
            'dentofacial_anomalies' => 'Dentofacial Anomalies',
            'malocclusion'         => 'Malocclusion',
            'tooth_disorders'      => 'Tooth Disorders',
            'alveolar_disorders'   => 'Alveolar Disorders',
            'cysts'                => 'Cysts',
            'jaw_disorders'        => 'Jaw Disorders',
            'salivary_gland'       => 'Salivary Gland',
            'stomatitis'           => 'Stomatitis',
            'oral_mucosa'          => 'Oral Mucosa',
            'common_conditions'    => 'Common Conditions',
            'abscess'              => 'Abscess',
            'other'                => 'Other',
        ];
    }

    /**
     * Human-readable category name
     */
    public function getCategoryNameAttribute(): string
    {
        return self::categories()[$this->category]
            ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    /**
     * Number of times this diagnosis is used
     */
    public function getUsageCountAttribute(): int
    {
        return $this->treatments()->count();
    }
}
