<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'category',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
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

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        });
    }

    // Relationships
    public function treatments()
    {
        if (class_exists(\App\Models\Treatment::class)) {
            return $this->hasMany(\App\Models\Treatment::class, 'diagnosis_code', 'code');
        }

        // Return an empty relation to avoid breaking queries
        return $this->hasManyDummy();
    }

    // Helper methods
    public static function categories()
    {
        return [
            'dental_caries' => 'Dental Caries',
            'pulp_diseases' => 'Pulp Diseases',
            'periodontal_diseases' => 'Periodontal Diseases',
            'gingivitis' => 'Gingivitis',
            'periodontitis' => 'Periodontitis',
            'gingival_disorders' => 'Gingival Disorders',
            'dentofacial_anomalies' => 'Dentofacial Anomalies',
            'malocclusion' => 'Malocclusion',
            'tooth_disorders' => 'Tooth Disorders',
            'alveolar_disorders' => 'Alveolar Disorders',
            'cysts' => 'Cysts',
            'jaw_disorders' => 'Jaw Disorders',
            'salivary_gland' => 'Salivary Gland',
            'stomatitis' => 'Stomatitis',
            'oral_mucosa' => 'Oral Mucosa',
            'common_conditions' => 'Common Conditions',
            'abscess' => 'Abscess',
            'other' => 'Other'
        ];
    }

    public function getCategoryNameAttribute()
    {
        $categories = self::categories();
        return $categories[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getFormattedCodeAttribute()
    {
        return '<span class="badge bg-primary">' . $this->code . '</span>';
    }

    public function getUsageCountAttribute()
    {
        return $this->treatments()->count();
    }

    // Helper method to return an empty relation
    protected function hasManyDummy()
    {
        return $this->hasMany(static::class)->whereRaw('1 = 0');
    }
}
