<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_code',
        'brand_name',
        'generic_name',
        'strength',
        'dosage_form',
        'unit',
        'manufacturer',
        'status',
    ];

    // =========================
    // SCOPES
    // =========================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('medicine_code', 'like', "%{$term}%")
                ->orWhere('brand_name', 'like', "%{$term}%")
                ->orWhere('generic_name', 'like', "%{$term}%")
                ->orWhere('manufacturer', 'like', "%{$term}%");
        });
    }

    public function scopeByDosageForm($query, $dosageForm)
    {
        return $query->where('dosage_form', $dosageForm);
    }

    // =========================
    // RELATIONSHIPS
    // =========================
    public function prescriptionItems()
    {
        return $this->hasMany(\App\Models\PrescriptionItem::class, 'medicine_id', 'id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(\App\Models\InventoryItem::class, 'generic_name', 'generic_name');
    }

    // =========================
    // HELPERS
    // =========================
    public static function dosageForms()
    {
        return [
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'syrup' => 'Syrup',
            'injection' => 'Injection',
            'gel' => 'Gel',
            'ointment' => 'Ointment',
            'mouthwash' => 'Mouthwash',
            'spray' => 'Spray',
            'drops' => 'Drops',
            'powder' => 'Powder',
            'cream' => 'Cream',
            'other' => 'Other',
        ];
    }

    public static function medicineCategories()
    {
        return [
            'analgesic' => 'Analgesic (Pain Killer)',
            'antibiotic' => 'Antibiotic',
            'local_anesthetic' => 'Local Anesthetic',
            'mouthwash' => 'Mouthwash/Rinse',
            'topical' => 'Topical Application',
            'muscle_relaxant' => 'Muscle Relaxant',
            'antifungal' => 'Antifungal',
            'corticosteroid' => 'Corticosteroid',
            'gi_medicine' => 'GI Medicine',
            'emergency' => 'Emergency Medicine',
            'dental_specific' => 'Dental Specific',
            'other' => 'Other',
        ];
    }

    // =========================
    // ATTRIBUTES
    // =========================
    public function getDosageFormNameAttribute()
    {
        $forms = self::dosageForms();
        return $forms[$this->dosage_form] ?? ucfirst($this->dosage_form ?? 'Unknown');
    }

    public function getFullNameAttribute()
    {
        $brand = $this->brand_name ?? '';
        $generic = $this->generic_name ?? '';
        $strength = $this->strength ?? '';
        return trim("{$brand} ({$generic}) {$strength}");
    }

    public function getFormattedCodeAttribute()
    {
        return '<span class="badge bg-info">' . ($this->medicine_code ?? '-') . '</span>';
    }

    public function getUsageCountAttribute()
    {
        return $this->prescriptionItems()->count();
    }

    // =========================
    // CATEGORY INFERENCE
    // =========================
    public function inferCategory(): string
    {
        $generic = strtolower($this->generic_name ?? '');

        if ($this->strContainsAny($generic, ['paracetamol', 'ibuprofen', 'diclofenac', 'aceclofenac', 'tramadol'])) {
            return 'analgesic';
        }
        if ($this->strContainsAny($generic, ['amoxicillin', 'metronidazole', 'clarithromycin', 'doxycycline', 'cefixime'])) {
            return 'antibiotic';
        }
        if ($this->strContainsAny($generic, ['lidocaine', 'articaine'])) {
            return 'local_anesthetic';
        }
        if ($this->strContainsAny($generic, ['chlorhexidine', 'povidone', 'fluoride'])) {
            return 'mouthwash';
        }
        if ($this->strContainsAny($generic, ['gel', 'ointment', 'paste'])) {
            return 'topical';
        }
        if ($this->strContainsAny($generic, ['chlorzoxazone', 'tizanidine'])) {
            return 'muscle_relaxant';
        }
        if ($this->strContainsAny($generic, ['fluconazole', 'nystatin'])) {
            return 'antifungal';
        }
        if ($this->strContainsAny($generic, ['dexamethasone', 'prednisolone', 'triamcinolone'])) {
            return 'corticosteroid';
        }
        if ($this->strContainsAny($generic, ['pantoprazole', 'omeprazole', 'ranitidine'])) {
            return 'gi_medicine';
        }
        if ($this->strContainsAny($generic, ['epinephrine', 'chlorpheniramine'])) {
            return 'emergency';
        }
        if ($this->strContainsAny($generic, ['tranexamic', 'hydrogen', 'saline'])) {
            return 'dental_specific';
        }

        return 'other';
    }


    public function getCategoryAttribute(): string
    {
        return $this->inferCategory();
    }

    public function getCategoryNameAttribute(): string
    {
        $categories = self::medicineCategories();
        $category = $this->inferCategory();
        return $categories[$category] ?? 'Other';
    }
}
